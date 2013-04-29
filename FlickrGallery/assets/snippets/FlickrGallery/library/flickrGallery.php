<?php

class FlickrGallery
{
  var $config;
  var $path;
  var $flickr;
  var $user;
  var $template;
  var $currentGallery;
  var $debugData;
  
  function __construct($apiKey, $config = array())
  {
    $this->config = $config;
    
    $this->path = $config['path'];
    
    include_once($this->path . '/library/phpFlickr.php');
    $this->flickr = new phpFlickr($apiKey);
    $this->flickr->enableCache('fs', $this->path.'/cache');
    
    $this->user = $this->flickr->people_findByEmail($config['email']);

    $this->template = $config['template'];
    
    $this->currentGallery = $config['gallery'];
    
    $this->execute();
  }
  
  function execute()
  {    
    switch($this->config['action']):
      case 'photos':
        echo $this->showPhotos();
        break;
      case 'galleries':
        echo $this->showGalleries();
        break;
      case 'list':
      default:
        echo $this->listPhotosets();
        break;
    endswitch;
  }
  
  function addToDebug($data)
  {
    if($this->config['debug'])
    {
      $this->debugData[] = print_r($data, TRUE);
    }
  }
  
  function listPhotoSets()
  {
    $photosets = $this->flickr->photosets_getList($this->user['nsid']);
    
    $rtn = array();
    if( empty($photosets)) {
      return 'No Set==empty';
    }

    foreach($photosets['photoset'] as $set) {
      $rtn[] = sprintf('%s==%s', $set['title'], $set['id']);
    }
    
    return implode('||', $rtn);
  }
  
  function showPhotos()
  {
    if( !isset($this->currentGallery) )
    {
      return 'No gallery is set.';
    }
    
    $galleries = $this->getGalleryInfo();
    
    ob_start();
    include($this->path.'/templates/'.$this->config['tpl'].'.php');
    $output = ob_get_clean();
    return $output;
  }
  
  function showGalleries()
  {
    global $modx;
    $childTVs = $modx->getDocumentChildrenTVars($this->config['parents'], '*');

    $count = count($childTVs);
    $galleries = array();
    for($i = 0; $i < $count; $i++)
    {
      foreach($childTVs[$i] as $tv)
      {
        $name  = $tv['name'];
        $value = $tv['value'];
        $galleries[$i]['document'][$name] = $value;
      }
      
      $this->currentGallery = $galleries[$i]['document']['GalleryID'];
      $galleries[$i]['photos'] = $this->getGalleryInfo();
    }

    ob_start();
    include($this->path.'/templates/'.$this->config['tpl'].'.php');
    $output = ob_get_clean();
    return $output;
  }
  
  function getGalleryInfo()
  {
    $api['info']    = $this->flickr->photosets_getInfo($this->currentGallery);
    $api['photos']  = $this->flickr->photosets_getPhotos($this->currentGallery);
    $gallery = array_merge($api['info'], $api['photos']['photoset']);
    unset($api);

    $count = count($gallery['photo']);

  	$allowedSizes = array(
  		'75'    => '_s',
  		'100'   => '_t',
  		'240'   => '_m',
  		'500'   => '',
  		'1024'  => '_b',
  	);

    for( $i = 0; $i < $count; $i++)
    {
      foreach($allowedSizes as $key => $value)
      {
        $photo = $gallery['photo'][$i];
        $gallery['photo'][$i]['image'][$key] = sprintf('http://farm%s.static.flickr.com/%s/%s_%s%s.jpg', $photo['farm'], $photo['server'], $photo['id'], $photo['secret'], $value);
      }
    }
    
    return $gallery;
  }
  
}