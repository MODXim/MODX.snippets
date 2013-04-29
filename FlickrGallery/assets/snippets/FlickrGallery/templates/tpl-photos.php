<link   href="/assets/snippets/flickrGallery/assets/highslide/highslide.css" media="screen" rel="stylesheet" type="text/css" />
<script src="/assets/snippets/flickrGallery/assets/highslide/highslide-with-gallery.js" type="text/javascript"></script>
<script type="text/javascript"> 
	hs.graphicsDir = '/assets/snippets/flickrGallery/assets/highslide/graphics/';
	hs.showCredits = false;
	hs.align = 'center';
	hs.transitions = ['expand', 'crossfade'];
	hs.outlineType = 'rounded-white';
	hs.fadeInOut = true;
	
	// Add the controlbar
	if (hs.addSlideshow) hs.addSlideshow({
		//slideshowGroup: 'group1',
		interval: 5000,
		repeat: false,
		useControls: true,
		fixedControls: true,
		overlayOptions: {
			opacity: .75,
			position: 'top center',
			hideOnMouseOut: true
		}
	});


</script>

<div class="highslide-wrapper">
<div class="highslide-inner">
  <?php foreach($galleries['photo'] as $photo): ?>
    <div class="highslide-item">
    <div class="highslide-item-inner">

      <span class="for-ie"></span>
      <a href="<?php echo $photo['image'][500]; ?>" class="highslide" onclick="return hs.expand(this)">
        <img src="<?php echo $photo['image'][75]; ?>" alt="<?php echo $photo['title']; ?>" title="Click to enlarge Image: <?php echo $photo['title']; ?>" />
      </a>

    </div>
    </div>
  <?php endforeach; ?>
  <br class="clear" />
</div>
</div>