<?php
$dy = isset($dy) ? (int)$dy : '2013'; //$dy - год разработки
if($dy===0) $dy=date('Y');

$sep = isset($sep) ? $sep : '&mdash;'; //Разделитель между годами

return (date('Y')==$dy) ? date('Y') : $dy.' '.$sep.' '.date('Y');
?>
