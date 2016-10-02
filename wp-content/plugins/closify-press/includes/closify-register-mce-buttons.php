<?php


// add new buttons
add_filter('mce_buttons', 'myplugin_register_buttons');

function myplugin_register_buttons($buttons) {
   array_push($buttons, 'separator', 'closify');
   return $buttons;
}
 
// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
add_filter('mce_external_plugins', 'closify_register_tinymce_javascript');

function closify_register_tinymce_javascript($plugin_array) {
   $plugin_array['closify'] = plugins_url( 'js/plugins/closify/plugin.js' , __FILE__ );
   return $plugin_array;
}

?>
