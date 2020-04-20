<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'yith_wc_product_shipping_row' ) ) {

	/**
	 * YITH WooCommerce Product Shipping Row
	 *
	 * @since 1.0.0
	 */
	function yith_wc_product_shipping_row( $product_id, $package, $quantity = 1, $weight = 0, $cart_weight = 0 ) {
		global $wpdb;

		/**
		 * Product ID filter
		 */
		$product_id = apply_filters( 'yith_wc_product_shipping_row_product_id', $product_id );
		$cart_total = WC()->cart->cart_contents_total;
		$cart_qty 	= WC()->cart->get_cart_contents_count();

		$country_code	= $package['destination']['country'];
		$state_code		= $package['destination']['state'];
		$postal_code	= $package['destination']['postcode'];

		$valid_postal_codes		= array( '', $postal_code );
		$postal_code_length		= strlen( $postal_code );
		$wildcard_postal_code	= $postal_code;

		for ( $i = 0; $i < $postal_code_length; $i ++ ) {
			$wildcard_postal_code = substr( $wildcard_postal_code, 0, -1 );
			$valid_postal_codes[] = $wildcard_postal_code . '*';
		}

		$shipping_row = array();

		/*
		 * User Role
		 */

		$user = wp_get_current_user();
		$user_role = isset( $user->roles[0] ) ? $user->roles[0] : 0;

		/**
		 * Check for product
		 */
		if ( get_post_meta( $product_id, '_yith_product_shipping', true ) == 'yes' ) {
			$query = "SELECT * FROM {$wpdb->prefix}yith_wcps_shippings WHERE
					product_id = $product_id AND
					( role = '$user_role' || role = '0' ) AND
					min_cart_qty <= $cart_qty AND
					( max_cart_qty = 0 OR max_cart_qty > $cart_qty ) AND
					min_quantity <= $quantity AND
					( max_quantity = 0 OR max_quantity > $quantity ) AND
					min_weight <= $weight AND
					( max_weight = 0 OR max_weight > $weight ) AND
					min_cart_weight <= $cart_weight AND
					( max_cart_weight = 0 OR max_cart_weight > $cart_weight ) AND
					min_cart_total <= $cart_total AND
					( max_cart_total = 0 OR max_cart_total > $cart_total ) AND
					( country_code LIKE '%" . strtoupper( $country_code ) . "%' OR country_code = '' ) AND
					( state_code LIKE '%" . strtoupper( $state_code ) . "%' OR state_code = '' ) AND
					( postal_code LIKE '%" . $postal_code . "%' OR postal_code = '' )
					ORDER BY ord LIMIT 1";
			$shipping_row = $wpdb->get_row( $query );
		}

		/**
		 * Check for parent
		 */
		if ( empty( $shipping_row ) ) {
			$_product = wc_get_product( $product_id );
			$parent_id = yit_get_base_product_id( $_product );
			if ( get_post_meta( $parent_id, '_yith_product_shipping', true ) == 'yes' ) {
				$query = "SELECT * FROM {$wpdb->prefix}yith_wcps_shippings WHERE
						product_id = $parent_id AND
						( role = '$user_role' || role = '0' ) AND
						min_cart_qty <= $cart_qty AND
						( max_cart_qty = 0 OR max_cart_qty > $cart_qty ) AND
						min_quantity <= $quantity AND
						( max_quantity = 0 OR max_quantity > $quantity ) AND
						min_weight <= $weight AND
						( max_weight = 0 OR max_weight > $weight ) AND
						min_cart_weight <= $cart_weight AND
						( max_cart_weight = 0 OR max_cart_weight > $cart_weight ) AND
						min_cart_total <= $cart_total AND
						( max_cart_total = 0 OR max_cart_total > $cart_total ) AND
						( country_code LIKE '%" . strtoupper( $country_code ) . "%' OR country_code = '' ) AND
						( state_code LIKE '%" . strtoupper( $state_code ) . "%' OR state_code = '' ) AND
						( postal_code LIKE '%" . $postal_code . "%' OR postal_code = '' )
						ORDER BY ord LIMIT 1";
				$shipping_row = $wpdb->get_row( $query );
			}
		}

		/**
		 * Check for global
		 */
		if ( empty( $shipping_row ) ) {
			$_product = wc_get_product( $product_id );
			$parent_id = yit_get_base_product_id($_product);
			if ( $parent_id > 0 ) { $product_id = $parent_id; }

			$term_list = wp_get_post_terms( $product_id, 'product_cat', array( 'fields'=>'ids' ) );
			$categories_query = "( categories = '0'";
			if ( count( $term_list ) ) {
				foreach ( $term_list as $key => $value ) {
					$categories_query .= " OR categories LIKE '%ID:$value%'";
				}
			}
			$categories_query .= ' ) AND';

			$term_list = wp_get_post_terms( $product_id, 'product_tag', array( 'fields'=>'ids' ) );
			$tags_query = "( tags = '0'";
			if ( count( $term_list ) ) {
				foreach ( $term_list as $key => $value ) {
					$tags_query .= " OR tags LIKE '%ID:$value%'";
				}
			}
			$tags_query .= ' ) AND';

			$query = "SELECT * FROM {$wpdb->prefix}yith_wcps_shippings WHERE
					product_id = 0 AND
					( role = '$user_role' || role = '0' ) AND
					min_cart_qty <= $cart_qty AND
					( max_cart_qty = 0 OR max_cart_qty > $cart_qty ) AND
					min_quantity <= $quantity AND
					( max_quantity = 0 OR max_quantity > $quantity ) AND
					min_weight <= $weight AND
					( max_weight = 0 OR max_weight > $weight ) AND
					min_cart_weight <= $cart_weight AND
					( max_cart_weight = 0 OR max_cart_weight > $cart_weight ) AND
					min_cart_total <= $cart_total AND
					( max_cart_total = 0 OR max_cart_total > $cart_total ) AND
					$categories_query
					$tags_query
					
					(
						(
							geo_exclude != '1' AND
							( country_code LIKE '%" . strtoupper( $country_code ) . "%' OR country_code = '' ) AND
							" . ( empty( $state_code ) ? "state_code = ''" : "( state_code LIKE '%" . strtoupper( $state_code ) . "%' OR state_code = '' )" ) . " AND
							( state_code LIKE '%" . strtoupper( $state_code ) . "%' OR state_code = '' ) AND
							( postal_code LIKE '%" . $postal_code . "%' OR postal_code = '' )
						) OR (
							geo_exclude = '1' AND
							! ( country_code LIKE '%" . strtoupper( $country_code ) . "%' OR country_code = '' ) AND
							! ( state_code LIKE '%" . strtoupper( $state_code ) . "%' OR state_code = '' ) AND
							! ( postal_code LIKE '%" . $postal_code . "%' OR postal_code = '' )
						)
					)

					ORDER BY ord LIMIT 1";
			$shipping_row = $wpdb->get_row( $query );
		}

		return $shipping_row;

	}

}
