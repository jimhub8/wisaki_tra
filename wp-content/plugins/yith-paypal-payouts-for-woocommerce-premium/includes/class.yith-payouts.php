<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'YITH_Payouts' ) ) {

	class YITH_Payouts {

		protected static $instance;

		protected static $payouts_table_name = 'yith_payouts';

		protected static $db_version = YITH_PAYOUTS_DB_VERSION;

		public function __construct() {
			add_action( 'init', array( $this, 'add_payouts_table_wpdb' ), 0 );
			add_action( 'switch_blog', array( $this, 'add_payouts_table_wpdb' ), 0 );
		}

		/**
		 * get single instance
		 * @author Salvatore Strano
		 * @since 1.0.0
		 * @return YITH_Payouts
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * add payouts table into wpdb
		 * @author Salvatore Strano
		 * @since 1.0.0
		 */
		public function add_payouts_table_wpdb() {
			global $wpdb;

			$wpdb->yith_payouts = $wpdb->prefix . self::$payouts_table_name;

			$wpdb->tables[] = $wpdb->prefix . self::$payouts_table_name;
		}

		/**
		 *create the PayOuts table
		 * @author Salvatore Strano
		 * @since 1.0.0
		 */
		public static function create_payouts_table() {

			$is_table_created = get_option( 'yith_payouts_table_created', false );
			$force_creation   = isset( $_GET['yith_force_payouts_table_creation'] );

			if ( $is_table_created && ! $force_creation ) {
				return;
			}

			if ( ! function_exists( 'dbDelta' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			}

			global $wpdb;

			$charset_collate = $wpdb->get_charset_collate();

			$table_name = $wpdb->prefix . self::$payouts_table_name;

			$query = "CREATE TABLE IF NOT EXISTS $table_name (
						ID bigint(20) NOT NULL AUTO_INCREMENT,
                        order_id bigint(20) NOT NULL,
                        sender_batch_id VARCHAR(200) NOT NULL DEFAULT '',
                        payout_batch_id VARCHAR(200) NOT NULL DEFAULT '',
                        payout_status VARCHAR(200) NOT NULL DEFAULT '',
                        payout_mode VARCHAR(200) NOT NULL DEFAULT 'instant',
                        last_edit DATETIME NOT NULL DEFAULT '000-00-00 00:00:00',
                        last_edit_gmt DATETIME NOT NULL DEFAULT '000-00-00 00:00:00',
                        PRIMARY KEY (ID)
                        ) $charset_collate;";


			$result = dbDelta( $query );


			update_option( 'yith_payouts_table_created', true );
			update_option( 'yith_payouts_db_version', self::$db_version );
		}

		/**
		 * Get Payouts
		 *
		 * @param array $q
		 *
		 * @return array
		 * @author Salvatore Strano
		 * @since  1.0.0
		 */
		public function get_payouts( $q = array() ) {
			global $wpdb;

			$default_args = array(
				'ID'              => 0,
				'order_id'        => '',
				'payout_batch_id' => '',
				'sender_batch_id' => '',
				'payout_mode'     => 'all',
				'payout_status'   => 'all',
				'm'               => false,
				'date_query'      => false,
				's'               => '',
				'number'          => '',
				'offset'          => '',
				'paged'           => '',
				'orderby'         => 'ID',
				'order'           => 'ASC',
				'fields'          => 'ids',
				'table'           => $wpdb->yith_payouts
			);

			$q = wp_parse_args( $q, $default_args );

			$table  = $q['table'];
			$join   = '';
			$search = '';

			if ( ! empty( $q['s'] ) ) {

				$table_item_name = $wpdb->yith_payout_item;
				$join            .= " JOIN  $table_item_name ON c.payout_batch_id = $table_item_name.payout_batch_id ";
				$like            = $wpdb->esc_like( $q['s'] );

				$search = " AND $table_item_name.receiver LIKE '%$like%'";
			}

			// First let's clear some variables
			$where  = '';
			$limits = '';

			$groupby = '';
			$orderby = '';

			// query parts initializating
			$pieces = array( 'where', 'groupby', 'join', 'orderby', 'limits' );

			// filter
			if ( ! empty( $q['ID'] ) ) {
				$where .= $wpdb->prepare( " AND c.ID = %d", $q['ID'] );
			}

			// filter
			if ( ! empty( $q['payout_batch_id'] ) ) {
				$where .= $wpdb->prepare( " AND c.payout_batch_id = %s", $q['payout_batch_id'] );
			}

			if ( ! empty( $q['sender_batch_id'] ) ) {
				$where .= $wpdb->prepare( " AND c.sender_batch_id = %s", $q['sender_batch_id'] );
			}


			if ( ! empty( $q['order_id'] ) ) {
				$where .= $wpdb->prepare( " AND c.order_id = %s", $q['order_id'] );
			}

			if ( ! empty( $q['payout_mode'] ) && 'all' != $q['payout_mode'] ) {
				$where .= $wpdb->prepare( " AND c.payout_mode = %s", $q['payout_mode'] );
			}
			if ( ! empty( $q['payout_status'] ) && 'all' != $q['payout_status'] ) {
				if ( is_array( $q['payout_status'] ) ) {
					$q['payout_status'] = implode( "', '", $q['payout_status'] );
				}
				$where .= sprintf( " AND c.payout_status IN ( '%s' )", $q['payout_status'] );
			}

			// Order
			if ( ! is_string( $q['order'] ) || empty( $q['order'] ) ) {
				$q['order'] = 'DESC';
			}

			if ( 'ASC' === strtoupper( $q['order'] ) ) {
				$q['order'] = 'ASC';
			} else {
				$q['order'] = 'DESC';
			}

			// Order by.
			if ( empty( $q['orderby'] ) ) {
				/*
				 * Boolean false or empty array blanks out ORDER BY,
				 * while leaving the value unset or otherwise empty sets the default.
				 */
				if ( isset( $q['orderby'] ) && ( is_array( $q['orderby'] ) || false === $q['orderby'] ) ) {
					$orderby = '';
				} else {
					$orderby = "c.ID " . $q['order'];
				}
			} elseif ( 'none' == $q['orderby'] ) {
				$orderby = '';
			} else {
				$orderby_array = array();
				if ( is_array( $q['orderby'] ) ) {
					foreach ( $q['orderby'] as $_orderby => $order ) {
						$orderby = addslashes_gpc( urldecode( $_orderby ) );

						if ( ! is_string( $order ) || empty( $order ) ) {
							$order = 'DESC';
						}

						if ( 'ASC' === strtoupper( $order ) ) {
							$order = 'ASC';
						} else {
							$order = 'DESC';
						}

						$orderby_array[] = $orderby . ' ' . $order;
					}
					$orderby = implode( ', ', $orderby_array );

				} else {
					$q['orderby'] = urldecode( $q['orderby'] );
					$q['orderby'] = addslashes_gpc( $q['orderby'] );

					foreach ( explode( ' ', $q['orderby'] ) as $i => $orderby ) {
						$orderby_array[] = $orderby;
					}
					$orderby = implode( ' ' . $q['order'] . ', ', $orderby_array );

					if ( empty( $orderby ) ) {
						$orderby = "c.ID " . $q['order'];
					} elseif ( ! empty( $q['order'] ) ) {
						$orderby .= " {$q['order']}";
					}
				}
			}

			// Paging
			if ( ! empty( $q['paged'] ) && ! empty( $q['number'] ) ) {
				$page = absint( $q['paged'] );
				if ( ! $page ) {
					$page = 1;
				}

				if ( empty( $q['offset'] ) ) {
					$pgstrt = absint( ( $page - 1 ) * $q['number'] ) . ', ';
				} else { // we're ignoring $page and using 'offset'
					$q['offset'] = absint( $q['offset'] );
					$pgstrt      = $q['offset'] . ', ';
				}
				$limits = 'LIMIT ' . $pgstrt . $q['number'];
			}

			$clauses = compact( $pieces );

			$where   = isset( $clauses['where'] ) ? $clauses['where'] : '';
			$groupby = isset( $clauses['groupby'] ) ? $clauses['groupby'] : '';
			$join    = isset( $clauses['join'] ) ? $clauses['join'] : '';
			$orderby = isset( $clauses['orderby'] ) ? $clauses['orderby'] : '';
			$limits  = isset( $clauses['limits'] ) ? $clauses['limits'] : '';

			if ( ! empty( $groupby ) ) {
				$groupby = 'GROUP BY ' . $groupby;
			}
			if ( ! empty( $orderby ) ) {
				$orderby = 'ORDER BY ' . $orderby;
			}

			$found_rows = '';
			if ( ! empty( $limits ) ) {
				$found_rows = 'SQL_CALC_FOUND_ROWS';
			}

			$fields = 'c.ID';

			if ( 'count' != $q['fields'] && 'ids' != $q['fields'] ) {
				if ( is_array( $q['fields'] ) ) {

					$fields = implode( ',', $q['fields'] );
				} else {
					$fields = $q['fields'];
				}
			}

			$where .= $search;
			$res   = $wpdb->get_results( "SELECT $found_rows DISTINCT $fields FROM $table c $join WHERE 1=1 $where $groupby $orderby $limits", ARRAY_A );


			// return count
			if ( 'count' == $q['fields'] ) {
				return ! empty( $limits ) ? $wpdb->get_var( 'SELECT FOUND_ROWS()' ) : count( $res );
			}

			return $res;
		}


		/**
		 * @return array
		 */
		public static function get_payouts_status() {

			$status = array(
				'unprocessed'  => 'UNPROCESSED',
				'acknowledged' => 'ACKNOWLEDGED',
				'denied'       => 'DENIED',
				'pending'      => 'PENDING',
				'processing'   => 'PROCESSING',
				'success'      => 'SUCCESS',
				'new'          => 'NEW',
				'canceled'     => 'CANCELED'
			);

			return $status;
		}

		/**
		 * Return the payment mode list
		 * @author Salvatore Strano
		 * @since 1.0.0
		 * @return array
		 */
		public static function get_payout_payment_mode() {

			$payment_mode = array(
				'instant'    => __( 'Instant Payment', 'yith-paypal-payouts-for-woocommerce' ),
				'commission' => __( 'Commission Payment', 'yith-paypal-payouts-for-woocommerce' ),
				'affiliate' => __( 'Affiliate Payment', 'yith-paypal-payouts-for-woocommerce' ),
			);

			return $payment_mode;
		}

		/**
		 * @param array $payout_id
		 */
		public static function delete_payout( $payout_id ) {

			global $wpdb;


			$query = $wpdb->prepare( "DELETE FROM $wpdb->yith_payouts WHERE ID= '%s' ", $payout_id );

			$wpdb->query( $query );
		}
	}

}

if ( ! function_exists( 'YITH_Payouts' ) ) {
	function YITH_Payouts() {

		return YITH_Payouts::get_instance();
	}
}

YITH_Payouts();