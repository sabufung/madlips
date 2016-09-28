<?php
/**
 * Created by PhpStorm.
 * User: phuongth
 * Date: 3/20/15
 * Time: 11:01 AM
 */
?>
<div class="entry-thumbnail title">
    <img width="<?php echo esc_attr($width) ?>" height="<?php echo esc_attr($height) ?>" src="<?php echo esc_url($thumbnail_url) ?>" alt="<?php echo get_the_title() ?>"/>
    <div class="entry-thumbnail-hover">
        <div class="entry-hover-wrapper">
            <div class="entry-hover-inner">
                <a href="<?php echo esc_url($url_origin) ?>" rel="prettyPhoto[pp_gal_<?php echo get_the_ID() ?>]"  title="<?php echo get_the_title() ?>">
                    <i class="pe-7s-search"></i>
                </a>
                <a href="<?php echo get_permalink(get_the_ID()) ?>"><h5><?php the_title() ?></h5> </a>
            </div>
        </div>
    </div>

</div>