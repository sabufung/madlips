<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) die( '-1' );
if(!class_exists('Zorka_Counter')){
    class Zorka_Counter{
        function __construct(){
            add_shortcode('zorka_counter', array($this, 'zorka_counter_shortcode'));
        }
        function zorka_counter_shortcode($atts){
            $value = $title = $html = $el_class ='';
            extract( shortcode_atts( array(
                'value' => '',
                'title' => '',
                'el_class'       => ''
            ), $atts ) );

			$min_suffix = defined( 'ZORKA_SCRIPT_DEBUG' ) && ZORKA_SCRIPT_DEBUG ? '' : '.min';
            wp_enqueue_script('zorka_counter',plugins_url('zorka-shortcode/counter/jquery.countTo'.$min_suffix.'.js'),array(),false, true);

            $html .= '<div class="zorka-counter '.esc_attr($el_class).'">';
            if ( $value != '' ) {
                $html .= '<span class="display-percentage" data-percentage="' . esc_attr($value) . '">' . esc_html($value) . '</span>';
                if ( $title != '' ) {
                    $html .= '<p class="counter-title">' .wp_kses_post($title) . '</p>';
                }
            }
            $html .= '</div>';
            return $html;
        }
    }
    new Zorka_Counter;
}