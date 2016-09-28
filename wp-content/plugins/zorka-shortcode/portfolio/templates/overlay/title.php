<?php
/**
 * Created by PhpStorm.
 * User: phuongth
 * Date: 3/20/15
 * Time: 11:01 AM
 */
$cat = '';
foreach ( $terms as $term ){
    //$cat .= $term->name.', ';
    $cat .= '<a href="'. get_term_link( $term, ZORKA_PORTFOLIO_CATEGORY_TAXONOMY ) .'">'.$term->name.'</a>, ';
}
$cat = rtrim($cat,', ');
?>
<div class="entry-thumbnail title">
    <img width="<?php echo esc_attr($width) ?>" height="<?php echo esc_attr($height) ?>" src="<?php echo esc_url($thumbnail_url) ?>" alt="<?php echo get_the_title() ?>"/>
    <div class="entry-thumbnail-hover">
        <div class="entry-hover-wrapper">
            <div class="entry-hover-inner">
                <a href="<?php echo get_permalink(get_the_ID()) ?>"><div><?php the_title() ?></div> </a>
                <span class="category"><?php echo wp_kses_post($cat) ?></span>
            </div>
        </div>
    </div>

</div>