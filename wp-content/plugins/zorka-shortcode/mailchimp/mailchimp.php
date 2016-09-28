<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) die( '-1' );
if(!class_exists('Zorka_Mailchimp')){
    class Zorka_Mailchimp{
        function __construct(){
            add_shortcode('zorka_mailchimp', array($this, 'zorka_mailchimp_shortcode'));
        }
        function zorka_mailchimp_shortcode($atts){
            $icon=$layout_style=$title= $sub_title = $html = $el_class = $g5plus_animation = $css_animation = $duration = $delay = $styles_animation ='';
            extract( shortcode_atts( array(
                'layout_style'  => 'style1',
                'icon'	        => '',
                'title'         => '',
                'sub_title'     => '',
                'el_class'      => '',
                'css_animation' => '',
                'duration'      => '',
                'delay'         => ''
            ), $atts ) );
            $g5plus_animation .= ' ' . esc_attr($el_class);
            $g5plus_animation .= g5plus_getCSSAnimation( $css_animation );
            ob_start();?>
                <div  class="zorka-mailchimp <?php echo esc_attr($layout_style) ?> <?php echo esc_attr($g5plus_animation) ?>" <?php echo g5plus_getStyleAnimation($duration,$delay); ?>>
                    <div class="content-middle-inner">
                        <i class="<?php echo esc_attr($icon) ?>"></i>
                        <h5><?php echo esc_attr($title) ?></h5>
                        <p><?php echo esc_attr($sub_title) ?></p>
                        <?php echo do_shortcode('[mc4wp_form]'); ?>
                    </div>
                </div>
            <?php
            $content = ob_get_clean();
            return $content;
        }
    }
    new Zorka_Mailchimp;
}?>