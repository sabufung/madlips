<?php
get_header();


if ( have_posts() ) {
    // Start the Loop.
    while ( have_posts() ) : the_post();
        $post_id = get_the_ID();
        $categories = get_the_terms($post_id, ZORKA_PORTFOLIO_CATEGORY_TAXONOMY);
        global $zorka_data;
        $detail_style =  get_post_meta(get_the_ID(),'portfolio_detail_style',true);
        if (!isset($detail_style) || $detail_style == 'none' || $detail_style == '') {
            $detail_style = $zorka_data['portfolio-single-style'];
        }

        if($detail_style!='fullwidth'){
            update_post_meta($post_id,'custom-page-sub-title',get_the_excerpt());
            get_template_part('content','top');
        }
        $client = get_post_meta($post_id, 'portfolio-client', true );
        $meta_values = get_post_meta( get_the_ID(), 'portfolio-format-gallery', false );
        $imgThumbs = wp_get_attachment_image_src(get_post_thumbnail_id($post_id),'full');
        $cat = '';
        $arrCatId = array();
        if($categories){
            foreach($categories as $category) {
                $cat .= '<span><a href="'. get_term_link( $category, ZORKA_PORTFOLIO_CATEGORY_TAXONOMY ) .'">'.$category->name.'</a></span>, ';
                $arrCatId[count($arrCatId)] = $category->term_id;
            }
            $cat = trim($cat, ', ');
        }
        $image_size = 'thumbnail-570x460'; // image size for related
        include_once(plugin_dir_path( __FILE__ ).'/'.$detail_style.'.php');

    endwhile;
    }
?>

<script type="text/javascript">
    (function($) {
        "use strict";
        $(document).ready(function(){
            $("a[rel^='prettyPhoto']").prettyPhoto(
                {
                    theme: 'light_rounded',
                    slideshow: 5000,
                    deeplinking: false,
                    social_tools: false
                });
            $('.portfolio-item > div.entry-thumbnail').hoverdir();
        })

        <?php if($detail_style!='verticalslider'){ ?>
            $(window).load(function(){
                $(".post-slideshow",'#content').owlCarousel({
                    items: 1,
                    singleItem: true,
                    navigation : true,
                    navigationText: ['<span class="arrow-wrapper"><img alt="zorka portfolio" src="<?php echo get_template_directory_uri() ?>/assets/images/arrow_prev.png"></span>','<span class="arrow-wrapper"><img alt="zorka portfolio" src="<?php echo get_template_directory_uri() ?>/assets/images/arrow_next.png"></span>'],
                    pagination: false
                });
            })
        <?php } ?>

    })(jQuery);
</script>

<?php get_footer(); ?>
