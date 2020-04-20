<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'YITH_Payout_Items' ) ) {

	class YITH_Payout_Items {

		protected static $instance;

		protected static $payout_item_table_name = 'yith_payout_item';

		protected static $db_version = YITH_PAYOUTS_DB_VERSION;

		public function __construct() {
			add_action( 'init', array( $this, 'add_payout_item_table_wpdb' ), 0 );
			add_action( 'switch_blog', array( $this, 'add_payout_item_table_wpdb' ), 0 );
		}

		/**
		 * get single instance
		 * @author Salvatore Strano
		 * @since 1.0.0
		 * @return YITH_Payout_Items
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
		public function add_payout_item_table_wpdb() {
			global $wpdb;

			$wpdb->yith_payout_item = $wpdb->prefix . self::$payout_item_table_name;

			$wpdb->tables[] = $wpdb->prefix . self::$payout_item_table_name;
		}

		/**
		 *create the PayOuts table
		 * @author Salvatore Strano
		 * @since 1.0.0
		 */
		public static function create_payouts_table() {

			$is_table_created = false;//get_option( 'yith_payout_item_table_created', false );
			$force_creation   = isset( $_GET['yith_force_payouts_table_creation'] );

			if ( $is_table_created && ! $force_creation ) {
				return;
			}

			if ( ! function_exists( 'dbDelta' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			}

			global $wpdb;

			$charset_collate = $wpdb->get_charset_collate();

			$table_name = $wpdb->prefix . self::$payout_item_table_name;

			$query = "CREATE TABLE IF NOT EXISTS $table_name (
						ID bigint(20) NOT NULL AUTO_INCREMENT,
						payout_item_id VARCHAR(30) NOT NULL,
						sender_batch_id VARCHAR(30) NOT NULL,
						sender_item_id VARCHAR(30) NOT NULL,
						transaction_id VARCHAR(200) NOT NULL,
						transaction_status VARCHAR(200) NOT NULL,
						payout_batch_id VARCHAR(200) NOT NULL,
						receiver VARCHAR(200) NOT NULL,
						amount FLOAT NOT NULL,
						fee FLOAT NOT NULL,
						currency VARCHAR(10) NOT NULL,
                        PRIMARY KEY (ID)
                        ) $charset_collate;";


			$result = dbDelta( $query );


			update_option( 'yith_payout_item_table_created', true );
			update_option( 'yith_payouts_db_version', self::$db_version );
		}

		/**
		 *check if a payout item exist
		 * @author Salvatore Strano
		 * @since 1.0.0
		 *
		 * @param string $payout_item_id
		 *
		 * @return bool
		 */
		public static function is_payout_item_exist( $payout_item_id ) {

			global $wpdb;
			$table_name = $wpdb->prefix . self::$payout_item_table_name;
			$query      = $wpdb->prepare( "SELECT payout_item_id  	FROM {$table_name} 	WHERE payout_item_id = %s", $payout_item_id );

			$result = $wpdb->get_col( $query );

			return count( $result ) > 0;
		}

		/**
		 * Add new Payout item
		 * @author Salvatore Strano
		 * @since 1.0.0
		 *
		 * @param array $args
		 */
		public static function add_payout_item( $args = array() ) {

			$defaults = array(
				'payout_item_id'     => '',
				'sender_batch_id'    => '',
				'transaction_id'     => '',
				'transaction_status' => 'unprocessed',
				'payout_batch_id'    => '',
				'sender_item_id'    => '',
				'receiver'           => '',
				'amount'             => 0,
				'fee'                => 0,
				'currency'           => ''
			);

			$args = wp_parse_args( $args, $defaults );


			global $wpdb;
			$table_name = $wpdb->prefix . self::$payout_item_table_name;
			$wpdb->insert( $table_name, (array) $args );


		}

		/**
		 * Add new Payout item
		 * @author Salvatore Strano
		 * @since 1.0.0
		 *
		 * @param string $sender_batch_id
		 * @param string $receiver
		 * @param array $args
		 */
		public static function update_payout_item( $sender_batch_id, $receiver, $args = array() ) {

			$defaults = array(
				'transaction_id'     => '',
				'transaction_status' => '',
				'payout_batch_id'    => '',
				'sender_item_id'    => '',
				'payout_item_id'    => '',
				'amount'             => 0,
				'fee'                => 0,
				'currency'           => ''
			);

			$args = wp_parse_args( $args, $defaults );

			if ( $sender_batch_id ) {

				global $wpdb;
				$table_name = $wpdb->prefix . self::$payout_item_table_name;
				$wpdb->update( $table_name, (array) $args, array( 'sender_batch_id' => $sender_batch_id, 'receiver' => $receiver ) );
			}

		}

		/**
		 * Get Payout Items
		 *
		 * @param array $q
		 *
		 * @return array
		 * @author Salvatore Strano
		 * @since  1.0.0
		 */
		public function get_payout_items( $q = array() ) {
			global $wpdb;

			$default_args = array(
				'ID' => '',
				'payout_batch_id'    => '',
				'transaction_id'     => '',
				'transaction_status' => 'all',
				'sender_batch_id'    => '',
				'sender_item_id'     => '',
				'payout_item_id'     => '',
				'receiver'           => '',
				'm'                  => false,
				'date_query'         => false,
				's'                  => '',
				'number'             => '',
				'offset'             => '',
				'paged'              => '',
				'orderby'            => 'payout_item_id',
				'order'              => 'ASC',
				'fields'             => 'ids',
				'table'              => $wpdb->yith_payout_item
			);

			$q = wp_parse_args( $q, $default_args );

			$table = $q['table'];

			// Fairly insane upper bound for search string lengths.
			if ( ! is_scalar( $q['s'] ) || ( ! empty( $q['s'] ) && strlen( $q['s'] ) > 1600 ) ) {
				$q['s'] = '';
			}

			// First let's clear some variables
			$where   = '';
			$limits  = '';
			$join    = '';
			$groupby = '';
			$orderby = '';

			// query parts initializating
			$pieces = array( 'where', 'groupby', 'join', 'orderby', 'limits' );

			// filter
			if ( ! empty( $q['ID'] ) ) {
				$where .= $wpdb->prepare( " AND c.ID = %d", $q['ID'] );
			}
			if ( ! empty( $q['payout_batch_id'] ) ) {
				$where .= $wpdb->prepare( " AND c.payout_batch_id = %s", $q['payout_batch_id'] );
			}
			if ( ! empty( $q['sender_batch_id'] ) ) {
				$where .= $wpdb->prepare( " AND c.sender_batch_id = %s", $q['sender_batch_id'] );
			}

			if ( ! empty( $q['sender_item_id'] ) ) {
				$where .= $wpdb->prepare( " AND c.sender_item_id = %s", $q['sender_item_id'] );
			}


			if ( ! empty( $q['payout_item_id'] ) ) {
				$where .= $wpdb->prepare( " AND c.payout_item_id = %s", $q['payout_item_id'] );
			}
			if ( ! empty( $q['transaction_id'] ) ) {
				$where .= $wpdb->prepare( " AND c.transaction_id = %s", $q['transaction_id'] );
			}

			if ( ! empty( $q['receiver'] ) && 'all' != $q['receiver'] ) {
				$where .= $wpdb->prepare( " AND c.receiver = %s", $q['receiver'] );
			}
			if ( ! empty( $q['transaction_status'] ) && 'all' != $q['transaction_status'] ) {
				if ( is_array( $q['transaction_status'] ) ) {
					$q['transaction_status'] = implode( "', '", $q['transaction_status'] );
				}
				$where .= sprintf( " AND c.transaction_status IN ( '%s' )", $q['transaction_status'] );
			}

			// The "m" parameter is meant for months but accepts datetimes of varying specificity
			if ( $q['m'] ) {
				$q['m'] = absint( preg_replace( '|[^0-9]|', '', $q['m'] ) );

				$join  .= strpos( $join, "$wpdb->posts o" ) === false ? " JOIN $wpdb->posts o ON o.ID = c.order_id" : '';
				$where .= " AND o.post_type = 'shop_order'";

				$where .= " AND YEAR(o.post_date)=" . substr( $q['m'], 0, 4 );
				if ( strlen( $q['m'] ) > 5 ) {
					$where .= " AND MONTH(o.post_date)=" . substr( $q['m'], 4, 2 );
				}
				if ( strlen( $q['m'] ) > 7 ) {
					$where .= " AND DAYOFMONTH(o.post_date)=" . substr( $q['m'], 6, 2 );
				}
				if ( strlen( $q['m'] ) > 9 ) {
					$where .= " AND HOUR(o.post_date)=" . substr( $q['m'], 8, 2 );
				}
				if ( strlen( $q['m'] ) > 11 ) {
					$where .= " AND MINUTE(o.post_date)=" . substr( $q['m'], 10, 2 );
				}
				if ( strlen( $q['m'] ) > 13 ) {
					$where .= " AND SECOND(o.post_date)=" . substr( $q['m'], 12, 2 );
				}
			}

			// Handle complex date queries
			if ( ! empty( $q['date_query'] ) ) {
				$join  .= strpos( $join, "$wpdb->posts o" ) === false ? " JOIN $wpdb->posts o ON o.ID = c.order_id" : '';
				$where .= " AND o.post_type = 'shop_order'";

				$date_query = new WP_Date_Query( $q['date_query'], 'o.post_date' );
				$where      .= $date_query->get_sql();
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

			$fields = 'c.payout_item_id';

			if ( 'count' != $q['fields'] && 'ids' != $q['fields'] ) {
				if ( is_array( $q['fields'] ) ) {
					$fields = implode( ',', $q['fields'] );
				} else {
					$fields = $q['fields'];
				}
			}

			$res = $wpdb->get_results( "SELECT $found_rows DISTINCT $fields FROM $table c $join WHERE 1=1 $where $groupby $orderby $limits", ARRAY_A );


			// return count
			if ( 'count' == $q['fields'] ) {
				return ! empty( $limits ) ? $wpdb->get_var( 'SELECT FOUND_ROWS()' ) : count( $res );
			}



			return $res;
		}

		/**
		 * get Payout item transaction status
		 * @author Salvatore Strano
		 * @since 1.0.0
		 * @return array
		 */
		public function get_transaction_status() {

			$status = array(
				'unprocessed' => 'UNPROCESSED',
				'blocked'     => 'BLOCKED',
				'denied'      => 'DENIED',
				'failed'      => 'FAILED',
				'new'         => 'NEW',
				'onhold'      => 'ONHOLD',
				'pending'     => 'PENDING',
				'refunded'    => 'REFUNDED',
				'returned'    => 'RETURNED',
				'success'     => 'SUCCESS',
				'unclaimed'   => 'UNCLAIMED'

			);


			return $status;
		}

		/**
		 * @param string $payout_batch_ids
		 */
		public static function delete_payout_item( $payout_batch_id ) {

			global $wpdb;
			$query = $wpdb->prepare( "DELETE FROM $wpdb->yith_payout_item WHERE payout_batch_id = '%s'", $payout_batch_id );

			$wpdb->query( $query );
		}

		/**
		 * anonymize receiver email
		 *
		 * @param $id
		 */
		public static function anonymize_email_transaction( $id ) {
			$anonymize_email = function_exists( 'wp_privacy_anonymize_data' ) ? wp_privacy_anonymize_data( 'email' ) : 'deleted@email.com';

			global $wpdb;

			$wpdb->update( $wpdb->yith_payout_item, array( 'receiver' => $anonymize_email ), array( 'ID' => $id ) );
		}

	}
}

if ( ! function_exists( 'YITH_Payout_Items' ) ) {
	function YITH_Payout_Items() {

		return YITH_Payout_Items::get_instance();
	}
}

YITH_Payout_Items();