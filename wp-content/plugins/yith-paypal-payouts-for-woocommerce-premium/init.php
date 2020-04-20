<?php
/**
 * Plugin Name: YITH PayPal Payouts for WooCommerce Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-paypal-payouts-for-woocommerce/
 * Description: The plugin allows you to share the profits of your store automatically with others thanks to PayPal payouts. <a href="https://yithemes.com">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>.
 * Version: 1.0.7
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-paypal-payouts-for-woocommerce
 * Domain Path: /languages/
 * WC requires at least: 3.4.0
 * WC tested up to: 3.6
 * @package YITH PayPal Payouts for WooCommerce
 * @version 1.0.7
 */

/*
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if( !function_exists( 'is_plugin_active' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}


function yith_payouts_install_woocommerce_admin_notice() {
	?>
    <div class="error">
        <p><?php _e( 'YITH PayPal Payouts for WooCommerce is enabled but not active. It requires WooCommerce in order to work.', 'yith-paypal-payouts-for-woocommerce' ); ?></p>
    </div>
	<?php
}

function yith_payout_error_message(){
	?>
    <div class="error">
        <p><?php _e( 'You can\'t activate YITH PayPal Payouts because opcache.save_comments isn\'t configured. Please, try to ask your host provider', 'yith-paypal-payouts-for-woocommerce' ); ?></p>
    </div>
	<?php
}


if( apply_filters('yith_payout_enable_plugin', ini_get( 'opcache.save_comments') == 0 ) ){
	add_action( 'admin_notices', 'yith_payout_error_message' );
	deactivate_plugins( plugin_basename( __FILE__ ) );
}


if ( ! defined( 'YITH_PAYOUTS_VERSION' ) ) {
	define( 'YITH_PAYOUTS_VERSION', '1.0.7' );
}

if ( ! defined( 'YITH_PAYOUTS_PREMIUM' ) ) {
	define( 'YITH_PAYOUTS_PREMIUM', '1' );
}

if( !defined( 'YITH_PAYOUTS_DB_VERSION')){
    define( 'YITH_PAYOUTS_DB_VERSION', '1.0.0' );
}

if ( ! defined( 'YITH_PAYOUTS_INIT' ) ) {
	define( 'YITH_PAYOUTS_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_PAYOUTS_FILE' ) ) {
	define( 'YITH_PAYOUTS_FILE', __FILE__ );
}

if ( ! defined( 'YITH_PAYOUTS_DIR' ) ) {
	define( 'YITH_PAYOUTS_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YITH_PAYOUTS_URL' ) ) {
	define( 'YITH_PAYOUTS_URL', plugins_url( '/', __FILE__ ) );
}

if ( ! defined( 'YITH_PAYOUTS_ASSETS_URL' ) ) {
	define( 'YITH_PAYOUTS_ASSETS_URL', YITH_PAYOUTS_URL . 'assets/' );
}

if ( ! defined( 'YITH_PAYOUTS_ASSETS_PATH' ) ) {
	define( 'YITH_PAYOUTS_ASSETS_PATH', YITH_PAYOUTS_DIR . 'assets/' );
}

if ( ! defined( 'YITH_PAYOUTS_TEMPLATE_PATH' ) ) {
	define( 'YITH_PAYOUTS_TEMPLATE_PATH', YITH_PAYOUTS_DIR . 'templates/' );
}

if ( ! defined( 'YITH_PAYOUTS_INC' ) ) {
	define( 'YITH_PAYOUTS_INC', YITH_PAYOUTS_DIR . 'includes/' );
}

if ( ! defined( 'YITH_PAYOUTS_SLUG' ) ) {
	define( 'YITH_PAYOUTS_SLUG', 'yith-paypal-payouts-for-woocommerce' );
}

if ( ! defined( 'YITH_PAYOUTS_SECRET_KEY' ) ) {
	define( 'YITH_PAYOUTS_SECRET_KEY', 'v93AiIDM1EvWaccSRS2G' );
}

if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}

register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_PAYOUTS_DIR . 'plugin-fw/init.php' ) ) {
	require_once( YITH_PAYOUTS_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_PAYOUTS_DIR );

if ( ! function_exists( 'YITH_PayOuts_Premium_Init' ) ) {

	/* Load  text domain */
	load_plugin_textdomain( 'yith-paypal-payouts-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	/**
	 * Unique access to instance of YITH_PayPal_Payouts class
	 * @since 1.0.0
	 */
	function YITH_PayOuts_Premium_Init() {

		require_once( 'class.yith-paypal-payouts.php' );
		require_once( YITH_PAYOUTS_INC . 'class.yith-payouts-admin.php' );
		require_once( YITH_PAYOUTS_INC . 'class.yith-payout.php' );
		require_once( YITH_PAYOUTS_INC . 'class.yith-payout-endpoint.php' );
		require_once( YITH_PAYOUTS_INC . 'class.yith-payouts-shortcodes.php' );


		/**
		 * Load PayPal library
		 */
		require_once( 'lib/paypal_sdk/autoload.php');

		YITH_PayPal_Payouts();
	}
}

add_action( 'yith_wc_payouts_premium_init', 'YITH_PayOuts_Premium_Init' );

if ( ! function_exists( 'yith_payouts_premium_install' ) ) {
	/**
	 * install payouts plugin
	 * @author YIThemes
	 * @since 1.0.0
	 */
	function yith_payouts_premium_install() {

		if( ini_get( 'opcache.save_comments') == 0 ){
			add_action( 'admin_notices', 'yith_payout_error_message' );
			deactivate_plugins( plugin_basename( __FILE__ ) );
		}else {
			if ( ! function_exists( 'WC' ) ) {
				add_action( 'admin_notices', 'yith_payouts_install_woocommerce_admin_notice' );
			} else {
				do_action( 'yith_wc_payouts_premium_init' );
			}
		}

	}
}

add_action( 'plugins_loaded', 'yith_payouts_premium_install', 11 );

if( !class_exists( 'YITH_Payouts' ) ) {
	require_once( YITH_PAYOUTS_INC . 'class.yith-payouts.php' );
}

if( !class_exists( 'YITH_Payout_Items' ) ){
	require_once( YITH_PAYOUTS_INC . 'class.yith-payout-items.php' );
}
register_activation_hook( YITH_PAYOUTS_FILE, 'YITH_Payout_Items::create_payouts_table' );
register_activation_hook( YITH_PAYOUTS_FILE, 'YITH_Payouts::create_payouts_table' );
