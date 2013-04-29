<?php
if ( !function_exists('pPrint') )
{
  function pPrint($arr, $return = false){
      $output = '<pre>'.print_r($arr, TRUE).'</pre>';
      if ($return)
          return $output;
      else
          echo $output;
  }
}

$apiKey = isset($apiKey) ? $apiKey : '780e85d13a23afb3f05f414e5573a7cf';

$config['action'] = isset($action) ? $action  : 'list';
$config['email']  = isset($email)  ? $email   : 'chucktrukk@yahoo.com';
$config['tpl']    = isset($tpl)    ? $tpl     : 'tpl-photos';
$config['debug']  = isset($debug)  ? $debug   : FALSE;
$config['gallery']= isset($gallery)? $gallery : FALSE;
$config['parents']= isset($parents)? $parents : $modx->documentIdentifier;
$config['path']   = dirname(__FILE__);

include_once($config['path'] . '/library/flickrGallery.php');
$flickrGallery = new FlickrGallery($apiKey, $config);