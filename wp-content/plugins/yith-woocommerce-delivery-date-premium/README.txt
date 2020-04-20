=== YITH WooCommerce Delivery Date ===
Contributors: yithemes
Requires at least: 3.5.1
Tested up to: 5.1.1
Stable tag: 2.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Documentation: https://yithemes.com/docs-plugins/yith-woocommerce-delivery-date/
== Changelog ==

= 2.1.0 =

* New: Option to enable the plugin with "Quantity Table" mode
* New: Possibility to set different delivery days in carriers based on WooCommerce Shipping Zones
* New: Added "Quantity Table" tab to create different tables by categories and by product
* New: New style available thanks to the new plugin framework
* New: Possibility to upload a custom icon for the Dynamic Delivery Messages
* Update: Plugin Framework
* Update: Language files

= 2.0.4 =
* New: Delivery details can be changed on admin side
* New: Support to WordPress 5.2.x
* Update: Plugin Framework
* Update: Language file

= 2.0.3 =
* Fix: Order without delivery date
* Update: Plugin Framework

= 2.0.2 =
* New: Support to WooCommerce 3.6.2
* Update: Plugin Framework
* Fix: Fatal error with WPML
* Dev: Added filter yith_delivery_date_email_fields
= 2.0.1 =
* New: Support to WooCommerce 3.6.0 RC2
* New: Support to WordPress 5.1.1
* Update: Plugin framework
* Fix: Custom processing day on checkout page
= 2.0.0 =

* New: Option Dynamic Delivery Message to show a message with the delivery date to carrier and to customer on the single product
* New: Possibility to set the number of days required for processing based on the product/category quantity  (Custom Processing Day )
* New: Possibility to enable/disable each "Custom Processing day" rule
* New: Possibility to customize both slot and fee name for each Time slot created in carriers
* New: Possibility to enable/disable the single time slot
* New: Option to enable/disable and change every holiday inserted in the calendar
* New: New style
* New: Moved menus "Order Processing Method" and "Carrier" to the main panel and renamed them as "Processing Options" and "Carrier Options"
* New: Removed the "Custom Shipping Day"  tab, "processing day" management is available in the "Processing Options" tab
* New: Merged carriers management ( removed "Delivery" and "Delivery Time Slot" tabs ) and added Carrier Options tab
* Tweak: Improved script to show the datepicker on the checkout
* Tweak: Code optimization
* Update: delivery-date-content.php template
* Update: Plugin Framework
* Update: Language files

= 1.1.5 =
* Update: Plugin Framework
* Fix: Delivery field doesn't show on checkout page

= 1.1.4 =
 * New: Support to WooCommerce 3.5.4
 * Update: Plugin Framework
 * Update: Language file
 * Dev: Add filter ywcdd_change_carrier_label  to change the carrier label in the email
 * Dev: Add filter ywcdd_change_shipping_date_label to change the shipping date label in the email
 * Dev: Add filter ywcdd_change_delivery_date_label to change the delivery date label in the email
 * Dev: Add filter ywcdd_change_timeslot_label to change the time slot label in the email
 * Dev: Add filter ywcdd_show_date_shipping_details to hide the shipping date info in the email
 * Dev: Add filter ywcdd_custom_order_column_date_format to change the date format of shipping and delivery date on the order list
 * Fix: Sorting woocommerce orders by shipping and delivery date

= 1.1.3 =
* New: Option to set a label for Time Slot fee
* New: Option to set the Time Slot fee to taxable
* New: Event Calendar localization
* New: Compatibility with WordPress 5.0
* Dev: Add filter ywcdd_get_last_shipping_date
* Update: Plugin Framework
* Update: Language Files

= 1.1.2 =
* Update: Plugin Framework
* Update: Language files

= 1.1.1 =
* New: Support to WooCommerce 3.5
* Tweak: Delivery date and Shipping date calculation
* Dev: Add filter ywcdd_time_slot_fee_taxable to set the slot fee as taxable
* Dev: Add filter ywcdd_fee_tax_class to set the fee tax class
* Update: Plugin Framework
* Update: Portuguese language file (thanks to Ricardo Araújo)
* Fix: Minor bugs

= 1.1.0 =
* New: Option to choose the date format
* New: Plugin template
* New: Portuguese language file (thanks to Ricardo Araújo)
* New: Added placeholder on delivery date field
* Update: Language files
* Update: delivery-date-selected-date template
* Dev: Added filter ywcdd_set_first_available_date to hide the date in the datepicker
* Fix: Time slot not deleted in plugin option
* Fix: Time slot field didn't appear as required
* Fix: Subject email didn't display properly
= 1.0.21 =
* Fix: Delivery field doesn't show on checkout page
* Update: Plugin Framework
* Update: Spanish language
* Update: Italian language

= 1.0.20 =
* New: Support to WooCommerce 3.4.0
* New: Support to WordPress 4.9.6
* New: Support to GDPR compliance
* Dev: New filter ywcdd_is_invalid_time_slot
* Dev: New filter ywcdd_time_slot_fee_text
* Fix: Lockout timeslot
* Update: Language files
* Update: Plugin Framework

= 1.0.19 =
* New: Support to WooCommerce 3.3.0 RC2
* New: Support to WordPress 4.9.2
* Update: Plugin Framework
* Tweak: Checkout date validation

= 1.0.18 =
* New: Support to WooCommerce 3.2.5
* New: Support to WordPress 4.9.1
* New: Support to WooCommerce Tree Table Rate Shipping ( version 1.16.5 )
* Dev: Added ywcdd_get_all_timeslots filter , to change the timeslots
* Update: Plugin Framework 3.0.1

= 1.0.17 =
* New: Support to WooCommerce Distance Rate Shipping ( version 5.72 )
* New: Option to control time increments when setting the delivery time
* Update: Plugin Framework
* Update: Language File
* Fix: Holidays are not added to calendar

= 1.0.16 =

* New: Add compatibility with  Flexible Shipping ( version 1.9.7 )
* New: Support to WooCommerce 3.2.0-RC2
* New: Support to WordPress 4.8.2
* Update: Plugin Framework
* Fix:  Custom shipping day for variable products

= 1.0.15 =
* New: Added compatibility with WooCommerce FedEx Shipping plugin ( version 3.4.9 )
* Update: Plugin Framework

= 1.0.14 =
* New: Added yith_delivery_date_base_carrier_day filter,to change the carrier workdays
* Update: Plugin Framework
* Fix: Delivery field not available after updating checkout

= 1.0.13 =
* New: Added compatibility with WooCommerce Table Rate Shipping (version 3.0.2)
* Update: Plugin Framework

= 1.0.12 =
* Update: Plugin Framework
* Fix: Wrong language file name

= 1.0.11 =
* New: Support to WooCommerce 3.0.8
* New: Added Italian Language File
* Fix: Custom shipping day for categories and variable products
* Update: Plugin Framework

= 1.0.10 =
* New: Integration with YITH WooCommerce PDF Invoice and Shipping List Premium ( version 1.4.12 )
* Update: Language file
* Update: Plugin Framework

= 1.0.9 =
* New: Support to WooCommerce 3.0.4
* Dev: Added yith_delivery_date_base_shipping_day filter,to change the necessary workdays to process an order
* Dev: Added ywcdd_cut_off_time filter, to add a cut off time for timeslot
* Update: Plugin Framework
* Fix: Fatal error due to huge amount of post meta

= 1.0.8 =
* New: Support WooCommerce 2.7.0-RC1
* Update: Plugin Framework

= 1.0.7 =

* New: Spanish language file
* Update: Plugin Framework
* Fix: Custom shipping day doesn't calculate properly
* Fix: TimeSlot doesn't available on frontend
* Fix: Issue on display carriers and processing method in admin

= 1.0.6 =

* Fix: Delivery Date not available if custom time format is set
* Update: Plugin Framework

= 1.0.5=

* New: Option to choose how to display the datepicker in frontend
* New: Support to WordPress 4.7
* New: Support to WooCommerce 2.6.9
* Update: Plugin Framework
* Update: Language file
* Fix: Day not available if no timeslot can be selected in frontend
* Fix: Expired TimeSlot always shows in frontend
* Fix: TimeSlot isn't required even when date is required in frontend
* Fix: Select All / Clear All option doesn't work for custom carrier timeslot
* Fix: Only one holiday event was added to calendar for a specific day

= 1.0.4 =

* Fix: Shipping email without subject
* Fix: Email not sent for guest orders
* Update: Plugin Framework
* Update: Language File

= 1.0.3 =

* New: Option to add event to calendar based on order status
* New: Compatibility with YITH WooCommerce Multi Vendor
* Fix: Timezone issue
* Fix: Datepicker position
* Update: Language file
* Update: Plugin Framework

= 1.0.2 =

* Tweak: Improved performance for loading date in checkout page
* Fix: CSS issue with datepicker
* Update: Plugin Framework

= 1.0.1 =

* Fix: No date shown if you set only one shipping method.
* Update: Plugin Framework

= 1.0.0 =

Initial release