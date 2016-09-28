<?php
/**
 * Created by PhpStorm.
 * User: phuongth
 * Date: 3/28/15
 * Time: 10:39 AM
 */

if ( ! defined( 'ABSPATH' ) ) die( '-1' );
if(!class_exists('Zorka_Trending_Product')){
    class Zorka_Trending_Product {
        function __construct() {
            add_action('wp_enqueue_scripts',array($this,'front_scripts'),11);
            add_shortcode('zorka_trending_product', array($this, 'zorka_trending_product_shortcode' ));
        }

        function front_scripts(){
            $min_suffix = defined( 'ZORKA_SCRIPT_DEBUG' ) && ZORKA_SCRIPT_DEBUG ? '' : '.min';
            wp_enqueue_script('zorka-trending',plugins_url() . '/zorka-shortcode/products/assets/js/trending'.$min_suffix.'.js', false, true);
        }

        function zorka_trending_product_shortcode($atts){
            $atts = shortcode_atts( array(
                'layout_style' => 'style1',
                'title' => '',
                'slider' => '',
                'per_page' => '12',
                'columns'  => '4',
                'orderby'  => 'date',
                'order'    => 'desc',
                'category' => '',  // Slugs
                'display_featured' => '0',
                'operator' => 'IN', // Possible values are 'IN', 'NOT IN', 'AND'.
                'el_class'      => '',
                'css_animation' => '',
                'duration'      => '',
                'delay'         => ''
            ), $atts );

            if ( ! $atts['category'] ) {
                return '';
            }

            $g5plus_animation = '';
            $g5plus_animation .= ' ' . esc_attr($atts['el_class']);
            $g5plus_animation .= g5plus_getCSSAnimation( $atts['css_animation'] );
            $styles_animation= g5plus_getStyleAnimation($atts['duration'] ,$atts['delay']);


            $data_section_id = uniqid();
            $is_active = 0;

            $return = '<div class="woocommerce columns-' . $atts['columns'] . ' '. $g5plus_animation . ' trending-product" id="'.$data_section_id.'" >';

            if(isset($atts['layout_style']) && $atts['layout_style']=='style1'){
                $list_cat = '<div class="tabs"><ul>';
                $list_item_by_cat= '';

                if(isset($atts['display_featured']) && $atts['display_featured']=='1')
                    $this->bindTrendingFeaturedData($atts,$return, $is_active, $data_section_id, $list_cat, $list_item_by_cat);

                $this->bindTrendingData($atts,$return, $is_active, $data_section_id, $list_cat, $list_item_by_cat);

                $list_cat .= '</ul></div>';
                $script = '<script type="text/javascript">jQuery(document).ready(function(){Trending.registerTrendingFilter("'. $data_section_id. '")});</script>';
                $return .= $list_cat.$list_item_by_cat.'</div>'.$script;
            }else
            {
                $this->bindTrendingDataSecondStyle($atts,$return, $data_section_id, $g5plus_animation);
                $return .= '</div>';
            }

            return $return;
        }

        function bindTrendingFeaturedData(&$atts, &$return, &$is_active, &$data_section_id, &$list_cat, &$list_item_by_cat){

            $is_active = 1;
            $ordering_args = WC()->query->get_catalog_ordering_args( $atts['orderby'], $atts['order'] );
            $meta_query[] = array(
                'key'   => '_featured',
                'value' => 'yes'
            );
            $args = array(
                'posts_per_page'	=> $atts['per_page'],
                'orderby' 			=> $atts['orderby'],
                'order' 			=> $atts['order'],
                'no_found_rows' 	=> 1,
                'post_status' 		=> 'publish',
                'post_type' 		=> 'product',
                'meta_query' 		=> $meta_query
            );
            $cat = 'zorka-meta-key-feature';

            $list_cat .= '<li class="active"><a  href="javascript:;" class="isotope-filter active ' .$cat. '" data-section-id="'.$data_section_id.'"  data-group="'.$cat.'" data-filter=".'.$cat.'">'.__('Featured','zorka').'</a></li>';

            if ( isset( $ordering_args['meta_key'] ) ) {
                $args['meta_key'] = $ordering_args['meta_key'];
            }

            global $woocommerce_loop,$zorka_product_layout;
            $products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );
            $woocommerce_loop['columns'] = $atts['columns'];
            $zorka_product_layout = $atts['slider'];

            ob_start();

            if ( $products->have_posts() ) : ?>

                <?php do_action( 'woocommerce_shortcode_before_product_cat_loop' ); ?>

                <?php woocommerce_product_loop_start(); ?>

                <?php while ( $products->have_posts() ) : $products->the_post(); ?>

                    <?php wc_get_template_part( 'content', 'product' ); ?>

                <?php endwhile; // end of the loop. ?>

                <?php woocommerce_product_loop_end(); ?>

                <?php do_action( 'woocommerce_shortcode_after_product_cat_loop' ); ?>

            <?php endif;

            woocommerce_reset_loop();
            wp_reset_postdata();

            $list_item_by_cat .= '<div class="trending-group '.$cat . ' active ">' . ob_get_clean() . '</div>';

            // Remove ordering query arguments
            WC()->query->remove_ordering_args();
        }

        function bindTrendingData(&$atts, &$return, &$is_active, &$data_section_id, &$list_cat, &$list_item_by_cat){
            $categories = explode( ',', $atts['category']);
            $active_class = '';
            $args = array(
                'number'     => '',
            );
            $product_categories = get_terms( 'product_cat', $args );

            foreach($categories as $cat){

                if(@$is_active==0){
                    $active_class='active';
                    $is_active = 1;
                }else{
                    $active_class = '';
                }
                $cat_name = $cat;
                if ( is_array( $product_categories ) ) {
                    foreach ( $product_categories as $product_cat ) {
                        if($cat == $product_cat->slug){
                            $cat_name = $product_cat->name;
                            break;
                        }
                    }
                }

                $list_cat .= '<li class="'.$active_class.'"><a  href="javascript:;" class="isotope-filter '. $active_class. ' ' .$cat. '" data-section-id="'.$data_section_id.'"  data-group="'.$cat.'" data-filter=".'.$cat.'">'.$cat_name.'</a></li>';
                // Default ordering args
                $ordering_args = WC()->query->get_catalog_ordering_args( $atts['orderby'], $atts['order'] );
                $meta_query    = WC()->query->get_meta_query();

                $args = array(
                    'post_type'				=> 'product',
                    'post_status' 			=> 'publish',
                    'ignore_sticky_posts'	=> 1,
                    'orderby' 				=> $ordering_args['orderby'],
                    'order' 				=> $ordering_args['order'],
                    'posts_per_page' 		=> $atts['per_page'],
                    'meta_query' 			=> $meta_query,
                    'tax_query' 			=> array(
                        array(
                            'taxonomy' 		=> 'product_cat',
                            'terms' 		=> $cat,
                            'field' 		=> 'slug',
                            'operator' 		=> 'IN'
                        )
                    )
                );

                if ( isset( $ordering_args['meta_key'] ) ) {
                    $args['meta_key'] = $ordering_args['meta_key'];
                }

                global $woocommerce_loop, $zorka_product_layout;
                $products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );
                $woocommerce_loop['columns'] = $atts['columns'];

                $zorka_product_layout = $atts['slider'];

                ob_start();

                if ( $products->have_posts() ) : ?>

                    <?php do_action( 'woocommerce_shortcode_before_product_cat_loop' ); ?>

                    <?php woocommerce_product_loop_start(); ?>

                    <?php while ( $products->have_posts() ) : $products->the_post(); ?>

                        <?php wc_get_template_part( 'content', 'product' ); ?>

                    <?php endwhile; // end of the loop. ?>

                    <?php woocommerce_product_loop_end(); ?>

                    <?php do_action( 'woocommerce_shortcode_after_product_cat_loop' ); ?>

                <?php endif;

                woocommerce_reset_loop();
                wp_reset_postdata();

                $list_item_by_cat .= '<div class="trending-group '.$cat . ' ' . $active_class .' ">' . ob_get_clean() . '</div>';

                // Remove ordering query arguments
                WC()->query->remove_ordering_args();
            }

        }

        function bindTrendingDataSecondStyle(&$atts, &$return, &$data_section_id, $g5plus_animation){

            $return = '<div class="woocommerce columns-' . $atts['columns'] . ' '. $g5plus_animation  . ' trending-product top-left" >';
            $title = isset($atts['title']) ? $atts['title']: '';
            if((isset($atts['slider']) && $atts['slider']=='slider') || !empty($title)){
                $return .= '<div class="title-shortcode"><h2>'.$title.'</h2></div>';
            }
            $categories = explode( ',', $atts['category']);
            $ordering_args = WC()->query->get_catalog_ordering_args( $atts['orderby'], $atts['order'] );
            $meta_query    = WC()->query->get_meta_query();
            $args = array(
                'post_type'				=> 'product',
                'post_status' 			=> 'publish',
                'ignore_sticky_posts'	=> 1,
                'orderby' 				=> $ordering_args['orderby'],
                'order' 				=> $ordering_args['order'],
                'posts_per_page' 		=> $atts['per_page'],
                'meta_query' 			=> $meta_query,
                'tax_query' 			=> array(
                    array(
                        'taxonomy' 		=> 'product_cat',
                        'terms' 		=> $categories,
                        'field' 		=> 'slug',
                        'operator' 		=> 'IN'
                    )
                )
            );

            if ( isset( $ordering_args['meta_key'] ) ) {
                $args['meta_key'] = $ordering_args['meta_key'];
            }

            global $woocommerce_loop, $zorka_product_layout;;
            $products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );
            $woocommerce_loop['columns'] = $atts['columns'];
            $zorka_product_layout = $atts['slider'];

            ob_start();

            if ( $products->have_posts() ) : ?>

                <?php do_action( 'woocommerce_shortcode_before_product_cat_loop' ); ?>

                <?php woocommerce_product_loop_start(); ?>

                <?php while ( $products->have_posts() ) : $products->the_post(); ?>

                    <?php wc_get_template_part( 'content', 'product' ); ?>

                <?php endwhile; // end of the loop. ?>

                <?php woocommerce_product_loop_end(); ?>

                <?php do_action( 'woocommerce_shortcode_after_product_cat_loop' ); ?>

            <?php endif;

            woocommerce_reset_loop();
            wp_reset_postdata();

            $return .= ob_get_clean();

            // Remove ordering query arguments
            WC()->query->remove_ordering_args();
        }
    }
    if(class_exists( 'WooCommerce' ))
        new Zorka_Trending_Product();
}