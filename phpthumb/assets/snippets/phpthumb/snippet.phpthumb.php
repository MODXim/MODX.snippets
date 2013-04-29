<?php
/**
 * SEOphpthumb
 *
 * @category  snippet
 * @version 	1.1
 * @license 	GNU General Public License (GPL), http://www.gnu.org/copyleft/gpl.html
 * @author Agel_Nash <Agel_Nash@xaker.ru>
 */
  
if(!defined('MODX_BASE_PATH')){die('What are you doing? Get out of here!');}
//[[phpthumb? &input=`[+tvimagename+]` &options=`w_255,h=200`]]
if($input == '' || !file_exists($_SERVER['DOCUMENT_ROOT']."/".$input))
    {return 'assets/snippets/phpthumb/noimage.png';}
else{   
     $replace  = Array("," => "&", "_" => "=");
    $options  = strtr($options, $replace);
    $opt = $options;
    $path_parts=pathinfo($input);
    require_once MODX_BASE_PATH."/assets/snippets/phpthumb/phpthumb.class.php";
    $phpThumb = new phpthumb();
    $phpThumb->setSourceFilename($input); 
    $options = explode("&", $options);
    $allow=array('f'=>'jpg','q'=>'96');
    $need=array_keys($allow);
    foreach ($options as $value) {
       $thumb = explode("=", $value);
      if(in_array($thumb[0],$need)) {
      	$opt.="&".$thumb[0]."=".$thumb[1];
        unset($allow[$thumb[0]]);
      }
       $phpThumb->setParameter($thumb[0], $thumb[1]);
       $op[$thumb[0]]=$thumb[1];
    }
  foreach($allow as $key=>$value){
    $opt.="&".$key."=".$value;
    $phpThumb->setParameter($key, $value);
  	$op[$key]=$value;
  }
  
  $tmp=preg_replace("#^".$_SERVER['DOCUMENT_ROOT']."assets/images/#","",$input);
  $tmp=preg_replace("#^assets/images/#","",$tmp);
	$tmp=preg_replace("#/".$path_parts['basename']."$#","",$tmp);
    
  $ftime=filemtime($input);
  $tmp="assets/cache/phpthumb/".$tmp;
  $tmp=explode("/",$tmp);
  $tmp[]=md5($opt);
  $tmp[]=date("Y-m",$ftime);
 
	for($i=0;$i<count($tmp);$i++){
		$folder.="/".$tmp[$i];
		if(!is_dir(MODX_BASE_PATH.$folder) || !file_exists(MODX_BASE_PATH.$folder)){
			mkdir(MODX_BASE_PATH.$folder);
		}
	}
 
  	$outputFilename =MODX_BASE_PATH.$folder."/".date("d_h_i_s",$ftime)."_".$path_parts['extension']."_".$path_parts['filename'].".".$op['f'];
   
    if (!file_exists($outputFilename)) if ($phpThumb->GenerateThumbnail()) $phpThumb->RenderToFile($outputFilename) ;
    $res = explode("/assets", $outputFilename,2); 
    $res = "/assets".$res[1];
    return $res;
}
?>