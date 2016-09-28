<?php
function xmenu_get_transition() {
	return array(
		'none' => __('None','xmenu'),
		'x-animate-slide-up' => __('Slide Up','xmenu'),
		'x-animate-slide-down' => __('Slide Down','xmenu'),
		'x-animate-slide-left' => __('Slide Left','xmenu'),
		'x-animate-slide-right' => __('Slide Right','xmenu'),
		'x-animate-sign-flip' => __('Sign Flip','xmenu'),
	);
}

function xmenu_get_grid () {
	return array(
		'basic' => array(
			'text' => __('Basic','xmenu'),
			'options' => array(
				'auto' => __('Automatic','xmenu'),
				'x-col x-col-12-12' => __('Full Width','xmenu'),
			)
		),
		'halves' => array(
			'text' => __('Halves','xmenu'),
			'options' => array(
				'x-col x-col-6-12' => __('1/2','xmenu'),
			)
		),
		'thirds' => array(
			'text' => __('Thirds','xmenu'),
			'options' => array(
				'x-col x-col-4-12' => __('1/3','xmenu'),
				'x-col x-col-8-12' => __('2/3','xmenu'),
			)
		),
		'quarters' => array(
			'text' => __('Quarters','xmenu'),
			'options' => array(
				'x-col x-col-3-12' => __('1/4','xmenu'),
				'x-col x-col-9-12' => __('3/4','xmenu'),
			)
		),
		'fifths' => array(
			'text' => __('Fifths','xmenu'),
			'options' => array(
				'x-col x-col-2-10' => __('1/5','xmenu'),
				'x-col x-col-4-10' => __('2/5','xmenu'),
				'x-col x-col-6-10' => __('3/5','xmenu'),
				'x-col x-col-8-10' => __('4/5','xmenu'),
			)
		),
		'sixths' => array(
			'text' => __('Sixths','xmenu'),
			'options' => array(
				'x-col x-col-2-12' => __('1/6','xmenu'),
				'x-col x-col-10-12' => __('5/6','xmenu'),
			)
		),
		'sevenths' => array(
			'text' => __('Sevenths','xmenu'),
			'options' => array(
				'x-col x-col-1-7' => __('1/7','xmenu'),
				'x-col x-col-2-7' => __('2/7','xmenu'),
				'x-col x-col-3-7' => __('3/7','xmenu'),
				'x-col x-col-4-7' => __('4/7','xmenu'),
				'x-col x-col-5-7' => __('5/7','xmenu'),
				'x-col x-col-6-7' => __('6/7','xmenu'),
			)
		),
		'eighths' => array(
			'text' => __('Eighths','xmenu'),
			'options' => array(
				'x-col x-col-1-8' => __('1/8','xmenu'),
				'x-col x-col-3-8' => __('3/8','xmenu'),
				'x-col x-col-5-8' => __('5/8','xmenu'),
				'x-col x-col-7-8' => __('7/8','xmenu'),
			)
		),
		'ninths' => array(
			'text' => __('Ninths','xmenu'),
			'options' => array(
				'x-col x-col-1-9' => __('1/9','xmenu'),
				'x-col x-col-2-9' => __('2/9','xmenu'),
				'x-col x-col-4-9' => __('4/9','xmenu'),
				'x-col x-col-5-9' => __('5/9','xmenu'),
				'x-col x-col-7-9' => __('7/9','xmenu'),
				'x-col x-col-8-9' => __('8/9','xmenu'),
			)
		),
		'tenths' => array(
			'text' => __('Tenths','xmenu'),
			'options' => array(
				'x-col x-col-1-10' => __('1/10','xmenu'),
				'x-col x-col-3-10' => __('3/10','xmenu'),
				'x-col x-col-7-10' => __('7/10','xmenu'),
				'x-col x-col-9-10' => __('9/10','xmenu'),
			)
		),
		'elevenths' => array(
			'text' => __('Elevenths','xmenu'),
			'options' => array(
				'x-col x-col-1-11' => __('1/11','xmenu'),
				'x-col x-col-2-11' => __('2/11','xmenu'),
				'x-col x-col-3-11' => __('3/11','xmenu'),
				'x-col x-col-4-11' => __('4/11','xmenu'),
				'x-col x-col-5-11' => __('5/11','xmenu'),
				'x-col x-col-6-11' => __('6/11','xmenu'),
				'x-col x-col-7-11' => __('7/11','xmenu'),
				'x-col x-col-8-11' => __('8/11','xmenu'),
				'x-col x-col-9-11' => __('9/11','xmenu'),
				'x-col x-col-10-11' => __('10/11','xmenu'),
			)
		),
		'twelfths' => array(
			'text' => __('Twelfths','xmenu'),
			'options' => array(
				'x-col x-col-1-12' => __('1/12','xmenu'),
				'x-col x-col-5-12' => __('5/12','xmenu'),
				'x-col x-col-7-12' => __('7/12','xmenu'),
				'x-col x-col-11-12' => __('11/12','xmenu'),
			)
		),
	);
}


global $xmenu_item_settings;
$xmenu_item_settings = array(
	'general' => array(
		'text' => __('General','xmenu'),
		'icon' => 'fa fa-cogs',
		'config' => array(
			'general-heading' => array(
				'text' => __('General','xmenu'),
				'type' => 'heading'
			),
			'general-url' => array(
				'text' => __('URL','xmenu'),
				'type' => 'text',
				'std'  => '',
			),
			'general-title' => array(
				'text' => __('Navigation Label','xmenu'),
				'type' => 'text',
				'std'  => '',
			),
			'general-attr-title' => array(
				'text' => __('Title Attribute','xmenu'),
				'type' => 'text',
				'std'  => '',
			),
			'general-target' => array(
				'text' => __('Open link in a new window/tab','xmenu'),
				'type' => 'checkbox',
				'std'  => '',
				'value' => '_blank',
			),
			'general-classes' => array(
				'text' => __('CSS Classes (optional)','xmenu'),
				'type' => 'array',
				'std'  => '',
			),
			'general-xfn' => array(
				'text' => __('Link Relationship (XFN)','xmenu'),
				'type' => 'text',
				'std'  => '',
			),
			'general-description' => array(
				'text' => __('Description','xmenu'),
				'type' => 'textarea',
				'std'  => '',
			),
			'general-other-heading' => array(
				'text' => __('Other','xmenu'),
				'type' => 'heading'
			),
			'other-disable-text' => array(
				'text' => __('Disable Text','xmenu'),
				'type' => 'checkbox',
				'std' => ''
			),
			'other-disable-menu-item' => array(
				'text' => __('Disable Menu Item','xmenu'),
				'type' => 'checkbox',
				'std' => ''
			),
			'other-disable-link' => array(
				'text' => __('Disable Link','xmenu'),
				'type' => 'checkbox',
				'std' => ''
			),
			'other-display-header-column' => array(
				'text' => __('Display as a Sub Menu column header','xmenu'),
				'type' => 'checkbox',
				'std' => ''
			),
			'other-feature-text' => array(
				'text' => __('Menu Feature Text','xmenu'),
				'type' => 'text',
				'std' => ''
			),
		)
	),
	'icon' => array(
		'text' => __('Icon','xmenu'),
		'icon' => 'fa fa-qrcode',
		'config' => array(
			'icon-heading' => array(
				'text' => __('Icon','xmenu'),
				'type' => 'heading'
			),
			'icon-value' => array(
				'text' => __('Set Icon','xmenu'),
				'type' => 'icon',
				'std'  => '',
			),
			'icon-position' => array(
				'text' => __('Icon Position','xmenu'),
				'type' => 'select',
				'std'  => 'left',
				'options' => array(
					'left' => __('Left of Menu Text','xmenu'),
					'right' => __('Right of Menu Text','xmenu'),
				)
			),
			'icon-padding' => array(
				'text' => __('Padding Icon and Text Menu','xmenu'),
				'type' => 'text',
				'std'  => '',
				'des' => __('Padding between Icon and Text Menu (px). Do not include units','xmenu')
			)
		)
	),
	'image' => array(
		'text' => __('Image','xmenu'),
		'icon' => 'fa fa-picture-o',
		'config' => array(
			'image-heading' => array(
				'text' => __('Image','xmenu'),
				'type' => 'heading'
			),
			'image-url' => array(
				'text' => __('Image Url','xmenu'),
				'type' => 'image',
				'std'  => '',
			),
			'image-size' => array(
				'text' => __('Image Size','xmenu'),
				'type' => 'select',
				'std'  => 'inherit',
				'options' => xmenu_get_image_size()
			),
			'image-dimensions' => array(
				'text' => __('Image Dimensions','xmenu'),
				'type' => 'select',
				'std'  => 'inherit',
				'options' => array(
					'inherit' => 'Inherit from Menu Settings',
					'custom' => 'Custom',
				)
			),
			'image-width' => array(
				'text' => __('Image Width','xmenu'),
				'type' => 'text',
				'std'  => '',
				'des' => __('Image width attribute (px). Do not include units. Only valid if "Image Dimension" is set to "Custom" above','xmenu')
			),
			'image-height' => array(
				'text' => __('Image Height','xmenu'),
				'type' => 'text',
				'std'  => '',
				'des' => __('Image width attribute (px). Do not include units. Only valid if "Image Dimension" is set to "Custom" above','xmenu')
			),
			'image-layout' => array(
				'text' => __('Image Layout','xmenu'),
				'type' => 'select',
				'std'  => 'image-only',
				'options' => array(
					'image-only' => __('Image Only','xmenu'),
					'left' => __('Image Left','xmenu'),
					'right' => __('Image Right','xmenu'),
					'above' => __('Image Above','xmenu'),
					'below' => __('Image Below','xmenu'),
				)
			),
			'image-feature' => array(
				'text' => __('Use Feature Image','xmenu'),
				'type' => 'checkbox',
				'std'  => '',
				'des' => 'Use Feature Image from Post/Page Menu Item',
			),
		)
	),

	'layout' => array(
		'text' => __('Layout','xmenu'),
		'icon' => 'fa fa-columns',
		'config' => array(
			'layout-heading' => array(
				'text' => __('Layout','xmenu'),
				'type' => 'heading'
			),
			'layout-width' => array(
				'text' => __('Menu Item Width','xmenu'),
				'type' => 'select-group',
				'std'  => 'auto',
				'options' => xmenu_get_grid()
			),
			'layout-text-align' => array(
				'text' => __('Item Content Alignment','xmenu'),
				'type' => 'select',
				'std'  => 'none',
				'options' => array(
					'none' => __('Default','xmenu'),
					'left' => __('Text Left','xmenu'),
					'center' => __('Text Center','xmenu'),
					'right' => __('Text Right','xmenu'),
				)
			),
			'layout-padding' => array(
				'text' => __('Padding','xmenu'),
				'type' => 'text',
				'std'  => '',
				'des' => __('Set padding for menu item. Include the units.','xmenu'),
			),
			'layout-margin' => array(
				'text' => __('Margin','xmenu'),
				'type' => 'text',
				'std'  => '',
				'des' => __('Set margin for menu item. Include the units.','xmenu'),
			),
			'layout-new-row' => array(
				'text' => __('New Row','xmenu'),
				'type' => 'checkbox',
				'std'  => ''
			),
		)
	),
	'submenu' => array(
		'text' => __('Sub Menu','xmenu'),
		'icon' => 'fa fa-list-alt',
		'config' => array(
			'submenu-heading' => array(
				'text' => __('Sub Menu','xmenu'),
				'type' => 'heading'
			),
			'submenu-type' => array(
				'text' => __('Sub Menu Type','xmenu'),
				'type' => 'select',
				'std'  => 'standard',
				'options' => array(
					'standard' => __('Standard','xmenu'),
					'multi-column' => __('Multi Column','xmenu'),
					/*'stack' => __('Stack','xmenu'),*/
					'tab' => __('Tab','xmenu'),
				)
			),
			'submenu-position' => array(
				'text' => __('Sub Menu Position','xmenu'),
				'type' => 'select',
				'std'  => '',
				'options' => array(
					'' => __('Automatic','xmenu'),
					'pos-left-menu-parent' => __('Left of Menu Parent','xmenu'),
					'pos-right-menu-parent' => __('Right of Menu Parent','xmenu'),
					'pos-center-menu-parent' => __('Center of Menu Parent','xmenu'),
					'pos-left-menu-bar' => __('Left of Menu Bar','xmenu'),
					'pos-right-menu-bar' => __('Right of Menu Bar','xmenu'),
					'pos-full' => __('Full Size','xmenu'),
				)
			),
			'submenu-width-custom' => array(
				'text' => __('Sub Menu Width Custom','xmenu'),
				'type' => 'text',
				'std'  => '',
				'des' => __('Set custom Sub Menu Width. Include the units (px/em/%).','xmenu'),
			),
			'submenu-col-width-default' => array(
				'text' => __('Sub Menu Column Width Default','xmenu'),
				'type' => 'select-group',
				'std'  => 'auto',
				'options' => xmenu_get_grid()
			),
			'submenu-col-spacing-default' => array(
				'text' => __('Sub Menu Column Spacing Default','xmenu'),
				'type' => 'text',
				'std'  => '',
				'des' => __('Set sub menu column spacing default. Do not include unit.','xmenu'),
			),
			'submenu-list-style' => array(
				'text' => __('Sub Menu List Style','xmenu'),
				'type' => 'select',
				'std'  => 'none',
				'options' => array(
					'none' => __('None','xmenu'),
					'disc' => __('Disc','xmenu'),
					'square' => __('Square','xmenu'),
					'circle' => __('Circle','xmenu'),
				)
			),
			'submenu-tab-position' => array(
				'text' => __('Tab Position','xmenu'),
				'type' => 'select',
				'std'  => 'left',
				'des' => __('Tab Position set to "Sub Menu Type" is "TAB".','xmenu'),
				'options' => array(
					'left' => __('Left','xmenu'),
					'right' => __('Right','xmenu'),
				)
			),
			'submenu-animation' => array(
				'text' => __('Sub Menu Animation','xmenu'),
				'type' => 'select',
				'std'  => 'none',
				'options' => xmenu_get_transition()
			),
		)
	),
	'custom-content' => array(
		'text' => __('Custom Content','xmenu'),
		'icon' => 'fa fa-code',
		'config' => array(
			'custom-content-heading' => array(
				'text' => __('Custom Content','xmenu'),
				'type' => 'heading'
			),
			'custom-content-value' => array(
				'text' => __('Custom Content','xmenu'),
				'type' => 'textarea',
				'std'  => '',
				'des' => __('Can contain HTML and shortcodes','xmenu'),
				'height' => '300px'
			),
		)
	),
	'widget' => array(
		'text' => __('Widget Area','xmenu'),
		'icon' => 'fa-puzzle-piece',
		'config' => array(
			'widget-heading' => array(
				'text' => __('Widget Area','xmenu'),
				'type' => 'heading'
			),
			'widget-area' => array(
				'text' => __('Widget Area','xmenu'),
				'type' => 'text',
				'std'  => '',
				'des' => __('Enter a name for your Widget Area, and a widget area specifically for this menu item will be automatically be created in the <a href="widgets.php" target="_blank">Widgets Screen</a>','xmenu'),
			),
		)
	),
	'customize-style' => array(
		'text' => __('Customize Style','xmenu'),
		'icon' => 'fa-paint-brush',
		'config' => array(
			'custom-style-menu-heading' => array(
				'text' => __('Menu Item','xmenu'),
				'type' => 'heading'
			),
			'custom-style-menu-bg-color' => array(
				'text' => __('Background Color','xmenu'),
				'type' => 'color',
				'std'  => '',
			),
			'custom-style-menu-text-color' => array(
				'text' => __('Text Color','xmenu'),
				'type' => 'color',
				'std'  => '',
			),
			'custom-style-menu-bg-color-active' => array(
				'text' => __('Background Color [Active]','xmenu'),
				'type' => 'color',
				'std'  => '',
			),
			'custom-style-menu-text-color-active' => array(
				'text' => __('Text Color [Active]','xmenu'),
				'type' => 'color',
				'std'  => '',
			),
			'custom-style-menu-bg-image' => array(
				'text' => __('Background Image','xmenu'),
				'type' => 'image',
				'std'  => '',
			),
			'custom-style-menu-bg-image-repeat' => array(
				'text' => __('Background Image Repeat','cupid'),
				'type' => 'select',
				'std' => 'no-repeat',
				'hide-label' => 'true',
				'options' => array(
					'no-repeat' => 'no-repeat',
					'repeat' => 'repeat',
					'repeat-x' => 'repeat-x',
					'repeat-y' => 'repeat-y'
				)
			),
			'custom-style-menu-bg-image-attachment' => array(
				'text' => __('Background Image Attachment','cupid'),
				'type' => 'select',
				'std' => 'scroll',
				'hide-label' => 'true',
				'options' => array(
					'scroll' => 'scroll',
					'fixed' => 'fixed'
				)
			),
			'custom-style-menu-bg-image-position' => array(
				'text' => __('Background Image Position','cupid'),
				'type' => 'select',
				'std' => 'center',
				'hide-label' => 'true',
				'options' => array(
					'center' => 'center',
					'center left' => 'center left',
					'center right' => 'center right',
					'top left' => 'top left',
					'top center' => 'top center',
					'top right' => 'top right',
					'bottom left' => 'bottom left',
					'bottom center' => 'bottom center',
					'bottom right' => 'bottom right'
				)
			),
			'custom-style-menu-bg-image-size' => array(
				'text' => __('Background Image Size','cupid'),
				'type' => 'select',
				'std' => 'auto',
				'hide-label' => 'true',
				'options' => array(
					'auto' => 'Keep original',
					'100% auto' => 'Stretch to width',
					'auto 100%' => 'Stretch to height',
					'cover' => 'Cover',
					'contain' => 'Contain'
				)
			),
			'custom-style-sub-menu-heading' => array(
				'text' => __('Sub Menu','xmenu'),
				'type' => 'heading'
			),
			'custom-style-sub-menu-bg-color' => array(
				'text' => __('Background Color','xmenu'),
				'type' => 'color',
				'std'  => '',
			),
			'custom-style-sub-menu-text-color' => array(
				'text' => __('Text Color','xmenu'),
				'type' => 'color',
				'std'  => '',
			),
			'custom-style-sub-menu-bg-image' => array(
				'text' => __('Background Image','xmenu'),
				'type' => 'image',
				'std'  => '',
			),
			'custom-style-sub-menu-bg-image-repeat' => array(
				'text' => __('Background Image Repeat','cupid'),
				'type' => 'select',
				'std' => 'no-repeat',
				'hide-label' => 'true',
				'options' => array(
					'no-repeat' => 'no-repeat',
					'repeat' => 'repeat',
					'repeat-x' => 'repeat-x',
					'repeat-y' => 'repeat-y'
				)
			),
			'custom-style-sub-menu-bg-image-attachment' => array(
				'text' => __('Background Image Attachment','cupid'),
				'type' => 'select',
				'std' => 'scroll',
				'hide-label' => 'true',
				'options' => array(
					'scroll' => 'scroll',
					'fixed' => 'fixed'
				)
			),
			'custom-style-sub-menu-bg-image-position' => array(
				'text' => __('Background Image Position','cupid'),
				'type' => 'select',
				'std' => 'center',
				'hide-label' => 'true',
				'options' => array(
					'center' => 'center',
					'center left' => 'center left',
					'center right' => 'center right',
					'top left' => 'top left',
					'top center' => 'top center',
					'top right' => 'top right',
					'bottom left' => 'bottom left',
					'bottom center' => 'bottom center',
					'bottom right' => 'bottom right'
				)
			),
			'custom-style-sub-menu-bg-image-size' => array(
				'text' => __('Background Image Size','cupid'),
				'type' => 'select',
				'std' => 'auto',
				'hide-label' => 'true',
				'options' => array(
					'auto' => 'Keep original',
					'100% auto' => 'Stretch to width',
					'auto 100%' => 'Stretch to height',
					'cover' => 'Cover',
					'contain' => 'Contain'
				)
			),
			'custom-style-col-min-width' => array(
				'text' => __('Column Min Width','xmenu'),
				'type' => 'text',
				'std'  => '',
				'des' => __('Set min-width for Sub Menu Column (px). Not include the units.','xmenu'),
			),
			'custom-style-padding' => array(
				'text' => __('Padding','xmenu'),
				'type' => 'text',
				'std'  => '',
				'des' => __('Set padding for Sub Menu. Include the units.','xmenu'),
			),

			'custom-style-feature-menu-text-heading' => array(
				'text' => __('Menu Feature Text','xmenu'),
				'type' => 'heading'
			),
			'custom-style-feature-menu-text-type' => array(
				'text' => __('Feature Menu Type','xmenu'),
				'type' => 'select',
				'std'  => '',
				'options' => array(
					'' => __('Standard','xmenu'),
					'x-feature-menu-not-float' => __('Not Float','xmenu')
				)
			),
			'custom-style-feature-menu-text-bg-color' => array(
				'text' => __('Background Color','xmenu'),
				'type' => 'color',
				'std'  => '',
			),
			'custom-style-feature-menu-text-color' => array(
				'text' => __('Text Color','xmenu'),
				'type' => 'color',
				'std'  => '',
			),
			'custom-style-feature-menu-text-top' => array(
				'text' => __('Position Top','xmenu'),
				'type' => 'text',
				'std'  => '',
				'des'  => 'Position Top (px) Feature Menu Text. Do not include units.',
			),
			'custom-style-feature-menu-text-left' => array(
				'text' => __('Position Left','xmenu'),
				'type' => 'text',
				'std'  => '',
				'des'  => 'Position Left (px) Feature Menu Text. Do not include units.',
			),
		)
	),
	'responsive' => array(
		'text' => __('Responsive','xmenu'),
		'icon' => 'fa-desktop',
		'config' => array(
			'responsive-heading' => array(
				'text' => __('Responsive','xmenu'),
				'type' => 'heading'
			),
			'responsive-hide-mobile-css' => array(
				'text' => __('Hide item on mobile via CSS','xmenu'),
				'type' => 'checkbox',
				'std' => ''
			),
			'responsive-hide-desktop-css' => array(
				'text' => __('Hide item on desktop via CSS','xmenu'),
				'type' => 'checkbox',
				'std' => ''
			),
			'responsive-hide-mobile-css-submenu' => array(
				'text' => __('Hide sub menu on mobile via CSS','xmenu'),
				'type' => 'checkbox',
				'std' => ''
			),
			'responsive-remove-mobile' => array(
				'text' => __('Remove this item when mobile device is detected via wp_is_mobile()','xmenu'),
				'type' => 'checkbox',
				'std' => ''
			),
			'responsive-remove-desktop' => array(
				'text' => __('Remove this item when desktop device is NOT detected via wp_is_mobile()','xmenu'),
				'type' => 'checkbox',
				'std' => ''
			),
			'responsive-remove-mobile-submenu' => array(
				'text' => __('Remove sub menu when desktop device is NOT detected via wp_is_mobile()','xmenu'),
				'type' => 'checkbox',
				'std' => ''
			),
		),
	),
	'responsive' => array(
		'text' => __('Responsive','xmenu'),
		'icon' => 'fa-desktop',
		'config' => array(
			'responsive-heading' => array(
				'text' => __('Responsive','xmenu'),
				'type' => 'heading'
			),
			'responsive-hide-mobile-css' => array(
				'text' => __('Hide item on mobile via CSS','xmenu'),
				'type' => 'checkbox',
				'std' => ''
			),
			'responsive-hide-desktop-css' => array(
				'text' => __('Hide item on desktop via CSS','xmenu'),
				'type' => 'checkbox',
				'std' => ''
			),
			'responsive-hide-mobile-css-submenu' => array(
				'text' => __('Hide sub menu on mobile via CSS','xmenu'),
				'type' => 'checkbox',
				'std' => ''
			),
			'responsive-hide-desktop-css-submenu' => array(
				'text' => __('Hide sub menu on desktop via CSS','xmenu'),
				'type' => 'checkbox',
				'std' => ''
			),
			/*'responsive-remove-mobile' => array(
				'text' => __('Remove this item when mobile device is detected via wp_is_mobile()','xmenu'),
				'type' => 'checkbox',
				'std' => ''
			),
			'responsive-remove-desktop' => array(
				'text' => __('Remove this item when desktop device is NOT detected via wp_is_mobile()','xmenu'),
				'type' => 'checkbox',
				'std' => ''
			),
			'responsive-remove-mobile-submenu' => array(
				'text' => __('Remove sub menu when desktop device is NOT detected via wp_is_mobile()','xmenu'),
				'type' => 'checkbox',
				'std' => ''
			),*/
		),
	)
);

global $xmenu_item_defaults;
$xmenu_item_defaults = xmenu_get_item_defaults($xmenu_item_settings);

function xmenu_get_item_defaults($items_setting, $defaults = array()) {
	if (!$defaults) {
		$defaults = array(
			'nosave-type_label' => '',
			'nosave-type' => '',
			'nosave-change' => 0
		);
	}

	foreach ($items_setting as $seting_key => $setting) {
		foreach ($setting['config'] as $key => $value) {
			if (isset($value['config']) && $value['config']) {

			}
			else {
				if ($value['type'] != 'heading') {
					$defaults[$key] = $value['std'];
				}
			}

		}
	}
	return $defaults;
}
function xmenu_get_image_size($is_setting = 0) {
	global $_wp_additional_image_sizes;

	$sizes = array();
	$get_intermediate_image_sizes = get_intermediate_image_sizes();

	// Create the full array with sizes and crop info
	foreach( $get_intermediate_image_sizes as $_size ) {

		if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {

			$sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
			$sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
			$sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );

		} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {

			$sizes[ $_size ] = array(
				'width' => $_wp_additional_image_sizes[ $_size ]['width'],
				'height' => $_wp_additional_image_sizes[ $_size ]['height'],
				'crop' =>  $_wp_additional_image_sizes[ $_size ]['crop']
			);

		}

	}
	$image_size = array();
	if (!$is_setting) {
		$image_size ['inherit'] = __('Inherit from Menu Setting','xmenu');
	}
	$image_size ['full'] = __('Full Size','xmenu');
	foreach ($sizes as $key => $value) {
		$image_size[$key] = ucfirst($key) . ' (' . $value['width'] . ' x ' . $value['height'] .')' . ($value['crop'] ? '[cropped]' : '') ;
	}
	return $image_size;
}