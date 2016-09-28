<?php
/**
 * Created by PhpStorm.
 * User: phuongth
 * Date: 3/28/15
 * Time: 9:32 AM
 */
if ( ! defined( 'ABSPATH' ) ) die( '-1' );
if(!class_exists('Zorka_Product')){
    class Zorka_Product {
        function __construct() {
            add_shortcode('zorka_product', array($this, 'zorka_product_shortcode' ));
        }
        function zorka_product_shortcode($atts){
            global $woocommerce_loop;
            $atts = shortcode_atts( array(
                'title' => '',
                'title_style' => '',
                'padding' => '',
                'source' => 'feature',
                'category' => '',
                'show_compare_wish_list_button' => '',
                'show_sale_count_down' => '',
                'show_rating' => '1',
                'bg_color' => '',
                'filter' => 'sale-product',
                'style' => '',
                'per_page' => '10',
                'columns'  => '5',
                'slider' => '',
                'navigator_position' => 'center',
                'orderby'  => 'date',
                'order'    => 'DESC',
                'el_class'      => '',
                'css_animation' => '',
                'duration'      => '',
                'delay'         => ''
            ), $atts );

            $meta_query = WC()->query->get_meta_query();

            $args = array();
            if($atts['source']=='feature'){
                switch($atts['filter']){
                    case 'sale-product':{
                        // Get products on sale
                        $product_ids_on_sale = wc_get_product_ids_on_sale();
                        $args = array(
                            'posts_per_page'	=> $atts['per_page'],
                            'orderby' 			=> $atts['orderby'],
                            'order' 			=> $atts['order'],
                            'no_found_rows' 	=> 1,
                            'post_status' 		=> 'publish',
                            'post_type' 		=> 'product',
                            'meta_query' 		=> $meta_query,
                            'post__in'			=> array_merge( array( 0 ), $product_ids_on_sale )
                        );
                        break;
                    }
                    case 'new-in':{
                        $args = array(
                            'posts_per_page'	=> $atts['per_page'],
                            'orderby' 			=> $atts['orderby'],
                            'order' 			=> $atts['order'],
                            'no_found_rows' 	=> 1,
                            'post_status' 		=> 'publish',
                            'post_type' 		=> 'product',
                            'meta_query' 		=> $meta_query
                        );
                        break;
                    }
                    case 'featured':{
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
                        break;
                    }
                    case 'top-rated':{
                        $args = array(
                            'posts_per_page'	=> $atts['per_page'],
                            'orderby' 			=> $atts['orderby'],
                            'order' 			=> $atts['order'],
                            'no_found_rows' 	=> 1,
                            'post_status' 		=> 'publish',
                            'post_type' 		=> 'product',
                            'meta_query' 		=> $meta_query
                        );
                        add_filter( 'posts_clauses',  array( WC()->query, 'order_by_rating_post_clauses' ) );
                        break;
                    }
                    case 'recent-review':{
                        $args = array(
                            'no_found_rows' 	=> 1,
                            'posts_per_page'	=> $atts['per_page'],
                            'post_status' 		=> 'publish',
                            'post_type' 		=> 'product'
                        );
                        add_filter( 'posts_clauses', array($this, 'zorka_order_by_comment_date_post_clauses' ) );
                        break;
                    }
                }
            }
            if($atts['source']=='category'){
                $args = array(
                    'post_type'				=> 'product',
                    'post_status' 			=> 'publish',
                    'ignore_sticky_posts'	=> 1,
                    'orderby' 				=> $atts['orderby'],
                    'order' 				=> $atts['order'],
                    'posts_per_page' 		=> $atts['per_page'],
                    'meta_query' 			=> $meta_query,
                    'tax_query' 			=> array(
                        array(
                            'taxonomy' 		=> 'product_cat',
                            'terms' 		=>  explode(',',$atts['category']),
                            'field' 		=> 'slug',
                            'operator' 		=> 'IN'
                        )
                    )
                );
            }

            ob_start();
            $products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );


            if($atts['filter']=='top-rated'){
                remove_filter( 'posts_clauses',  array( WC()->query, 'order_by_rating_post_clauses' ) );
            }
            if($atts['filter']=='recent-review'){
                remove_filter( 'posts_clauses', array($this, 'zorka_order_by_comment_date_post_clauses' )  );
            }


            $template_name = '';
            if($atts['style']=='product-style-two')
                $template_name = '-style-2';
            if($atts['style']=='product-style-three')
                $template_name = '-style-3';
            if($atts['style']=='product-style-four'){
                $template_name = '-style-4';
                $atts['title']  = $atts['title_style'] = $atts['slider'] = $atts['bg_color'] = $atts['padding'] = '';
            }

            $woocommerce_loop['columns'] = $atts['columns'];
            global $zorka_product_layout, $zorka_product_add_to_cart_layout, $zorka_sc_product_is_show_button, $zorka_sc_product_show_rating;
            $zorka_product_layout = $atts['slider'];
            $zorka_product_add_to_cart_layout = 'icon';

            if($atts['show_compare_wish_list_button']=='hide-button')
                $zorka_sc_product_is_show_button = 0;
            else
                $zorka_sc_product_is_show_button = 1;
            $zorka_sc_product_show_rating = $atts['show_rating'];

            if ( $products->have_posts() ) : ?>

                <?php woocommerce_product_loop_start(); ?>

                <?php while ( $products->have_posts() ) : $products->the_post(); ?>

                    <?php wc_get_template_part( 'content', 'product'.$template_name ); ?>

                <?php endwhile; // end of the loop. ?>

                <?php woocommerce_product_loop_end(); ?>

            <?php else: ?>
                <div class="item-not-found"><?php esc_html_e('No item found','zorka') ?></div>
            <?php endif;

            wp_reset_postdata();

            $title = $atts['title'];
            if((isset($title) && $title!='') || $atts['navigator_position']=='top-left')
                $title = '<div class="title-shortcode"><h2>'.$title.'</h2></div>';

            $bg_color = '';
            if(isset($atts['bg_color']) && $atts['bg_color']!=''){
                $bg_color = ' style="background-color:'.$atts['bg_color'].'"';
            }

            $g5plus_animation = '';
            $g5plus_animation .= ' ' . esc_attr($atts['el_class']);
            $g5plus_animation .= g5plus_getCSSAnimation( $atts['css_animation'] );
            $styles_animation= g5plus_getStyleAnimation($atts['duration'] ,$atts['delay']);
            $customer_class = array();
            $customer_class[] = 'woocommerce shortcode-product';
            $customer_class[] = 'columns-' . $atts['columns'];
            $customer_class[] = $atts['show_sale_count_down'];
            $customer_class[] = $atts['show_compare_wish_list_button'];
            $customer_class[] = $g5plus_animation;
            $customer_class[] = $atts['style'];
            $customer_class[] = $atts['navigator_position'];
            $customer_class[] = $atts['padding'];
            $customer_class[] = $atts['title_style'];
            $class_name = join(' ',$customer_class);


            return '<div class="' . $class_name. '"'. $bg_color.'>' .$title . ob_get_clean() . '</div>';
        }

        function zorka_order_by_comment_date_post_clauses($args){
            global $wpdb;

            $args['join'] .= "
                LEFT JOIN (
                    SELECT comment_post_ID, MAX(comment_date)  as  comment_date
                    FROM $wpdb->comments
                    WHERE comment_approved = 1
                    GROUP BY comment_post_ID
                ) as wp_comments ON($wpdb->posts.ID = wp_comments.comment_post_ID)
            ";
            $args['orderby'] = "wp_comments.comment_date DESC";
            return $args;
        }
    }
    if(class_exists( 'WooCommerce' ))
        new Zorka_Product();
}