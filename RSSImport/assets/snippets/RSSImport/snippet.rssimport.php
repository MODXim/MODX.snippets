<?php
/*RSS import
snippet can imports RSS or Atom feeds and show at your site
uses templates.

---
Author: Smornov Sergey aka Ifman http://ifman.ru
Mailto: ifman@yandex.ru
Page of snippet: http://dayte2.com/?act=state&num=174
Official page on modx.com: http://modx.com/extras/package/rssimport
*/
//error_reporting(E_ALL | E_STRICT);
//ini_set('display_errors', 1);

//sets default timexone to kill some errors in strict mode
if (function_exists('date_default_timezone_set'))
date_default_timezone_set('Europe/Moscow');

//parameters and defaults
if(!isset($url) || $url=='')	{return 'No RSS URL to parse.';}
$baseurl = $url;
//if urls separated with ',,' - this is many urls. Must collect all of them
$urls = (strstr($url,',,')!==false)	?	explode(',,', $url)	:	array($url);
if(!isset($num) || $num=='')	{$num = 10;}
if(!isset($tpl) || !$modx->getChunk($tpl))	{$tpl = '';}
if(!isset($more) || $more=='')	{$more = 'more';}
if(!isset($dateFormat) || $dateFormat=='')	{$dateFormat = false;}
if(!isset($cacheAge) || $cacheAge=='')	{$cacheAge = 0;}
$default_tpl = <<<EOF
<div class="rss_item">
<div class="rss_header">
	<span class="rss_date">[+date+]</span>
	<a href="[+link+]">[+title+]</a> из <a href="[+feed_link+]">[+feed_description+]</a>
</div>
<div class="rss_text">[+text+]</div>
<a href="[+link+]">[+more+]</a>
</div>
EOF;
$output='';
$basePath = $modx->config['base_path'];


//Work with cache
if($cacheAge)	{
	define( CACHE_DIR, $basePath.$modx->getCachePath());
	define( CACHE_AGE, $cacheAge);
	require_once($basePath.'manager/media/rss/rss_cache.inc');
	$cache = new RSSCache(CACHE_DIR, CACHE_AGE);
	$cache_status = $cache->check_cache($url);
	if($cache_status == 'HIT') {	//have fresh cache
		$output = $cache->get($baseurl);
		//uncomment this if you want to see and control cache work
		//RSS::_log('FROM CACHE');
	}
}
if($output=='')	{	//cache is off OR have no cache OR it's STALE
	//use system class Snoopy
	//this class is used in manager panel to show news on startpage.
	require_once($basePath.'manager/media/rss/extlib/Snoopy.class.inc');
	$client = new Snoopy();
	$client->agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13 GTB7.1';
	$client->read_timeout = '10';
	$client->use_gzip = true;
	$datas = array();
	foreach($urls as $url)	{
		//that's it!
		$client->fetch($url);
		//parse xml
		$data = RSS::parse($client->results, $url, $more);
		//merge results
		$datas = array_merge($datas, $data);
	}
	//sort results by date
	usort($datas, 'RSS::date_sort');
	//cut some items
	$datas = array_splice($datas, 0, $num);
	//parse template and collect results
	$outputs = array();
	foreach($datas as $param)	{
		//translate rss date (Fri, 01 Apr 2011 14:13:08 +0400) to readable view
		//need $dateFormat. If no $dateFormat - don't touch date
		$param[date] = RSS::format_date($param[date], $dateFormat);
		if($tpl)	{	//has chunk
			array_push($outputs, $modx->parseChunk($tpl, $param, '[+','+]'));
		}
		else	{	//no chunk, use default template
			array_push($outputs, RSS::parse_tpl($default_tpl, $param));
		}
	}
	//join results to string
	$output = implode($outputs,"\n\n");
	//if cache is on
	if($cacheAge)	{
		//store cache for future generations!
		$cache->set($baseurl,$output);
	}
}
return $output;




/**********************/
//Class incapsulating functions to avoid coincidence of functions names
class RSS	{
	//function for usort to sort by GMT date
	public static function date_sort($a,$b)	{
		$d1 = strtotime($a[date]);
		$d2 = strtotime($b[date]);
		if($d1 == $d2)	{return 0;}
		return ($d1<$d2) ? 1 : -1;
	}
	//parse source to array of assoc arrays
	//auto recode, auto escape specials
	//return $data[0..n][link, title, date, text, more];
	public static function parse($source, $url, $more)	{
		$source = str_replace('$','&#36;',$source);
		//try to get encoding of RSS
		if (preg_match('/<?xml.*encoding=[\'"](.*?)[\'"].*?>/m', $source, $m)) {
			$in_enc = strtoupper($m[1]);
		}
		else {$in_enc = 'UTF-8';}	//default encoding is utf-8
		//change encoding if it's needed
		$source = RSS::recode($in_enc, $modx->config['modx_charset'], $source);
		
		//Collect data about feeed
		$feed_title = RSS::find_tag($source, 'title');
		$feed_link = RSS::find_tag($source, 'link');
		$feed_description = RSS::find_tag($source, 'description');
		#RSS::_log("$feed_title | $feed_link | $feed_description");
		
		//parse items
		preg_match_all('/<item[^>]*>(.*?)<\/item>/ism', $source, $items);
		$items = $items[1];
		
		/*******************\
		<link>link to item</link>
		<title>Title of item</title>
		<pubDate>Fri, 01 Apr 2011 14:13:08 +0400</pubDate> 
		<description><![CDATA[Text of item]]></description>	
		\*******************/
		$outputs = array();
		//walk on items
		for($i=0; $i<count($items); $i++)    {
			$item = $items[$i];
			//Collect data
			$link = RSS::find_tag($item, 'link');
			$title = RSS::find_tag($item, 'title');
			$date = RSS::find_tag($item, 'pubdate');
			$text = RSS::find_tag($item, 'description');
			//clear CDATA
			$text = preg_replace('/(<!\[CDATA\[|\]\]>)/i', '', $text);
			
			//Escape MODx specials
			$from = array('{', '}', '[', ']');
			$to = array('&#123;', '&#125;', '&#91;', '&#93;');
			$text = str_replace($from, $to, $text);
			
			$param = array(
				'link'=>$link,
				'title'=>$title,
				'date'=>$date,
				'text'=>$text,
				'more'=>$more,
				'feed_title'=>$feed_title,	//item contents info about feed
				'feed_link'=>$feed_link,
				'feed_description'=>$feed_description,
			);
			array_push($outputs, $param);
		}
		return $outputs;
	}
	//Get string date. Date must be in GNU format.
	//If has $dateFormat - change date according it
	//If no $dateFormat - don't touch date
	public static function format_date($date, $format)	{
		global $modx;
		if(!$format)	{return $date;}
		$unix = strtotime($date);
		return strftime($format, $unix);
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