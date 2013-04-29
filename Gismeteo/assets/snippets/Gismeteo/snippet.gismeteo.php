<?php
//error_reporting(E_ALL | E_STRICT);
//ini_set('display_errors', 1);
/*Gismeteo informer
snippet gets weather from Gismeteo.ru and shows it using themes

---
Author: Smornov Sergey aka Ifman http://ifman.ru
Mailto: ifman@yandex.ru
Page of snippet: http://dayte2.com/modx-rss-import
Official page on modx.com: http://modx.com/extras/package/gismeteoinformer
*/
//region
if(!isset($region) || $region=='')	{$region=27612;} //Moscow
$region = str_replace('_1','',$region);
//language
if(!isset($lang) || $lang=='')	{$lang='ru';}
$lang = preg_replace('/[.\/]/','',$lang);
if($lang)	{define(LANG,$lang);}
//theme of output
if(!isset($theme) || $theme=='')	{$theme='Silk';}
$theme = preg_replace('/[.\/]/','',$theme);
//template
if(!isset($tpl) || !$modx->getChunk($tpl))	{$tpl='default';}


$output='';
if(!defined('BASE_PATH'))	{	define(BASE_PATH, $modx->config['base_path']);}
if(!defined('SNIPPET_PATH'))	{	define(SNIPPET_PATH, BASE_PATH.'assets/snippets/Gismeteo/');}
if(!defined('MODX_CHARSET'))	{	define(MODX_CHARSET, $modx->config['modx_charset']);}
if(!is_dir(SNIPPET_PATH.'lib/'.$theme))	{$theme = 'Text';}

$updates = array(150, 510, 870, 1230);
$timemark = Gis::get_timemark($updates);
$cachemark = Gis::cachemark($timemark, $region);

if(file_exists($_SERVER['DOCUMENT_ROOT'].$modx->getCachePath().'/gismeteo'.$cachemark.'.xml'))	{
	$source = file_get_contents($_SERVER['DOCUMENT_ROOT'].$modx->getCachePath().'/gismeteo'.$cachemark.'.xml');
}
else	{
	$url = 'http://informer.gismeteo.ru/xml/'.$region.'_1.xml';
	//Use system lib to emulate browser and get data
	require_once(BASE_PATH.'manager/media/rss/extlib/Snoopy.class.inc');
	$client = new Snoopy();
	$client->agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13 GTB7.1';
	$client->read_timeout = '3';
	$client->use_gzip = true;
	@$client->fetch($url);
	$source = $client->results;
	if(!$source)	{
		return "Can't get xml from gismeteo.ru";
	}
	//cache
	Gis::rewrite_cache($source, $timemark, $region, $updates);
}
//parsing
$xml_parser = xml_parser_create();
xml_parse_into_struct($xml_parser, $source, $vals, $index);
xml_parser_free($xml_parser);

$data = array();
$cur_key=0;
foreach($vals as $i=>$elm)	{
	if(!$data[town] && $elm[tag] == 'TOWN' && $elm[attributes][SNAME])	{
		//url-decode
		$data[town] = Gis::recode('Windows-1251', MODX_CHARSET, urldecode($elm[attributes][SNAME]));
	}
	if($elm[tag]=='FORECAST' && $elm[type]=='open')	{
		//forecast parsing
		$date = $elm[attributes][DAY].'.'.$elm[attributes][MONTH].'.'.$elm[attributes][YEAR];
		$cur_key++;
		$data[forecast][$cur_key][weekday] = $elm[attributes][WEEKDAY];
		$data[forecast][$cur_key][hour] = $elm[attributes][HOUR];
		$data[forecast][$cur_key][tod] = $elm[attributes][TOD];
		$data[forecast][$cur_key][date] = $date;
	}
	if($elm[type]=='complete' && preg_match('/(PHENOMENA|PRESSURE|TEMPERATURE|WIND|RELWET|HEAT)/',$elm[tag],$m))	{
		$m=strtolower($m[1]);
		foreach($elm[attributes] as $key=>$val)	{
			$data[forecast][$cur_key][$m.'_'.strtolower($key)] = $val;
		}
	}
}

$theme_path = SNIPPET_PATH.'lib/'.$theme.'/theme.php';
//if theme presents - use it
if(file_exists($theme_path))	{
	require_once($theme_path);
	$th = new $theme;
	$output = $th->show($data, $tpl);
}
else	{
	$output = "No theme file.";
}

return $output;


/***************************************************************************/
//Class incapsulating functions to avoid coincidence of functions names
class Gis	{
	//write a new cache of xml with curtime mark, returned by cachemark()
	//remove other caches
	public static function rewrite_cache($source, $timemark, $region, $updates)	{
		global $modx;
		$cachemark = Gis::cachemark($timemark, $region);
		$fh = fopen(BASE_PATH.$modx->getCachePath().'/gismeteo'.$cachemark.'.xml', 'w');
		fwrite($fh, $source);
		fclose($fh);
		foreach($updates as $time)	{
			if($time == $timemark)	{continue;}
			$mark = Gis::cachemark($time, $region);
			$path = BASE_PATH.$modx->getCachePath().'/gismeteo'.$mark.'.xml';
			if(file_exists($path))	{
				unlink($path);
			}
		}
	}
	public static function cachemark($timemark, $region)	{
		return $region.'_'.$timemark;
	}
	//Returns needed time-mark for current time
	//Gismeteo uses winter time in Moscow. It's GMT+3.
	//Don't change it. It's not your time, it's time of Gismeteo servers!
	//Timemark is hours*60+seconds
	public static function get_timemark($updates)	{
		$cur_date = gmdate('G:i');
		$a = explode(':',$cur_date);
		$d = ($a[0]+3)*60 + $a[1];
		for($i=0;$i<count($updates);$i++)	{
			if($d < $updates[$i])	{
				return $updates[$i-1];
			}
		}
		return end($updates);
	}
	//Get some text as template and assoc array with params
	//finds in template constructions like [+tag+]
	//replaces them with $param[tag]
	//return filled template
	public static function parse_tpl($tpl, $param)	{
		foreach($param as $tag=>$val)	{
			$reg = "/\[\+".$tag."\+\]/ism";
			$tpl = preg_replace($reg, $val, $tpl);
		}
		return $tpl;
	}
	//Get some xml ($text) and name of tag ($tag)
	//returns contents of FIRST tag
	public static function find_tag($text, $tag)    {
		$reg = "/<".$tag."[^>]*>(.*?)<\/".$tag.">/ism";
		preg_match($reg, $text, $res);
		return $res[1];
	}
	//simple output
	public static function _log($msg)	{
		print $msg."<br>\n";
	}
	//readable dump of var
	public static function dumper($msg)	{
		print '<pre>';
		print_r($msg);
		print '</pre>';
	}
	//Function to recode string from one encoding to other
	//Gets $from - string with name of source encoding
	//$to - string with name of target encoding
	//$text - string to recode
	//if $from and $to - same encodings - don't do anything
	//trying to use iconv and mb_convert_encoding like it done in system libs
	public static function recode($from, $to, $text)    {
		if(preg_match('/^\s*$/',$text))	{return '';}
		if(strtoupper($from) == strtoupper($to))	{return $text;}
		if (function_exists('iconv'))  {
			$text = iconv($from, $to, $text);
		}
		elseif(function_exists('mb_convert_encoding')) {
			// iconv didn't work, try mb_convert_encoding
			// @see http://php.net/mbstring
			$text = mb_convert_encoding($text, $to, $from );
		}
		else {
			//if no functions to recode - text'll be broken
			//so replace text to error msg and instructions.
			$text = "Can't recode text from $from to $to. Try to install iconv on your server.";
		}
		return $text;
	}
}
?>