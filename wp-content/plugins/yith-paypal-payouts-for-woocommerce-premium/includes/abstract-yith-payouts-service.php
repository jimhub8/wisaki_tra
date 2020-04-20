<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Abstract_PayOuts_Service' ) ) {

	class YITH_Abstract_PayOuts_Service {

		protected $business_email;
		protected $application_id;
		protected $application_secret_key;
		protected $enable_log;
		protected $is_sandbox;


		public function __construct() {

			$this->business_email         = get_option( 'yith_payouts_business_email', '' );
			$this->application_id         = get_option( 'yith_payouts_application_id', '' );
			$this->application_secret_key = get_option( 'yith_payouts_application_secret_key', '' );
			$this->is_sandbox             = ( 'yes' === get_option( 'yith_payouts_sandbox_mode', 'no' ) );
			$this->enable_log             = ( 'yes' === get_option( 'yith_payouts_enable_log', 'no' ) );

			add_action( 'woocommerce_checkout_order_processed', array( $this, 'register_payouts_on_checkout' ), 20, 1 );
			add_action( 'woocommerce_api_yith_payouts_response', array( $this, 'trigger_webhook_response' ) );

		}

		/**
		 * get the paypal business email
		 * @author Salvatore Strano
		 * @since 1.0.0
		 * @return string
		 */
		public function get_business_email() {

			return $this->business_email;
		}

		/**
		 * get the paypal application client id
		 * @author Salvatore Strano
		 * @since 1.0.0
		 * @return string
		 */
		public function get_application_id() {

			return $this->application_id;
		}

		/**
		 * get the paypal application client secret key
		 * @author Salvatore Strano
		 * @since 1.0.0
		 * @return string
		 */
		public function get_application_secret_key() {

			return $this->application_secret_key;
		}

		/**
		 * check if enabled sandbox mode
		 * @author Salvatore Strano
		 * @since 1.0.0
		 * @return bool
		 */
		public function is_sandbox() {

			return $this->is_sandbox;
		}

		/**
		 * check if the plugin is configured properly
		 * @author Salvatore Strano
		 * @since 1.0.0
		 * @return bool
		 */
		public function check_service_configuration() {

			$check_configuration = ( ! empty( $this->get_business_email() ) && ! empty( $this->get_application_id() ) && ! empty( $this->get_application_secret_key() ) );

			return apply_filters( 'yith_payout_check_service_configuration', $check_configuration );
		}


		/**
		 * check if is enabled the log
		 * @author Salvatore Strano
		 * @since 1.0.0
		 * @return bool
		 */
		public function is_log_enabled() {
			return $this->enable_log;
		}

		/**
		 * @author Salvatore Strano
		 * @since 1.0.0
		 * @return bool|\PayPal\Rest\ApiContext
		 */
		public function get_PayPalAPIContext() {

			$api_context = false;
			if ( $this->check_service_configuration() ) {

				$client_id  = $this->get_application_id();
				$client_key = $this->get_application_secret_key();

				$api_context = new \PayPal\Rest\ApiContext(

					new \PayPal\Auth\OAuthTokenCredential(
						$client_id, $client_key
					)
				);

				if ( defined( 'WC_LOG_DIR' ) ) {

					$log_file_name = WC_LOG_DIR . 'yith-paypal-payouts-for-woocommerce-' . date( 'Y-m-d' ) . '.log';
				} else {
					$log_file_name = YITH_PAYOUTS_DIR . "yith-paypal-payouts-for-woocommerce-" . date( 'Y-m-d' ) . ".log";
				}

				$args = array(
					'mode'           => $this->is_sandbox() ? 'sandbox' : 'live',
					'log.LogEnabled' => $this->is_log_enabled(),
					'log.FileName'   => $log_file_name,
					'log.LogLevel'   => $this->is_sandbox() ? 'DEBUG' : 'INFO',
					// PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
					'cache.enabled'  => true,
				);

				$api_context->setConfig( $args );


			}

			return $api_context;
		}

		/**
		 * $args is array with this structure
		 * array(
		 *      "recipient_type" => "EMAIL",
		 *        "receiver" => "shirt-supplier-three@mail.com",
		 *        "note" => "Thank you.",
		 *        "sender_item_id" => uniqid(),
		 *        "amount" => array(
		 *        "value" => "0.90",
		 *        "currency" => "USD"
		 *        )
		 *
		 *  )
		 *
		 * @author Salvatore Strano
		 * @since 1.0.0
		 *
		 * @param array
		 *
		 * @return \PayPal\Api\PayoutItem
		 */
		public function get_payout_item( $args ) {

			return new \PayPal\Api\PayoutItem(
				$args
			);
		}

		/**
		 * create the sender batch header
		 * @author Salvatore Strano
		 * @since 1.0.0
		 *
		 * @param int $unique_id ( should be order_id or other unique_id )
		 *
		 * @return \PayPal\Api\PayoutSenderBatchHeader
		 */
		public function get_batch_header( $unique_id ) {

			$senderBatchHeader = new \PayPal\Api\PayoutSenderBatchHeader();
			$senderBatchHeader->setSenderBatchId( $unique_id )
			                  ->setEmailSubject( "You have a payment" );

			return $senderBatchHeader;
		}

		/**
		 * this method create an array with all payout items
		 * @author Salvatore Strano
		 * @since 1.0.0
		 * @see get_payout_item
		 * @return array
		 */
		public function get_payout_items( $order_id ) {

			$order = wc_get_order( $order_id );

			$receivers = apply_filters( 'yith_payouts_receivers', get_option( 'yith_payouts_receiver_list', array() ) );
			$payout_items = array();

			if ( count( $receivers ) > 0 ) {

				$order_items = $order->get_items();
				$currency    = $order->get_currency();

				foreach ( $order_items as $item ) {


					if ( apply_filters( 'yith_payouts_include_item', true, $item, $order_id ) ) {
						$line_total = $order->get_line_total( $item, true );

						foreach ( $receivers as $receiver ) {

							$user_id         = $receiver['user_id'];
							$user_email      = $receiver['paypal_email'];
							$commission_rate = floatval( $receiver['commission_rate'] / 100 );

							if ( ! isset( $payout_items[ $user_id ] ) ) {
								$payout_items[ $user_id ] = array(
									"recipient_type" => "EMAIL",
									"receiver"       => $user_email,
									"note"           => "Thank you.",

									"amount" => array(
										"value"    => $commission_rate * $line_total,
										"currency" => $currency
									)
								);
							} else {
								$payout_items[ $user_id ]['amount']['value'] += $commission_rate * $line_total;
							}
						}
					}
				}
			}

			return apply_filters( 'yith_payout_items_receiver', $payout_items, $order_id );
		}

		/**
		 * @author Salvatore Strano
		 *
		 * @param string $sender_batch_id
		 *
		 * @since 1.0.0
		 * @return array
		 */
		public function get_payout_items_by_sender_batch_id( $sender_batch_id ) {

			$payout_items = YITH_Payout_Items()->get_payout_items( array(
				'sender_batch_id' => $sender_batch_id,
				'fields'          => array(
					'receiver',
					'amount',
					'currency'
				)
			) );

			$items = array();

			foreach ( $payout_items as $payout_item ) {

				$item = array(
					'recipient_type' => 'EMAIL',
					"receiver"       => $payout_item['receiver'],
					"note"           => "Thank you.",

					"amount" => array(
						"value"    => $payout_item['amount'],
						"currency" => $payout_item['currency']
					)

				);

				$items[] = $item;
			}

			return $items;
		}


		/**
		 * make a payout for an order
		 * @author Salvatore Strano
		 * @since 1.0.0
		 *
		 * @param array $args
		 *
		 * @return \PayPal\Api\PayoutBatch
		 */
		public function PayOuts( $args ) {

			$payouts     = new \PayPal\Api\Payout();
			$api_context = $this->get_PayPalAPIContext();

			$sender_batch_id = isset( $args['sender_batch_id'] ) ? $args['sender_batch_id'] : '';
			$sender_items    = isset( $args['sender_items'] ) ? $args['sender_items'] : array();
			$output          = null;
			if ( ! empty( $sender_batch_id ) && count( $sender_items ) > 0 ) {
				$senderBatchHeader = $this->get_batch_header( $sender_batch_id );
				$payouts->setSenderBatchHeader( $senderBatchHeader );
				foreach ( $sender_items as $item ) {

					$payout_item = $this->get_payout_item( $item );
					$payouts->addItem( $payout_item );

				}

				try {
					$output = $payouts->create( null, $api_context );
				} catch ( Exception $ex ) {


					error_log( "ERROR : " . $ex->getMessage() . ' for #' . $sender_batch_id );
				}
			}

			return $output;
		}

		/**
		 * register an entry when the order is processed
		 * @author Salvatore Strano
		 * @since 1.0.0
		 * @param $order_id
		 */
		public function register_payouts_on_checkout( $order_id ) {

			if ( apply_filters( 'yith_payouts_register_new_payout', false, $order_id ) ) {
				$payment_mode = get_option( 'yith_payouts_mode', 'instant' );

				$sender_batch_id = uniqid();
				$items           = $this->get_payout_items( $order_id );
				$args            = array(
					'order_id'        => $order_id,
					'payout_mode'     => $payment_mode,
					'sender_batch_id' => $sender_batch_id,
					'items'           => $items
				);

				$this->register_payouts( $args );

				update_post_meta( $order_id, 'yith_payouts_sender_id', $sender_batch_id );
			}
		}

		/**
		 * add a new entry into db
		 * @author Salvatore Strano
		 * @since 1.0.0
		 * @param array $payouts_info
		 */
		public function register_payouts( $payouts_info ) {

			$payout_args = array(
				'order_id'        => $payouts_info['order_id'],
				'payout_mode'     => $payouts_info['payout_mode'],
				'sender_batch_id' => $payouts_info['sender_batch_id']
			);

			YITH_Payout()->add( $payout_args );

			$payout_items = $payouts_info['items'];

			foreach ( $payout_items as $item ) {

				$args = array(
					'sender_batch_id' => $payouts_info['sender_batch_id'],
					'sender_item_id'  => isset( $item['sender_item_id'] ) ? $item['sender_item_id'] : '',
					'amount'          => $item['amount']['value'],
					'currency'        => $item['amount']['currency'],
					'receiver'        => $item['receiver']
				);

				YITH_Payout_Items::add_payout_item( $args );

			}
		}

		/**
		 * manage the webhooks PayOuts
		 * @author Salvatore Strano
		 * @since 1.0.0
		 */
		public function trigger_webhook_response() {

			$body     = @file_get_contents( 'php://input' );
			$response = json_decode( $body, true );

			$event_name = explode( '.', $response['event_type'] );
			$event_name = isset( $event_name[1] ) ? $event_name[1] : '';

			if ( $event_name === 'PAYOUTSBATCH' ) {
				$this->process_payoutsbatch_event( $response );
			} elseif ( $event_name === 'PAYOUTS-ITEM' ) {

				$this->process_payouts_item_event( $response );
			}

		}

		/**
		 * process PayOut Batch event
		 * @author Salvatore Strano
		 * @since 1.0.0
		 * @param array $response
		 */
		public function process_payoutsbatch_event( $response ) {

			$batch_header    = isset( $response['resource']['batch_header'] ) ? $response['resource']['batch_header'] : array();
			$payout_batch_id = $batch_header['payout_batch_id'];
			$sender_batch_id = $batch_header['sender_batch_header']['sender_batch_id'];
			$status          = strtolower( $batch_header['batch_status'] );

			$payout = YITH_Payout( $sender_batch_id );

			if ( empty( $payout->payout_batch_id ) ) {
				$payout->payout_batch_id = $payout_batch_id;
			}
			$payout->payout_status = $status;

			do_action( 'yith_paypal_payout_batch_change_status', $payout_batch_id, $status, $batch_header );

		}

		/**
		 * Process Payout Item
		 * @author Salvatore Strano
		 * @since 1.0.0
		 *
		 * @param array $response
		 */
		public function process_payouts_item_event( $response ) {

			$resource           = isset( $response['resource'] ) ? $response['resource'] : array();
			$payout_item_id     = $resource['payout_item_id'];
			$transaction_id     = isset( $resource['transaction_id'] ) ? $resource['transaction_id'] : '';
			$transaction_status = strtolower( $resource['transaction_status'] );
			$payout_batch_id    = $resource['payout_batch_id'];
			$payout_item_fee    = $resource['payout_item_fee'];
			$payout_item        = $resource['payout_item'];
			$sender_batch_id    = $resource['sender_batch_id'];
			$sender_item_id     = isset( $resource['payout_item']['sender_item_id'] ) ? $resource['payout_item']['sender_item_id'] : '';

			$receiver = $payout_item['receiver'];

			$args = array(
				'transaction_id'     => $transaction_id,
				'transaction_status' => $transaction_status,
				'payout_batch_id'    => $payout_batch_id,
				'payout_item_id'     => $payout_item_id,
				'sender_item_id'     => $sender_item_id,
				'amount'             => $payout_item['amount']['value'],
				'fee'                => $payout_item_fee['value'],
				'currency'           => $payout_item['amount']['currency']
			);


			YITH_Payout_Items::update_payout_item( $sender_batch_id, $receiver, $args );


			do_action( 'yith_paypal_payout_item_change_status', $payout_item_id, $transaction_status, $resource );

		}
	}
}