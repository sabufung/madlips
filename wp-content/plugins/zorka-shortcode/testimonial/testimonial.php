<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) die( '-1' );
if(!class_exists('Zorka_Testimonial')){
    class Zorka_Testimonial{
        function __construct(){
            add_action('init', array($this,'zorka_register_post_types'), 5 );
            add_shortcode('zorka_testimonial', array($this, 'zorka_testimonial_shortcode'));
            add_filter( 'rwmb_meta_boxes', array($this,'zorka_register_meta_boxes' ));
            if(is_admin()){
                add_filter('manage_edit-testimonial_columns' , array($this,'zorka_add_columns'));
                add_action('manage_testimonial_posts_custom_column' ,array($this,'zorka_set_columns_value'), 10, 2 );
            }
        }
        function zorka_add_columns($columns) {
            unset(
            $columns['cb'],
            $columns['title'],
            $columns['date']
            );
            $cols = array_merge(array('cb'=>('')),$columns);
            $cols = array_merge($cols,array('title'=>__('Name','zorka')));
            $cols = array_merge($cols,array('job'=>__('Job','zorka')));
            $cols = array_merge($cols,array('thumbnail'=>__('Picture','zorka')));
            $cols = array_merge($cols,array('date'=>__('Date','zorka')));
            return $cols;
        }
        function zorka_set_columns_value( $column, $post_id ) {
            switch($column){
                case 'id':{
                    echo wp_kses_post($post_id);
                    break;
                }
                case 'job':
                {
                    echo get_post_meta($post_id, 'job', true);
                    break;
                }
                case 'thumbnail':
                {
                    echo get_the_post_thumbnail($post_id,'thumbnail');
                    break;
                }
            }
        }
        function zorka_register_meta_boxes( $meta_boxes )
        {
            $meta_boxes[] = array(
                'title'  => esc_html__('Testimonials', 'zorka' ),
                'pages'  => array( 'testimonial' ),
                'fields' => array(
                    array(
                        'name' => esc_html__('Job', 'zorka' ),
                        'id'   => 'job',
                        'type' => 'text',
                    ),
                )
            );
            return $meta_boxes;
        }
        function zorka_register_post_types() {
            if ( post_type_exists('testimonial') ) {
                return;
            }

            register_post_type( 'testimonial',
                array(
                    'label'       => esc_html__('zorka_testimonial', 'zorka' ),
                    'description' => esc_html__('Testimonial Description', 'zorka' ),
                    'labels'      => array(
                        'name'               => _x( 'Testimonials', 'Post Type General Name', 'zorka' ),
                        'singular_name'      => _x( 'Testimonials', 'Post Type Singular Name', 'zorka' ),
                        'menu_name'          => esc_html__('Testimonials', 'zorka' ),
                        'all_items'          => esc_html__('All Testimonials', 'zorka' ),
                        'add_new_item'       => esc_html__('Add New Testimonial', 'zorka' ),
                    ),
                    'supports'    => array( 'title', 'excerpt', 'thumbnail'),
                    'public'      => true,
                    'has_archive' => true
                )
            );
        }
        function zorka_testimonial_shortcode($atts){
            $title=$sub_title = $layout_style = $html = $el_class = $g5plus_animation = $css_animation = $duration = $delay = $styles_animation ='';
            extract( shortcode_atts( array(
                'title'         => '',
                'sub_title'     => '',
                'layout_style'  => 'style1',
                'el_class'      => '',
                'css_animation' => '',
                'duration'      => '',
                'delay'         => ''
            ), $atts ) );

            $g5plus_animation .= ' ' . esc_attr($el_class);
            $g5plus_animation .= g5plus_getCSSAnimation( $css_animation );
            $styles_animation= g5plus_getStyleAnimation($duration,$delay);
            $args    = array(
                'posts_per_page'   	=> -1,
                'post_type'      => 'testimonial',
                'orderby'   => 'date',
                'order'     => 'ASC',
                'post_status'      	=> 'publish'
            );
            $data = new WP_Query( $args );
            if ( $data->have_posts() ) {
                $html .= '<div class="zorka-testimonial ' . esc_attr($layout_style) . $g5plus_animation . '" '.$styles_animation.'>
                              <h6>'.esc_html($title).'</h6>';
                if($layout_style!='style1' && $sub_title!='')
                {
                    $html .= '<p>'.esc_html($sub_title).'</p>';
                }
                    $html .= '<div class="owl-carousel" data-plugin-options=\'{"singleItem" : true,"pagination": true, "autoPlay": true}\'>';
                if($layout_style=='style1')
                {
                    while ( $data->have_posts() ): $data->the_post();
                        $job   = get_post_meta(get_the_ID(), 'job', true);
                        $image_id  = get_post_thumbnail_id();
                        $image_url = wp_get_attachment_image( $image_id, 'full', false, array( 'alt' => get_the_title(), 'title' => get_the_title()));
                        $html .= '<div class="testimonial-item">
                                    <p class="testimonial-content">'.esc_html(get_the_excerpt()).'</p>
                                    '.wp_kses_post($image_url).'
                                    <p class="testimonial-name">' . get_the_title() . '</p>
                                    <p class="testimonial-job">'.esc_html($job).'</p>
                                  </div>';
                    endwhile;
                }
                else
                {
                    while ( $data->have_posts() ): $data->the_post();
                        $job   = get_post_meta(get_the_ID(), 'job', true);
                        $image_id  = get_post_thumbnail_id();
                        $image_url = wp_get_attachment_image( $image_id, 'full', false, array( 'alt' => get_the_title(), 'title' => get_the_title()));
                        $html .= '<div class="testimonial-item">
                                    '.wp_kses_post($image_url).'
                                    <p class="testimonial-name">' . get_the_title() . '</p>
                                    <p class="testimonial-job">'.esc_html($job).'</p>
                                    <p class="testimonial-content">'.esc_html(get_the_excerpt()).'</p>
                                  </div>';
                    endwhile;
                }
                $html .= '</div>
                     </div>';
            }
            wp_reset_postdata();
            return $html;
        }
    }
    new Zorka_Testimonial;
}