<?php
$list=isset($list)?explode(',',$list):array();
$id='';
if(isset($cookie) && isset($_COOKIE[$cookie])){
  $id=$_COOKIE[$cookie];
}
if($id==''){
   $id=array_rand($list);
			if(isset($cookie)){
			   setcookie($cookie,$id,time()+365*24*3600);
			}
}
return $list[$id];