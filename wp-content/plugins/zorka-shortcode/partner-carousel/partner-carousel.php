<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) die( '-1' );
if(!class_exists('Zorka_Partner_Carousel')){
    class Zorka_Partner_Carousel{
        function __construct(){
            add_shortcode('zorka_partner_carousel', array($this, 'zorka_partner_carousel_shortcode'));
        }
        function zorka_partner_carousel_shortcode($atts){
            $pagination=$img_size=$autoplay=$column=$custom_links_target=$custom_links = $images = $html = $el_class = $g5plus_animation = $css_animation = $duration = $delay = $styles_animation ='';
            extract( shortcode_atts( array(
                'images'        => '',
                'custom_links'  => '',
                'custom_links_target' => '_blank',
                'img_size'      => 'thumbnail',
                'column'        => '5',
                'autoplay'      => '',
                'pagination'    => '',
                'el_class'      => '',
                'css_animation' => '',
                'duration'      => '',
                'delay'         => ''
            ), $atts ) );
            $g5plus_animation .= ' ' . esc_attr($el_class);
            $g5plus_animation .= g5plus_getCSSAnimation( $css_animation );
            $styles_animation= g5plus_getStyleAnimation($duration,$delay);
            if ( $images == '' ) $images = '-1,-2,-3';

            $custom_links = explode( ',', $custom_links );

            $images = explode( ',', $images );
            $i = - 1;

            $autoplay= ($autoplay == 'yes') ? 'true' : 'false';
            $pagination= ($pagination == 'yes') ? 'true' : 'false';
            $html .= '<div class="zorka-partner-carousel ' . $g5plus_animation . ' container" '.$styles_animation.'>';
            $html .= '<div class="owl-carousel" data-plugin-options=\'{"items" : '.esc_attr($column).', "autoPlay": '.esc_attr($autoplay).',"pagination": '.esc_attr($pagination).'}\'>';
            foreach ( $images as $attach_id ):
                $i ++;
                if ( $attach_id > 0 ) {
                    $post_thumbnail = wpb_getImageBySize( array( 'attach_id' => $attach_id, 'thumb_size' => $img_size ) );
                } else {
                    $post_thumbnail = array();
                    $post_thumbnail['thumbnail'] = '<img src="' . vc_asset_url( 'vc/no_image.png' ) . '" />';
                    $post_thumbnail['p_img_large'][0] = vc_asset_url( 'vc/no_image.png' );
                }
                $thumbnail = $post_thumbnail['thumbnail'];
                if (isset( $custom_links[$i] ) && $custom_links[$i] != '' )
                {
                    $html .='<div class="content-middle-inner"><a href="'.esc_url($custom_links[$i]).'" target="' . esc_attr($custom_links_target) . '">';
                    $html .= $thumbnail .'</a></div>';
                }
                else
                {
                    $html .= '<div class="content-middle-inner">'.$thumbnail.'</div>';
                }
            endforeach;
            $html .= '</div></div>';
            return $html;
        }
    }
    new Zorka_Partner_Carousel;
}