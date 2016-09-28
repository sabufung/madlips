<?php
/**
 * Plugin Name: Zorka Shortcode
 * Plugin URI: http://g5plus.net
 * Description: This is plugin that create shortcode of theme
 * Version: 1.0
 * Author: g5plus
 * Author URI: http://g5plus.net
 * License: GPLv2 or later
 */
// don't load directly
if ( ! defined( 'ABSPATH' ) ) die( '-1' );
if(! function_exists('g5plus_getCSSAnimation')){
    function g5plus_getCSSAnimation( $css_animation ) {
        $output = '';
        if ( $css_animation != '' ) {
            wp_enqueue_script( 'waypoints' );
            $output = ' wpb_animate_when_almost_visible g5plus-css-animation wpb_' . $css_animation;
        }
        return $output;
    }
}
if(! function_exists('g5plus_getStyleAnimation')){
    function g5plus_getStyleAnimation( $duration,$delay ) {
        $duration=esc_attr($duration);
        $delay=esc_attr($delay);
        $styles = array();
        if ( $duration != '0' && ! empty( $duration ) ) {
            $duration = (float)trim( $duration, "\n\ts" );
            $styles[] = "-webkit-animation-duration: {$duration}s";
            $styles[] = "-moz-animation-duration: {$duration}s";
            $styles[] = "-ms-animation-duration: {$duration}s";
            $styles[] = "-o-animation-duration: {$duration}s";
            $styles[] = "animation-duration: {$duration}s";
        }
        if ( $delay != '0' && ! empty( $delay ) ) {
            $delay = (float)trim( $delay, "\n\ts" );
            $styles[] = "opacity: 0";
            $styles[] = "-webkit-animation-delay: {$delay}s";
            $styles[] = "-moz-animation-delay: {$delay}s";
            $styles[] = "-ms-animation-delay: {$delay}s";
            $styles[] = "-o-animation-delay: {$delay}s";
            $styles[] = "animation-delay: {$delay}s";
        }
        if (count($styles) > 1) {
            return 'style="'. implode( ';', $styles ).'"';
        }
        return implode( ';', $styles );
    }
}
$dir = plugin_dir_path( __FILE__ );
include_once($dir.'vc-extend/vc-extend.php');
include_once($dir.'heading/heading.php');
include_once($dir.'call-action/call-action.php');
include_once($dir.'parallax-sections/parallax-sections.php');
include_once($dir.'button/button.php');
include_once($dir.'counter/counter.php');
include_once($dir.'icon-box/icon-box.php');
include_once($dir.'partner-carousel/partner-carousel.php');
include_once($dir.'our-team/our-team.php');
include_once($dir.'testimonial/testimonial.php');
include_once($dir.'mailchimp/mailchimp.php');
include_once($dir.'banner/banner.php');
include_once($dir.'portfolio/portfolio.php');
include_once($dir.'products/sale-product.php');
include_once($dir.'products/trending-product.php');
include_once($dir.'products/product-categories.php');
include_once($dir.'latest-post/latest-post.php');

if(!class_exists('Zorka_Shortcode')){
    class Zorka_Shortcode{
        function __construct(){
            add_action('init', array($this,'register_vc_map'),10);

        }
        function register_vc_map()
        {
            if ( function_exists( 'vc_map' ) ) {
                $add_css_animation = array(
                    'type' => 'dropdown',
                    'heading' => esc_html__('CSS Animation', 'zorka' ),
                    'param_name' => 'css_animation',
                    'admin_label' => true,
                    'value' => array(
                        esc_html__('No', 'zorka' ) => '',
                        esc_html__('Top to bottom', 'zorka' ) => 'top-to-bottom',
                        esc_html__('Bottom to top', 'zorka' ) => 'bottom-to-top',
                        esc_html__('Left to right', 'zorka' ) => 'left-to-right',
                        esc_html__('Right to left', 'zorka' ) => 'right-to-left',
                        esc_html__('Appear from center', 'zorka' ) => 'appear',
                        esc_html__('FadeIn', 'zorka' ) => 'fadein'
                    ),
                    'description' => esc_html__('Select type of animation if you want this element to be animated when it enters into the browsers viewport. Note: Works only in modern browsers.', 'js_composer' )
                );
                $add_duration_animation= array(
                    'type' => 'textfield',
                    'heading' => esc_html__('Animation Duration', 'zorka' ),
                    'param_name' => 'duration',
                    'value' => '',
                    'description' => esc_html__('Duration in seconds. You can use decimal points in the value. Use this field to specify the amount of time the animation plays. <em>The default value depends on the animation, leave blank to use the default.</em>', 'zorka' ),
                    'dependency'  => Array( 'element' => 'css_animation', 'value' => array( 'top-to-bottom','bottom-to-top','left-to-right','right-to-left','appear','fadein') ),
                );
                $add_delay_animation=array(
                    'type' => 'textfield',
                    'heading' => esc_html__('Animation Delay', 'zorka' ),
                    'param_name' => 'delay',
                    'value' => '',
                    'description' => esc_html__('Delay in seconds. You can use decimal points in the value. Use this field to delay the animation for a few seconds, this is helpful if you want to chain different effects one after another above the fold.', 'zorka' ),
                    'dependency'  => Array( 'element' => 'css_animation', 'value' => array( 'top-to-bottom','bottom-to-top','left-to-right','right-to-left','appear','fadein') ),
                );
                $add_el_class = array(
                    'type'        => 'textfield',
                    'heading'     => esc_html__('Extra class name', 'zorka' ),
                    'param_name'  => 'el_class',
                    'description' => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'zorka' ),
                );
                $colors_arr = array(
                    esc_html__('Zorka Color', 'zorka' ) => 'zorka_color',
                    esc_html__('Grey', 'zorka' ) => 'wpb_button',
                    esc_html__('Blue', 'zorka' ) => 'btn-primary',
                    esc_html__('Turquoise', 'zorka' ) => 'btn-info',
                    esc_html__('Green', 'zorka' ) => 'btn-success',
                    esc_html__('Orange', 'zorka' ) => 'btn-warning',
                    esc_html__('Red', 'zorka' ) => 'btn-danger',
                    esc_html__('Black', 'zorka' ) => "btn-inverse"
                );
                $target_arr = array(
                    esc_html__('Same window', 'zorka' ) => '_self',
                    esc_html__('New window', 'zorka' ) => '_blank'
                );
                vc_map( array(
                    'name'     => esc_html__('Headings', 'zorka' ),
                    'base'     => 'zorka_heading',
                    'class'    => '',
                    'icon'     => 'icon-wpb-title',
                    'category' => esc_html__('Zorka Shortcodes', 'zorka' ),
                    'params'   => array(
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Layout Style', 'zorka' ),
                            'param_name'  => 'layout_style',
                            'admin_label' => true,
                            'value'       => array( esc_html__('style 1', 'zorka' ) => 'style1', esc_html__('style 2', 'zorka' ) => 'style2', esc_html__('style 3', 'zorka' ) => 'style3', esc_html__('style 4', 'zorka' ) => 'style4'),
                            'description' => esc_html__('Select Layout Style.', 'zorka' )
                        ),
                        array(
                            'type'       => 'textfield',
                            'heading'    => esc_html__('Title', 'zorka' ),
                            'param_name' => 'title',
                            'value'      => '',
                            'admin_label' => true,
                        ),
                        array(
                            'type'        => 'textarea',
                            'heading'     => esc_html__('Description', 'zorka' ),
                            'param_name'  => 'description',
                            'value'       => '',
                            'description' => esc_html__('Provide the description for this heading', 'zorka' ),
                            'dependency'  => Array( 'element' => 'layout_style', 'value' => array( 'style1','style3') ),
                        ),
                        $add_el_class,
                        $add_css_animation,
                        $add_duration_animation,
                        $add_delay_animation
                    )
                ) );
                vc_map( array(
                    'name'     => esc_html__('Call To Action', 'zorka' ),
                    'base'     => 'zorka_call_action',
                    'class'    => '',
                    'icon'     => 'icon-wpb-title',
                    'category' => esc_html__('Zorka Shortcodes', 'zorka' ),
                    'params'   => array(
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Layout Style', 'zorka' ),
                            'param_name'  => 'layout_style',
                            'admin_label' => true,
                            'value'       => array( esc_html__('style 1', 'zorka' ) => 'style1', esc_html__('style 2', 'zorka' ) => 'style2', esc_html__('style 3', 'zorka' ) => 'style3', esc_html__('style 4', 'zorka' ) => 'style4', esc_html__('style 5', 'zorka' ) => 'style5'),
                            'description' => esc_html__('Select Layout Style.', 'zorka' )
                        ),
                        array(
                            'type' => 'attach_image',
                            'heading' => esc_html__('Background Images', 'zorka' ),
                            'param_name' => 'bg_images',
                            'value' => '',
                            'description' => esc_html__('Select images from media library.', 'zorka' )
                        ),
                        array(
                            'type'       => 'textfield',
                            'heading'    => esc_html__('Title', 'zorka' ),
                            'param_name' => 'title',
                            'value'      => '',
                        ),
                        array(
                            'type'       => 'textarea',
                            'heading'    => esc_html__('Description', 'zorka' ),
                            'param_name' => 'description',
                            'value'      => '',
                            'dependency'  => Array( 'element' => 'layout_style', 'value' => array( 'style1','style3') )
                        ),
                        array(
                            'type'        => 'icon_text',
                            'heading'     => esc_html__('Select Icon:', 'zorka' ),
                            'param_name'  => 'icon',
                            'value'       => '',
                            'description' => esc_html__('Select the icon from the list.', 'zorka' ),
                            'dependency'  => Array( 'element' => 'layout_style', 'value' => array( 'style2') )
                        ),
                        array(
                            'type'       => 'textfield',
                            'heading'    => esc_html__('Button Label', 'zorka' ),
                            'param_name' => 'button_label',
                            'value'      => '',
                        ),
                        array(
                            'type'       => 'textfield',
                            'heading'    => esc_html__('Link (url)', 'zorka' ),
                            'param_name' => 'link',
                            'value'      => '',
                        ),
                        array(
                            'type' => 'checkbox',
                            'heading' => esc_html__('Open link in a new window/tab', 'zorka' ),
                            'param_name' => 'link_target',
                            'value' => array( esc_html__('Yes, please', 'zorka' ) => 'yes' )
                        ),
                        $add_el_class,
                        $add_css_animation,
                        $add_duration_animation,
                        $add_delay_animation
                    )
                ) );
                vc_map( array(
                    'name'     => esc_html__('Parallax Sections', 'zorka' ),
                    'base'     => 'zorka_parallax_sections',
                    'class'    => '',
                    'icon'     => 'icon-wpb-title',
                    'category' => esc_html__('Zorka Shortcodes', 'zorka' ),
                    'params'   => array(
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Layout Style', 'zorka' ),
                            'param_name'  => 'layout_style',
                            'admin_label' => true,
                            'value'       => array( esc_html__('style 1', 'zorka' ) => 'style1', esc_html__('style 2', 'zorka' ) => 'style2', esc_html__('style 3', 'zorka' ) => 'style3'),
                            'description' => esc_html__('Select Layout Style.', 'zorka' )
                        ),
                        array(
                            'type'       => 'textfield',
                            'heading'    => esc_html__('Title', 'zorka' ),
                            'param_name' => 'title',
                            'value'      => '',
                        ),
                        array(
                            'type'       => 'textarea',
                            'heading'    => esc_html__('Description', 'zorka' ),
                            'param_name' => 'description',
                            'value'      => '',
                        ),
                        array(
                            'type'        => 'icon_text',
                            'heading'     => esc_html__('Select Icon:', 'zorka' ),
                            'param_name'  => 'icon',
                            'value'       => '',
                            'description' => esc_html__('Select the icon from the list.', 'zorka' ),
                            'dependency'  => Array( 'element' => 'layout_style', 'value' => array( 'style2') )
                        ),
                        array(
                            'type'       => 'textfield',
                            'heading'    => esc_html__('Button 1: Label', 'zorka' ),
                            'param_name' => 'bt1_label',
                            'value'      => '',
                        ),
                        array(
                            'type'       => 'textfield',
                            'heading'    => esc_html__('Button 1: Link (url)', 'zorka' ),
                            'param_name' => 'bt1_link',
                            'value'      => '',
                        ),
                        array(
                            'type'       => 'checkbox',
                            'heading'    => esc_html__('Button 1: Open link in a new window/tab', 'zorka' ),
                            'param_name' => 'bt1_link_target',
                            'value'      => array( esc_html__('Yes, please', 'zorka' ) => 'yes' )
                        ),
                        array(
                            'type'       => 'textfield',
                            'heading'    => esc_html__('Button 2: Label', 'zorka' ),
                            'param_name' => 'bt2_label',
                            'value'      => '',
                        ),
                        array(
                            'type'       => 'textfield',
                            'heading'    => esc_html__('Button 2: Link (url)', 'zorka' ),
                            'param_name' => 'bt2_link',
                            'value'      => '',
                        ),
                        array(
                            'type' => 'checkbox',
                            'heading' => esc_html__('Button 2: Open link in a new window/tab', 'zorka' ),
                            'param_name' => 'bt2_link_target',
                            'value' => array( esc_html__('Yes, please', 'zorka' ) => 'yes' )
                        ),
                        $add_el_class,
                        $add_css_animation,
                        $add_duration_animation,
                        $add_delay_animation
                    )
                ) );
                vc_map( array(
                    'name'     => esc_html__('Button', 'zorka' ),
                    'base'     => 'zorka_button',
                    'class'    => '',
                    'icon'     => 'icon-wpb-title',
                    'category' => esc_html__('Zorka Shortcodes', 'zorka' ),
                    'params'   => array(
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Layout Style', 'zorka' ),
                            'param_name'  => 'layout_style',
                            'admin_label' => true,
                            'value'       => array( esc_html__('style 1', 'zorka' ) => 'style1', esc_html__('style 2', 'zorka' ) => 'style2', esc_html__('style 3', 'zorka' ) => 'style3', esc_html__('style 4', 'zorka' ) => 'style4'),
                            'description' => esc_html__('Select Layout Style.', 'zorka' )
                        ),
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Size', 'zorka' ),
                            'param_name'  => 'size',
                            'admin_label' => true,
                            'value'       => array( esc_html__('small', 'zorka' ) => 'button-sm', esc_html__('medium', 'zorka' ) => 'button-md', esc_html__('large', 'zorka' ) => 'button-lg'),
                        ),
                        array(
                            'type'       => 'textfield',
                            'heading'    => esc_html__('Button Label', 'zorka' ),
                            'param_name' => 'button_label',
                            'value'      => '',
                        ),
                        array(
                            'type'       => 'textfield',
                            'heading'    => esc_html__('Link (url)', 'zorka' ),
                            'param_name' => 'link',
                            'value'      => '',
                        ),
                        array(
                            'type' => 'checkbox',
                            'heading' => esc_html__('Open link in a new window/tab', 'zorka' ),
                            'param_name' => 'link_target',
                            'value' => array( esc_html__('Yes, please', 'zorka' ) => 'yes' )
                        ),
                        $add_el_class,
                        $add_css_animation,
                        $add_duration_animation,
                        $add_delay_animation
                    )
                ) );
                vc_map( array(
                    'name'     => esc_html__('Banner', 'zorka' ),
                    'base'     => 'zorka_banner',
                    'class'    => '',
                    'icon'     => 'icon-wpb-title',
                    'category' => esc_html__('Zorka Shortcodes', 'zorka' ),
                    'params'   => array(
                        array(
                            'type'       => 'textfield',
                            'heading'    => esc_html__('Title 1', 'zorka' ),
                            'param_name' => 'title1',
                            'value'      => '',
                        ),
                        array(
                            'type'       => 'textfield',
                            'heading'    => esc_html__('Title 2', 'zorka' ),
                            'param_name' => 'title2',
                            'value'      => '',
                        ),
                        array(
                            'type'       => 'textarea',
                            'heading'    => esc_html__('Description', 'zorka' ),
                            'param_name' => 'description',
                            'value'      => '',
                        ),
                        $add_el_class,
                        $add_css_animation,
                        $add_duration_animation,
                        $add_delay_animation
                    )
                ) );
                vc_map( array(
                    'name'     => esc_html__('Counter', 'zorka' ),
                    'base'     => 'zorka_counter',
                    'class'    => '',
                    'icon'     => 'icon-wpb-title',
                    'category' => esc_html__('Zorka Shortcodes', 'zorka' ),
                    'params'   => array(
                        array(
                            'type'       => 'textfield',
                            'heading'    => esc_html__('Value', 'zorka' ),
                            'param_name' => 'value',
                            'value'      => '',
                        ),
                        array(
                            'type'       => 'textfield',
                            'heading'    => esc_html__('Title', 'zorka' ),
                            'param_name' => 'title',
                            'value'      => '',
                        ),
                        $add_el_class
                    )
                ) );
                vc_map( array(
                    'name' => esc_html__('Pie chart', 'vc_extend' ),
                    'base' => 'vc_pie',
                    'class' => '',
                    'icon' => 'icon-wpb-vc_pie',
                    "category" => esc_html__('Zorka Shortcodes', 'zorka' ),
                    'description' => esc_html__('Animated pie chart', 'zorka' ),
                    'params' => array(
                        array(
                            'type' => 'textfield',
                            'heading' => esc_html__('Pie value', 'zorka' ),
                            'param_name' => 'value',
                            'description' => esc_html__('Input graph value here. Choose range between 0 and 100.', 'zorka' ),
                            'value' => '50',
                            'admin_label' => true
                        ),
                        array(
                            'type' => 'textfield',
                            'heading' => esc_html__('Pie label value', 'zorka' ),
                            'param_name' => 'label_value',
                            'description' => esc_html__('Input integer value for label. If empty "Pie value" will be used.', 'zorka' ),
                            'value' => ''
                        ),
                        array(
                            'type' => 'textfield',
                            'heading' => esc_html__('Units', 'zorka' ),
                            'param_name' => 'units',
                            'description' => esc_html__('Enter measurement units (if needed) Eg. %, px, points, etc. Graph value and unit will be appended to the graph title.', 'zorka' )
                        ),
                        array(
                            'type' => 'dropdown',
                            'heading' => esc_html__('Bar color', 'zorka' ),
                            'param_name' => 'color',
                            'value' => $colors_arr, //$pie_colors,
                            'description' => esc_html__('Select pie chart color.', 'zorka' ),
                            'admin_label' => true,
                            'param_holder_class' => 'vc_colored-dropdown'
                        ),
                        array(
                            'type' => 'textfield',
                            'heading' => esc_html__('Title', 'zorka' ),
                            'param_name' => 'title',
                            'value' => ''
                        ),
                        $add_el_class
                    )
                ) );
                $portfolio_categories = get_terms( ZORKA_PORTFOLIO_CATEGORY_TAXONOMY, array('hide_empty' => 0, 'orderby' => 'ASC') );
                $portfolio_cat = null;
                $portfolio_cat['All'] = '';
                if ( is_array( $portfolio_categories ) ) {
                    foreach ( $portfolio_categories as $cat ) {
                        $portfolio_cat[$cat->name] = $cat->slug;
                    }
                }
                vc_map( array(
                    'name'     => esc_html__('Portfolio', 'zorka' ),
                    'base'     => 'zorka_portfolio',
                    'class'    => '',
                    'icon'     => 'icon-wpb-title',
                    'category' => esc_html__('Zorka Shortcodes', 'zorka' ),
                    'params'   => array(
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Portfolio Category', 'zorka' ),
                            'param_name'  => 'category',
                            'admin_label' => true,
                            'value'       => $portfolio_cat
                        ),
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Show Category', 'zorka' ),
                            'param_name'  => 'show_category',
                            'admin_label' => true,
                            'value'       => array( 'None' => '', esc_html__('Show in left','zorka') => 'left', esc_html__('Show in center','zorka') => 'center', esc_html__('Show in right','zorka') => 'right')
                        ),
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Number of column', 'zorka' ),
                            'param_name'  => 'column',
                            'value'       => array( '2' => '2', '3' => '3', '4' => '4')
                        ),
                        array(
                            'type'        => 'textfield',
                            'heading'     => esc_html__('Number of item (or number of item per page if choose show paging)', 'zorka' ),
                            'param_name'  => 'item',
                            'value'       => ''
                        ),
                        array(
                            'type'        => 'checkbox',
                            'heading'     => esc_html__('Show Paging (or show loading more)', 'zorka' ),
                            'param_name'  => 'show_pagging',
                            'admin_label' => true,
                            'value'       => array( esc_html__('Show', 'zorka' ) => '1')
                        ),
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Padding', 'zorka' ),
                            'param_name'  => 'padding',
                            'value'       =>  array( esc_html__('No padding', 'zorka' ) => '', '10 px' => 'col-padding-10', '15 px' => 'col-padding-15', '20 px' => 'col-padding-20', '40 px' => 'col-padding-40')
                        ),
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Layout Type', 'zorka' ),
                            'param_name'  => 'layout_type',
                            'admin_label' => true,
                            'value'       => array(__( 'Grid', 'zorka' ) => 'grid',__( 'Info', 'zorka' ) => 'info')
                        ),
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Overlay Style', 'zorka' ),
                            'param_name'  => 'overlay_style',
                            'admin_label' => true,
                            'value'       => array( esc_html__('Icon', 'zorka' ) => 'icon', esc_html__('Title & Category', 'zorka' ) => 'title',
                                                    esc_html__('Icon & Title', 'zorka' ) => 'icon-view', esc_html__('Zoom Out','zorka') => 'zoom-out'
                                                    )
                        ),
                        $add_el_class,
                        $add_css_animation,
                        $add_duration_animation,
                        $add_delay_animation

                    )
                ));


                vc_map( array(
                    "name"     => esc_html__("Countdown", "zorka" ),
                    "base"     => "zorka_countdown",
                    "class"    => "",
                    "icon"     => "icon-wpb-title",
                    "category" => esc_html__('Zorka Shortcodes', 'zorka' ),
                    "params"   => array(
                        array(
                            "type"        => "dropdown",
                            "heading"     => esc_html__("Countdown Type", "zorka" ),
                            "param_name"  => "type",
                            "admin_label" => true,
                            "value"       => array( esc_html__('Coming Soon','zorka') => 'comming-soon', esc_html__('Under Construction','zorka') => 'under-construction')
                        ),
                        array(
                            "type"        => "textfield",
                            "heading"     => esc_html__("Extra class name", "zorka" ),
                            "param_name"  => "css",
                            "value"       => '',
                            "description" => esc_html__("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "zorka" )
                        ),
                    )
                ));

                $product_categories = array();
                $product_cat = array();
                if(class_exists( 'WooCommerce' )){
                    $args = array(
                        'number'     => '',
                    );
                    $product_categories = get_terms( 'product_cat', $args );
                    if ( is_array( $product_categories ) ) {
                        foreach ( $product_categories as $cat ) {
                            $product_cat[$cat->name] = $cat->slug;
                        }
                    }
                }

                vc_map( array(
                    "name"     => esc_html__("Product", "zorka" ),
                    "base"     => "zorka_product",
                    "class"    => "",
                    "icon"     => "icon-wpb-title",
                    "category" => esc_html__('Zorka Shortcodes', 'zorka' ),
                    "params"   => array(
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Style', 'zorka' ),
                            'param_name'  => 'style',
                            'value'       => array( esc_html__('Style 1', 'zorka' ) => '',
                                esc_html__('Style 2', 'zorka' ) => 'product-style-two',
                                esc_html__('Style 3', 'zorka' ) => 'product-style-three',
                                esc_html__('Style 4', 'zorka' ) => 'product-style-four'
                            )

                        ),

                        array(
                            "type"        => "textfield",
                            "heading"     => esc_html__("Title", "zorka" ),
                            "param_name"  => "title",
                            "admin_label" => true,
                            "value"       => '',
                            'dependency'  => Array( 'element' => 'style', 'value' => array( '','product-style-two','product-style-three') )
                        ),
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Title style', 'zorka' ),
                            'param_name'  => 'title_style',
                            'value'       => array( esc_html__('Border bottom inline', 'zorka' ) => '',
                                esc_html__('Border bottom full', 'zorka' ) => 'border-title-full'),
                            'dependency'  => Array( 'element' => 'style', 'value' => array( '','product-style-two','product-style-three') )
                        ),
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Padding', 'zorka' ),
                            'param_name'  => 'padding',
                            'value'       => array(  esc_html__('No Padding', 'zorka' ) => '',
                                esc_html__('Padding 10px', 'zorka' ) => 'zorka-padding-10',
                                esc_html__('Padding 15px', 'zorka' ) => 'zorka-padding-15',
                                esc_html__('Padding 20px', 'zorka' ) => 'zorka-padding-20',
                            ),
                            'dependency'  => Array( 'element' => 'style', 'value' => array( '','product-style-two','product-style-three') )
                        ),
                        array(
                            'type'        => 'colorpicker',
                            'heading'     => esc_html__('Background color', 'zorka' ),
                            'param_name'  => 'bg_color',
                            'value'       => '',
                            'dependency'  => Array( 'element' => 'style', 'value' => array( '','product-style-two','product-style-three') )
                        ),
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Select product source', 'zorka' ),
                            'param_name'  => 'source',
                            'value'       => array( esc_html__('From feature', 'zorka' ) => 'feature',
                                                    esc_html__('From category', 'zorka' ) => 'category',
                                                 ),
                            'dependency'  => Array( 'element' => 'style', 'value' => array( '','product-style-two','product-style-three') )

                        ),
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Feature', 'zorka' ),
                            'param_name'  => 'filter',
                            'value'       => array( esc_html__('Sale Off', 'zorka' ) => 'sale-product',
                                                    esc_html__('New In', 'zorka' ) => 'new-in',
                                                    esc_html__('Featured', 'zorka' ) => 'featured',
                                                    esc_html__('Top rated', 'zorka' ) => 'top-rated',
                                                    esc_html__('Recent review', 'zorka' ) => 'recent-review'),
                            'dependency'  => Array( 'element' => 'source', 'value' => array( 'feature') )


                        ),
                        array(
                            'type' => 'multi-select',
                            'heading' => esc_html__('Category', 'zorka' ),
                            'param_name' => 'category',
                            'options' => $product_cat,
                            'dependency'  => Array( 'element' => 'source', 'value' => array( 'category') )
                        ),
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Show button compare & wishlist', 'zorka' ),
                            'param_name'  => 'show_compare_wish_list_button',
                            'value'       => array(  esc_html__('Yes', 'zorka' ) => '',
                                esc_html__('No', 'zorka' ) => 'hide-button',
                            ),
                            'dependency'  => Array( 'element' => 'style', 'value' => array( '','product-style-four') )
                        ),
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Show sale countdown', 'zorka' ),
                            'param_name'  => 'show_sale_count_down',
                            'value'       => array(  esc_html__('Yes', 'zorka' ) => '',
                                esc_html__('No', 'zorka' ) => 'hide-count-down',
                            ),
                            'dependency'  => Array( 'element' => 'style', 'value' => array( '','product-style-four') )
                        ),
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Show rating', 'zorka' ),
                            'param_name'  => 'show_rating',
                            'value'       => array(  esc_html__('Yes', 'zorka' ) => '1',
                                esc_html__('No', 'zorka' ) => '0',
                            ),
                            'dependency'  => Array( 'element' => 'style', 'value' => array( '','product-style-four') )
                        ),

                        array(
                            "type"        => "textfield",
                            "heading"     => esc_html__("Per Page", "zorka" ),
                            "param_name"  => "per_page",
                            "admin_label" => true,
                            "value"       => '',
                            "description" => esc_html__('How much items per page to show','zorka')
                        ),
                        array(
                            "type"        => "dropdown",
                            "heading"     => esc_html__("Columns", "zorka" ),
                            "param_name"  => "columns",
                            'value'       => array(
                                '5' => '5',
                                '4' => '4',
                                '3' => '3',
                                '2' => '2',
                                '1' => '1'
                            ),
                            "description" => esc_html__("How much columns grid", "zorka" )
                        ),
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Display Slider', 'zorka' ),
                            'param_name'  => 'slider',
                            'value'       => array( esc_html__('No', 'zorka' ) => '', esc_html__('Yes', 'zorka' ) => 'slider'),
                            'dependency'  => Array( 'element' => 'style', 'value' => array( '','product-style-two','product-style-three') )

                        ),
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Navigator position', 'zorka' ),
                            'param_name'  => 'navigator_position',
                            'value'       => array( esc_html__('Center', 'zorka' ) => 'center', esc_html__('Top - right', 'zorka' ) => 'top-right'),
                            'dependency'  => Array( 'element' => 'style', 'value' => array( '','product-style-two','product-style-three') )

                        ),
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Order by', 'zorka' ),
                            'param_name'  => 'orderby',
                            'value'       => array( esc_html__('Date', 'zorka' ) => 'date', esc_html__('ID', 'zorka' ) => 'ID',
                                                    esc_html__('Author', 'zorka' ) => 'author', esc_html__('Modified', 'zorka' ) => 'modified',
                                                    esc_html__('Random', 'zorka' ) => 'rand', esc_html__('Comment count', 'zorka' ) => 'comment_count',
                                                    esc_html__('Menu Order', 'zorka' ) => 'menu_order'
                                                    ),
                            'description' => esc_html__('Select how to sort retrieved products.', 'zorka' ),
                        ),
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Order way', 'zorka' ),
                            'param_name'  => 'order',
                            'value'       => array( esc_html__('Descending', 'zorka' ) => 'DESC', esc_html__('Ascending', 'zorka' ) => 'ASC'),
                            'description' => esc_html__('Designates the ascending or descending order.', 'zorka' ),
                        ),
                        $add_el_class,
                        $add_css_animation,
                        $add_duration_animation,
                        $add_delay_animation
                    )
                ));


                vc_map( array(
                    "name"     => esc_html__("Trending Product", "zorka" ),
                    "base"     => "zorka_trending_product",
                    "class"    => "",
                    "icon"     => "icon-wpb-title",
                    "category" => esc_html__('Zorka Shortcodes', 'zorka' ),
                    "params"   => array(
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Layout Style', 'zorka' ),
                            'param_name'  => 'layout_style',
                            'admin_label' => true,
                            'value'       => array( esc_html__('style 1', 'zorka' ) => 'style1', esc_html__('style 2', 'zorka' ) => 'style2'),
                            'description' => esc_html__('Select Layout Style.', 'zorka' )
                        ),
                        array(
                            "type"        => "textfield",
                            "heading"     => esc_html__("Title", "zorka" ),
                            "param_name"  => "title",
                            "admin_label" => true,
                            "value"       => '',
                            'dependency'  => Array( 'element' => 'layout_style', 'value' => array( 'style2') )
                        ),
                        array(
                            'type' => 'multi-select',
                            'heading' => esc_html__('Product Category', 'zorka' ),
                            'param_name' => 'category',
                            'options' => $product_cat
                        ),
                        array(
                            'type' => 'checkbox',
                            'heading' => esc_html__('Display Featured Product', 'zorka' ),
                            'param_name' => 'display_featured',
                            'value' =>  array( esc_html__('Show', 'zorka' ) => '1'),
                            'dependency'  => Array( 'element' => 'layout_style', 'value' => array( 'style1') )
                        ),
                        array(
                            "type"        => "textfield",
                            "heading"     => esc_html__("Per Page", "zorka" ),
                            "param_name"  => "per_page",
                            "admin_label" => true,
                            "value"       => '',
                            "description" => esc_html__('How much items per page to show','zorka')
                        ),
                        array(
                            "type"        => "textfield",
                            "heading"     => esc_html__("Columns", "zorka" ),
                            "param_name"  => "columns",
                            "value"       => '4',
                            "description" => esc_html__("How much columns grid", "zorka" )
                        ),
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Slider', 'zorka' ),
                            'param_name'  => 'slider',
                            'value'       => array( esc_html__('No', 'zorka' ) => '',
                                            esc_html__('Yes', 'zorka' ) => 'slider'
                            ),
                        ),
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Order by', 'zorka' ),
                            'param_name'  => 'orderby',
                            'value'       => array( esc_html__('Date', 'zorka' ) => 'date', esc_html__('ID', 'zorka' ) => 'ID',
                                esc_html__('Author', 'zorka' ) => 'author', esc_html__('Modified', 'zorka' ) => 'modified',
                                esc_html__('Random', 'zorka' ) => 'rand', esc_html__('Comment count', 'zorka' ) => 'comment_count',
                                esc_html__('Menu Order', 'zorka' ) => 'menu_order'
                            ),
                            'description' => esc_html__('Select how to sort retrieved products.', 'zorka' )
                        ),
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Order way', 'zorka' ),
                            'param_name'  => 'order',
                            'value'       => array( esc_html__('Descending', 'zorka' ) => 'DESC', esc_html__('Ascending', 'zorka' ) => 'ASC'),
                            'description' => esc_html__('Designates the ascending or descending orde.', 'zorka' )
                        ),
                        $add_el_class,
                        $add_css_animation,
                        $add_duration_animation,
                        $add_delay_animation
                    )
                ));

                vc_map(array(
                    'name' => esc_html__('Product Categories','zorka'),
                    'base' => 'zorka_product_categories',
                    'class' => '',
                    'icon' => 'icon-wpb-title',
                    'category' => esc_html__('Zorka Shortcodes', 'zorka' ),
                    'params' => array(
                        array(
                            'type' => 'multi-select',
                            'heading' => esc_html__('Product Category', 'zorka' ),
                            'param_name' => 'category',
                            'options' => $product_cat
                        ),
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Number Column', 'zorka' ),
                            'param_name'  => 'columns',
                            'admin_label' => true,
                            'value'       => array('3' => 3,'4' => 4, '5' => '5'),
                            'std'   => '5',
                        ),
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Slider', 'zorka' ),
                            'param_name'  => 'slider',
                            'value'       => array( esc_html__('No', 'zorka' ) => '',
                                esc_html__('Yes', 'zorka' ) => 'slider'
                            ),
                        ),
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Hide empty', 'zorka' ),
                            'param_name'  => 'hide_empty',
                            'value'       => array( esc_html__('No', 'zorka' ) => '0',
                                esc_html__('Yes', 'zorka' ) => '1'
                            ),
                        ),
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Order by', 'zorka' ),
                            'param_name'  => 'orderby',
                            'value'       => array( esc_html__('Date', 'zorka' ) => 'date', esc_html__('ID', 'zorka' ) => 'ID',
                                esc_html__('Author', 'zorka' ) => 'author', esc_html__('Modified', 'zorka' ) => 'modified',
                                esc_html__('Random', 'zorka' ) => 'rand', esc_html__('Comment count', 'zorka' ) => 'comment_count',
                                esc_html__('Menu Order', 'zorka' ) => 'menu_order'
                            ),
                            'description' => esc_html__('Select how to sort retrieved products.', 'zorka' )
                        ),
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Order way', 'zorka' ),
                            'param_name'  => 'order',
                            'value'       => array( esc_html__('Descending', 'zorka' ) => 'DESC', esc_html__('Ascending', 'zorka' ) => 'ASC'),
                            'description' => esc_html__('Designates the ascending or descending orde.', 'zorka' )
                        ),
                        $add_el_class,
                        $add_css_animation,
                        $add_duration_animation,
                        $add_delay_animation
                    )
                ));

                vc_map( array(
                    'name'     => esc_html__('Our Team', 'zorka' ),
                    'base'     => 'zorka_our_team',
                    'class'    => '',
                    'icon'     => 'icon-wpb-title',
                    'category' => esc_html__('Zorka Shortcodes', 'zorka' ),
                    'params'   => array(
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Layout Style', 'zorka' ),
                            'param_name'  => 'layout_style',
                            'admin_label' => true,
                            'value'       => array( esc_html__('style 1', 'zorka' ) => 'style1', esc_html__('style 2', 'zorka' ) => 'style2'),
                            'description' => esc_html__('Select Layout Style.', 'zorka' )
                        ),
                        array(
                            'type'        => 'checkbox',
                            'heading'     => esc_html__('Slider Style', 'zorka' ),
                            'param_name'  => 'is_slider',
                            'admin_label' => false,
                            'value' => array( esc_html__('Yes, please', 'zorka' ) => 'yes' ),
                        ),
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Number Column', 'zorka' ),
                            'param_name'  => 'column',
                            'admin_label' => true,
                            'value'       => array('1' => 1,'2' => 2,'3' => 3,'4' => 4),
                            'std'   => '3',
                        ),
                        array(
                            'type'       => 'textfield',
                            'heading'    => esc_html__('Item Amount', 'zorka' ),
                            'param_name' => 'item_amount',
                            'value'      => '12',
                        ),
                        $add_el_class,
                        $add_css_animation,
                        $add_duration_animation,
                        $add_delay_animation
                    )
                ) );
                vc_map( array(
                    'name'     => esc_html__('Testimonials', 'zorka' ),
                    'base'     => 'zorka_testimonial',
                    'class'    => '',
                    'icon'     => 'icon-wpb-title',
                    'category' => esc_html__('Zorka Shortcodes', 'zorka' ),
                    'params'   => array(
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Layout Style', 'zorka' ),
                            'param_name'  => 'layout_style',
                            'admin_label' => true,
                            'value'       => array( esc_html__('style 1', 'zorka' ) => 'style1', esc_html__('style 2', 'zorka' ) => 'style2', esc_html__('style 3', 'zorka' ) => 'style3', esc_html__('style 4', 'zorka' ) => 'style4'),
                            'description' => esc_html__('Select Layout Style.', 'zorka' )
                        ),
                        array(
                            'type'       => 'textfield',
                            'heading'    => esc_html__('Title', 'zorka' ),
                            'param_name' => 'title',
                            'value'      => '',
                        ),
                        array(
                            'type'       => 'textfield',
                            'heading'    => esc_html__('Sub Title', 'zorka' ),
                            'param_name' => 'sub_title',
                            'value'      => '',
                            'dependency'  => Array( 'element' => 'layout_style', 'value' => array( 'style2','style3','style4') )
                        ),
                        $add_el_class,
                        $add_css_animation,
                        $add_duration_animation,
                        $add_delay_animation
                    )
                ) );
                vc_map( array(
                    'name' => esc_html__('Latest Posts', 'zorka' ),
                    'base' => 'zorka_latest_post',
                    'icon' => 'icon-wpb-title',
                    'category' => esc_html__('Zorka Shortcodes', 'zorka' ),
                    'description' => esc_html__('Latest Posts', 'zorka' ),
                    'params' => array(
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Layout Style', 'zorka' ),
                            'param_name'  => 'layout_style',
                            'admin_label' => true,
                            'value'       => array( esc_html__('style 1', 'zorka' ) => 'style1', esc_html__('style 2', 'zorka' ) => 'style2'),
                            'description' => esc_html__('Select Layout Style.', 'zorka' )
                        ),
                        array(
                            'type'       => 'textfield',
                            'heading'    => esc_html__('Title', 'zorka' ),
                            'param_name' => 'title',
                            'value'      => '',
                            'dependency'  => Array( 'element' => 'layout_style', 'value' => array( 'style2') )
                        ),
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__('Number Column', 'zorka' ),
                            'param_name'  => 'column',
                            'admin_label' => true,
                            'value'       => array('1' => 1,'2' => 2,'3' => 3,'4' => 4),
                            'std'         => '2',
                            'dependency'  => Array( 'element' => 'layout_style', 'value' => array( 'style1') )
                        ),
                        array(
                            'type'       => 'textfield',
                            'heading'    => esc_html__('Item Amount', 'zorka' ),
                            'param_name' => 'item_amount',
                            'value'      => '10'
                        ),
                        array(
                            'type'        => 'checkbox',
                            'heading'     => esc_html__('Slider Style', 'zorka' ),
                            'param_name'  => 'is_slider',
                            'admin_label' => false,
                            'value' => array( esc_html__('Yes, please', 'zorka' ) => 'yes' ),
                        ),
                        $add_el_class,
                        $add_css_animation,
                        $add_duration_animation,
                        $add_delay_animation
                    )
                ));
                vc_map( array(
                    'name' => esc_html__('Partner Carousel', 'zorka' ),
                    'base' => 'zorka_partner_carousel',
                    'icon' => 'icon-wpb-title',
                    'category' => esc_html__('Zorka Shortcodes', 'zorka' ),
                    'description' => esc_html__('Animated carousel with images', 'zorka' ),
                    'params' => array(
                        array(
                            'type' => 'attach_images',
                            'heading' => esc_html__('Images', 'zorka' ),
                            'param_name' => 'images',
                            'value' => '',
                            'description' => esc_html__('Select images from media library.', 'zorka' )
                        ),
                        array(
                            'type' => 'textfield',
                            'heading' => esc_html__('Image size', 'zorka' ),
                            'param_name' => 'img_size',
                            'description' => esc_html__('Enter image size. Example: thumbnail, medium, large, full or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use "thumbnail" size.', 'zorka' )
                        ),
                        array(
                            'type' => 'exploded_textarea',
                            'heading' => esc_html__('Custom links', 'zorka' ),
                            'param_name' => 'custom_links',
                            'description' => esc_html__('Enter links for each slide here. Divide links with linebreaks (Enter) . ', 'zorka' ),
                        ),
                        array(
                            'type' => 'dropdown',
                            'heading' => esc_html__('Custom link target', 'zorka' ),
                            'param_name' => 'custom_links_target',
                            'description' => esc_html__('Select where to open  custom links.', 'zorka' ),
                            'value' => $target_arr
                        ),
                        array(
                            'type' => 'textfield',
                            'heading' => esc_html__('Slides per view', 'zorka' ),
                            'param_name' => 'column',
                            'value' => '5',
                            'description' => esc_html__('Set numbers of slides you want to display', 'zorka' )
                        ),
                        array(
                            'type' => 'checkbox',
                            'heading' => esc_html__('Slider autoplay', 'zorka' ),
                            'param_name' => 'autoplay',
                            'description' => esc_html__('Enables autoplay mode.', 'zorka' ),
                            'value' => array( esc_html__('Yes, please', 'zorka' ) => 'yes' )
                        ),
                        array(
                            'type' => 'checkbox',
                            'heading' => esc_html__('Show pagination control', 'zorka' ),
                            'param_name' => 'pagination',
                            'value' => array( esc_html__('Yes, please', 'zorka' ) => 'yes' )
                        ),
                        $add_el_class,
                        $add_css_animation,
                        $add_duration_animation,
                        $add_delay_animation
                    )
                ));
                vc_map(
                    array(
                        'name'                    => esc_html__('MailChimp', 'zorka' ),
                        'base'                    => 'zorka_mailchimp',
                        'icon'                    => 'icon-wpb-title',
                        'category'                => esc_html__('Zorka Shortcodes', 'zorka' ),
                        'params'                  => array(
                            array(
                                'type'        => 'dropdown',
                                'heading'     => esc_html__('Layout Style', 'zorka' ),
                                'param_name'  => 'layout_style',
                                'admin_label' => true,
                                'value'       => array( esc_html__('style 1', 'zorka' ) => 'style1', esc_html__('style 2', 'zorka' ) => 'style2'),
                                'description' => esc_html__('Select Layout Style.', 'zorka' )
                            ),
                            array(
                                'type'        => 'icon_text',
                                'heading'     => esc_html__('Select Icon:', 'zorka' ),
                                'param_name'  => 'icon',
                                'value'       => '',
                                'description' => esc_html__('Select the icon from the list.', 'zorka' ),
                            ),
                            array(
                                'type'        => 'textfield',
                                'heading'     => esc_html__('Title', 'zorka' ),
                                'param_name'  => 'title',
                                'value'       => '',
                            ),
                            array(
                                'type'        => 'textfield',
                                'heading'     => esc_html__('Sub Title', 'zorka' ),
                                'param_name'  => 'sub_title',
                                'value'       => '',
                            ),
                            $add_el_class,
                            $add_css_animation,
                            $add_duration_animation,
                            $add_delay_animation
                        )
                    )
                ); // end vc_map
                vc_map(
                    array(
                        'name'                    => esc_html__('Icon Box', 'zorka' ),
                        'base'                    => 'zorka_icon_box',
                        'icon'                    => 'icon-wpb-title',
                        'category'                => esc_html__('Zorka Shortcodes', 'zorka' ),
                        'description'             => 'Adds icon box with font icons',
                        'params'                  => array(
                            array(
                                'type'        => 'dropdown',
                                'heading'     => esc_html__('Layout Style', 'zorka' ),
                                'param_name'  => 'layout_style',
                                'admin_label' => true,
                                'value'       => array( esc_html__('style 1', 'zorka' ) => 'style1', esc_html__('style 2', 'zorka' ) => 'style2', esc_html__('style 3', 'zorka' ) => 'style3', esc_html__('style 4', 'zorka' ) => 'style4', esc_html__('style 5', 'zorka' ) => 'style5'),
                                'description' => esc_html__('Select Layout Style.', 'zorka' )
                            ),
                            array(
                                'type'        => 'icon_text',
                                'heading'     => esc_html__('Select Icon:', 'zorka' ),
                                'param_name'  => 'icon',
                                'value'       => '',
                                'description' => esc_html__('Select the icon from the list.', 'zorka' ),
                            ),
                            array(
                                'type'        => 'dropdown',
                                'heading'     => esc_html__('Icon position', 'zorka' ),
                                'param_name'  => 'icon_position',
                                'value'       => array( esc_html__('left', 'zorka' ) => 'left', esc_html__('right', 'zorka' ) => 'right', esc_html__('center', 'zorka' ) => 'center'),
                                'dependency'  => Array( 'element' => 'layout_style', 'value' => array( 'style1','style2' ) )
                            ),
                            array(
                                'type'        => 'colorpicker',
                                'heading'     => esc_html__('Icon Background Color', 'zorka' ),
                                'param_name'  => 'icon_bg_color',
                                'value'       => '',
                                'dependency'  => Array( 'element' => 'layout_style', 'value' => array( 'style5') )
                            ),
                            array(
                                'type'        => 'colorpicker',
                                'heading'     => esc_html__('Background Color', 'zorka' ),
                                'param_name'  => 'bg_color',
                                'value'       => '',
                                'dependency'  => Array( 'element' => 'layout_style', 'value' => array( 'style5') )
                            ),
                            array(
                                'type'       => 'textfield',
                                'heading'    => esc_html__('Link (url)', 'zorka' ),
                                'param_name' => 'link',
                                'value'      => '#',
                            ),
                            array(
                                'type'        => 'textfield',
                                'heading'     => esc_html__('Title', 'zorka' ),
                                'param_name'  => 'title',
                                'value'       => '',
                                'description' => esc_html__('Provide the title for this icon box.', 'zorka' ),
                            ),
                            array(
                                'type'        => 'textarea',
                                'heading'     => esc_html__('Description', 'zorka' ),
                                'param_name'  => 'description',
                                'value'       => '',
                                'description' => esc_html__('Provide the description for this icon box.', 'zorka' ),
                                'dependency'  => Array( 'element' => 'layout_style', 'value' => array( 'style1','style2','style3','style4' ) )
                            ),
                            $add_el_class,
                            $add_css_animation,
                            $add_duration_animation,
                            $add_delay_animation
                        )
                    )
                ); // end vc_map                               
            }
        }
    }
    new Zorka_Shortcode;
}