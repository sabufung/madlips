<?php
/**
 * Created by PhpStorm.
 * User: phuongth
 * Date: 3/19/15
 * Time: 3:26 PM
 */

if ( ! defined( 'ABSPATH' ) ) die( '-1' );

if ( ! defined( 'ZORKA_PORTFOLIO_CATEGORY_TAXONOMY' ) )
    define( 'ZORKA_PORTFOLIO_CATEGORY_TAXONOMY', 'portfolio-category');

if ( ! defined( 'ZORKA_PORTFOLIO_POST_TYPE' ) )
    define( 'ZORKA_PORTFOLIO_POST_TYPE', 'portfolio');

if(! defined( 'ZORKA_PORTFOLIO_DIR_PATH' ))
    define( 'ZORKA_PORTFOLIO_DIR_PATH', plugin_dir_path( __FILE__ ));

if(!class_exists('Zorka_Portfolio')){
    class Zorka_Portfolio {
        function __construct() {
            add_action('wp_enqueue_scripts',array($this,'front_scripts'),11);
            add_action( 'init', array($this, 'register_taxonomies' ), 5 );
            add_action( 'init', array($this, 'register_post_types' ), 6 );
            add_shortcode('zorka_portfolio', array($this, 'zorka_portfolio_shortcode' ));
            add_filter( 'rwmb_meta_boxes', array($this,'zorka_register_meta_boxes' ));
            add_action( 'after_setup_theme', array($this,'register_image_size'));
            add_filter('single_template',array($this,'get_portfolio_single_template' ) );
            add_filter('template_include', array($this,'get_portfolio_taxonomy_template'));

            if(is_admin()){
                add_filter('manage_edit-'.ZORKA_PORTFOLIO_POST_TYPE.'_columns' , array($this,'add_portfolios_columns'));
                add_action( 'manage_'.ZORKA_PORTFOLIO_POST_TYPE.'_posts_custom_column' ,array($this,'set_portfolios_columns_value'), 10, 2 );
                add_action('restrict_manage_posts',array($this,'portfolio_manage_posts'));
                add_filter('parse_query',array($this,'convert_taxonomy_term_in_query'));
            }

            $this->includes();
        }

        function front_scripts(){
            $min_suffix = defined( 'ZORKA_SCRIPT_DEBUG' ) && ZORKA_SCRIPT_DEBUG ? '' : '.min';
            wp_enqueue_style( 'zorka-pretty-css', plugins_url() . '/zorka-shortcode/portfolio/assets/css/prettyPhoto.css', array() );
            wp_enqueue_script('zorka-pretty-js',plugins_url() . '/zorka-shortcode/portfolio/assets/js/prettyPhoto/jquery.prettyPhoto.js', false, true);

            wp_enqueue_script('zorka-isotope',plugins_url() . '/zorka-shortcode/portfolio/assets/js/isotope/isotope.pkgd.min.js', false, true);

            wp_enqueue_script('zorka-modernizr',plugins_url() . '/zorka-shortcode/portfolio/assets/js/hoverdir/modernizr.js', false, true);
            wp_enqueue_script('zorka-hoverdir',plugins_url() . '/zorka-shortcode/portfolio/assets/js/hoverdir/jquery.hoverdir.js', false, true);
            wp_enqueue_script('zorka-portfolio-ajax-action',plugins_url() . '/zorka-shortcode/portfolio/assets/js/ajax-action'.$min_suffix.'.js', false, true);
        }

        function register_post_types() {
            if ( post_type_exists('portfolio') ) {
                return;
            }
            register_post_type('portfolio',
                array(
                    'label' => esc_html__('Portfolio','zorka'),
                    'description' => esc_html__('Portfolio Description', 'zorka' ),
                    'labels' => array(
                        'name'					=>'Portfolio',
                        'singular_name' 		=> 'Portfolio',
                        'menu_name'    			=> esc_html__('Portfolio', 'zorka' ),
                        'parent_item_colon'  	=> esc_html__('Parent Item:', 'zorka' ),
                        'all_items'          	=> esc_html__('All Portfolio', 'zorka' ),
                        'view_item'          	=> esc_html__('View Item', 'zorka' ),
                        'add_new_item'       	=> esc_html__('Add New Portfolio', 'zorka' ),
                        'add_new'            	=> esc_html__('Add New', 'zorka' ),
                        'edit_item'          	=> esc_html__('Edit Item', 'zorka' ),
                        'update_item'        	=> esc_html__('Update Item', 'zorka' ),
                        'search_items'       	=> esc_html__('Search Item', 'zorka' ),
                        'not_found'          	=> esc_html__('Not found', 'zorka' ),
                        'not_found_in_trash' 	=> esc_html__('Not found in Trash', 'zorka' ),
                    ),
                    'supports'    => array( 'title', 'editor', 'excerpt', 'thumbnail'),
                    'public'      => true,
                    'show_ui'            => true,
                    '_builtin' => false,
                    'has_archive' => true,
                    'menu_icon'   => 'dashicons-screenoptions'
                )
            );
        }

        function register_taxonomies(){
            if ( taxonomy_exists( ZORKA_PORTFOLIO_CATEGORY_TAXONOMY ) ) {
                return;
            }
            register_taxonomy( ZORKA_PORTFOLIO_CATEGORY_TAXONOMY,  ZORKA_PORTFOLIO_POST_TYPE, array( 'hierarchical' => true, 'label' => esc_html__('Portfolio Categories','zorka'), 'query_var' => true, 'rewrite' => true ) );
        }

        function zorka_portfolio_shortcode($atts){
            $offset = $current_page = $overlay_style =$show_pagging = $show_category = $category = $column = $item = $padding = $layout_type = $el_class = $g5plus_animation = $css_animation = $duration = $delay = $styles_animation = '';
            extract( shortcode_atts( array(
                'show_pagging'   => '',
                'show_category' => '',
                'category'     => '',
                'column'  => '2',
                'item' => '',
                'padding' => '',
                'layout_type'  => 'grid',
                'schema_style' => '',
                'overlay_style' => 'icon',
                'el_class'      => '',
                'css_animation' => '',
                'duration'      => '',
                'delay'         => '',
                'current_page' => '1'
            ), $atts ) );

            $post_per_page = $item;
            if($category!='')
                $show_category = 0;

            $offset = ($current_page-1) * $item;

            $g5plus_animation .= ' ' . esc_attr($el_class);
            $g5plus_animation .= g5plus_getCSSAnimation( $css_animation );
            $styles_animation= g5plus_getStyleAnimation($duration,$delay);

            $plugin_path =  untrailingslashit( plugin_dir_path( __FILE__ ) );
            $template_path = $plugin_path . '/templates/listing.php';

            ob_start();
            include($template_path);
            $ret = ob_get_contents();
            ob_end_clean();
            return $ret;
        }

        function zorka_register_meta_boxes($meta_boxes){
            $meta_boxes[] = array(
                'title'  => esc_html__('Portfolio Extra', 'zorka' ),
                'id'     => 'zorka-meta-box-portfolio-format-gallery',
                'pages'  => array( ZORKA_PORTFOLIO_POST_TYPE ),
                'fields' => array(
                    array(
                        'name' => esc_html__('Client', 'zorka' ),
                        'id'   => 'portfolio-client',
                        'type' => 'text',
                    ),
                    array(
                        'name' => esc_html__('Gallery', 'zorka' ),
                        'id'   => 'portfolio-format-gallery',
                        'type' => 'image_advanced',
                    ),
                    array(
                        'name'     => esc_html__('View Detail Style', 'zorka' ),
                        'id'       => 'portfolio_detail_style',
                        'type'     => 'select',
                        'options'  => array(
                            'none' => esc_html__('None','zorka'),
                            'fullwidth' 	=> esc_html__('Full width', 'zorka' ),
                            'bigslider' 	=> esc_html__('Big Slider', 'zorka' ),
                            'smallslider' 	=> esc_html__('Small Slider', 'zorka' ),
                            'sidebar' 	=> esc_html__('Sidebar', 'zorka' ),
                            'verticalslider' 	=> esc_html__('Vertical Slider', 'zorka' ),
                        ),
                        'multiple'    => false,
                        'std'         => 'none',
                    )
                )
            );
            return $meta_boxes;
        }

        function get_portfolio_single_template($single) {
            global $post;
            /* Checks for single template by post type */
            if ($post->post_type == ZORKA_PORTFOLIO_POST_TYPE){
                $plugin_path =  untrailingslashit( ZORKA_PORTFOLIO_DIR_PATH );
                $template_path = $plugin_path . '/templates/single/single-portfolio.php';
                if(file_exists($template_path))
                    return $template_path;
            }
            return $single;
        }

        function get_portfolio_taxonomy_template($template) {
            if( !is_single() && is_tax(ZORKA_PORTFOLIO_CATEGORY_TAXONOMY)){
                $plugin_path =  untrailingslashit( ZORKA_PORTFOLIO_DIR_PATH );
                $template_path = $plugin_path . '/templates/taxonomy/taxonomy-portfolio.php';
                if(file_exists($template_path)){
                    return $template_path;
                }
            }
            return $template;
        }

        function register_image_size(){
            add_image_size( 'thumbnail-570x460', 570, 460, true );
            add_image_size( 'thumbnail-770x514', 770, 514, true );
            add_image_size( 'thumbnail-1920x640', 1920, 640, true );
            add_image_size( 'thumbnail-1200x774', 1200, 774, true );
        }

        function add_portfolios_columns($columns) {
            unset(
                $columns['cb'],
                $columns['title'],
                $columns['date']
            );
            $cols = array_merge(array('cb'=>('')),$columns);
            $cols = array_merge($cols,array('title'=>__('Porfolio Name','zorka')));
            $cols = array_merge($cols,array('thumbnail'=>__('Thumbnail','zorka')));
            $cols = array_merge($cols,array(ZORKA_PORTFOLIO_CATEGORY_TAXONOMY=>__('Categories','zorka')));
            $cols = array_merge($cols,array('date'=>__('Date','zorka')));
            return $cols;
        }

        function set_portfolios_columns_value( $column, $post_id ) {
            switch($column){
                case 'id':{
                    echo wp_kses_post($post_id);
                    break;
                }
                case 'thumbnail':
                {
                    echo get_the_post_thumbnail($post_id,'thumbnail');
                    break;
                }
                case ZORKA_PORTFOLIO_CATEGORY_TAXONOMY:
                {
                    $terms = wp_get_post_terms( get_the_ID(), array( ZORKA_PORTFOLIO_CATEGORY_TAXONOMY));
                    $cat = '<ul>';
                    foreach ( $terms as $term ){
                        $cat .= '<li><a href="'.get_term_link( $term, ZORKA_PORTFOLIO_CATEGORY_TAXONOMY ).'">'.$term->name.'<a/></li>';
                    }
                    $cat .= '</ul>';
                    echo wp_kses_post($cat);
                    break;
                }
            }
        }

        function portfolio_manage_posts() {
            global $typenow;
            if ($typenow==ZORKA_PORTFOLIO_POST_TYPE){
                $selected = isset($_GET[ZORKA_PORTFOLIO_CATEGORY_TAXONOMY]) ? $_GET[ZORKA_PORTFOLIO_CATEGORY_TAXONOMY] : '';
                $args = array(
                    'show_count' => true,
                    'show_option_all' => esc_html__('Show All Categories','zorka'),
                    'taxonomy'        => ZORKA_PORTFOLIO_CATEGORY_TAXONOMY,
                    'name'               => ZORKA_PORTFOLIO_CATEGORY_TAXONOMY,
                    'selected' => $selected,

                );
                wp_dropdown_categories($args);
            }
        }

        function convert_taxonomy_term_in_query($query) {
            global $pagenow;
            $qv = &$query->query_vars;
            if ($pagenow=='edit.php' &&
                isset($qv[ZORKA_PORTFOLIO_CATEGORY_TAXONOMY])  &&
                is_numeric($qv[ZORKA_PORTFOLIO_CATEGORY_TAXONOMY])) {
                $term = get_term_by('id',$qv[ZORKA_PORTFOLIO_CATEGORY_TAXONOMY],ZORKA_PORTFOLIO_CATEGORY_TAXONOMY);
                $qv[ZORKA_PORTFOLIO_CATEGORY_TAXONOMY] = $term->slug;
            }
        }

        private function includes(){
            include_once('utils/ajax-action.php');
        }
    }
    new Zorka_Portfolio();


}