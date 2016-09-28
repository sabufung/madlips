<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) die( '-1' );
if(!class_exists('Zorka_Banner')){
    class Zorka_Banner{
        function __construct(){
            add_shortcode('zorka_banner', array($this, 'zorka_banner_shortcode'));
        }
        function zorka_banner_shortcode($atts){
            $description= $title2 = $title1 = $html = $el_class = $g5plus_animation = $css_animation = $duration = $delay = $styles_animation ='';
            extract( shortcode_atts( array(
                'title1'        => '',
                'title2'        => '',
                'description'	=> '',
                'el_class'      => '',
                'css_animation' => '',
                'duration'      => '',
                'delay'         => ''
            ), $atts ) );
            $g5plus_animation .= ' ' . esc_attr($el_class);
            $g5plus_animation .= g5plus_getCSSAnimation( $css_animation );
            $styles_animation= g5plus_getStyleAnimation($duration,$delay);
            $html .= '<div class="container zorka-banner '. $g5plus_animation . '" '.$styles_animation.'>
                        <div class="row">
                            <div class="col-lg-5 col-lg-push-7">
                                <div class="banner-content">
                                    <p class="banner-content-title"><span>'.esc_html($title1).'</span></p>
                                    <p class="banner-content-title"><span class="banner-content-sub-title">'.esc_html($title2).'</span></p>
                                    <p class="banner-content-description">'.esc_html($description).'</p>
                                </div>
                            </div>
                        </div>
                    </div>';
            return $html;
        }
    }
    new Zorka_Banner;
}