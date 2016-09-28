<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) die( '-1' );
if(!class_exists('Zorka_Heading')){
    class Zorka_Heading{
        function __construct(){
            add_shortcode('zorka_heading', array($this, 'zorka_heading_shortcode'));
        }
        function zorka_heading_shortcode($atts){
            $layout_style=$description = $title = $html = $el_class = $g5plus_animation = $css_animation = $duration = $delay = $styles_animation ='';
            extract( shortcode_atts( array(
                'layout_style'  => 'style1',
                'title'         => '',
                'description'   => '',
                'el_class'      => '',
                'css_animation' => '',
                'duration'      => '',
                'delay'         => ''
            ), $atts ) );
            $g5plus_animation .= ' ' . esc_attr($el_class);
            $g5plus_animation .= g5plus_getCSSAnimation( $css_animation );
            $styles_animation= g5plus_getStyleAnimation($duration,$delay);
            if($layout_style=='style2' || $layout_style=='style4') $description='';
            $html = '<div class="zorka-heading ' . esc_attr($layout_style) . $g5plus_animation . '" '.$styles_animation.'>
                        <div class="content-middle-inner">
                            <h2>'. wp_kses_post($title) .'</h2>';
            if($description!='')
            {
                    $html .='<p>'.esc_html($description).'</p>';
            }
            $html .=    '</div>
                    </div>';
            return $html;
        }
    }
    new Zorka_Heading;
}