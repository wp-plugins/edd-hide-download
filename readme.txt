=== EDD Hide Download ===
Contributors: sumobi, alex.i
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=EFUPMPEZPGW7L
Tags: easy digital downloads, digital downloads, e-downloads, edd, hide, e-commerce, ecommerce, hidden, sumobi
Requires at least: 3.3
Tested up to: 3.6
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Prevents a download appearing on the custom post type archive page or [downloads] listing.

== Description ==

This plugin requires [Easy Digital Downloads](http://wordpress.org/extend/plugins/easy-digital-downloads/ "Easy Digital Downloads"). 

It allows you to:

1. Hide a download so it doesn't appear on the custom post type archive page or anywhere where the [downloads] shortcode is being used.
1. Prevent direct access to the download. The browser will redirec the user to the site's homepage.

This plugin is extremely useful in the following situations:

1. You've created a product landing page and inserted a buy now button to your product. Since the landing page contains all the required product information, you can hide the product on the rest of your site and even prevent direct access to it.
1. You've added a product (eg support package) that shouldn't sit with your other products you have listed. In this case we can simply hide it from appearing with the other products and insert it where we'd like it to appear using the shortcode. 

**Looking for the perfect Easy Digital Downloads theme?**

[http://wordpress.org/themes/shop-front/](http://wordpress.org/themes/shop-front/ "Shop Front")

Shop Front is a simple, responsive & easily extensible theme for the Easy Digital Downloads plugin. It also functions perfectly without the plugin as a standard WordPress blog. A free child theme for modifications can be downloaded from [http://sumobi.com/shop/shop-front-child-theme](http://sumobi.com/shop/shop-front-child-theme "Shop Front Child Theme") as well as other free and paid add-ons to enhance the functionality and styling.

**Stay up to date**

*Subscribe to updates* 
[http://sumobi.com](http://sumobi.com "Sumobi")

*Become a fan on Facebook* 
[http://www.facebook.com/sumobicom](http://www.facebook.com/sumobicom "Facebook")

*Follow me on Twitter* 
[http://twitter.com/sumobi_](http://twitter.com/sumobi_ "Twitter")

== Installation ==

1. Unpack the entire contents of this plugin zip file into your `wp-content/plugins/` folder locally
1. Upload to your site
1. Navigate to `wp-admin/plugins.php` on your site (your WP Admin plugin page)
1. Activate this plugin

OR you can just install it with WordPress by going to Plugins >> Add New >> and type this plugin's name

After activation, a new "Hide Download" section will appear at the bottom of Easy Digital Download's Download Configuration metabox

== Frequently Asked Questions ==

= Where is my download hidden? =

Your download will be hidden on the Custom Post Type Archive page and anywhere where you have used the [downloads] shortcode to display your downloads.

= Where is the Custom Post Type Archive page? =

By default, Easy Digital Downloads turns on the custom post type archive page where all your downloads are listed. This page can be found by appending the following onto your website URL

/downloads

= I'm using a custom page template, my download is not hidden =

This plugin only hides the download from the Custom Post Type Archive page and anywhere where the [downloads] shortcode has been used. 

If you would like to hide it from your custom page template you will need to modify your WP_Query to exclude the hidden downloads. Your query might look like the following:

    $exclude_posts = class_exists( 'EDD_Hide_Download' ) ? $EDD_Hide_Download->get_hidden_downloads() : '';
    
    $args = array(
        'post_type' => 'download',
        'post__not_in' => $exclude_posts // this is an array of IDs passed in from the plugin
    );

    $downloads = new WP_Query( $args );

== Screenshots ==

1. The new options added to the bottom of Easy Digital Download's Download Configuration metabox


== Upgrade Notice ==

= 1.1 =
You can now prevent direct access to a download

== Changelog ==

= 1.1 =
* New: Option for disabling direct access to the download. User will be redirected to homepage. Props alex.i
* Tweak: Updated .pot file with new localized text strings

= 1.0.1 =
* Tweak: Moved to bottom of download configuration metabox and added a title.
* Tweak: Removed unused constants function
* New: edd_hide_download_label filter for changing the checkbox label
* New: edd_hide_download_header filter for changing the title text

= 1.0 =
* Initial release