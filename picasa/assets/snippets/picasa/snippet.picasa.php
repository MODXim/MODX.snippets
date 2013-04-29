<?php
/*
 * @name picasa
 * @author EGO(r) <23@7000.TEL>
 * 
 * @param &username - Google account username
 * @param &exclude_gid - Exclude Google+ Gallery by id
 * @param &row_images - Count of images in a row
 * @param images_width - Size of returns images (default '200,800')
 * @license WTFPL
 * 
 * @example [!picasa? &username=`will.smith` &exclude_gid=`5659479153094413297` &images_width=`144,1024`!]
 * Return all public gallery by will.smith G+ account, exclude gallery with id=5659479153094413297 and thumb size is 144, fulll images 1024.
 * 
 * This snippet output a picasaweb or Google+ gallery at one page
 * for MODx EVO 1.0.6 or later.
 * Needs PHP 5.2 or later.
 * 
 * Based on the Lightweight PHP Picasa API v3.3 by Cameron Hinkle
 * Cameron Hinkle site: http://cameronhinkle.com
 */

$output = '';
$cid = $modx->documentIdentifier;
$picasa_path 	= "/assets/snippets/picasa/";
$cache_path  	= "/assets/cache/picasa_api_cache/";

$username		= isset($username) ? $username : "will.smith"; // имя пользователя google, чтобы вывести его альбомы
$exclude_gid 	= isset($exclude_gid) ? explode(",",$exclude_gid) : array(); // массив id-номеров галерей, которые нужно исключить
$row_images 	= isset($row_images) ? $row_images : 4; // количество картинок в строке (по умолчанию по 4)

$solt = "SUPER-PUPER-SECRET-KEY"; // ЗАМЕНИТЬ НА СВОЁ СУПЕР-СЕКРЕТНОЕ-СОЧЕТАНИЕ!

	ini_set('include_path', getenv(DOCUMENT_ROOT).$picasa_path);
	$library_path = $_SERVER['DOCUMENT_ROOT'].$picasa_path;
		include_once ($_SERVER['DOCUMENT_ROOT'].$picasa_path.'Picasa.php');

      function get_album($user, $album_id, $images_width = NULL)
      {
        if ($images_width==NULL) { $images_width = '200,800'; } // $images_width - размер возвращаемых изображений

        // для любителей кэша тут код а-ля, если есть кеш, берем данные из кеша:
        /*
        if (file_exists($_SERVER['DOCUMENT_ROOT'].'/cache/picasa_api_cache/'.$user.'/'.$album_name) )
          {
              $album_data = unserialize(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/cache/picasa_api_cache/'.$user.'/'.$album_name));
          }
          else      
          { // и нужно закрыть ручками это условие в конце функции
				*/
				$pic = new Picasa();
				// Получаем данные для альбома, в последнем параметре указываем размеры необходимых изображений.
        // Можно также указать размеры: 72, 144, 200, 320, 400, 512, 576, 640, 720, 800, 912, 1024, 1152, 1280, 1440, 1600
        // googlesystem.blogspot.com/2006/12/embed-photos-from-picasa-web-albums.html
				$album = $pic->getAlbumById($user, $album_id,null,null,null,null,$images_width);
        // Получаем данные о изображениях в альбоме
				$images = $album->getImages();
						foreach ($images as $image)
              {
                  $thumbnails = $image->getThumbnails();
                  $album_data['images'][] = array('url'=>(string)$thumbnails[1]->getUrl(),
                                                  'width'=>(string)$thumbnails[1]->getWidth(),
                                                  'height'=>(string)$thumbnails[1]->getHeight(),
                                                  'title'=>(string)$image->getDescription(),
                                                  'tn_url'=>(string)$thumbnails[0]->getUrl(),
                                                  'tn_width'=>(string)$thumbnails[0]->getWidth(),
                                                  'tn_height'=>(string)$thumbnails[0]->getHeight(),
                                            );
              }
              // иконка альбома
              $album_data['url'] = (string)$album->getIcon();
              $album_data['width'] = '160';
              $album_data['height'] = '160';
              $album_data['title'] = (string)$album->getTitle();

              // сохраняем данные в кеш (оставил это для тех кому нужно)
        			//if(!is_dir($_SERVER['DOCUMENT_ROOT'].$cache_path.$user))
        			//mkdir($_SERVER['DOCUMENT_ROOT'].$cache_path.$user,0777);
							//file_put_contents($_SERVER['DOCUMENT_ROOT'].$cache_path.$user.'/'.$album_name,serialize($album_data));

          return $album_data;
      }

$input[gid] = (is_numeric($_GET[gallery_id])) ? $_GET[gallery_id] : $_GET[gallery_id]*1; // примитивная защита от инъекций
$input[hash] = is_string($_GET[hash]) ? htmlentities($_GET[hash]) : strval(htmlentities($_GET[hash]));

if ($input[gid]!="")
{
    if (sha1($input[gid].$solt)==$input[hash])
    {
      //echo "Всё ок, имеем id нужной галереии, поэтому едем дальше и выводим нужную галерею!";
      
      $GoBackButton = '
<a class="btn btn-primary" style="position: relative; margin-top: -43px; float: right;" href="[~'.$cid.'~]">
<i class="icon-chevron-left icon-white"></i>&nbsp;
назад</a>
';
      // проверка на случай если имеется id с правильным хэшем для неправильной галереи (которая исключена или удалена)
      if (in_array($input[gid], $exclude_gid)) { echo "<div class=\"alert alert-error\">Увы, но данная галерея не найдена! Возможно она была удалена.</div>"; return false; }

      $current_album = get_album($username, $input[gid], $images_width);

      	$modx->setPlaceholder('AlbumTitle', '<h2><small>&laquo;'.$current_album['title'].'&raquo;</small></h2>');
      	$modx->setPlaceholder('GoBackBtn', $GoBackButton);
      	
      $row_count=0;
      	foreach($current_album[images] as $image)
        {
          $row_count++;
          if ($row_count==1) { echo "\n<div class=\"row-fluid\">"; }

echo '
	<div class="span3">
		<a class="thumbnail fancybox" rel="gallery" href="'.$image[url].'">
		<img class="img-rounded" src="'.$image[tn_url].'" width="'.$image[tn_width].'" height="'.$image[tn_height].'">
		</a>
	</div>
';
          if ($row_count==$row_images) { echo '</div><hr>'; $row_count=0; }
        }
    }
    else
    {
      echo "<div class=\"alert alert-error\"><h2>Порошок — уходи! &nbsp;&nbsp;&nbsp;(<i class=\"icon-remove\"></i>__<i class=\"icon-remove\"></i>)</h2></div>";
      return false;
    }
}
else
{
  $modx->setPlaceholder('AlbumTitle', '<h2><small>Фотографии наших объектов со всех уголков России</small></h2>');
  $pic = new Picasa();
  $all_albums = $pic->getAlbumsByUsername($username);
  $albums = $all_albums->getAlbums();
/*
    echo "<pre>";
    print_r ($albums);
    echo "</pre>";
*/
  $row_count=0;
		foreach($albums as $album)
  	{
    if (in_array($album->getIdnum(), $exclude_gid))
   	 	{ continue; }
    else
      {
      $row_count++;
              // иконка альбома
              $album_thumb['id'] = $album->getIdnum();
              $album_thumb['url'] = $album->getIcon();
              $album_thumb['width'] = '160';
              $album_thumb['height'] = '160';
              $album_thumb['title'] = $album->getTitle();
/*
    echo "<pre>";
    print_r ($album);
    echo "</pre>";
*/
      if ($row_count==1) { echo '<div class="row-fluid">'; }

echo '
<div class="span3">
	<a class="thumbnail" href="[~'.$cid.'~]?gallery_id='.$album_thumb['id'].'&hash='.sha1($album_thumb['id'].$solt).'">
		<img src="'.$album_thumb['url'].'" class="img-rounded" width="'.$album_thumb['width'].'" height="'.$album_thumb['height'].'" alt="'.$album_thumb['title'].'" title="'.$album_thumb['title'].'">
	</a>
	<div class="album_description">'.$album_thumb['title'].'</div>
</div>';
      
      if ($row_count==$row_images) { echo '</div>'; $row_count=0; }
    	}
 		}
return $output;
}
?>