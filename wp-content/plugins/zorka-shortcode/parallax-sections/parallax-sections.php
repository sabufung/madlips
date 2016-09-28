<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) die( '-1' );
if(!class_exists('Zorka_Parallax_Sections')){
    class Zorka_Parallax_Sections{
        function __construct(){
            add_shortcode('zorka_parallax_sections', array($this, 'zorka_parallax_sections_shortcode'));
        }
        function zorka_parallax_sections_shortcode($atts){
            $icon=$layout_style=$description = $bt1_link_target= $bt1_link = $bt1_label = $bt2_link_target= $bt2_link = $bt2_label = $bg_images = $title =  $html = $el_class = $g5plus_animation = $css_animation = $duration = $delay = $styles_animation ='';
            extract( shortcode_atts( array(
                'layout_style'      => 'style1',
                'title'             => '',
                'description'       => '',
                'icon'              => '',
                'bt1_label'         => '',
                'bt1_link'	        => '',
                'bt1_link_target'	=> '',
                'bt2_label'         => '',
                'bt2_link'	        => '',
                'bt2_link_target'	=> '',
                'el_class'          => '',
                'css_animation'     => '',
                'duration'          => '',
                'delay'             => ''
            ), $atts ) );
            $g5plus_animation .= ' ' . esc_attr($el_class);
            $g5plus_animation .= g5plus_getCSSAnimation( $css_animation );
            $styles_animation= g5plus_getStyleAnimation($duration,$delay);
            $bt1_link_target= ($bt1_link_target == 'yes') ? '_blank' : '_self';
            $bt2_link_target= ($bt2_link_target == 'yes') ? '_blank' : '_self';

            if($layout_style=='style1')
            {
                $html .= '<div class="zorka-parallax_sections content-middle '. esc_attr($layout_style) . $g5plus_animation . '" '.$styles_animation.'>
                            <div class="content-middle-inner">
                                <h2>'.esc_html($title).'</h2>
                                <p>'.esc_html($description).'</p>';
                                if($bt1_label!='')
                                {
                                    $html.='<a class="zorka-button style3 button-sm" href="'.esc_url($bt1_link).'" target="'.$bt1_link_target.'">'.esc_html($bt1_label).'</a>';
                                }
                                if($bt2_label!='')
                                {
                                    $html.='<a class="zorka-button style4 button-sm" href="'.esc_url($bt2_link).'" target="'.$bt2_link_target.'">'.esc_html($bt2_label).'</a>';
                                }
                    $html.='</div>
                         </div>';
            }
            else if($layout_style=='style2')
            {
                $html .= '<div class="zorka-parallax_sections content-middle '. esc_attr($layout_style) . $g5plus_animation . '" '.$styles_animation.'>
                            <div class="content-middle-inner">
                                <i class="'.esc_attr($icon).'"></i>
                                <h2>'.esc_html($title).'</h2>
                                <p>'.esc_html($description).'</p>';
                                if($bt1_label!='')
                                {
                                    $html.='<a class="zorka-button style3 button-sm" href="'.esc_url($bt1_link).'" target="'.$bt1_link_target.'">'.esc_html($bt1_label).'</a>';
                                }
                                if($bt2_label!='')
                                {
                                    $html.='<a class="zorka-button style4 button-sm" href="'.esc_url($bt2_link).'" target="'.$bt2_link_target.'">'.esc_html($bt2_label).'</a>';
                                }
                    $html.='</div>
                         </div>';
            }
            else if($layout_style=='style3')
            {
                $html .= '<div class="zorka-parallax_sections content-middle '. esc_attr($layout_style) . $g5plus_animation . '" '.$styles_animation.'>
                            <div class="content-middle-inner">
                                <p>'.esc_html($description).'</p>
                                <h2>'.esc_html($title).'</h2>';
                                if($bt1_label!='')
                                {
                                    $html.='<a class="zorka-button style3 button-sm" href="'.esc_url($bt1_link).'" target="'.$bt1_link_target.'">'.esc_html($bt1_label).'</a>';
                                }
                                if($bt2_label!='')
                                {
                                    $html.='<a class="zorka-button style4 button-sm" href="'.esc_url($bt2_link).'" target="'.$bt2_link_target.'">'.esc_html($bt2_label).'</a>';
                                }
                        $html.='</div>
                         </div>';
            }
            return $html;
        }
    }
    new Zorka_Parallax_Sections;
}