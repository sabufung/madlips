<div class="portfolio-item <?php echo esc_attr($cat_filter) ?>">

    <?php
        $post_thumbnail_id = get_post_thumbnail_id(  get_the_ID() );
        $arrImages = wp_get_attachment_image_src( $post_thumbnail_id, $image_size );
        $thumbnail_url = $arrImages[0];
        $arrImages = wp_get_attachment_image_src( $post_thumbnail_id,'full');
        $url_origin = $arrImages[0];
        $width = 570;
        $height = 460;
        if($column==3){
            $width = 370;
            $height = 300;
        }
        if($column==4){
            $width = 270;
            $height = 219;
        }
        include(plugin_dir_path( __FILE__ ).'/overlay/'.$overlay_style.'.php');
    ?>

    <div class="post-title"><a href="<?php echo get_permalink(get_the_ID()) ?>"><?php echo esc_html(get_the_title()); ?></a> </div>
    <div class="category"><?php echo wp_kses_post($cat) ?></div>

    <?php if($overlay_style=='icon-view' || $overlay_style=='icon'){ ?>
        <div style="display: none">
            <?php
            $meta_values = get_post_meta( get_the_ID(), 'portfolio-format-gallery', false );
            if(count($meta_values) > 0){
                foreach($meta_values as $image){
                    $urls = wp_get_attachment_image_src($image,'full');
                    $gallery_img = '';
                    if(count($urls)>0)
                        $gallery_img = $urls[0];
                    ?>
                    <div>
                        <a href="<?php echo esc_url($gallery_img) ?>" rel="prettyPhoto[pp_gal_<?php echo get_the_ID()?>]" title="<?php echo "<a href='".$permalink."'>".$title_post."</a>"?>"></a>
                    </div>
                <?php        }
            }
            ?>
        </div>
    <?php } ?>
</div>
<?php if($index%$column==0) {?>
    <div style="clear:both"></div>
<?php }?>