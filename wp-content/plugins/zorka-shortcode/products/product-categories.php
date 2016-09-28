<?php
/**
 * Created by PhpStorm.
 * User: phuongth
 * Date: 4/3/15
 * Time: 11:35 AM
 */
if ( ! defined( 'ABSPATH' ) ) die( '-1' );
if(!class_exists('Zorka_Product_Categories')){
    class Zorka_Product_Categories {
        function __construct() {
            add_shortcode('zorka_product_categories', array($this, 'zorka_product_categories_shortcode' ));
        }
        function zorka_product_categories_shortcode($atts){

            $atts = shortcode_atts( array(
                'category' => '',
                'slider' => '',
                'columns'  => '3',
                'orderby'  => 'date',
                'order'    => 'desc',
                'only_parent' => '0',
                'hide_empty' => 1,
                'el_class'      => '',
                'css_animation' => '',
                'duration'      => '',
                'delay'         => ''
            ), $atts );

            $g5plus_animation = '';
            $g5plus_animation .= ' ' . esc_attr($atts['el_class']);
            $g5plus_animation .= g5plus_getCSSAnimation( $atts['css_animation'] );
            $styles_animation= g5plus_getStyleAnimation($atts['duration'] ,$atts['delay']);

            $hide_empty = ( isset($atts['hide_empty']) && $atts['hide_empty'] == '1' ) ? 1 : 0;

            // get terms and workaround WP bug with parents/pad counts
            $args = array(
                'orderby'    => $atts['orderby'],
                'order'      => $atts['order'],
                'hide_empty' => $hide_empty,
                'pad_counts' => true
            );

            $product_categories = get_terms( 'product_cat', $args );

            $cats = explode(',',$atts['category']);
            foreach ( $product_categories as $key => $category ) {
                if ( ($hide_empty && $category->count == 0) || !in_array($category->slug,$cats) ) {
                    unset( $product_categories[ $key ] );
                }
            }
            global $zorka_product_layout,$woocommerce_loop;
            $woocommerce_loop['columns'] = $atts['columns'];

            ob_start();

            // Reset loop/columns globals when starting a new loop
            $zorka_product_layout = $atts['slider'];


            if ( $product_categories ) {

                woocommerce_product_loop_start();

                foreach ( $product_categories as $category ) {

                    wc_get_template( 'content-product-cat.php', array(
                        'category' => $category
                    ) );

                }

                woocommerce_product_loop_end();

            }

            woocommerce_reset_loop();

            return '<div class="woocommerce columns-' . $atts['columns'] . ' shortcode-product-categories">' . ob_get_clean() . '</div>';


        }
    }

    if(class_exists( 'WooCommerce' ))
        new Zorka_Product_Categories();
}