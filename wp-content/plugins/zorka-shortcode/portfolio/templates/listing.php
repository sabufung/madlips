<?php
/**
 * Created by PhpStorm.
 * User: phuongth
 * Date: 3/19/15
 * Time: 5:31 PM
 */

$args = array(
    'offset' => $offset,
    'posts_per_page'   => $post_per_page,
    'orderby'          => 'post_date',
    'order'            => 'DESC',
    'post_type'        => ZORKA_PORTFOLIO_POST_TYPE,
    ZORKA_PORTFOLIO_CATEGORY_TAXONOMY    => strtolower($category),
    'post_status'      => 'publish');

$posts_array  = new WP_Query( $args );
$total_post = $posts_array->found_posts;
$col_class = '';
$image_size = 'thumbnail-570x460';
$col_class='zorka-col-md-'.$column;
$data_section_id = uniqid();
?>
<div class="portfolio <?php echo esc_attr($g5plus_animation . ' '. $styles_animation) ?>" id="portfolio-<?php echo esc_attr($data_section_id)?>">
    <?php if($show_category!=''){
        $arr_terms = array();
        while ( $posts_array->have_posts() ) : $posts_array->the_post();
            $terms = wp_get_post_terms( get_the_ID(), array( ZORKA_PORTFOLIO_CATEGORY_TAXONOMY));
            foreach ( $terms as $term ){
                if(!in_array($term->name,$arr_terms )){
                    $arr_terms[count($arr_terms)] = $term->name;
                }
            }
        endwhile;
        wp_reset_postdata();

        if(count($arr_terms)>0){
            ?>
            <div class="portfolio-tabs <?php echo esc_attr($show_category) ?>">
                <ul>
                    <li class="active"><a class="isotope-portfolio active" data-section-id="<?php echo esc_attr($data_section_id) ?>" data-group="all" data-filter="*" href="javascript:;"><?php esc_html_e('All','zorka') ?></a></li>
                    <?php
                    foreach ( $arr_terms as $i=>$term ) {?>
                        <li><a class="isotope-portfolio" href="javascript:;" data-section-id="<?php echo esc_attr($data_section_id) ?>"  data-group="<?php echo preg_replace('/\s+/', '', $term) ?>" data-filter=".<?php echo preg_replace('/\s+/', '', $term) ?>"><?php echo wp_kses_post($term) ?></a></li>
                    <?php } ?>
                </ul>
            </div>
        <?php }
    }
    ?>

    <div class="portfolio-wrapper <?php echo sprintf('%s %s %s',$col_class,$padding,$layout_type)  ?>" data-section-id="<?php echo esc_attr($data_section_id) ?>" id="portfolio-container-<?php echo esc_attr($data_section_id) ?>" data-columns="<?php echo esc_attr($column) ?>">
        <?php
        $index = 0;
        while ( $posts_array->have_posts() ) : $posts_array->the_post();
            $index++;
            $permalink = get_permalink();
            $title_post = get_the_title();
            $terms = wp_get_post_terms( get_the_ID(), array( ZORKA_PORTFOLIO_CATEGORY_TAXONOMY));
            $cat = $cat_filter = '';
            foreach ( $terms as $term ){
                $cat_filter .= preg_replace('/\s+/', '', $term->name) .' ';
                $cat .= '<a href="'. get_term_link( $term, ZORKA_PORTFOLIO_CATEGORY_TAXONOMY ) .'">'.$term->name.'</a>, ';
            }
            $cat = rtrim($cat,', ');
            ?>
            <?php include(plugin_dir_path( __FILE__ ).'/'.$layout_type.'-item.php');?>
        <?php
        endwhile;
        wp_reset_postdata();
        ?>

    </div>
    <?php if($show_pagging=='1' && $post_per_page > 0 && $total_post/$post_per_page>1 && $total_post > ($post_per_page*$current_page)){ ?>
        <div style="clear: both"></div>
        <div class="paging" id="load-more-<?php echo esc_attr($data_section_id) ?>">
            <a href="javascript:;" class="zorka-button style1 button-sm load-more" data-loading-text="<i class='fa fa-refresh fa-spin'></i> <?php esc_html_e("Loading...",'zorka'); ?>"
                data-section-id="<?php echo esc_attr($data_section_id) ?>"
                data-current-page="<?php echo esc_attr($current_page + 1) ?>"
                data-offset="<?php echo esc_attr($offset) ?>"
                data-post-per-page = "<?php echo esc_attr($post_per_page) ?>"
                data-overlay-style = "<?php echo esc_attr($overlay_style) ?>"
                data-column = "<?php echo esc_attr($column) ?>"
                data-padding = "<?php echo esc_attr($padding) ?>"
                data-layout-type = "<?php echo esc_attr($layout_type) ?>"
            ><?php esc_html_e('LOAD MORE','zorka') ?></a>
        </div>
    <?php } ?>
</div>

<script type="text/javascript">
    (function($) {
        <?php if($show_category!='') {?>
            /*$(window).load(function(){
                jQuery('div[data-section-id="<?php echo esc_attr($data_section_id); ?>"]').isotope('layout');
            })*/
            $(document).ready(function(){
                $('.isotope-portfolio','.portfolio-tabs').off();
                $('.isotope-portfolio','.portfolio-tabs').click(function(){
                    $('.isotope-portfolio','.portfolio-tabs').removeClass('active');
                    $('li','.portfolio-tabs').removeClass('active');
                    $(this).addClass('active');
                    $(this).parent().addClass('active');
                    var dataSectionId = $(this).attr('data-section-id');
                    var filter = $(this).attr('data-filter');
                    var $container = jQuery('div[data-section-id="' + dataSectionId + '"]').isotope({ filter: filter});
                    $container.imagesLoaded( function() {
                        $container.isotope('layout');
                    });
                });
                var $container = jQuery('div[data-section-id="<?php echo esc_attr($data_section_id); ?>"]');
                $container.imagesLoaded( function() {
                    $container.isotope({
                        itemSelector: '.portfolio-item'
                    }).isotope('layout');
                });
            });

        <?php } ?>

        $(document).ready(function(){
            $('.portfolio-item > div.entry-thumbnail').hoverdir();
            PortfolioAjaxAction.init('<?php echo esc_url(get_site_url() . '/wp-admin/admin-ajax.php') ?>');
        })

    })(jQuery);
</script>


