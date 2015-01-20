=== EDD Hide Download ===
Contributors: sumobi, alex.i
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=EFUPMPEZPGW7L
Tags: easy digital downloads, digital downloads, e-downloads, edd, hide, e-commerce, ecommerce, hidden, sumobi
Requires at least: 3.3
Tested up to: 4.1
Stable tag: 1.2.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows a download to be hidden as well as preventing direct access to the download

== Description ==

This plugin requires [Easy Digital Downloads](http://wordpress.org/extend/plugins/easy-digital-downloads/ "Easy Digital Downloads"). 

It allows you to:

1. Hide a download so it doesn't appear on the custom post type archive page, anywhere where the [downloads] shortcode is being used, or any custom query on a page template
1. Prevent direct access to the download. The browser will redirect the user to the site's homepage.
1. Do a combination of hiding the download and preventing direct access to it

This plugin is extremely useful in the following situations:

1. You've created a product landing page and inserted a buy now button to your product. Since the landing page contains all the required product information, you can hide the product on the rest of your site and even prevent direct access to it.
1. You've added a product (eg support package) that shouldn't sit with your other products you have listed. In this case we can simply hide it from appearing with the other products and insert it where we'd like it to appear using the shortcode. 

**Filter example**

Example filter of how you can change the redirect based on the download ID. Copy this function to your child theme's functions.php or custom plugin

    function sumobi_custom_edd_hide_download_redirect( $url ) {
    	// download has ID of 17
		if ( '17' == get_the_ID() ) {
			$url = 'http://easydigitaldownloads.com'; // redirect user to another external URL
		}
		
		// download has ID of 15
		if( '15' == get_the_ID() ) {
			$url = get_permalink( '8' ); // redirect to another download which has an ID of 8
		}
		
		// return our new URL
		return $url;
	}
	add_filter( 'edd_hide_download_redirect', 'sumobi_custom_edd_hide_download_redirect' );

Example filter of how you can globally change the redirect. Copy this function to your child theme's functions.php or custom plugin

    function sumobi_custom_edd_hide_download_redirect_url( $url ) {
		$url = get_permalink( '8' ); // redirect to another download, post or page
		
		return $url;
	}
	add_filter( 'edd_hide_download_redirect', 'sumobi_custom_edd_hide_download_redirect' );

**Extensions for Easy Digital Downloads**

[https://easydigitaldownloads.com/extensions/](https://easydigitaldownloads.com/extensions/?ref=166 "Plugins for Easy Digital Downloads")

**Tips for Easy Digital Downloads**

[http://sumobi.com/blog](http://sumobi.com/blog "Tips for Easy Digital Downloads")

**Stay up to date**

*Follow me on Twitter* 
[http://twitter.com/sumobi_](http://twitter.com/sumobi_ "Twitter")

*Become a fan on Facebook* 
[http://www.facebook.com/sumobicom](http://www.facebook.com/sumobicom "Facebook")


== Installation ==

1. Unpack the entire contents of this plugin zip file into your `wp-content/plugins/` folder locally
1. Upload to your site
1. Navigate to `wp-admin/plugins.php` on your site (your WP Admin plugin page)
1. Activate this plugin

OR you can just install it with WordPress by going to Plugins >> Add New >> and type this plugin's name

After activation, a new "Hide Download" section will appear at the bottom of Easy Digital Download's Download Configuration metabox

== Screenshots ==

1. The new options added to the bottom of Easy Digital Download's Download Configuration metabox


== Upgrade Notice ==

= 1.2.6 =
New: Compability with the Front End Submissions extension. When a download is hidden it will remain visible on the vendor's dashboard product page

== Changelog ==

= 1.2.6 =
* New: Compability with the Front End Submissions extension. When a download is hidden it will remain visible on the vendor's dashboard product page

= 1.2.5 =
* Fix: Plugin became deactivated when EDD was updated 

= 1.2.4 =
* Fix: Hidden downloads not being hidden properly on some pages such as the custom post type archive pages

= 1.2.3 =
* Fix: Forums not being shown in bbPress
* Tweak: Moved the plugin's options to EDD's "download settings" metabox

= 1.2.2 =
* Fix: Fatal error when bbPress was not active. Added check for existance of bbPress. 

= 1.2.1 =
* Fix: Compatibility with bbPress - props @nphaskins‎

= 1.2 =
* Fix: array merge for post__in - props @StephenCronin
* New: activation check for EDD
* Tweak: Improved localization function

= 1.1.5 =
* New: edd_hide_download_redirect filter for changing the redirection URL for downloads that have "Disable direct access to this download" enabled. Can also change the URL on a per download level
* Fix: "Disable direct access to this download" will now prevent direct access to the download, even when "Hide this download" is off

= 1.1.4 =
* Fix: compatibility issue between EDD Tickets and EDD Hide Download that was causing downloads to not be hidden


= 1.1.3 =
* Fix: Fixed bug where some downloads were not showing

= 1.1.2 =
* Fix: Fixed downloads not showing when using page templates

= 1.1.1 =
* New: Now hides downloads in page templates using custom queries
* Tweak: Modified text in configuration options
* Tweak: Updated .pot file with modified text strings

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