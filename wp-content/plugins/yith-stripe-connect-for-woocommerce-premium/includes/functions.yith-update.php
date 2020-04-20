<?php

/**
 * Alter Database Table
 */
function yith_stripe_connect_update_1_0_1() {
	$db_version = get_option( 'yith_wcsc_db_version', '1.0.0' );
	if ( $db_version && version_compare( $db_version, '1.0.1', '<' ) ) {
		global $wpdb;

		/**
		 * Check if dbDelta() exists
		 */
		if ( ! function_exists( 'dbDelta' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		}

		// retrieve table charset
		$charset_collate = $wpdb->get_charset_collate();

		$sql_commissions = "CREATE TABLE `{$wpdb->prefix}yith_wcsc_commissions` (
                    ID bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    receiver_id bigint(20) NOT NULL,
                    user_id bigint (20) NOT NULL,
                    order_id bigint (20) NOT NULL,
                    order_item_id bigint(20) NOT NULL,
                    product_id bigint(20) NOT NULL,
                    commission DECIMAL(10,2) ,
                    commission_status VARCHAR (120) NOT NULL,
                    commission_type VARCHAR (20),
                    commission_rate DECIMAL ,
                    payment_retarded bigint (20),
                    purchased_date DATETIME,
                    note LONGTEXT,
                    integration_item LONGTEXT
                ) $charset_collate;";
		dbDelta( $sql_commissions );

		update_option( 'yith_wcsc_db_version', '1.0.1' );
	}
}

add_action( 'admin_init', 'yith_stripe_connect_update_1_0_1' );



