=== Age Gate ===
Contributors: philsbury
Tags: age, age verification, age gate, adult, age restriction, age verify, adults-only, modal, over 16, over 18, over 19, over 20, over 21, pop-up, popup, restrict, splash, beer, alcohol, restriction
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=donate%40wordpressagegate%2ecom&lc=GB&item_name=Age%20Gate&item_number=Age%20Gate%20Donation&no_note=0&currency_code=GBP&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest
Requires at least: 5.0.0
Requires PHP: 5.6
Tested up to: 5.8
Stable tag: 2.17.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin to check the age of a visitor before view site or specified content

== Description ==
There are many uses for restricting content based on age, be it movie trailers, beer or other adult themes. This plugin allows you to set a restriction on what content can been seen or restricted based on the age of the user.

__Features__

* Ask users to verify their age on page load
* SEO Friendly - common bots and crawlers are omitted from age checks
* Ability to add custom user agents for less common bots
* Shortcode for in content restrictions
* Choose to restrict an entire site, or selected content
* Select a different age on individual content
* Allow certain content to not be age gated under "all content" mode
* Three choices for input; dropdowns, input fields or a simple yes/no button
* Customise the order of the inputs based on your region (DD MM YYYY or MM DD YYYY)
* Allow a "remember me" check box if desired
* Ability to omit logged in users from being checked
* Add your own logo
* Update the text displayed on the entry form
* Select background colour/image, foreground colour and text colour
* Use built in styling out of the box, or your own custom style
* Ability to add legal note or information to the bottom of the form
* Redirect failed logins to a URL of your choice e.g. an alcohol awareness website.
* Ability to use a non caching version
* Various hooks to add even more customisation such as additional form fields
* Compatible with multilingual plugins WPML, Polylang (2.3+), WP Multilang

== Installation ==
1. Upload the 'age-gate' folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Visit 'Age Gate' admin section and adjust your configuration.

__Important__
Be sure to check the 'Access' settings tab to grant permissions and omit any post types you don't wish to display Age Gate options on.

== Frequently Asked Questions ==
= I can't get past the Age Gate / The Age Gate only worked once =

The most likely cause for this is caching on your site either via a plugin or your hosting provider. If the Age Gate appears stuck try using the JavaScript mode in the advanced tab and clear any caches.

= Can I restrict a particular page? =

You can. If you use selected content, a checkbox will appear in content pages

= Can I add my own logo/branding? =

Of course, it's your site

= I'm in X country, can I format the date style? =

Yes! DD MM YYYY and MM DD YYYY are supported along with a choice of how the dates are entered.

= I use caching, will that be affected? =

From version 1.4.0 those using caching can select the "Cache Bypass" option to allow age gating even with caching on. Be sure to empty your cache when making changes to the plugin settings. From 2.0.0 this option is in Advanced -> Use uncachable version

== Screenshots ==
1. An example of Age Gate
2. The Restrictions settings page
3. Age Gate has a variety of customisable message settings
4. The appearance can be tailored to fit your website
5. Advanced options include the use of a JavaScript Age Gate and a custom CSS editor
6. Manage what users can change Age Gate's setting, restrict posts and exclude settings from certain post types.

== Changelog ==

= 2.17.0 =
* Adds validation for correct dates. E.g 1990-02-31 will fail vaildation

= 2.16.6 =
* Fixed: Standard mode causing Divi to not load.
* Added: Javascript mode bypass on Divi.

= 2.16.5 =
* Fixed: p tags stripped from additional content
* Changed: Load admin assets only if required
* Changed: Default mode will now be JavaScript

= 2.16.4 =
* Added stricter filtering of additional content
* Added age_gate/presentation/allowed_tags filter for additional allowed tags

= 2.16.3 =
* Fixed potential XSS vulnerability in additional content field

= 2.16.2 =
* Fixed PHP notice in JavaScript implmentation

= 2.16.1 =
* Fixed notice about unset array item

= 2.16.0 =
* Adds option to output Javascript options not as object
* Fixed REST route warnings

= 2.15.0 =
* Removed unused nonce field in JS mode
* Added actions `age_gate/script_template/before` and `age_gate/script_template/after`
* Improved shortcode functionality with AJAX requests

= 2.14.0 =
* Fixed issue when using transitions for some users
* Improved bot detection in JavaScript mode
* Added cross subdomain cookie support in JS for multisite
* Added added support for RTA meta tag

= 2.13.6 =
* Fixed slashed sometimes appearing in WYSIWYG editors

= 2.13.5 =
* Fixed potential securtity threat
* Fixed issue in JS mode with some caching minification

= 2.13.4 =
* Bugfix for rechallenge in standard mode when using shortcode

= 2.13.3 =
* Bugfixes in Shortcode

= 2.13.2 =
* Added support for single digit entry on day/month inputs

= 2.13.1 =
* Fixed Viewport meta tag to only affect when Age Gate is shown

= 2.13.0 =
* Allow markdown in message fields

= 2.12.0 =
* Added filter for REST URL

= 2.11.0 =
* Adds simple test to auto detect Wordpress caching

= 2.10.2 =
* Fixed HTML rendering in Additional Content

= 2.10.1 =
* Fixed additional content display

= 2.10.0 =
* Added network activation support
* Fixed issue in multisite experienced but some users

= 2.9.0 =
* Added Age Gate as a Shortcode
* Updated admin area UI

= 2.8.0 = 
* Added some behind the scenes tweaks

= 2.7.2 = 
* Fixed auto tabbing not working

= 2.7.1 =
* Fixed event issue in IE11 introduced in 2.7.0

= 2.7.0 =
* Added additional internal actions/filters for Regional support
* Update JS event triggering

= 2.6.1 =
* Fixed users being able to resubmit in JS mode when the is a redirection and no rechallenge
* Fixed styling in JS mode for no-rechallenge error

= 2.6.0 =
* Added option to customise cookie name
* Added better compatibility with Elementor

= 2.5.3 =
* Fixed issue where taxonomy restriction was always returning true
* Updated age_gate_inherited filter to pass the taxonomies that restrict/bypass

= 2.5.2 =
* Fixed issue where range sliders outside of plugin were being styled

= 2.5.1 =
* Fixed a JavaScript bug for if the toolbar is shown but the AG toggle is not

= 2.5.0 =
* Added function for testing aga gate status (Standard Mode)
* Added class to body on error
* Fixed an issue where some users couldn't access customiser
* Fixed bug where ajax calls resolved to the wrong URL
* Fixed deprecation warning for get_magic_quotes_gpc
* Updated dependencies
* Prep for V3 rollout

= 2.4.0 =
* Added option to change order of yes/no buttons

= 2.3.6 =
* Bug fix for multilingual sites dropping links in "additional content" field

= 2.3.5 = 
* Removes Age Gate options from Publish actions in favour of a meta box

= 2.3.4 = 
* Pass `remember` to the age_gate_set_cookie filter

= 2.3.2 =
* Stop custom CSS from preventing save if the editor perceives errors

= 2.3.1 =
* Fix issue in Firefox where scrolling didn't work as expected
* Fix meta box for attachments not saving

= 2.3.0 =
* Inputs on mobile devices will now trigger numeric keyboard
* Adds option to post to current page in standard mode for better compatibility with masking plugins
* Improves ability to not set cookie in line with privacy regulations
* Adds some hooks to JavaScript (optional)
* Fixes bug with semi transparent backgrounds in JS mode

= 2.2.6 =
* Minor tweaks to how `age_gate_restricted` filter is applied in JavaScript mode
* Optionally pass query params to `age_gate_restricted` filter in JavaScript mode

= 2.2.5 =
* Fixes an issue in multilingual sites where the default language text wasn't displayed

= 2.2.4 =
* Fixes an issue in Safari (Desktop and Mobile) where the Age Gate wasn't displayed.

= 2.2.3 =
* Error messages are now passed to `age_gate_form_failed` action
* Stops ability to have duplicate custom bots
* Fixes an issue in IE when using a background image
* Adds options for background image positioning
* Fixes some PHP notices and warnings

= 2.2.2 =
* Adds Day, Month and Year labels to options
* In JS mode, ensures user is at the top of the page after success

= 2.2.1 =
* Adds support for WP Multilang plugin
* Fixes issue on page list where Age Gate column always showed
* Fixes a bug where pages in admin became inaccessible
* Fixes PHP warning appearing when new CPT is added

= 2.2.0 =
* Add custom user agents for bot testing
* Adds ability for content to inherit restriction setting from taxonomy
* Improves toolbar usage on the front end
* Fixes an issue in Polylang when not using a URL structure to determine language
* Fixes missing class for additional content
* Adds quick edit and bulk edit options
* Options for RTEs used in Age Gate settings
* Adds ability to import/export settings
* Improves Gutenberg detection

= 2.1.0 =
* Adds option to use REST API in JS mode instead of admin-ajax
* Adds fallback for themes that do not support `title-tag`
* Removes calculation factor that affected a small number of users
* Remove age validation on registration pages
* Improves auto tabbing between fields
* Improves CSS for smaller devices
* Transient purging now only happens in admin and if WP Cron is disabled
* In JavaScript mode you can now transition the Age Gate out when it is passed
* Huge multilingual improvements with support for WPML and Polylang
* Adds filters for custom cookie length
* Adds option to display post settings in a metabox for Gutenberg compatibility
* Fixes bug where logged in users were still age checked despite bypass setting
* Other minor fixes and improvements

= 2.0.6 =
* Critical fix for anyone on a standard timezone setting in Wordpress

= 2.0.5 =
* Age calculation now uses a timezone to hopefully help rare occasions were valid users are denied access
* Adds minimum year input which can be filtered with age_gate_select_years
* Other minor background changes

= 2.0.4 =
* Added 'age_gate_set_cookie' filter, useful if you want to gain consent before setting cookies
* Added "Anonymous Age Gate" that will only set a flag saying the Age Gate has been passed

= 2.0.3 =
* Fixes an issue where Age Gate removed buttons from TinyMCE
* Fixes an issue where Age Gate options wouldn't show for post types created after installation
* Deprecated: Restrict registration
* Improves support for multilingual sites
* Temporarily disabling Network activation
* Minor background changes

= 2.0.2 =
* Fixes issues with custom archive pages for WooCommerce and custom posts page
* Disables Age Gate in Customiser
* Adds notification to users if cookies are disabled

= 2.0.1 =
* Fixes issue where users could not register
* Fixes and issue where posts home would require Age Checking if the first post was restricted
* Minor CSS update for themes not using border-box
* Fixed missing closing form tag in admin

= 2.0.0 =
* Complete rewrite
* Age can be changed for individual content
* Tags/Categories can now be restricted independently
* Other archives can be independently restricted using filters
* Age gate form can be extended with additional fields
* Addition of various actions and filters
* Custom CSS editor
* Improved form validation and custom messages
* Ability to set which users can manage Age Gate settings
* Input fields can automatically tab to the next

= 2.0.0-beta5 =
* Fixes an issue where apostrophes were being escaped incorrectly in message fields
* Fixes an install issue with the access settings

= 2.0.0-beta4 =
* Fixes an issue where Age Gate options were not showing in page/post meta box
* Fixes issue where content was not being tested in selected content mode (pages/categories and WooCommerce taxonomies)
* Fixes page title switching
* Add custom page title field for when challenge is shown

= 2.0.0-beta3 =
* Fixes issue in Internet Explorer where Age Gate would stick in JS mode

= 2.0.0-beta2 =
* Fixes bug when trying to import new categories via Woocommerce import

= 2.0.0-beta1 =
* Admin style for opacity sliders
* Updated dev notices to only show for certain users
* Adds notice for new dev releases

= 2.0.0-alpha4 =
* Adds additional CSS classes to HTML.
* Minor CSS tweak

= 2.0.0-alpha3 =
* Adds event listener when JS Age Gate is passed.
* Various minor bugfixes

= 2.0.0-alpha2 =
* Minor change to age_gate_before and age_gate_after
* Added opacity settings for backgrounds


= 1.5.0 =
* Adds ability to define how long Remember Me lasts

= 1.4.13 =
* Fixes issue in Cache Bypass where strings were not translated
* Fixes issue in some Multisite installs where wp-admin was becoming invalid
* Fix for conflict with Jetpack and wp_editor function - to be remove when Jetpack fix is released

= 1.4.12 =
* Adds additional test to enable restriction of WooCommerce shop page

= 1.4.11 =
* Fixes issue with includes in admin area

= 1.4.10 =
* CSS change for users using large logos where the them doesn't use max-width: 100% by default
* Corrected README typo.

= 1.4.9 =
* Minor change to logo class from `logo` to `age-gate-logo`

= 1.4.8 =
* Updates to translation files
* Adds user preference to alter page title when Age Gate is displayed
* Added additional test for Bots in "Cache Bypass" version
* Minor text change on post editor

= 1.4.7 =
* Added Facebook and Twitter crawlers to the Bot ignore list
* Minor CSS changes
* Added CSS classes to Age Gate plus a guide in the admin
* Fixes issue where links in "Additional Content" could not be opened in a new window
* Fixes issue where links in "Additional Content" could not not have their text altered
* Fixes bug where adding a link to "Additional Content" also updated "Redirect Failures".

= 1.4.6 =
* Due to an issue when using WPeCommerce the trigger for the JS version has been altered - JS version will be selected by default
* When using Yes/No buttons, the question "Are you over (n) years of age?" is now optional/customisable.

= 1.4.5 =
* Fixes issue when using standard mode, selected restrictions and Woocommerce when the age check would not show on the product page

= 1.4.4 =
* Addresses an issue in some themes where default style misbehaves on smaller screens
* Fixes issue where plugin was causing inability to scroll in some themes
* Added a cheeky paypal button

= 1.4.3 =
* Added option to have the Remember Me option checked by default

= 1.4.2 =
* In Cache Bypass mode, session storage dropped in favour of cookies to support Private/Incognito sessions.
* Also in Cache Bypass, when the AG is successfully passed the page will refresh as some occurences of JS manipulated content were not working.

= 1.4.1 =
* Fixes a bug when using Cache Bypass mode but not using Remember me

= 1.4.0 =
* Caching support! Ability to use the age gate on cached sites
* Bugfix for uninstall not working (thanks to @nate22 for the heads up)
* Removed bad translations

= 1.3.5 =
* Addresses issue on mobile devices where rendering is a little small as raised by @fwusquare2com. Option to add viewport meta added.

= 1.3.4 =
* Fixes issue created in 1.3.2 where listing pages were not age gated even if "all content" was selected

= 1.3.3 =
* Bugfix for missing text domain for "remember me" text
* Updated translation files

= 1.3.2 =
* Bugfix for listing pages being age gated incorrectly (e.g. blog home and archives)

= 1.3.1 =
* Options added for custom error messaging
* Slight change to admin page layout

= 1.3.0 =
* Option to bypass particular content when using "All Content" setting. Useful to allow Terms pages etc.
* Option to redirect users to a custom destination if age test is failed

= 1.2.0 =
* Fixed issue where correct input was not honoured in some browsers

= 1.1.0 =
* Added "no second chance" option which disallows multiple attempts if failed.
* Minor background improvements

= 1.0.1 =
* Minor changes to default style
* Fixed typo in readme

= 1.0.0 =
* Initial release

== Upgrade Notice ==
= 2.16.3 =
* This release fixes a possible, though relatively unlikely, vulnerability to XSS.

= 2.13.5 =
* Fixed a potential security risk. Update recommended.

= 2.3.0 =
* Background colour now has it's own element `.age-gate-background-colour` rather that styling `.age-gate-wrapper`

= 2.2.4 =
* Fixes an issue in Safari (Desktop and Mobile) where the Age Gate wasn't displayed.

= 2.0.6 =
* Critical fix for anyone on a standard timezone setting in Wordpress

= 2.0.1 =
* Fixes issue where users could not register regardless of Age Gate settings
* Fixes and issue where posts home would require Age Checking if the first post was restricted
* Minor CSS update for themes not using border-box
* Fixed missing closing form tag in admin

= 2.0.0 =
* Completely reworked, this release should be considered a breaking change. Testing on local/staging environment is recommended.

= 1.4.8 =
* Adds user preference to alter page title when Age Gate is displayed
* Added additional test for Bots in "Cache Bypass" version

= 1.4.5 =
* Woocommerce users using "Selected content" should update to show age gate on the product page

= 1.4.1 =
* Fixes a bug when using Cache Bypass mode but not using Remember me

= 1.4.0 =
* Adds support for sites with caching enabled

= 1.2.0 =
* Contains vital fix for correct input not accepting some browsers
