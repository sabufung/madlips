<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) die( '-1' );
if(!class_exists('Zorka_Our_Team')){
    class Zorka_Our_Team{
        function __construct(){
            add_action('init', array($this,'zorka_register_post_types'), 5 );
            add_shortcode('zorka_our_team', array($this, 'zorka_our_team_shortcode'));
            add_filter( 'rwmb_meta_boxes', array($this,'zorka_register_meta_boxes' ));
            add_filter('single_template',array($this,'zorka_our_team_single_template' ) );
            if(is_admin()){
                add_filter('manage_edit-our-team_columns' , array($this,'zorka_add_columns'));
                add_action('manage_our-team_posts_custom_column' ,array($this,'zorka_set_columns_value'), 10, 2 );
            }
        }
        function zorka_our_team_single_template($single) {
            global $post;
            /* Checks for single template by post type */
            if ($post->post_type == 'our-team'){
                $plugin_path =  untrailingslashit( plugin_dir_path( __FILE__ ) );
                $template_path = $plugin_path . '/single-our-team.php';
                if(file_exists($template_path))
                    return $template_path;
            }
            return $single;
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
                'title'  => esc_html__('Our Team Information', 'zorka' ),
                'pages'  => array( 'our-team' ),
                'fields' => array(
                    array(
                        'name' => esc_html__('Job', 'zorka' ),
                        'id'   => 'job',
                        'type' => 'text',
                    ),
                    array(
                        'name' => esc_html__('Facebook URL', 'zorka' ),
                        'id'   => 'face_url',
                        'type' => 'url',
                    ),
                    array(
                        'name' => esc_html__('Twitter URL', 'zorka' ),
                        'id'   => 'twitter_url',
                        'type' => 'url',
                    ),
                    array(
                        'name' => esc_html__('Google URL', 'zorka' ),
                        'id'   => 'google_url',
                        'type' => 'url',
                    ),
                    array(
                        'name' => esc_html__('Dribbble URL', 'zorka' ),
                        'id'   => 'dribbble_url',
                        'type' => 'url',
                    ),
                    array(
                        'name' => esc_html__('Linked In URL', 'zorka' ),
                        'id'   => 'linkedin_url',
                        'type' => 'url',
                    ),
                    array(
                        'name' => esc_html__('Phone', 'zorka' ),
                        'id'   => 'phone',
                        'type' => 'text',
                    ),
                    array(
                        'name' => esc_html__('Email', 'zorka' ),
                        'id'   => 'email',
                        'type' => 'text',
                    ),
                )
            );
            return $meta_boxes;
        }
        function zorka_register_post_types() {
            if ( post_type_exists('our-team') ) {
                return;
            }

            register_post_type( 'our-team',
                array(
                    'label'       => esc_html__('zorka_our_team', 'zorka' ),
                    'description' => esc_html__('Our Team Description', 'zorka' ),
                    'labels'      => array(
                        'name'               => _x( 'Our Team', 'Post Type General Name', 'zorka' ),
                        'singular_name'      => _x( 'Our Team', 'Post Type Singular Name', 'zorka' ),
                        'menu_name'          => esc_html__('Our Team', 'zorka' ),
                        'all_items'          => esc_html__('All Our Team', 'zorka' ),
                        'add_new_item'       => esc_html__('Add New Our Team', 'zorka' ),
                    ),
                    'supports'    => array( 'title', 'editor', 'excerpt', 'thumbnail'),
                    'public'      => true,
                    'has_archive' => true
                )
            );
        }
        function zorka_our_team_shortcode($atts){
            $is_slider=$layout_style=$item_amount = $column = $html = $el_class = $g5plus_animation = $css_animation = $duration = $delay = $styles_animation ='';
            extract( shortcode_atts( array(
                'layout_style'  => 'style1',
                'is_slider'     => '',
                'column'        => '3',
                'item_amount'   => '12',
                'el_class'      => '',
                'css_animation' => '',
                'duration'      => '',
                'delay'         => ''
            ), $atts ) );

            $g5plus_animation .= ' ' . esc_attr($el_class);
            $g5plus_animation .= g5plus_getCSSAnimation( $css_animation );
            $styles_animation= g5plus_getStyleAnimation($duration,$delay);
            $args    = array(
                'posts_per_page'   	=> $item_amount,
                'post_type'      => 'our-team',
                'orderby'   => 'date',
                'order'     => 'ASC',
                'post_status'      	=> 'publish'
            );
            $data = new WP_Query( $args );
            if ( $data->have_posts() ) {
                $class_col=' col-lg-'.(12/$column).' col-md-'.(12/$column).' col-sm-6  col-xs-12';
                if($layout_style=='style2')
                {
                    $class_col=' col-lg-'.(12/$column).' col-md-'.(12/$column).' col-sm-12  col-xs-12';
                }
                $html .= '<div class="zorka-our-team ' . esc_attr($layout_style) . $g5plus_animation . '" '.$styles_animation.'>
                           <div class="row">';
                if($is_slider=='yes')
                {
                    if($layout_style=='style1')
                    {
                        $html .= '<div class="owl-carousel" data-plugin-options=\'{"items" : '.$column.',"itemsDesktop" : [1199,'.$column.'],"itemsDesktopSmall" : [980,2],"itemsTablet": [768,1],"pagination": false, "autoPlay": true}\'>';
                    }
                    else
                    {
                        $html .= '<div class="owl-carousel" data-plugin-options=\'{"items" : '.$column.',"itemsDesktop" : [1199,'.$column.'],"itemsDesktopSmall" : [980,1],"itemsTablet": [768,1],"pagination": false, "autoPlay": true}\'>';
                    }
                    $class_col=' col-xs-12';
                }
                if($layout_style=='style1')
                {
                    while ( $data->have_posts() ): $data->the_post();
                        $job   = get_post_meta(get_the_ID(), 'job', true);
                        $face_url = get_post_meta( get_the_ID(), 'face_url', true );
                        $twitter_url = get_post_meta( get_the_ID(), 'twitter_url', true );
                        $google_url = get_post_meta( get_the_ID(), 'google_url', true );
                        $dribbble_url = get_post_meta( get_the_ID(), 'dribbble_url', true );
                        $linkedin_url = get_post_meta( get_the_ID(), 'linkedin_url', true );
                        $image_id  = get_post_thumbnail_id();
                        $image_url = wp_get_attachment_image( $image_id, 'full', false, array( 'alt' => get_the_title(), 'title' => get_the_title()));
                        $html .= '<div class="our-team-item'.$class_col.'">
                                    <div class="our-team-image">
                                        <a href="' . get_permalink() . '" title="' . get_the_title() . '" >'.wp_kses_post($image_url).'</a>
                                    </div>
                                    <a class="our-team-name" href="' . get_permalink() . '" title="' . get_the_title() . '" >' . get_the_title() . '</a>
                                    <p>'.esc_html($job).'</p>';
                                    if (!empty($face_url) || !empty($twitter_url) || !empty($google_url) || !empty($dribbble_url) || !empty($linkedin_url)){
                                        $html .= '<ul class="our-team-social">';
                                        if (!empty($face_url)){
                                            $html .= '<li><a data-toggle="tooltip" href="'.esc_url($face_url).'" class="facebook" title="'. esc_html__("Facebook","zorka") .'"><i class="fa fa-facebook"></i></a></li>';
                                        }
                                        if (!empty($twitter_url)){
                                            $html .= '<li><a data-toggle="tooltip" href="'.esc_url($twitter_url).'" class="twitter" title="'. esc_html__("Twitter","zorka") .' "><i class="fa fa-twitter"></i></a></li>';
                                        }
                                        if (!empty($google_url)){
                                            $html .= '<li><a data-toggle="tooltip" href="'.esc_url($google_url).'" class="google" title="'. esc_html__("Google","zorka") .'"><i class="fa fa-google-plus"></i></a></li>';
                                        }
                                        if (!empty($dribbble_url)){
                                            $html .= '<li><a data-toggle="tooltip" href="'.esc_url($dribbble_url).'" class="dribbble" title="'. esc_html__("Dribbble","zorka").' "><i class="fa fa-dribbble"></i></a></li>';
                                        }
                                        if (!empty($linkedin_url)){
                                            $html .= '<li><a data-toggle="tooltip" href="'.esc_url($linkedin_url).'" class="linkedin" title="'. esc_html__("Linkedin","zorka") .'"><i class="fa fa-linkedin"></i></a></li>';
                                        }
                                        $html .= '</ul>';
                                    }
                        $html .= '</div>';
                    endwhile;
                }
                else
                {
                    while ( $data->have_posts() ): $data->the_post();
                        $job   = get_post_meta(get_the_ID(), 'job', true);
                        $face_url = get_post_meta( get_the_ID(), 'face_url', true );
                        $twitter_url = get_post_meta( get_the_ID(), 'twitter_url', true );
                        $google_url = get_post_meta( get_the_ID(), 'google_url', true );
                        $dribbble_url = get_post_meta( get_the_ID(), 'dribbble_url', true );
                        $linkedin_url = get_post_meta( get_the_ID(), 'linkedin_url', true );
                        $image_id  = get_post_thumbnail_id();
                        $image_url = wp_get_attachment_image( $image_id, 'full', false, array( 'alt' => get_the_title(), 'title' => get_the_title()));
                        $html .= '<div class="our-team-item'.$class_col.'">
                                    <div class="row">
                                        <div class="our-team-image col-sm-6 col-xs-12"><a href="' . get_permalink() . '" title="' . get_the_title() . '" >'.wp_kses_post($image_url).'</a></div>
                                        <div class="our-team-info col-sm-6 col-xs-12">
                                            <a class="our-team-name" href="' . get_permalink() . '" title="' . get_the_title() . '" >' . get_the_title() . '</a>
                                            <p>'.esc_html($job).'</p>';
                                if (!empty($face_url) || !empty($twitter_url) || !empty($google_url) || !empty($dribbble_url) || !empty($linkedin_url)){
                                    $html .= '<ul class="our-team-social">';
                                    if (!empty($face_url)){
                                        $html .= '<li><a href="'.esc_url($face_url).'" class="facebook" title="Facebook"><i class="fa fa-facebook"></i></a></li>';
                                    }
                                    if (!empty($twitter_url)){
                                        $html .= '<li><a href="'.esc_url($twitter_url).'" class="twitter" title="Twitter"><i class="fa fa-twitter"></i></a></li>';
                                    }
                                    if (!empty($google_url)){
                                        $html .= '<li><a href="'.esc_url($google_url).'" class="google" title="Google"><i class="fa fa-google-plus"></i></a></li>';
                                    }
                                    if (!empty($dribbble_url)){
                                        $html .= '<li><a href="'.esc_url($dribbble_url).'" class="dribbble" title="Dribbble"><i class="fa fa-dribbble"></i></a></li>';
                                    }
                                    if (!empty($linkedin_url)){
                                        $html .= '<li><a href="'.esc_url($linkedin_url).'" class="linkedin" title="Linkedin"><i class="fa fa-linkedin"></i></a></li>';
                                    }
                                    $html .= '</ul>';
                                }
                                    $html .= '<div class="our-team-description">'.esc_html(get_the_excerpt()).'</div>
                                        </div>
                                    </div>
                               </div>';
                    endwhile;
                }
                if($is_slider=='yes')
                {
                    $html .= '</div>';
                }
                $html .= '</div>
                        </div>';
            }
            wp_reset_postdata();
            return $html;
        }
    }
    new Zorka_Our_Team;
}