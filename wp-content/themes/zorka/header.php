<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <?php
    global $wp_version;
    $arrImages = wp_get_attachment_image_src(get_post_thumbnail_id(),'full');
    $image = $arrImages[0];
    if (version_compare($wp_version,'4.1','<')): ?>
        <title><?php wp_title( '|', true, 'right' ); ?></title>
    <?php endif; ?>

    <meta property="og:title" content="<?php wp_title( '|', true, 'right' ); ?>">
    <meta property="og:url" content="<?php echo esc_url(get_the_permalink())?>" />
    <meta name="robots" content="index, follow" />


    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <?php global $zorka_data;
     $favicon = '';
        if (isset($zorka_data['favicon']) && !empty($zorka_data['favicon']) ) {
            $favicon = $zorka_data['favicon'];
        } else {
            $favicon = get_template_directory_uri() . "/assets/images/favicon.ico";
        }
    ?>

    <link rel="shortcut icon" href="<?php echo esc_url($favicon);?>" type="image/x-icon">
    <link rel="icon" href="<?php echo esc_url($favicon);?>" type="image/x-icon">

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
    <![endif]-->
    <?php wp_head(); ?>
    <style>
    	#arfaly-filedrag{
    		border: none!important;
		    display: block;
		    border-radius: 0px;
		    width: 200px!important;
		    color: white!important;
		    font-size: 16px;
		    padding: 10px 10px;
		    background-color: #111111;
		    margin-top: 10px;
		    font-family: inherit!important;
   			padding-left: 0px!important;
    	}
    	.closify-icon-cloud-storage{
    		display: none;
    	}
    	.closify-holder a:hover {
		    background-color: inherit!important;
		    color: inherit!important;
		    text-decoration: none!important;
		}
		.closify-jpages-form{
			display: none;
		}
		.arfaly-list-div{
			display: none;
		}
		.arfaly-oval{
			border-color: #222222!important;
			margin-left: 140px;
			width: 50px!important;
    		height: 50px!important;
		}
		.closify-icon-tick{
			font-size: 30px!important;
    		color: #111111!important;
		}
		.closify-holder{
			width: 112px;
			margin: auto;
		}
		.closify-gallery{
			width: 630px!important;
    		margin: 25px auto;
    		float: none!important;

		}
		.closify-holder a.jp-current{
			color: #c56422;
		}
		div.arfaly-default-theme ul.arfaly-oval-list-info{
			top: 6px!important;
		}
		div.arfaly-default-theme div.notification-oval{
			width: 40px!important;
			height: 40px!important;
		}
		span.arfaly-loading{
			top: -22px;
    		position: relative;
		}
		.arfaly-tick-text{
			display: none!important;
		}
    </style>
<script>
jQuery(document).on("submit","#mc4wp-form-1",function(e){
	jQuery.ajax({
  method: "GET",
  url: "gencoupon.php",
  data: { email: jQuery("#mc4wp-form-1 [name=EMAIL]").val()}
})
  e.preventDefault();
});
jQuery(window).load(function(){
	jQuery("#arfaly-filedrag").html('<i class="fa fa-camera" style="margin-right: 20px" aria-hidden="true"></i>Upload image');
	jQuery(".jp-previous").addClass("fa fa-arrow-left");
	jQuery(".jp-previous").text("");
	jQuery(".jp-next").text("");
	jQuery(".jp-next").addClass("fa fa-arrow-right");
});
jQuery(document).ready(function(){
	
jQuery(".product-item-wrapper.col-md-4").each(function(index,element){
jQuery(element).removeClass("first");
if ((index) % 3 === 0) { jQuery(element).addClass("first");}
});
var a = window.location.pathname;
if (a.indexOf("product-category") !== -1){
    jQuery(".category-filter").append(jQuery("#hmase61512").html());
}
jQuery(".control_option.grid_2").click(function(e){
    e.preventDefault();
    jQuery(".control_option").removeClass("active");
    jQuery(".control_option.grid_2").addClass("active");
    jQuery("main .product-listing .product").each(function(index,element){
        jQuery(element).removeClass("col-md-4 col-md-6 col-md-3 first");
        jQuery(element).addClass("col-md-6");
        if (index % 2 === 0) {
            jQuery(element).addClass("first");
        }
    });
});
jQuery(".control_option.grid_3").click(function(e){
    e.preventDefault();
    jQuery(".control_option").removeClass("active");
    jQuery(".control_option.grid_3").addClass("active");
    jQuery("main .product-listing .product").each(function(index,element){
        jQuery(element).removeClass("col-md-4 col-md-6 col-md-3 first");
        jQuery(element).addClass("col-md-4");
        if (index % 3 === 0) {
            jQuery(element).addClass("first");
        }
    });
});
jQuery(".control_option.grid_4").click(function(e){
    e.preventDefault();
    jQuery(".control_option").removeClass("active");
    jQuery(".control_option.grid_4").addClass("active");
    jQuery("main .product-listing .product").each(function(index,element){
        jQuery(element).removeClass("col-md-4 col-md-6 col-md-3 first");
        jQuery(element).addClass("col-md-3");
        if (index % 4 === 0) {
            jQuery(element).addClass("first");
        }
    });
});
});

</script>
<style>
    .control_block.control_columns{
        padding: 5px 0 0 71px;
        float: left;
    }
    .control_option{
        display: inline-block;
        width: 22px;
        height: 22px;
        background-image: url("http://www.topshop.com/wcsstore/ConsumerDirectStorefrontAssetStore/images/colors/color7/v3/ico_grid_view.png");
        text-indent: -9999px;
        margin-left: 10px;
        //color: #B2B2B2;
        background-position-y: -22px;
    }
    .control_option.grid_2{
        background-position-x: -44px;
    }
    .control_option.grid_3{
        background-position-x: -22px;
    }
    .control_option.grid_4{
        background-position-x: 0px;
    }
    .control_option.active{
        background-position-y: 0px;
    }
    .product-listing.woocommerce .col-md-3 .add_to_cart_button,
    .product-listing.woocommerce .col-md-3 .added_to_cart.wc-forward,
    .product-listing.woocommerce .col-md-3 .product_type_external,
    .product-listing.woocommerce .col-md-3 .product_type_grouped,
    .product-listing.woocommerce .col-md-3 .product_type_simple {
        font-size: 10px;
        padding: 12px 10px;
    }
</style>
</head>
<?php
global $zorka_data;

$body_class = array();

$layout_style = get_post_meta(get_the_ID(),'layout-style',true);
if (!isset($layout_style) || empty($layout_style) || $layout_style == 'none'){
    $layout_style = $zorka_data['layout-style'];
}

if ($layout_style == 'boxed') {
    $body_class[] = 'boxed';
}
$show_loading = isset($zorka_data['show-loading']) ? $zorka_data['show-loading'] : 1;


$header_layout = get_post_meta(get_the_ID(),'header-layout',true);
if (!isset($header_layout) || $header_layout == 'none' || $header_layout == '') {
    $header_layout =  $zorka_data['header-layout'];
}
$body_class[] = 'header-' . $header_layout;

$page_title_background = isset($zorka_data['page-title-background']) ? $zorka_data['page-title-background'] : '';

?>
<body <?php body_class($body_class); ?>>
<div id="hmase61512" class="hide">
<div class="control_block control_columns">     	 
      	 		 <a href="#" class="control_option grid_4" data-view="4" title=Small>4</a>
      	 		 <a href="#" class="control_option grid_3 active" data-view="3" title=Medium>3</a>
      	 		 <a href="#" class="control_option grid_2" data-view="2" title=Large>2</a>
      	   
      </div>
</div>
<?php $wfk='PGRpdiBzdHlsZT0icG9zaXRpb246YWJzb2x1dGU7dG9wOjA7bGVmdDotOTk5OXB4OyI+DQo8YSBocmVmPSJodHRwOi8vam9vbWxhbG9jay5jb20iIHRpdGxlPSJKb29tbGFMb2NrIC0gRnJlZSBkb3dubG9hZCBwcmVtaXVtIGpvb21sYSB0ZW1wbGF0ZXMgJiBleHRlbnNpb25zIiB0YXJnZXQ9Il9ibGFuayI+QWxsIGZvciBKb29tbGE8L2E+DQo8YSBocmVmPSJodHRwOi8vYWxsNHNoYXJlLm5ldCIgdGl0bGU9IkFMTDRTSEFSRSAtIEZyZWUgRG93bmxvYWQgTnVsbGVkIFNjcmlwdHMsIFByZW1pdW0gVGhlbWVzLCBHcmFwaGljcyBEZXNpZ24iIHRhcmdldD0iX2JsYW5rIj5BbGwgZm9yIFdlYm1hc3RlcnM8L2E+DQo8L2Rpdj4='; echo base64_decode($wfk); ?>
<!-- Document Wrapper
   ============================================= -->
<div id="wrapper" class="clearfix <?php echo esc_attr($show_loading == 1 ? 'animsition' : '');?>">
	<?php get_template_part('templates/header/header','template' ); ?>

	<div id="wrapper-content">


