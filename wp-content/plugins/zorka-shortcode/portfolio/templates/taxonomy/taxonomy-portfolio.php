<?php get_header(); ?>
<?php get_template_part('archive','top');?>
<?php
    $current_page = 1;
    $offset = 0;
    $posts_per_page = -1;
    $layout_type = "info";
    $overlay_style = "icon";
    $column = 4;
    $padding = 'col-padding-10';
    $current_page = $current_page;
    $category = get_query_var( 'term' );
    $short_code = sprintf('[zorka_portfolio show_category="" category="%s" column="%s" item="%s" show_pagging="1" overlay_style="%s" layout_type="%s" padding="%s" current_page="%s"]',$category, $column, $posts_per_page, $overlay_style, $layout_type, $padding, $current_page);
?>
<main role="main" class="site-content-archive">
    <div class="container clearfix">
        <div class="row clearfix">
            <div class="archive-portfolio">
                <?php echo do_shortcode($short_code); ?>
            </div>
        </div>
    </div>
</main>
<?php get_footer(); ?>
