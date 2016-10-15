=== WC Fields Factory ===
Contributors: mycholan
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=U3ENPZS5CYMH4
Tags: wc fields factory, custom product fields, customize woocommerce product page, add custom fields to woocommerce product page, custom fields validations, custom fields grouping, 
Requires at least: 3.5
Tested up to: 4.6
Stable tag: 1.3.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Sell your products with customized, personalised options. Add custom fields or fields group to your products, your admin screens and customize everything.


== Description ==

Allows you to customize your products by adding custom fields or fields group. You can add custom fields and validations without tweaking any of your theme's code & templates, It also allows you to group the fields and add them to particular products or for product categories.

Add File uploads, Image uplods, Date picker, Color picker, Text box, Number, Email, Hidden, Select, Check box groups, Radio button groups and Label fields to your product page.

You can also add custom fields to your back end admin product category, product and product tabs ( General, Inventory, Shipping, Attributes, Variable, Related, Advanced ... ) as well.

= How it Works =
* Create a fields group
* Add fields to the group
* Assign fields group to whatever products or products category you want

= Features =
* Powerful interface to create your custom fields.
* Client side validation and custom error messages.
* Grouping custom fields.
* Assign groups to particular product or product categories.
* Cloning custom fields ( fields per product quantity ).
* Automatically embeds custom fields meta into cart, checkout, order and email.
* Powerful APIs to customize and extend your products.

= Documentation =
* [Product Fields](https://sarkware.com/wc-fields-factory-a-wordpress-plugin-to-add-custom-fields-to-woocommerce-product-page/)
* [Admin Fields](https://sarkware.com/add-custom-fields-woocommerce-admin-products-admin-product-category-admin-product-tabs-using-wc-fields-factory/)
* [WC Fields Factory APIs](https://sarkware.com/wc-fields-factory-api/)
* [Overriding Product Prices](http://sarkware.com/woocommerce-change-product-price-dynamically-while-adding-to-cart-without-using-plugins/#override-price-wc-fields-factory)
* [Customize Rendering Behavior](http://sarkware.com/how-to-change-wc-fields-factory-custom-product-fields-rendering-behavior/)


== Installation ==
1. Ensure you have latest version of WooCommerce plugin installed ( 2.2 or above )
2. Unzip and upload contents of the plugin to your /wp-content/plugins/ directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Use the "Add New" button from "Fields Factory" menu in the wp-admin to create custom fields for woocommerce product page

== Screenshots ==
1. Wccpf product custom fields list
2. Wccpf fields factory
3. Wccpf rules
4. Wccpf settings

== Changelog ==

= 1.3.5 =
* File upload validation issue fixed
* New field ( Image Upload ) has been added ( available only for Admin Fields )
* Now you can display your custom fields under Product Tab ( New Product Tab will be created, you have to enable it via WCFF Settings Screen )
* Single & Double quotes escaping problem fix ( on Fields Label )
* Year range option has been added for Date Picker ( '-50:+0',-100:+100 or absolute 1985:2065 )
* Date picker default language added ( English/US )
* Variable product Admin Fields saving issue fix
* Client side validation on blur settings added ( now you can specify whether the validation done on on submit or on field out focus )
* Show fields group title on Front End ( Post Title ( Fields group ) will be displayed )
* Number field validation Reg Exp fix ( Client Side )
* WCFF option access has been centralized ( now you can add 'wcff_options' filter to update options before it reaches to WCFF )
* Woocommerce ( If it is not activated yet ) not found alert added ( It's funny that I didn't checked this far, but this plugin will work even without woocommerce but there won't be much use then )
* Overly mask will be displayed while trying to edit or remove fields meta ( on wp-admin screen )

= 1.3.4 =
* Default color option for Color Field
* Admin Select field shows wrong value on Product Front End page issue fixed
* i18n support for Field's Label ( now you can create fields on Arabic, Chinese, korean .... ) 

= 1.3.3 =
* Validation error fix for Admin Field ( "this field can't be empty" is shown )

= 1.3.2 =
* fix for : Undefined variable ( Trying to get property of non-object ): product in /wc-fields-factory/classes/wcff-product-form.php on line 247

= 1.3.1 =
* Product rules error fixed
* Datepicker on chinese language issue fixed
* Checkout order review table heading spell mistakes fixed
* Rendering admin fields on product front end support added ( By default it's not, you will have to enable the option for each fields - for product page, cart & checkout page and order meta )
* Fields location not supported fix ( now you can use 'woocommerce_before_add_to_cart_form', 'woocommerce_after_add_to_cart_form', 'woocommerce_before_single_product_summary', 'woocommerce_after_single_product_summary' and 'woocommerce_single_product_summary' )

= 1.3.0 =
* Fields update issue fixed.
* File validation issue ( Fatal error: Call to undefined function finfo_open() ) fixed.

= 1.2.9 =
* Admin fields validation ( for mandatory ) added.
* File types server side validation - fixed.
* Validation $passed var usage - added.
* wccpf_unique_key conditional - removed ( as it no longer needed ).
* Time picker option added.
* Localization ( multi language support ) for datepicker added.
* Show dropdowns for month and year - datepicker.
* Uncaught ReferenceError: wcff_fields_cloning is not defined - fixed.
* Enque script without protocol ( caused issue over https ) - fixed.
* Show & hide on cart & checkoput pge option added for hidden field
* from V1.2.9, we are using Fileinfo module to validate file uploads ( using their mime types )
  PHP 5.3.0 and later have Fileinfo built in, but on Windows you must enable it manually in your php.ini


= 1.2.8 =
* "Display on Cart & Checkout" option on Setting page - issue fixed.

= 1.2.7 =
* Check box field's choice option not updated - issue fixed.

= 1.2.6 =
* Product rules broken issue fixed. 

= 1.2.5 =
* Two new fields has been added. Label ( you can now display custom message on product page ) & Hidden fields
* Client side validation included ( by default it's disabled, you will have to enable it through settings pags )
* Validation error message for each field, will be shown at the bottom of each fields.
* wccaf post type introduced ( custom fields for backend admin prducts section )
* Now you can add custom fields for back end as well ( on Product Data tabs, like you can add extra fields on general, inventory, shipping, variables, attributes tabs too )
* Multi file uploads support added ( for file field )
* Support for rules by tags & rules by product types added
* Order Item Meta visibility option added
* Datepicker disable dates issue solved
* Fields cancel button issue ( on the edit screen ) solved
* "Allowed File Types" in the File field, you will have to prefix DOT for all extensions 
* Entire plugin code has been re structured, proper namespace added for all files & classes, more comments added

= 1.2.4 =
* Fix for "Fields Group Title showing on all products since the V1.2.3"
* Wrapper added for each field groups

= 1.2.3 =
* Multiple colour pickers issue fix
* wccpf_init_color_pickers undefined issue fix
* Group title index will be hidden if product count is 1
* Minimum product quantity issue fix
* File type validation issue fix
* "Zero fields message" while deleting custom fields ( on wp-admin )

= 1.2.2 =
* Fields cloning option added ( Fields per count, If customer increase product count custom fields also cloned )
* Visibility of custom meta can be set ( show or hide on cart & checkout page )

* Setting page added
* Visibility Option - you can set custom data visibility globally ( applicable for all custom fields - created by this plugin )
* Field Location - you can specifiy where the custom fields should be included.
* Enable or Disbale - fields cloning option.
* Grouping the meta on cart & checkout page, option added.
* Grouping custom fields on cart & checkout page, option added.
* Set label for fields group
* Option to disable past or future dates
* Option to disbale particular week days
* Read only option added for Datepicker textbox ( usefull for mobile view )
* heigher value z-index applied for datepickers
* Pallete option added to color picker
* Option to show only palette or along with color picker
* Color format option added

= 1.2.1 =
* Add to cart validation issue fixed

= 1.2.0 =
* Woocommerce 2.4.X compatible 
* File upload field type added
* Internationalization ( i18n ) support added

= 1.1.6 =
* fixed "Missing argument" error log warning message

= 1.1.5 =
* Select field with variable product - issue fixed
* Order conflict while updating fields - issue fixed
* Newline character ( for select, checkbox and radio ) - issue fixed

= 1.1.4 =
* utf-8 encoding issue fixed
* Internationalization support.

= 1.1.3 =
* Order meta ( as well as email ) not added Issue fixed  

= 1.1.2 =
* Removed unnecessary hooks ( 'woocommerce_add_to_cart', 'woocommerce_cart_item_name' and 'woocommerce_checkout_cart_item_quantity' ) 
  yes they no longer required.
* Now custom fields data has been saved in session through 'woocommerce_add_cart_item_data' hook
* Custom fields rendered on cart & checkout page using 'woocommerce_get_item_data' ( actually rendered via 'cart-item-data.php' template )

= 1.1.1 =
* Color picker field type added

= 1.1.0 =
* Date picker field type added

= 1.0.4 =
* Validation issue fixed.
* Issue fixed ( warning log for each non mandatory custom fields ).
* Some css changes ( only class name ) to avoid collision with Bootstrap. 

= 1.0.3 =
* Hiding empty fields from cart table, checkout order review table and order meta.

= 1.0.2 =
* Issue fixing with "ACF" meta key namespace collition. 

= 1.0.1 =
* "wccpf/before/field/rendering" and "wccpf/after/field/rendering" actions has been added to customize wccpf fields rendering

= 1.0.0 =
* First Public Release.