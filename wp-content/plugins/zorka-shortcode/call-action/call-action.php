<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) die( '-1' );
if(!class_exists('Zorka_Call_Action')){
    class Zorka_Call_Action{
        function __construct(){
            add_shortcode('zorka_call_action', array($this, 'zorka_call_action_shortcode'));
        }
        function zorka_call_action_shortcode($atts){
            $icon=$layout_style=$description = $link_target= $link = $button_label = $bg_images = $title =  $html = $el_class = $g5plus_animation = $css_animation = $duration = $delay = $styles_animation ='';
            extract( shortcode_atts( array(
                'layout_style'  => 'style1',
                'bg_images'     => '',
                'title'          => '',
                'description'   => '',
                'icon'          => '',
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

            $style_bg_images = '';


            if (!empty($bg_images)) {
                $bg_images_attr = wp_get_attachment_image_src($bg_images, "full");
                if (isset($bg_images_attr)) {
                    $style_bg_images = 'style="background-image: url(' . $bg_images_attr[0]. ')"';
                }
            }


            if($layout_style=='style1')
            {
                $html .= '<div ' . $style_bg_images .' class="zorka-call-action '. esc_attr($layout_style) . $g5plus_animation . '" '.$styles_animation.'>
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-6 col-lg-push-6">
                                        <h2>'.esc_html($title).'</h2>
                                        <p>'.esc_html($description).'</p>
                                        <a class="zorka-button style2 button-sm" href="'.esc_url($link).'" target="'.$link_target.'">'.esc_html($button_label).'</a>
                                    </div>
                                </div>
                            </div>
                         </div>';
            }
            else if($layout_style=='style2')
            {
                $html .= '<div ' . $style_bg_images .' class="zorka-call-action '. esc_attr($layout_style) . $g5plus_animation . '" '.$styles_animation.'>
                            <i class="'.esc_attr($icon).'"></i>
                            <h2>'.esc_html($title).'</h2>
                            <a class="zorka-button style2 button-sm" href="'.esc_url($link).'" target="'.$link_target.'">'.esc_html($button_label).'</a>
                          </div>';
            }
            else if($layout_style=='style3')
            {
                $html .= '<div ' . $style_bg_images .' class="zorka-call-action '. esc_attr($layout_style) . $g5plus_animation . '" '.$styles_animation.'>
                            <h2>'.esc_html($title).'</h2>
                            <p>'.esc_html($description).'</p>
                            <a class="zorka-button style2 button-sm" href="'.esc_url($link).'" target="'.$link_target.'">'.esc_html($button_label).'</a>
                          </div>';
            }
            else
            {
                $html .= '<div ' . $style_bg_images .' class="zorka-call-action '. esc_attr($layout_style) . $g5plus_animation . '" '.$styles_animation.'>
                            <div class="container">
                                 <div class="content-middle">
                                    <div class="content-middle-inner">
                                        <h2>'.esc_html($title).'</h2>
                                        <a class="zorka-button style1 button-sm" href="'.esc_url($link).'" target="'.$link_target.'">'.esc_html($button_label).'</a>
                                    </div>
                                </div>
                            </div>
                          </div>';
            }
            return $html;
        }
    }
    new Zorka_Call_Action;
}