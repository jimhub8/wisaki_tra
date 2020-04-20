=== YITH Stripe Connect for WooCommerce ===

Contributors: yithemes
Tags: Stripe, Stripe Connect, commissions, e-commerce, WooCommerce, payments, yit, yith, yithemes
Requires at least: 4.7
Tested up to: 5.1.1
Stable tag: 1.1.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html/

Automatized payments with Stripe Connect.

== Description ==

Automatized payments with Stripe Connect.


= Features: =

== Installation ==

**Important**: First of all, you have to download and activate [WooCommerce](https://wordpress.org/plugins/woocommerce) plugin, because without it YITH Event Ticket for WooCommerce cannot work.

1. Unzip the downloaded zip file.
2. Upload the plugin folder into the `wp-content/plugins/` directory of your WordPress site.
3. Activate `YITH Stripe Connect for WooCommerce` from Plugins page.

= 1.1.7 - Released: May, 30 - 2019 =

* Tweak: add no cache headers
* Tweak: improve how to get the CSV file
* Tweak: added ignore_user_abort
* Update: internal plugin framework
* Update: Updated .pot
* Fix: preventing notice when user_id not found in the receiver array
* Fix: warning when creating export CSV
* Fix: Fixed users could get all commissions in csv and pdf files
* Fix: Prevent error of insufficient funds of a card
* Fix: Removed undefined method add_block that generated fatal error with subscription item
* Fix: Fixed subscription renew orders payment issue
* Dev: Added wc-credit-card-form among yith-stripe-connect-js script dependencies, to be sure that it is always loaded at checkout
* Dev: Added new filters 'yith_wcsc_prepare_columns_list' and 'yith_wcsc_prepare_rows_list'

= 1.1.6 - Released: Apr, 17 - 2019 =

* Fix: js error preventing card submission

= 1.1.5 - Released: Apr, 17 - 2019 =

* New: WooCommerce 3.6 support
* Tweak: removed unused fonts from MPDF library
* Update: internal plugin framework
* Fix: retrieve subscriptions from session when needed
* Fix: js error at checkout possibly causing payment failure
* Dev: added filters yith_wcsc_add_tax_to_commission and yith_wcsc_order_total_with_tax
* Dev: added filter yith_wcsc_account_menu_item

= 1.1.4 - Released: Feb, 19 - 2019 =

* Update: Updated plugin FW
* Update: Updated Stripe-PHP lib to revision 6.27
* Update: Updated Dutch translation
* Dev: new filter yith_wcstripe_connect_metadata

= 1.1.3 - Released: Dec, 31 - 2018 =

* New: Support WordPress 5.0.2
* Tweak: Allow payments with source when customer already registered one previously
* Update: Updated plugin FW
* Update: Updated Dutch language
* Update: Updated .pot
* Fix: fixed issue with subscriptions
* Fix: Fixed subscription processing with new card
* Fix: Fixed issue with new sources, when purchasing non subscribed products

= 1.1.2 - Released: Oct, 25 - 2018 =

* New: WooCommerce 3.5 support
* Tweak: updated plugin framework

= 1.1.1 - Released: Oct, 15 - 2018 =

* New: WooCommerce 3.5-rc support
* New: WordPress 4.9.8 support
* Tweak: updated plugin framework
* Update: Italian language
* Update: Dutch language
* Fix: some warning and notice if $order doesn't exist
* Fix: name of american express logo file
* Fix: gateway now works on page "pay order"
* Fix: minified js files
* Dev: added filter yith_wc_stripe_connect_credit_cards_logos_width

= 1.1.0 Released: Jul, 30 - 2018 =
* New: Integration with YITH WooCommerce Subscription Premium from v 1.4.6
* Update: Language files
* Update: plugin framework to latest revision

= 1.0.6 Released: Jun, 12 - 2018 =

* Dev: yith_wcsc_process_product_commissions to check if process the current product or not
* Dev: yith_wcsc_process_order_commissions to check if process the current order or not

= 1.0.5 Released: Jun, 11 - 2018 =

* New French translation (thanks to Josselyn Jayant)
* Fix: Commissions with notes above 320 characters are not saved
* Fix: Prevent fatal error on unserialize function

= 1.0.4 Released: Jun, 04 - 2018 =

* Update: Spanish language

* New: YITH WooCommerce Multi Vendor (3.0.0 +) support (admin can now pay vendors' commissions using Stripe Connect)
* Dev: added yith_wcsc_payment_complete action to add charge_id in stripe transfers

= 1.0.3 Released: May, 28 - 2018 =

New: WooCommerce 3.4 compatibility
New: WordPress 4.9.6 compatibility
New: GDPR compliance
New: Spanish language
New: Italian language
New: Dutch language
New: added option to show Name on Card field at checkout
Tweak: now gateway works on pay page too
Tweak: added transfer group to charges
Update: plugin framework to latest revision
Dev: added filter 'yith_wcsc_schedule_timestamp_change_format'
Dev: added filter 'yith_wc_stripe_connect_credit_cards_logos'
Dev: added filter 'yith_wcsc_connect_account_template_args' to let third party code filter the connect template args
Dev: added filter 'yith_wcsc_account_page_script_data' to let third party code filter data in localize scripts for disconnection
Dev: added filter 'yith_wcsc_order_button_text'

= 1.0.2 Released: Jan 31, 2018 =

New: Support to YITH Plugin Framework 3.0.11
Fix: Redirect URI messages.
Fix: Backbone modal window, now can display for all templates.

= 1.0.1 Released: Jan 30, 2018 =

Fix: Issue with Endpoint.
New: Support to WooCommerce 3.3.0 RC2

= 1.0.0 Released: Jan 29, 2018 =

* Initial release

