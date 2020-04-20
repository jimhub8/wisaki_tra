=== YITH WooCommerce Recover Abandoned Cart  ===

Requires at least: 3.5.1
Tested up to: 4.9.8
Stable tag: 1.2.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

YITH WooCommerce Recover Abandoned Cart helps you manage easily and efficiently all the abandoned carts of your customers.

== Description ==
Your customers often fill their carts and leave them: thanks to YITH WooCommerce Recover Abandoned Cart you will be able to contact them and remind what they were purchasing and invite them to complete their action.
Set the time span to consider a cart abandoned and customize a contact email that you can send to your customer: a direct contact to make them see what they were ready to purchase!


== Installation ==
Important: First of all, you have to download and activate WooCommerce plugin, which is mandatory for YITH WooCommerce Recover Abandoned Cart to be working.

1. Unzip the downloaded zip file.
2. Upload the plugin folder into the `wp-content/plugins/` directory of your WordPress site.
3. Activate `YITH WooCommerce Recover Abandoned Cart` from Plugins page.


= Configuration =
YITH WooCommerce Recover Abandoned Cart will add a new tab called "Abandoned Carts" in "YIT Plugins" menu item.
There, you will find all YITH plugins with quick access to plugin setting page.


== Changelog ==
= 1.2.8 - Released: Oct 16, 2018  =
New: Support to WooCommerce 3.5 RC2
Update: Update Core Framework 3.0.25
Fix: Fix Guest cart issue

= 1.2.7 - Released: Sep 26, 2018  =
Update: Update Core Framework 3.0.23
Fix: Issue with YITH WooCommerce Deposits & Down Payments
Fix: Shop Manager Option

= 1.2.6 - Released: Aug 5, 2018  =
New: Support to WordPress 4.9.8
New: French translation (credits to Josselyn Jayant)
Tweak: Unsubscribe system
Tweak: Load css frontend only under condition
Tweak: Email content cart
Update: Update Core Framework 3.0.20
Update: Language files

= 1.2.5 - Released: May 25, 2018  =
Tweak: Support to GDPR compliance
Update: Localization files
Update: Update Core Framework 3.0.16
Fix: Show/hide privacy textarea option

= 1.2.4 - Released: May 24, 2018  =
Fix: Privacy message position

= 1.2.3 - Released: May 24, 2018  =
New: Support to WordPress 4.9.6
New: Support to WooCommerce 3.4.0
New: Support to GDPR compliance - Export personal data
New: Support to GDPR compliance - Erase personal data
Update: Update Core Framework 3.0.15
Update: Localization files
Tweak: Wait an hour from an order creation before a new cart of customer is registered
Fix: Check if a coupon exists before create a new one
Fix: Session cart when a cart is recovered
Fix: Aelia compatibility

= 1.2.2 - Release on Mar 29, 2018 =
Dev: Added filter 'ywrac_allow_current_user'
Fix: Multi currency Issue
Fix: Delete cart after that a customer completed an order
Fix: Default option value
Update: Plugin Core 3.0.13

= 1.2.1 - Release on Jan 30, 2018 =
New: Support to WooCommerce 3.3.0 RC2
Fix: Coupon creation
Fix: Email subject
Update: Plugin Core 3.0.10

= 1.2.0 - Release on Dec 21, 2017 =
Update: Plugin Core 3.0.1
Dev: Added filter 'ywrac_get_timestamp_with_gtm'

= 1.1.9 - Release on Nov 29, 2017 =
New: Added search by customer in Cart Abandoned and Pending Orders Tabs
New: Add the existent order pending in the main counter of Pending Orders when the plugin is activated for the first time
Dev: Added filter 'ywrac_recurrence'
Update: Localization files
Fix: Conflicts with Mandrill when an email is rejected
Fix: Multiple email sent for Pending Orders

= 1.1.8 - Release on Oct 30, 2017 =
New: Dutch translation
Update: Localization files
Fix: Thumbnails of variations in recover cart email

= 1.1.7 - Release on Oct 10, 2017 =
New: Support to WooCommerce 3.2
Update: Localization files
Update: Plugin Core
Fix: Emails not sent

= 1.1.6 - Release on Sept 14, 2017 =
New: Spanish translation (Fernando Tellado)
New: Italian translation (A.Mercurio)
Fix: _emails_sent meta when using PHP 7.1
Update: Plugin Core

= 1.1.5 - Release on Jul 03, 2017 =
New: Support to WooCommerce 3.1
Update: Plugin Core

= 1.1.4 - Release on Jun 08, 2017 =
New: Support to WooCommerce 3.0.8
Fix: WPML email issue
Fix: Image size on cart list
Dev: New filter ywrac_template_content
Update: Plugin Core

= 1.1.3 - Release on Apr 26, 2017 =
New: Support to WooCommerce 3.0.4
Tweak: Improved the query to send email
Tweak: Phone number catch for guest
Fix: Display of thumbnails in some email clients
Fix: Emails for pending orders
Update: Plugin Core

= 1.1.2 - Release on Apr 13, 2017 =
Fix: Delete cart after an order is submitted
Update: Plugin Core

= 1.1.1 - Release on Apr 06, 2017 =
New: Option to enable the shop manager capabilities to the plugin options
Update: Plugin Core


= 1.1.0 - Release on Mar 29, 2017 =
New: Pending order options
New: Support to WooCommerce 3.0 RC 2
Dev: Filter 'ywrac_recover_cart_link' to change the recover cart link
Fix: format email test
Fix: Shortocode list in the Email Template editor
Fix: Admin Ajax Url
Update: Plugin Core


= 1.0.7 - Released on Jun 10, 2016 =
Added: Compatibility with WooCommerce 2.6 RC1
Tweak: Encripted the url of cart
Fixed: Cart change status method

= 1.0.6 - Released on May 04, 2016 =
Fixed:  Compatibility with WooCommerce Currency Switcher

= 1.0.5 - Released on May 02, 2016 =
Added: Compatibility with Wordpress 4.5.1
Added: Compatibility with WooCommerce Currency Switcher
Tweak: List Table width in the administrator panel
Fixed: Compatibility with YITH WooCommerce Email Templates Premium
Fixed: Cancel Recover abandoned cart coupon after use

= 1.0.4 - Released on Dec 30, 2015 =
Added: Support to WooCommerce 2.5 beta 3
Fixed: Replaced time() with current_time() function
Fixed: Send manual email in the detail page of Abandoned Cart item

= 1.0.3 - Released on Dec 9, 2015 =
Added: Support to Wordpress 4.4
Fixed: fixed removing abandoned cart for guest
Updated: Changed Text Domain from 'ywrac' to 'yith-woocommerce-recover-abandoned-cart'
Updated: Plugin Core Framework

= 1.0.2 - Released on Dec 1, 2015 =
Added: Added phone number on cart abandoned
Updated: Changed Text Domain from 'ywrac' to 'yith-woocommerce-recover-abandoned-cart'
Updated: Plugin Core Framework
Fixed: Removing abandoned cart for guest
Fixed: Minor bugs


= 1.0.1 - Released on Aug 13, 2015 =
Fixed: Cookie check in checkout page
Fixed: Spaces in Email layout
Added: Support to WooCommerce 2.4.2
Updated: Plugin Core Framework

= 1.0.0 - Released: July 30, 2015 =
Initial release

== Suggestions ==
If you have any suggestions concerning how to improve YITH WooCommerce Recover Abandoned Cart, you can [write to us](mailto:plugins@yithemes.com "Your Inspiration Themes"), so that we can improve YITH WooCommerce Recover Abandoned Cart.
