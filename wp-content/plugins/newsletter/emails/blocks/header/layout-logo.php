<?php 
if (!$media) {
    echo '<p>Set your logo on company info page, thank you.</p>';
    return;
}
?>
<a href="<?php echo esc_url($media->link) ?>" target="_blank"><img src="<?php echo $media->url ?>" width="<?php echo $media->width ?>" height="<?php echo $media->height ?>" border="0" alt="<?php echo esc_attr($media->alt) ?>" inline-class="image"></a>                
