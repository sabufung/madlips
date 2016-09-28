<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) die( '-1' );
if(!class_exists('Zorka_Button')){
    class Zorka_Button{
        function __construct(){
            add_shortcode('zorka_button', array($this, 'zorka_button_shortcode'));
        }
        function zorka_button_shortcode($atts){
            $size=$layout_style=$link_target= $link = $button_label = $html = $el_class = $g5plus_animation = $css_animation = $duration = $delay = $styles_animation ='';
            extract( shortcode_atts( array(
                'layout_style'  => 'style1',
                'size'          => 'button-sm',
                'button_label'  => '',
                'link'	        => '',
                'link_target'	=> '',
                'el_class'      => '',
                'css_animation' => '',
                'duration'      => '',
                'delay'         => ''
            ), $atts ) );
            $g5plus_animation .= ' ' . esc_attr($el_class);
            $g5plus_animation .= g5plus_getCSSAnimation( $css_animation );
            $styles_animation= g5plus_getStyleAnimation($duration,$delay);
            $link_target= ($link_target == 'yes') ? '_blank' : '_self';
            $html .= '<a class="zorka-button ' . esc_attr($layout_style) .' '. esc_attr($size) . $g5plus_animation . '" '.$styles_animation.' href="'.esc_url($link).'" target="'.$link_target.'">'.esc_html($button_label).'</a>';
            return $html;
        }
    }
    new Zorka_Button;
}