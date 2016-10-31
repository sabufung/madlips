=== Plugin Name ===
Contributors: daiki.suganuma
Tags: facebook, facebook pages, facebook graph api, album, photos
Requires at least: 4.0
Tested up to: 4.2.1
Stable tag: 3.0.0
License: Apache License Version 2.0
License URI: http://www.apache.org/licenses/LICENSE-2.0

Get the all albums from Facebook Page by using Facebook Graph API.

== Description ==

"Facebook Page Albums" get albums/photos from your Facebook Page through <a href="https://developers.facebook.com/docs/reference/api/">Facebook Graph API</a>.

This version provide only a few functions for your Theme, you have to develop the gallery page by yourself using html and javascript.

Once set up the API key and Facebook Page's address in admin panel, 
you can call **`facebook_page_albums_get_album_list`** or **`facebook_page_albums_get_photo_list`** function to get album/photo list.

Or you just copy 'themes/example' in your themes folder, and activate in admin panel. you will see album list and photo list of your facebook page.

This plugin is using <a href="https://developers.facebook.com/docs/php/gettingstarted/">Facebook PHP SDK</a>.
This SDK PHP 5.4 or greater.


### Demo ###

"<a href="http://www.jka.sg/gallery/">Gallery | JKA Singapore</a>" is using this plugin.

== Installation ==

1. Upload `facebook-page-albums` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in admin panel.
3. Go to 'Facebook Page Albums' under Settings menu, and set up for API. you can get the API key from <a href="https://developers.facebook.com/apps">Facebook Developers</a>
4. Copy 'themes/example' to your theme and activate in admin panel. You will see album list of your Facebook Page.
5. Develop your theme using PHP.

== Screenshots ==

1. **Setting** - you can set up the API key and Facebook page address.
2. **Album List** - `facebook_page_albums_get_album_list` function provide the album list.
3. **Photo List** - `facebook_page_albums_get_photo_list` function provide the photo list.

== Frequently Asked Questions ==

= I did activate and set up the Configuration. What's next? =

You have to develop PHP in your theme. 
Please see 'themes/example/index.php', this is very simple sample code.
Or you just copy 'themes/example' in your themes folder, and activate in admin panel. you will see album list and photo list of facebook page.

= I don't know PHP, HTML, JavaScript =

If a lots of request came to me, I will develop the feature providing html and javascript gallery for theme.

== Changelog ==

= 1.0.0 =
* First release.

= 1.0.1 =
* Fixed paging bug.

= 1.1.0 =
* Update Facebook PHP SDK v3.2.2
* Add 'Album Cache' option on setting page

= 1.1.1 =
* Add Example Theme

= 2.0.0 =
* Update Facebook PHP SDK v4.0
* Update example theme

= 3.0.0 =
* Remove original cache system using wp_options.
* Fixed functions for paging album list
* Update example theme.

