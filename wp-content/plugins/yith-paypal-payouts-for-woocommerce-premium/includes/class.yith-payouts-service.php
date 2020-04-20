<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_PayOuts_Service' ) && class_exists( 'YITH_Abstract_PayOuts_Service' ) ) {

	class YITH_PayOuts_Service extends YITH_Abstract_PayOuts_Service {

		protected static $instance;

		public function __construct() {

			parent::__construct();

			if ( $this->check_service_configuration() ) {

				add_action( 'woocommerce_order_status_changed', array( $this, 'process_payment' ), 20, 3 );
				add_filter( 'yith_payouts_register_new_payout', array(
					$this,
					'check_is_possible_register_payout'
				), 10, 2 );

			} else {
				add_action( 'admin_notices', array( $this, 'show_gateway_notices' ) );
			}
		}

		/**
		 * @author Salvatore Strano
		 * @since 1.0.0
		 * @return YITH_PayOuts_Service
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}


		/**
		 * @param $order_id
		 * @param string $old_status
		 * @param string $new_status
		 */
		public function process_payment( $order_id, $old_status, $new_status ) {

			if ( 'completed' == $new_status ) {

				$result          = YITH_Payouts()->get_payouts( array(
					'order_id' => $order_id,
					'fields'   => array( 'sender_batch_id' )
				) );
				$sender_batch_id = isset( $result[0]['sender_batch_id'] ) ? $result[0]['sender_batch_id'] : '';
				//get_post_meta( $order_id, 'yith_payouts_sender_id', true );
				$payout = new YITH_Payout( $sender_batch_id );
				if ( '' !== $sender_batch_id && 'unprocessed' == $payout->payout_status && 'instant' === $payout->payout_mode ) {

					$args   = array(
						'sender_batch_id' => $sender_batch_id,
						'order_id'        => $order_id,
						'sender_items'    => $this->get_payout_items_by_sender_batch_id( $sender_batch_id )
					);
					$result = $this->PayOuts( $args );
					if ( $result instanceof \PayPal\Api\PayoutBatch ) {
						$payout_batch_id     = $result->getBatchHeader()->getPayoutBatchId();
						$payout_batch_status = strtolower( $result->getBatchHeader()->getBatchStatus() );


						$payout->payout_batch_id = $payout_batch_id;
						$payout->payout_status   = $payout_batch_status;
					}
				}
			}
		}

		/**
		 * check if is possible add new entry into db
		 * @author Salvatore Strano
		 * @since 1.0.0
		 *
		 * @param bool $register_payout
		 * @param int $order_id
		 *
		 * @return bool
		 */
		public function check_is_possible_register_payout( $register_payout, $order_id ) {

			return count( $this->get_payout_items( $order_id ) ) > 0;
		}

		/**
		 * Show admin notices
		 * @author Salvatore Strano
		 * @since 1.0.0
		 */
		public function show_gateway_notices() {

			if ( isset( $_GET['page'] ) && 'yith_wc_paypal_payouts_panel' == $_GET['page'] ) {

				$error = array();

				if ( empty( $this->get_application_id() ) ) {
					$error[] = __( 'Application Client ID', 'yith-paypal-payouts-for-woocommerce' );
				}

				if ( empty( $this->get_application_secret_key() ) ) {
					$error[] = __( 'Application Client Secret Key', 'yith-paypal-payouts-for-woocommerce' );
				}


				if ( count( $error ) > 0 ) {

					$message         = implode( ', ', $error );
					$options_message = _n( 'The following option is empty', 'The following options are empty', count( $error ), 'yith-paypal-payouts-for-woocommerce' );
					$error_message   = sprintf( '<div class="notice notice-error"><p><strong>%s</strong>, %s: %s</p></div>',
						__( 'YITH PayPal Payouts for WooCommerce is disabled', 'yith-paypal-payouts-for-woocommerce' ),
						$options_message,
						$message
					);

					echo $error_message;
				}


			}
		}

	}
}

/**
 * @return YITH_PayOuts_Service
 */
function YITH_PayOuts_Service() {
	return YITH_PayOuts_Service::get_instance();
}