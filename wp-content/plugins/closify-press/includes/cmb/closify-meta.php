<?php
/**
 * Include and setup custom metaboxes and fields.
 *
 * @category YourThemeOrPlugin
 * @package  Metaboxes
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/webdevstudios/Custom-Metaboxes-and-Fields-for-WordPress
 */
        
// Number sanitization function
function number_sanitization($number)
{
  $number = preg_replace( "/[^0-9]/", "", $number );

    return $number;
}

add_filter( 'Closify_Meta_Boxes', 'closify_metaboxes' );
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
function closify_metaboxes( array $meta_boxes ) {

	// Start with an underscore to hide fields from custom fields list
    $prefix = '_closify_';
    $meta_boxes['closify_mode_selection'] = array(
        'id'         => 'closify_mode_selection',
		'title'      => __( 'Closify mode selection', 'closify-press' ),
		'pages'      => array( 'closify' ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true,
		'fields'     => array(
            array(
              'name'    => 'Upload mode selection',
              'desc'    => 'Select an option',
              'id'      => $prefix . 'mode_select',
              'type'    => 'select',
              'options' => array(
                  'single' => __('Single image', 'closify-press' ),
                  'multi'   => __('Multi images', 'closify-press' ),
              )
           )
        )
    );
    
    $prefix = '_multi_';
    $meta_boxes['arfaly_metabox'] = array(
        'id'         => 'arfaly_metabox',
		'title'      => __( 'Closify multi-image uploader options', 'closify-press' ),
		'pages'      => array( 'closify' ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true,
		'fields'     => array(
            array(
				'name' => __( 'Allow guests', 'closify-press' ),
				'desc' => __( 'Allow guests to upload files', 'closify-press' ),
				'id'   => $prefix . 'allow_guests',
				'type' => 'checkbox',
			),
            array(
				'name' => __( 'Enforce information submission', 'closify-uploader' ),
				'desc' => __( 'Enforce users to submit Title and Description for each image upload.', 'closify-uploader' ),
				'id'   => $prefix . 'enforce_info',
				'type' => 'checkbox',
			),
            array(
				'name' => __( 'Disable Drag & Drop effect', 'closify-uploader' ),
				'desc' => __( 'Use native browse button for file upload.', 'closify-uploader' ),
				'id'   => $prefix . 'disable_drag_drop',
				'type' => 'checkbox',
			),
            array(
				'name' => __( 'Disable image preview', 'closify-press' ),
				'desc' => __( 'Disable image preview feature', 'closify-press' ),
				'id'   => $prefix . 'image_preview',
				'type' => 'checkbox',
			),
            array(
              'name'       => __( 'Max file size (MB)', 'closify-press' ),
              'desc'       => __( '(MB) Max upload file size allowed.', 'closify-press' ),
              'id'         => $prefix . 'max_file_size',
              'type'       => 'text_small',
              'default'    => '10',
              'sanitization_cb' => 'number_sanitization', // custom sanitization callback parameter
                          // 'escape_cb'       => 'number_escaping',  // custom escaping callback parameter
              'attributes'  => array(
                  'placeholder' => 10,
              ),
            ),
            array(
              'name'       => __( 'Max images limit)', 'closify-press' ),
              'desc'       => __( 'Max upload images limit.', 'closify-press' ),
              'id'         => $prefix . 'file_upload_limit',
              'type'       => 'text_small',
              'default'    => '10',
              'sanitization_cb' => 'number_sanitization', // custom sanitization callback parameter
                          // 'escape_cb'       => 'number_escaping',  // custom escaping callback parameter
              'attributes'  => array(
                  'placeholder' => 10,
              ),
            ),
            array(
				'name'    => __( 'Allowed images/graphics', 'closify-press' ),
				'desc'    => __( 'Allowed file format.', 'closify-press' ),
				'id'      => $prefix . 'image_formats',
				'type'    => 'multicheck',
                'default' => array('png','gif','jpg'),
				'options' => array(
					'png' => __( 'png', 'closify-press' ),
                    'gif' => __( 'gif', 'closify-press' ),
					'jpg' => __( 'jpeg jpg', 'closify-press' ),
                    'psd' => __( 'psd', 'closify-press' ),
                    'ai' => __( 'ai eps', 'closify-press' ),
					'svg' => __( 'svg', 'closify-press' ),
                    'bmp' => __( 'bmp', 'closify-press' ),
                    'tiff' => __( 'tiff', 'closify-press' ),
				),
				// 'inline'  => true, // Toggles display to inline
			),
            array(
              'name' => __( 'Debug', 'closify-press' ),
              'desc' => __( 'Print out debug messages', 'closify-press' ),
              'id'   => $prefix . 'debug',
              'type' => 'checkbox',
			),
            array(
				'name'       => __( 'Debugging info target', 'closify-press' ),
				'desc'       => __( 'For class name add "." letter prefix. For ID targeting put "#" letter', 'closify-press' ),
				'id'         => $prefix . 'target_debug',
				'type'       => 'text',
                'attributes'  => array(
                    'placeholder' => '#output-name',
                ),
			),
            array(
				'name'       => __( 'Uploader Message Label', 'closify-press' ),
				'desc'       => __( 'A label that will be displayed in the bottom of the uploader box', 'closify-press' ),
				'id'         => $prefix . 'label',
				'type'       => 'text',
                'attributes'  => array(
                    'placeholder' => 'Allowed file types are psd, ai, bmp, svg, tiff, gif, jpg, and png.',
                ),
			),
            array(
				'name'       => __( 'File Title placeholder', 'closify-uploader' ),
				'desc'       => __( 'Customize title placeholder', 'closify-uploader' ),
				'id'         => $prefix . 'title_placeholder',
				'type'       => 'text',
                'attributes'  => array(
                    'placeholder' => 'Enter file title...',
                ),
			),
            array(
				'name'       => __( 'File Description placeholder', 'closify-uploader' ),
				'desc'       => __( 'Customize description placeholder', 'closify-uploader' ),
				'id'         => $prefix . 'desc_placeholder',
				'type'       => 'text',
                'attributes'  => array(
                    'placeholder' => 'Enter file description...',
                ),
			),
            array(
              'name' => __( 'Theme options', 'closify-uploader' ),
              'desc' => __( 'Options related to plugin theme.', 'closify-uploader' ),
              'id'   => $prefix . 'test_title',
              'type' => 'title',
              ),
            array(
				'name'    => __( 'Change uploader\'s theme', 'closify-uploader' ),
				'desc'    => __( 'Select the suitable theme for your website.', 'closify-uploader' ),
				'id'      => $prefix . 'uploader_theme',
				'type'    => 'select',
                'default' => array('default'),
				'options' => array(
					'default' => __( 'Default Theme', 'closify-uploader' ),
                    'simplex' => __( 'Simplex (White background)', 'closify-uploader' ),
					'super-simplex' => __( 'Super Simplex', 'closify-uploader' ),
				),
				// 'inline'  => true, // Toggles display to inline
			),
            array(
              'name'    => __( 'Logo Color', 'closify-uploader' ),
              'desc'    => __( 'Logo color', 'closify-uploader' ),
              'id'      => $prefix . 'logo_color',
              'type'    => 'colorpicker',
              'default' => '#639AFF'
              ),
            array(
              'name'    => __( 'Text color', 'closify-press' ),
              'desc'    => __( 'Text color', 'closify-press' ),
              'id'      => $prefix . 'text_color',
              'type'    => 'colorpicker',
              'default' => '#818080'
              ),
            array(
              'name'    => __( 'Upload border color', 'closify-press' ),
              'desc'    => __( 'Upload border color', 'closify-press' ),
              'id'      => $prefix . 'border_color',
              'type'    => 'colorpicker',
              'default' => '#cecece'
              ),
            array(
              'name'    => __( 'Label Color', 'closify-press' ),
              'desc'    => __( 'Upload label text color', 'closify-press' ),
              'id'      => $prefix . 'label_color',
              'type'    => 'colorpicker',
              'default' => '#818080'
              ),
            )
        );

    $prefix = '_closify_';
	$meta_boxes['closify_metabox'] = array(
		'id'         => 'closify_metabox',
		'title'      => __( 'Closify Options', 'closify-press' ),
		'pages'      => array( 'closify' ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		// 'cmb_Closify_styles' => true, // Enqueue the CMB stylesheet on the frontend
		'fields'     => array(
			array(
				'name'       => __( 'Description', 'closify-press' ),
				'desc'       => __( 'field description (optional)', 'closify-press' ),
				'id'         => $prefix . 'description',
				'type'       => 'text',
				'show_on_cb' => 'cmb_Closify_test_text_show_on_cb', // function should return a bool value
			),
            array(
				'name' => __( 'Allow guests', 'closify-press' ),
				'desc' => __( 'Allow guests to upload images', 'closify-press' ),
				'id'   => $prefix . 'allow_guests',
				'type' => 'checkbox',
			),
            array(
				'name' => __( 'Enforce information submission', 'closify-uploader' ),
				'desc' => __( 'Enforce users to submit Title and Description for each image upload.', 'closify-uploader' ),
				'id'   => $prefix . 'enforce_info',
				'type' => 'checkbox',
			),
            array(
				'name' => __( 'Image uploader dimension', 'closify-press' ),
				'desc' => __( 'Define uploader dimension.', 'closify-press' ),
				'id'   => $prefix . 'test_title',
				'type' => 'title',
			),
            array(
				'name'       => __( "Image container's width", 'closify-press' ),
				'desc'       => __( 'Width (px).', 'closify-press' ),
				'id'         => $prefix . 'width',
				'type'       => 'text_small',
            'sanitization_cb' => 'number_sanitization', // custom sanitization callback parameter
            'attributes'  => array(
                'placeholder' => '700',
            ),
                    ),
              array(
				'name'       => __( "Image container's height", 'closify-press' ),
				'desc'       => __( 'Height (px).', 'closify-press' ),
				'id'         => $prefix . 'height',
                'sanitization_cb' => 'number_sanitization', // custom sanitization callback parameter
                            'type'       => 'text_small',
                'attributes'  => array(
                    'placeholder' => '140',
                ),
                        ),
              array(
				'name' => __( 'More settings', 'closify-press' ),
				'desc' => __( 'Technical settings.', 'closify-press' ),
				'id'   => $prefix . 'test_title',
				'type' => 'title',
              ),
              array(
				'name'       => __( 'Max file size (MB)', 'closify-press' ),
				'desc'       => __( '(MB) Max upload file size allowed.', 'closify-press' ),
				'id'         => $prefix . 'max_file_size',
				'type'       => 'text_small',
                'default'    => '10',
                'sanitization_cb' => 'number_sanitization', // custom sanitization callback parameter
                            // 'escape_cb'       => 'number_escaping',  // custom escaping callback parameter
                'attributes'  => array(
                    'placeholder' => 10,
                ),
              ),
              array(
				'name' => __( 'Progress bar', 'closify-press' ),
				'desc' => __( 'Enable progress bar feature.', 'closify-press' ),
				'id'   => $prefix . 'progress',
				'type' => 'checkbox',
                'default' => 1
			),
   array(
				'name'    => __( 'Select quality', 'closify-press' ),
				'desc'    => __( 'Select image quality', 'closify-press' ),
				'id'      => $prefix . 'quality',
				'type'    => 'select',
				'options' => array(
					'High 3x' => __( '3x', 'closify-press' ),
					'Medium 2x'   => __( '2x', 'closify-press' ),
					'Normal 1x'     => __( '1x', 'closify-press' ),
				),
			),
   array(
				'name' => __( 'Theme options', 'closify-press' ),
				'desc' => __( 'Options related to plugin theme.', 'closify-press' ),
				'id'   => $prefix . 'test_title',
				'type' => 'title',
			),
			array(
				'name'    => __( 'Background Color', 'closify-press' ),
				'desc'    => __( 'Menu Item background color', 'closify-press' ),
				'id'      => $prefix . 'menu_background_color',
				'type'    => 'colorpicker',
				'default' => '#595858'
			),
            array(
				'name'    => __( 'Menu Item color', 'closify-press' ),
				'desc'    => __( 'Menu Item color', 'closify-press' ),
				'id'      => $prefix . 'menu_text_color',
				'type'    => 'colorpicker',
				'default' => '#fff'
			),
            array(
				'name'    => __( 'Background color', 'closify-press' ),
				'desc'    => __( 'Shadow box background color', 'closify-press' ),
				'id'      => $prefix . 'shadow_background_color',
				'type'    => 'colorpicker',
				'default' => '#000'
			),
            array(
				'name'    => __( "Container's shape", 'closify-press' ),
				'desc'    => __( 'Choose the shape of the container', 'closify-press' ),
				'id'      => $prefix . 'shape',
				'type'    => 'radio',
				'options' => array(
                    array('name' => __( 'Rectangular', 'closify-press' ),'checked' => 'true'),
                    array('name'   => __( 'Circular', 'closify-press' )),
                ),
                ),
			array(
				'name'    => __( 'Disable rounded corner', 'closify-press' ),
				'desc'    => __( 'Works only when you enable cricular container.', 'closify-press' ),
				'id'      => $prefix . 'corner_multi_selection',
				'type'    => 'multicheck',
				'options' => array(
					'tl' => __( 'Top left corner', 'closify-press' ),
                    'tr' => __( 'Top right corner', 'closify-press' ),
					'br' => __( 'Bottom right corner', 'closify-press' ),
					'bl' => __( 'Bottom left corner', 'closify-press' ),
				),
				// 'inline'  => true, // Toggles display to inline
			),
            array(
				'name' => __( 'Output target', 'closify-press' ),
				'desc' => __( 'Relocate ouput message position.', 'closify-press' ),
				'id'   => $prefix . 'output',
				'type' => 'title',
			),
            array(
				'name'       => __( 'Output Target', 'closify-press' ),
				'desc'       => __( 'For class name add "." letter prefix. For ID targeting put "#" letter', 'closify-press' ),
				'id'         => $prefix . 'target_output',
				'type'       => 'text',
                'attributes'  => array(
                    'placeholder' => '#output-name',
                ),
			),array(
				'name'       => __( 'File Title placeholder', 'closify-uploader' ),
				'desc'       => __( 'Customize title placeholder', 'closify-uploader' ),
				'id'         => $prefix . 'title_placeholder',
				'type'       => 'text',
                'attributes'  => array(
                    'placeholder' => 'Enter file title...',
                ),
			),
            array(
				'name'       => __( 'File Description placeholder', 'closify-uploader' ),
				'desc'       => __( 'Customize description placeholder', 'closify-uploader' ),
				'id'         => $prefix . 'desc_placeholder',
				'type'       => 'text',
                'attributes'  => array(
                    'placeholder' => 'Enter file description...',
                ),
			),
		),
	);
	// Add other metaboxes as needed

	return $meta_boxes;
}

add_action( 'init', 'closify_init_meta_boxes', 9999 );
/**
 * Initialize the metabox class.
 */
function closify_init_meta_boxes() {

	if ( ! class_exists( 'Closify_Meta_Box' ) )
		require_once 'init.php';

}
