<?php
/*
* This file belongs to the YITH Framework.
*
* This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://www.gnu.org/licenses/gpl-3.0.txt
*/
if ( ! defined( 'YITH_WCSC_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Stripe_YWSBS_Subscription
 * @package    Yithemes
 * @since      Version 1.1.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_Stripe_YWSBS_Subscription' ) ) {

	/**
	 * Class YITH_Stripe_YWSBS_Subscription
	 *
	 */
	class YITH_Stripe_YWSBS_Subscription {

		/**
		 * YITH_Stripe_YWSBS_Subscription Instance
		 *
		 * @var YITH_Stripe_YWSBS_Subscription
		 * @since  1.1.0
		 * @access protected
		 */
		protected static $_instance = null;

		/**
		 * Main plugin Instance
		 *
		 * @return YITH_Stripe_YWSBS_Subscription instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Construct
		 *
		 * @since  1.1.0
		 */
		public function __construct() {

			add_filter( 'woocommerce_payment_gateways', array( $this, 'add_stripe_connect_sources_gateway' ), 10 );
			add_filter( 'ywsbs_max_failed_attempts_list', array( $this, 'add_failed_attempts' ) );
			add_filter( 'ywsbs_get_num_of_days_between_attemps', array( $this, 'add_num_of_days_between_attempts' ) );
			add_filter( 'ywsbs_from_list', array( $this, 'add_from_list' ) );

			//scheduling
			add_action( 'ywsbs_renew_subscription', array( $this , 'add_meta_stripe_connect_to_renew_order' ), 10, 2 );
		}

		/**
		 * Add to renew order post meta to initialize some fields to the query
		 * These fields will be added only if the subscription is payed with Stripe Connect
		 *
		 * @param $order_id
		 * @param $subscription_id
		 */
		public function add_meta_stripe_connect_to_renew_order ( $order_id, $subscription_id ) {

			$yith_stripe_connect_source_id = get_post_meta( $subscription_id, 'yith_stripe_connect_source_id' );

			if ( ! empty( $yith_stripe_connect_source_id ) ) {
				$order = wc_get_order( $order_id );
				$order->update_meta_data( 'yith_stripe_connect_source_id', $yith_stripe_connect_source_id );
				$order->save();
			}
		}

		/**
		 * Add this gateway in the list of maximum number of attempts to do.
		 *
		 * @param $list
		 *
		 * @return mixed
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_failed_attempts( $list ) {
			$list[ YITH_Stripe_Connect::$gateway_id ] = 4;

			return $list;
		}

		/**
		 * Add this gateway in the list of maximum number of attempts to do.
		 *
		 * @param $list
		 *
		 * @return mixed
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_num_of_days_between_attempts( $list ) {
			$list[ YITH_Stripe_Connect::$gateway_id ] = 5;

			return $list;
		}

		/**
		 * Add this gateway in the list "from" to understand from where the
		 * update status is requested.
		 *
		 * @param $list
		 *
		 * @return mixed
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_from_list( $list ) {
			$list[ YITH_Stripe_Connect::$gateway_id ] = YITH_Stripe_Connect_Gateway::instance()->get_method_title();

			return $list;
		}

		/**
		 * Replace the main gateway with the sources gateway.
		 *
		 * @param $gateways
		 *
		 * @return array
		 */
		public function add_stripe_connect_sources_gateway( $methods ) {

			foreach ( $methods as $key => $method ) {
				/**@var WC_Payment_Gateway_CC $method **/
				if ( 'YITH_Stripe_Connect_Gateway' == $method  ) {
					$methods[ $key ] = 'YITH_Stripe_Connect_Source_Gateway';
				}
			}

			return $methods;
		}

	}
}