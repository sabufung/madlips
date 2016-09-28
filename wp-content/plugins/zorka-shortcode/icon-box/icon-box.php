<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) die( '-1' );
if(!class_exists('Zorka_Icon_Box')){
    class Zorka_Icon_Box{
        function __construct(){
            add_shortcode('zorka_icon_box', array($this, 'zorka_icon_box_shortcode'));
        }
        function zorka_icon_box_shortcode($atts){
            $icon_bg_color=$bg_color=$icon_position=$layout_style=$link=$description=$title=$image=$icon= $html = $el_class = $g5plus_animation = $css_animation = $duration = $delay = $styles_animation = '';
            extract( shortcode_atts( array(
                'layout_style'  => 'style1',
                'icon'          => '',
                'icon_position' => 'left',
                'bg_color'      => '',
                'icon_bg_color' => '',
                'link'          => '',
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
            if($icon_position!='') $icon_position=' '.esc_attr($icon_position);
            if(($layout_style=='style3')||($layout_style=='style4')||($layout_style=='style5')) $icon_position='';
            $html .= '<div class="zorka-icon-box ' . esc_attr($layout_style). $icon_position . $g5plus_animation .'" '.$styles_animation .'>';
            if($layout_style=='style5')
            {
                $html .= '<a style="background-color:'.$icon_bg_color.';" class="zbox-icon" href="'.esc_url($link).'"><i class="'.esc_attr($icon).'"></i></a>
                          <a style="background-color:'.$bg_color.';" class="zbox-title" href="'.esc_url($link).'">'. esc_html($title) .'</a>';
            }
            else
            {
                $html .= '<a class="zbox-icon" href="'.esc_url($link).'"><i class="'.esc_attr($icon).'"></i></a>
                          <a class="zbox-title" href="'.esc_url($link).'">'. esc_html($title) .'</a>';
                if($description!='')
                {
                    $html .='<p>'.esc_html($description).'</p>';
                }
            }
            $html .= '</div>';
            return $html;
        }
    }
    new Zorka_Icon_Box;
}