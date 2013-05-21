<?php
if(!defined('MODX_BASE_PATH')){die('What are you doing? Get out of here!');}
 /**
 * GetDate
 *
 * @category  parser
 * @version   0.1
 * @license     GNU General Public License (GPL), http://www.gnu.org/copyleft/gpl.html
 * @author Agel_Nash <Agel_Nash@xaker.ru>
 */
if(isset($strtime)){
  $time = strtotime($strtime);
}
if(!isset($time)){
  $time=time();
}

if(!function_exists("HumanDate")){function HumanDate($date,$format) {
	$out='';
	$a = preg_split("/[:\.\s-]+/", date("Y.m.d H:i:s",$date));
    $d = time() - $date;
	if($d>0){
		switch(true){
			case ($d>0 && $d < 3540):{ //До 59 минут назад
				switch ($tmp=abs(floor($d / 60))) {
					case 0:
						$out="меньше минуты назад";
						break;
					default:
						$out = $tmp . ' мин. назад';
						break;
				}
				break;
			}
			case ($d>=3540 && $d < 46800):{ //До 12 часов назад
				switch (floor($d / 3600)) {
					case 1:
						$out="1 час назад";
						break;
					case 2:
						$out="2 часа назад";
						break;
					case 3:
						$out="3 часа назад";
						break;
					case 4:
						$out="4 часа назад";
						break;
					default:{
						$out = floor($d / 3600)." часов назад";
					}
				}
				break;
			}
			case ($d>=46800 && $d < 259200):{ //Сегодня, вчера, позавчера
				switch($a[2]) {
					case date('d'):{
						$out="сегодня в {$a[3]}:{$a[4]}";
						break;
					}
					case date('d', time() - 86400):{
						$out="вчера в {$a[3]}:{$a[4]}";
						break;
					}
					case date('d', time() - 172800):{
						$out="позавчера в {$a[3]}:{$a[4]}";
						break;
					}
				}
				break;
			}
		}
	}else{
		switch(true){
			case ($d<=0 && $d > -3540):{ //До 59 минут вперед
				switch ($tmp=abs(floor($d / 60))) { 
					case 0:
						$out="сейчас";
						break;
					default:
						$out="через " . $tmp . ' мин.';
						break;
				}
				break;
			}
			case ($d<=-3540 && $d > -43200):{ //До 12 часов вперед
				switch ($tmp=abs(floor($d / 3600))) {
					case 0:
					case 1:
						$out="через час";
						break;
					case 2:
					case 3:
					case 4:
						$out="через ".$tmp." часа";
						break;
					default:
						$out="через ".$tmp." часов";
						break;
				}
				break;
			}
			case ($d<=-43200 && $d > -259200):{ //Сегодня, завтра, послезавтра
				switch($a[2]){
					case date('d'):{
						$out="сегодня в {$a[3]}:{$a[4]}";
						break;
					}
					case date('d', time() + 86400):{
						$out="завтра в {$a[3]}:{$a[4]}";
						break;
					}
					case date('d', time() + 172800):{
						$out="послезавтра в {$a[3]}:{$a[4]}";
						break;
					}
				}
				break;
			}
		}
	}
	if($out==''){
		$out = date($format, $date);
	}
  return $out;
}}

switch($format){
  case 'HumanDate':{
  	$out = HumanDate($time,isset($aformat)?$aformat:"Y.m.d H:i:s");
    break;
  }
	case 'W3C':{
  	$date = new DateTime();
    $date->setTimestamp($time);
    $out = $date->format(DateTime::W3C);
    break;
  }
  default:{
  	$out = date($format,$time);
  }
}
return $out;
?>