/**
 * Created by phuongth on 3/30/15.
 */
var Trending = {
    registerTrendingFilter:function(section_id){
        jQuery(document).ready(function(){
            jQuery('a.isotope-filter','.trending-product').off();
            jQuery('a.isotope-filter','.trending-product').click(function(){
                var dataSectionId = jQuery(this).attr('data-section-id');
                var filter_active = jQuery('.isotope-filter.active','#'+dataSectionId).attr('data-filter');

                jQuery('.isotope-filter','#'+dataSectionId).removeClass('active');
                jQuery('li','#'+dataSectionId).removeClass('active');
                jQuery(this).parent().addClass('active');
                jQuery(this).addClass('active');
                var filter = jQuery(this).attr('data-filter');

                var $group_active = jQuery('.trending-group' + filter_active, '#'+dataSectionId);
                $group_active.fadeOut("slow",function(){
                    jQuery( filter, '#'+dataSectionId).fadeIn("slow");
                });
            });

        });
    }
};
