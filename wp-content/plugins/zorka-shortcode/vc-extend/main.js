(function( $ ){
    "use strict";
    var $window = jQuery(window);
    var windowHeight = $window.height();

    $window.resize(function () {
        windowHeight = $window.height();
    });

    jQuery.fn.vparallax = function(xpos, speedFactor, outerHeight) {
        var $this = jQuery(this);
        var attach = $this.data('scroll_effect');
        $this.css('background-attachment', attach);
        var getHeight;
        var firstTop;
        if (outerHeight) {
            getHeight = function(jqo) {
                return jqo.outerHeight(true);
            };
        } else {
            getHeight = function(jqo) {
                return jqo.height();
            };
        }
        if (arguments.length < 1 || xpos === null) xpos = "50%";
        if (arguments.length < 2 || speedFactor === null) speedFactor = 0.5;
        if (arguments.length < 3 || outerHeight === null) outerHeight = true;
        function update(){
            var pos = $window.scrollTop();
            $this.each(function(){
                firstTop = jQuery(this).offset().top;
                var $element = jQuery(this);
                var top = $element.offset().top;
                var height = getHeight($element);
                if (top + height < pos || top > pos + windowHeight) {
                    return;
                }
                var f = Math.round((firstTop - pos) * speedFactor);
                if(firstTop >= windowHeight){ f = f-(speedFactor*windowHeight);	}
                else{	f=-f;	}
                $this.css('backgroundPosition', xpos + " " + f + "px");
            });
        }
        $window.bind('scroll', update).resize(update);
        update();
    };

    jQuery.fn.hparallax = function(xpos, speedFactor, outerHeight) {
        var $this = jQuery(this);
        var attach = $this.data('scroll_effect');
        $this.css('background-attachment', attach);
        var getHeight;
        var firstTop;
        if (outerHeight) {
            getHeight = function(jqo) {
                return jqo.outerHeight(true);
            };
        } else {
            getHeight = function(jqo) {
                return jqo.height();
            };
        }
        if (arguments.length < 1 || xpos === null) xpos = "50%";
        if (arguments.length < 2 || speedFactor === null) speedFactor = 0.5;
        if (arguments.length < 3 || outerHeight === null) outerHeight = true;
        xpos = '0px';
        var prev_pos = $window.scrollTop();
        function update(){
            var pos = $window.scrollTop();
            $this.each(function(){
                firstTop = jQuery(this).offset().top;
                var $element = jQuery(this);
                var top = $element.offset().top;
                var height = getHeight($element);
                if (top + height < pos || top > pos + windowHeight) {
                    return;
                }
                var bg = $this.css('backgroundPosition');
                var pxpos = bg.indexOf('px');
                var bgxpos= bg.substring(0,pxpos);
                var f =0;
                if(prev_pos-pos <= 0){
                    f = parseInt(bgxpos) - parseInt(speedFactor*(Math.abs(prev_pos-pos)));
                }else{
                    f = parseInt(bgxpos) + parseInt(speedFactor*(prev_pos-pos));
                    if(f>0)
                        f=0;
                }
                $this.css('backgroundPosition', f + "px "+ xpos);

            });
            prev_pos = pos;
        }

        $window.bind('scroll', update).resize(update);
        update();
    };
})(jQuery);
// Auto Initialization
jQuery(document).ready(function(){
    jQuery('.vertical-parallax').each(function () {
        jQuery(this).vparallax("50%", jQuery(this).data('parallax_speed'));
    });
    jQuery('.horizontal-parallax').each(function(){
        jQuery(this).hparallax("0", jQuery(this).data('parallax_speed'));
    });
    if(jQuery('.horizontal-parallax').length>0){
        setTimeout(function() {
            jQuery(window).scrollTop(0);
        }, 1000);
    }
    jQuery('[data-overlay_image]').each(function() {
        var selector =jQuery(this);
        var overlay_image = selector.data('overlay_image');
        var overlay_opacity = selector.data('overlay_opacity');
        var overlay_id = selector.attr('id');
        var style_css= '#'+overlay_id +'.overlay:before{background-image: url('+ overlay_image +') ;background-repeat:repeat; opacity:'+overlay_opacity+';}';
        jQuery('[data-type=vc_shortcodes-custom-css]').append(style_css);
    });
    jQuery('[data-overlay_color]').each(function() {
        var selector =jQuery(this);
        var overlay_color = selector.data('overlay_color');
        var overlay_id = selector.attr('id');
        var style_css= '#'+overlay_id +'.overlay:before{background-color: '+ overlay_color +';}';
        jQuery('[data-type=vc_shortcodes-custom-css]').append(style_css);
    });
});