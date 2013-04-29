<?php
/********
Theme 'Tango' for MODx-Gismeteo-snippet.
Author: Smirnov Sergey aka ifman http://ifman.ru
Icons by Tango project: http://tango.freedesktop.org/Tango_Desktop_Project
Site: http://dayte2.com
Mail: ifman@yandex.ru

This must be located in /assets/snippets/gismeteo/Tango/theme.pnp
Requires translation file: /assets/snippets/gismeteo/translate-LANG.inc.php
	where LANG - name of language. 'ru' is default.

Theme supports template (chunk) and provides default template.

===Supported placeholders
	[+ico+] - url to icon for current weather /assets/snippets/gismeteo/Tango/$ico.png use as 'src' of <img>
	[+weekday+] - name of weekday
    [+hour+] - for what hour forecast is
    [+tod+] - time of day
    [+date+] - date in format dd.mm
    [+phenomena_cloudiness+] - cloudiness
    [+phenomena_precipitation+] - precipitation
    [+phenomena_rpower+] - power of rain if it's raining
    [+phenomena_spower+] - power of storm if it's storming
    [+pressure_max+]
    [+pressure_min+]
    [+temperature_max+]
    [+temperature_min+]
    [+wind_min+]
    [+wind_max+]
    [+wind_direction+] - wind direction in points
    [+relwet_max+]
    [+relwet_min+]
    [+heat_min+] - the temperature by feelings of season-weared human
    [+heat_max+]
    [+town+] - name of town.
    [+fulldate+] - date in format dd.mm.yyyy
    [+yyyy+] - year in format yyyy
    [+yy+] - year in format yy
    [+dd+] - day in format dd
    [+d+] - day in format d
    [+m+] - month in format m
    [+mm+] - month in format mm
    [+smonth+] - month as string.

NOTE. This snippet uses gismeteo xml API. In term of use of this API wroted, that backlink to gismeteo.ru is necessary. If you change template - note it.
********/
global $modx;
class Tango	{
function show($data, $tpl)	{
	global $modx;
	$param=$data[forecast][1];
	//icon find
	$ico_precipitation = array(
		4=>'rain.png',
		5=>'showers.png',
		6=>'snow.png',
		7=>'snow.png',
		8=>'storm.png'
	);
	$ico_cloudiness = array(
		'clear.png',
		'few-clouds.png',
		'clouds.png',
		'clouds.png',
	);
	$ico = $ico_precipitation[$param[phenomena_precipitation]];
	if(!$ico)	{	//if no precipitations - show cloudiness
		$ico = $ico_cloudiness[$param[phenomena_cloudiness]];
	}
	
	$translate_path = SNIPPET_PATH.'translate-'.LANG.'.inc.php';
	if(file_exists($translate_path)){
		require($translate_path);
	}
	foreach($param as $k=>&$v)	{
		$v = (isset($translate[$k])) ? Gis::recode('utf-8',MODX_CHARSET, $translate[$k][$v]) : $v;
	}
	$param[town]=$data[town];
	preg_match('/(\d\d)\.(\d\d)\.(\d\d\d\d)/',$param[date],$m);
	$param[fulldate] = $param[date];
	$param[date] = $m[1].'.'.$m[2];
	$param[yyyy] = $m[3];
	$param[yy] = substr($m[3],2);
	$param[dd] = $m[1];
	$param[d] = preg_replace('/^0/','',$m[1]);
	$param[mm] = $m[2];
	$param[m] = preg_replace('/^0/','',$m[2]);
	$param[smonth] = Gis::recode('utf-8',MODX_CHARSET, $months[$param[m]]);
	$param[ico] = '/assets/snippets/gismeteo/Tango/'.$ico;

	
	
	$default_tpl = <<<HERE
<div class="weather_outer">
	<img src="[+ico+]" class="w_ico">
	<div class="w_out_text">
		<div class="w_town">[+town+]</div>
		<div>[+fulldate+], [+weekday+].</div>
		<div class="w_temp">[+temperature_min+]&nbsp;&mdash; [+temperature_max+]&#176;</div>
	</div>
	<a href="http://www.gismeteo.ru" class="w_backlink">Предоставлено Gismeteo.Ru</a>
</div>
HERE;
	if($tpl == 'default')	{
		$output = Gis::parse_tpl($default_tpl, $param);
	}
	else	{
		$output = $modx->parseChunk($tpl, $param, '[+','+]');
	}
	$output = Gis::recode('UTF-8',MODX_CHARSET,$output);
	$modx->regClientCSS($modx->config['base_url'].'assets/snippets/gismeteo/Tango/theme.css');
	return($output);
}
}
?>