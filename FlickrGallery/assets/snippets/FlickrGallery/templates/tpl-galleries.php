<link href="/assets/snippets/flickrGallery/assets/highslide/highslide.css" media="screen" rel="stylesheet" type="text/css" />

<div class="highslide-wrapper">
<div class="highslide-inner">
  <?php foreach($galleries as $gallery): ?>
    <div class="highslide-item">
    <div class="highslide-item-inner">

      <span class="for-ie"></span>
      <a href="<?php echo $modx->makeUrl($gallery['document']['id']); ?>" class="highslide" >
        <img src="<?php echo $gallery['photos']['photo'][0]['image']['100']; ?>" alt="" title="Click to enlarge Image:" />
        <span class="gallery-title"><?php echo $gallery['document']['pagetitle']; ?></span>
      </a>
    </div>
    </div>
  <?php endforeach; ?>
  <br class="clear" />
</div>
</div>