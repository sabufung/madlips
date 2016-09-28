<?php
/**
 * Created by PhpStorm.
 * User: phuongth
 * Date: 3/20/15
 * Time: 2:42 PM
 */
?>
<div class="entry-thumbnail-zoom-out">
    <a href="<?php echo get_permalink(get_the_ID()) ?>">
        <img width="<?php echo esc_attr($width) ?>" height="<?php echo esc_attr($height) ?>" src="<?php echo esc_url($thumbnail_url) ?>" alt="<?php echo get_the_title() ?>"/>
    </a>
</div>