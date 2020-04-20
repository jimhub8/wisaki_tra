<?php 
/**
 * Plugin Name: Sw Vendor Slider
 * Plugin URI: http://www.wpthemego.com/
 * Description: A plugin help to display vendor as beauty slider.
 * Version: 1.0.7
 * Author: SmartAddons
 * Author URI: http://www.wpthemego.com/
 * Requires at least: 4.1
 * Tested up to: WorPress 5.3.x and WooCommerce 3.8.x
 * WC tested up to: 3.9.0
 * Text Domain: sw_vendor
 * Domain Path: /languages/
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// define plugin path
if ( ! defined( 'SVPATH' ) ) {
	define( 'SVPATH', plugin_dir_path( __FILE__ ) );
}

// define plugin URL
if ( ! defined( 'SVURL' ) ) {
	define( 'SVURL', plugins_url(). '/sw_vendor_slider' );
}

// define plugin theme path
if ( ! defined( 'SVTHEME' ) ) {
	define( 'SVTHEME', plugin_dir_path( __FILE__ ). 'includes' );
}

function sw_vendor_construct(){
	global $woocommerce;

	if ( ( ! isset( $woocommerce ) || ! function_exists( 'WC' ) ) && class_exists( 'WC_Vendors' ) ) {
		add_action( 'admin_notices', 'sw_vendor_admin_notice' );
		return;
	}
	
	/* Enqueue Script */
	add_action( 'wp_enqueue_scripts', 'sw_vendor_custom_script', 10 );
	
	if ( class_exists( 'Vc_Manager' ) ) {
		add_action( 'vc_frontend_editor_render', 'sw_vendor_enqueueJsFrontend' );
	}
	
	/* Load text domain */
	load_plugin_textdomain( 'sw_vendor_slider', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
	
	/* Include files */
	require_once( SVTHEME . '/functions.php' );
}

add_action( 'plugins_loaded', 'sw_vendor_construct', 20 );


/*
** Load admin notice when WooCommerce not active
*/
function sw_vendor_admin_notice(){
	?>
	<div class="error">
		<p><?php _e( 'Sw Vendor is enabled but not effective. It requires WooCommerce and WC Vendor in order to work.', 'sw_vendor_slider' ); ?></p>
	</div>
<?php
}

/*
** Load Custom variation script
*/
function sw_vendor_custom_script(){	
	if (!wp_script_is('slick_slider') && !function_exists( 'sw_woocommerce_construct' ) ) {
		wp_enqueue_script( 'slick_slider', PBDIR .'assets/js/slick.min.js', __FILE__ ,array(), null, true );
	}
	wp_enqueue_style( 'sw-vendor-slider', plugins_url( 'css/style.css', __FILE__ ), array(), null );	
}

/*
** Load script frontend edit by visual
*/
function sw_vendor_enqueueJsFrontend(){
	if (!wp_script_is('slick_slider') && !function_exists( 'sw_woocommerce_construct' ) ) {
		wp_enqueue_script( 'slick_slider', PBDIR .'assets/js/slick.min.js', __FILE__ ,array(), null, true );
	}
	wp_register_script( 'custom-vendorjs', plugins_url( 'js/custom_js.js', __FILE__ ),array( 'slick_slider' ), null, true );	
	wp_enqueue_script('custom-vendorjs');
}