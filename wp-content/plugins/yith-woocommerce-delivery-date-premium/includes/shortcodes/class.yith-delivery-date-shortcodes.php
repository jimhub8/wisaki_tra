<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Delivery_Date_Shortcodes' ) ) {

	class YITH_Delivery_Date_Shortcodes {


		public static function print_dynamic_message_shortcode( $atts = array() ) {

			$default = array(
				'product_id' => ''
			);

			$atts    = shortcode_atts( $default, $atts );
			$product = null;
			if ( '' !== $atts['product_id'] ) {
				$product = wc_get_product( $atts['product_id'] );
			}

			if ( ! $product instanceof WC_Product ) {
				global $product;
			}
			YITH_Delivery_Date_Product_Frontend()->get_date_info( $product );
		}

	}
}

add_shortcode( 'ywcdd_dynamic_messages', array( 'YITH_Delivery_Date_Shortcodes', 'print_dynamic_message_shortcode' ) );