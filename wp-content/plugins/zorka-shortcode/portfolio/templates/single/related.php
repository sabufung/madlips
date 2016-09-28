<?php
$args = array(
    'post__not_in' => array($post_id),
    'posts_per_page'   => 4,
    'orderby'			=> 'rand',
    'post_type'        => ZORKA_PORTFOLIO_POST_TYPE,
    'portfolio_category__in'    => $arrCatId,
    'post_status'      => 'publish'
);
$posts_array = new WP_Query( $args );
?>
<div class="portfolio-related portfolio-wrapper zorka-col-md-4 col-padding-15">
    <?php
    $index=0;
    while ( $posts_array->have_posts() ) : $posts_array->the_post();
        $index++;
        $overlay_style = 'icon';
        $column = 4;
        $terms = wp_get_post_terms( get_the_ID(), array( ZORKA_PORTFOLIO_CATEGORY_TAXONOMY));
        $permalink = get_permalink();
        $title_post = get_the_title();
        $cat = $cat_filter = '';
        foreach ( $terms as $term ){
            $cat .= '<a href="'. get_term_link( $term, ZORKA_PORTFOLIO_CATEGORY_TAXONOMY ) .'">'.$term->name.'</a>, ';
        }
        $cat = rtrim($cat,', ');
        ?>
        <?php include(ZORKA_PORTFOLIO_DIR_PATH.'/templates/info-item.php'); ?>
    <?php
    endwhile;
    wp_reset_postdata();
    ?>
    <div style="clear: both"></div>
</div>
