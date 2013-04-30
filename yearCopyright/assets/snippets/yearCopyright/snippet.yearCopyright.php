<?php
$dy = isset($dy) ? (int)$dy : '2013'; //$dy - год разработки
if($dy===0)$dy=date('Y');
if(date('Y')==$dy)return date('Y');else return $dy.' &mdash; '.date('Y');
?>