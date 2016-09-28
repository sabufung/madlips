<?php
/**
 * Created by PhpStorm.
 * User: phuongth
 * Date: 3/20/15
 * Time: 4:53 PM
 */

add_action("wp_ajax_nopriv_zorka_portfolio_load_more", "zorka_portfolio_load_more");
add_action("wp_ajax_zorka_portfolio_load_more", "zorka_portfolio_load_more");
function zorka_portfolio_load_more(){
    $current_page = $_REQUEST['current_page'];
    $offset = $_REQUEST['offset'];
    $posts_per_page = $_REQUEST['postsPerPage'];
    $layout_type = $_REQUEST['layoutType'];
    $overlay_style = $_REQUEST['overlayStyle'];
    $column = $_REQUEST['columns'];
    $padding = $_REQUEST['colPadding'];
    $current_page = $current_page;
    $short_code = sprintf('[zorka_portfolio show_category="" column="%s" item="%s" show_pagging="1" overlay_style="%s" layout_type="%s" padding="%s" current_page="%s"]', $column, $posts_per_page, $overlay_style, $layout_type, $padding, $current_page);
    echo do_shortcode($short_code);
    die();
}