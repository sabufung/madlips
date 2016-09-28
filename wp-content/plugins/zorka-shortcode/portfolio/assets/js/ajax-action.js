/**
 * Created by phuongth on 3/20/15.
 */
var PortfolioAjaxAction = {
    htmlTag:{
        load_more :'.load-more',
        portfolio_container: '#portfolio-'
    },
    vars:{
        ajax_url: ''
    },
    registerPagingEvent:function(){
        jQuery(PortfolioAjaxAction.htmlTag.load_more,'.portfolio').off();
        jQuery(PortfolioAjaxAction.htmlTag.load_more,'.portfolio').click(function(){
            var $this = jQuery(this).button('loading');
            var $section_id = $this.attr('data-section-id');
            var $current_page = $this.attr('data-current-page');
            var $offset = $this.attr('data-offset');
            var $post_per_page = $this.attr('data-post-per-page');
            var $overlay_style = $this.attr('data-overlay-style');
            var $column = $this.attr('data-column');
            var $padding = $this.attr('data-padding');
            var $layout_type = $this.attr('data-layout-type');
            jQuery.ajax({
                url: PortfolioAjaxAction.vars.ajax_url,
                data: ({action : 'zorka_portfolio_load_more', postsPerPage: $post_per_page, current_page: $current_page,
                    layoutType: $layout_type, overlayStyle: $overlay_style,
                    columns: $column, colPadding: $padding, offset: $offset
                }),
                success: function(data) {
                    $this.button('reset');
                    var $container = jQuery('#portfolio-container-' + $section_id);
                    var $item = jQuery('.portfolio-item',data);
                    if(jQuery('.load-more',data)!=null && jQuery('.load-more',data).length > 0){
                        $this.attr('data-current-page',jQuery('.load-more',data).attr('data-current-page'));
                    }else
                        $this.hide();
                    $container.append( $item ).isotope( 'appended', $item );
                    $container.imagesLoaded( function() {
                        //$container.isotope('layout');
                        jQuery('.portfolio-item > div').hoverdir('destroy');
                        jQuery('.portfolio-item > div').hoverdir('rebuild');

                        jQuery('a','#portfolio-' + $section_id + ' .portfolio-tabs ').removeClass('active');
                        jQuery('a[data-group="all"]').addClass('active');
                        $container.isotope({ filter: '*' });
                    });

                    PortfolioAjaxAction.registerPrettyPhoto();



                }
            });
        });
    },
    registerPrettyPhoto:function(){
        jQuery("a[rel^='prettyPhoto']").prettyPhoto(
            {
                theme: 'light_rounded',
                slideshow: 5000,
                deeplinking: false,
                social_tools: false
            });
    },
    init:function(ajax_url){
        PortfolioAjaxAction.vars.ajax_url = ajax_url;
        PortfolioAjaxAction.registerPagingEvent();
        PortfolioAjaxAction.registerPrettyPhoto();
    }
}