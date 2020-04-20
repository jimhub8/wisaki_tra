<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'yith_wcps_is_wcfm' ) ) {

	/**
	 * YITH WooCommerce Product Shipping Row
	 *
	 * @since 1.0.10
	 */
	function yith_wcps_is_wcfm() {

		if ( class_exists('YITH_Frontend_Manager')
				&& ! is_admin()
				&& isset( $_GET['product_id'] )
				&& $_GET['product_id'] > 0
			) {

			return true;

		}

		return false;

	}

}
