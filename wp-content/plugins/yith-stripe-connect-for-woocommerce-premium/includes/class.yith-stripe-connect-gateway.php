<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCSC_PATH' ) ) {
	exit( 'Direct access forbidden.' );
}

use \Stripe\Error;

/**
 *
 *
 * @class      YITH_Stripe_Connect_Gateway
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Francisco Javier Mateo <francisco.mateo@yithemes.com>
 *
 */

if ( ! class_exists( 'YITH_Stripe_Connect_Gateway' ) ) {
	/**
	 * Class YITH_Stripe_Connect_Gateway
	 *
	 * @author Francisco Javier Mateo <francisco.mateo@yithemes.com>
	 */
	class YITH_Stripe_Connect_Gateway extends WC_Payment_Gateway_CC {

		protected static $_instance = null;

		/** @var bool Whether or not logging is enabled */
		public $log_enabled = false;

		/** @var WC_Logger Logger instance */
		public $log = false;

		/**
		 * @var \YITH_Stripe_Connect_API_Handler
		 */
		public $api_handler = null;

		/**
		 * Constructor for the gateway.
		 */
		public function __construct() {
			$this->id                 = YITH_Stripe_Connect::$gateway_id;
			$this->has_fields         = false;
			$this->order_button_text  = apply_filters('yith_wcsc_order_button_text',_x( 'Proceed to Stripe Connect', 'Order button text on Stripe Connect Gateway', 'yith-stripe-connect-for-woocommerce' ));
			$this->method_title       = _x( 'Stripe Connect', 'The Gateway title, no need translation :D', 'yith-stripe-connect-for-woocommerce' );
			$this->method_description = _x( 'Stripe Connect Gateway for WooCommerce', 'Stripe Connect Gateway description', 'yith-stripe-connect-for-woocommerce' );
			$this->supports           = array(
				'products'
			);

			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();

			// Define user set variables.
			$this->title             = $this->get_option( 'label-title' );
			$this->description       = $this->get_option( 'label-description' );
			$this->description       = ! empty( $this->description ) ? $this->description : __( 'Stripe Connect Gateway', 'yith-stripe-connect-for-woocommerce' );  //@since 1.0.3
			$this->test_live         = 'yes' === $this->get_option( 'test-live', 'no' );
			$this->log_enabled       = 'yes' === $this->get_option( 'log', 'no' );
			$this->public_key        = ( 'yes' == $this->test_live ) ? $this->get_option( 'api-public-test-key' ) : $this->get_option( 'api-public-live-key' ); // Switch the plublic key between test and live mode.
			$this->credit_cards_logo = $this->get_option( 'credit-cards-logo', array() );
			$this->show_name_on_card = $this->get_option( 'show-name-on-card', 'no' );

			if ( $this->log_enabled ) {
				$this->log = new WC_Logger();
			}

			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array(
				$this,
				'process_admin_options'
			) );

			add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
			add_filter( 'woocommerce_credit_card_form_fields', array( $this, 'credit_form_add_fields' ), 10, 2 );



		}

		public function payment_scripts() {

			if ( ! $this->is_available() || ! ( is_checkout() || is_wc_endpoint_url( 'add-payment-method' ) ) ) {
				return;
			}

			$debug_enabled   = defined( 'WP_DEBUG' ) ? WP_DEBUG : false;
			$prefix          = ! $debug_enabled ? '.min' : '';
			$js_dependencies = array( 'jquery', 'stripe-js' );

			wp_register_script( 'stripe-js', 'https://js.stripe.com/v2/', array( 'jquery' ), false, true );

			wp_register_script( 'yith-stripe-connect-js', YITH_WCSC_ASSETS_URL . 'js/script-yith-sc-checkout' . $prefix . '.js', $js_dependencies, YITH_WCSC_VERSION, true );

			wp_localize_script( 'yith-stripe-connect-js', 'yith_stripe_connect_info', array(
				'public_key'     => $this->public_key,
				//'mode'           => $this->mode,
				'card.name'      => __( 'A valid Name on Card is required.', 'yith-stripe-connect-for-woocommerce' ),
				'card.number'    => __( 'The credit card number seems to be invalid.', 'yith-stripe-connect-for-woocommerce' ),
				'card.cvc'       => __( 'The CVC number seems to be invalid.', 'yith-stripe-connect-for-woocommerce' ),
				'card.expire'    => __( 'The expiration date seems to be invalid.', 'yith-stripe-connect-for-woocommerce' ),
				'billing.fields' => __( 'You have to add extra information to checkout.', 'yith-stripe-connect-for-woocommerce' ),
			) );

			wp_register_style( 'yith-stripe-connect-css', YITH_WCSC_ASSETS_URL . 'css/style-yith-sc-checkout.css', null, YITH_WCSC_VERSION );

			wp_enqueue_script( 'yith-stripe-connect-js' );
			wp_enqueue_style( 'yith-stripe-connect-css' );
		}

		/**
		 * Handling payment and processing the order.
		 *
		 * @param int $order_id
		 *
		 * @return array
		 * @since 1.0.0
		 * @throws Error\Api
		 */
		public function process_payment( $order_id ) {
			$order                = wc_get_order( $order_id );
			$this->_current_order = $order;
			$this->log( 'info', 'Generating payment form for order ' . $order->get_order_number() . '.' );

			return $this->process_standard_payment();
		}

		/**
		 * Handling payment and processing the order.
		 *
		 * @param WC_Order $order
		 *
		 * @return array
		 * @throws Error\Api
		 * @since 1.0.0
		 */
		protected function process_standard_payment( $order = null ) {
			if ( empty( $order ) ) {
				$order = $this->_current_order;
			}

			try {

				// Initializate SDK and set private key
				$this->init_stripe_connect_api();

				// Card selected during payment
				$this->token = $this->get_token();

				if ( empty( $this->token ) ) {
					$error_msg = __( 'Please make sure that your card details have been entered correctly and that your browser supports JavaScript.', 'yith-stripe-connect-for-woocommerce' );

					if ( 'test' == $this->test_live ) {
						$error_msg .= ' ' . __( 'Developers: Please make sure that you\'re including jQuery and that there are no JavaScript errors in the page.', 'yith-stripe-connect-for-woocommerce' );
					}

					$this->log( 'error', 'Wrong token ' . $this->token . ': ' . print_r( $_POST, true ) );

					throw new Error\Api( $error_msg );
				}

				// pay
				$response = $this->pay( $order );

				if ( $response === true ) {
					$response = array(
						'result'   => 'success',
						'redirect' => $this->get_return_url( $order )
					);

				} elseif ( is_a( $response, 'WP_Error' ) ) {
					throw new Error\Api( $response->get_error_message( 'stripe_error' ) );
				}

				return $response;

			} catch ( Error\Base $e ) {
				$body    = $e->getJsonBody();
				$message = $e->getMessage();

				if ( $body ) {
					$err = $body['error'];
					if ( isset( $this->errors[ $err['code'] ] ) ) {
						$message = $this->errors[ $err['code'] ];
					}

					$this->log( 'info', 'Stripe Error: ' . $e->getHttpStatus() . ' - ' . print_r( $e->getJsonBody(), true ) );

					// add order note
					$order->add_order_note( 'Stripe Error: ' . $e->getHttpStatus() . ' - ' . $e->getMessage() );
					$order_id = yit_get_prop( $order, 'id' );

					// add block if there is an error on card
					if ( $err['type'] == 'card_error' ) {
						$this->add_block( "order_id={$order_id}" );
						WC()->session->refresh_totals = true;
					}
				}

				wc_add_notice( $message, 'error' );

				return array(
					'result'   => 'fail',
					'redirect' => ''
				);

			}
		}

		/**
		 * Retrieve source selected for current subscription
		 *
		 * @return string
		 */
		protected function get_source() {
			$card_id = ( isset( $_POST['wc-yith-stripe-connect-payment-token'] ) && 'new' != $_POST['wc-yith-stripe-connect-payment-token'] ) ? $_POST['wc-yith-stripe-connect-payment-token'] : false;

			if ( $card_id ) {
				$token = WC_Payment_Tokens::get( $card_id );
				if ( $token && $token->get_user_id() === get_current_user_id() ) {
					$card_id = $token->get_token();
				}
			}

			return $card_id;
		}

		/**
		 * Get token card from post
		 *
		 * @access protected
		 * @return string
		 * @author Francisco Javier Mateo
		 */
		protected function get_token() {
			$card_id = $this->get_source();

			if( ! $card_id ) {
				if ( isset( $_POST['stripe_connect_token'] ) ) {
					$card_id = $_POST['stripe_connect_token'];
				} else {
					return 'new';
				}
			}

			return apply_filters( 'yith_stripe_connect_selected_card', $card_id );
		}

		/**
		 * Performs the payment on Stripe
		 *
		 * @param $order  WC_Order
		 *
		 * @return array|WP_Error
		 * @since 1.0.0
		 */
		public function pay( $order = null ) {
			// Initializate SDK and set private key
			$this->init_stripe_connect_api();

			// get amount
			$amount = $order->get_total();

			if ( 0 == $amount ) {
				// Payment complete
				$order->payment_complete();

				return true;
			}

			if ( $amount * 100 < 50 ) {
				return new WP_Error( 'stripe_error', __( 'Sorry, the minimum order total allowed to use this payment method is 0.50.',
					'yith-stripe-connect-for-woocommerce' ) );
			}

			$args = array(
				'amount'         => yith_wcsc_get_amount( $amount ),
				'description' => apply_filters( 'yith_wcsc_charge_description',  sprintf( __( '%s - Order %s', 'yith-stripe-connect-for-woocommerce' ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() ),
				'currency'       => yit_get_prop( $order, 'currency' ),
				'source'         => $this->get_token(),
				'transfer_group' => yit_get_order_id( $order )
			);

			if( $this->get_source() ){
				$source_gateway = YITH_Stripe_Connect_Source_Gateway::instance();

				$args['customer'] = $source_gateway->get_customer( $order );
			}

			$charge = $this->api_handler->create_charge( $args );

			$this->log( 'info', 'Stripe Connect Request: ' . print_r( $args, true ) );

			if ( ! is_a( $charge, 'Stripe\Charge' ) && is_array( $charge ) && isset( $charge['error_charge'] ) ) {
				$this->log( 'error', 'Stripe Connect Response: A problem happens when tried to proceed the payment, we can\'t get the charge result from Stripe server. Probably card data wrong...' . print_r( $args, true ) );

				return new WP_Error( 'stripe_error', $charge['error_charge'] );
			}

			$this->log( 'info', 'Stripe Connect Response: ' . print_r( $charge, true ) );

			// Payment complete
			$is_payment_complete = $order->payment_complete( $charge->id );

			if( $is_payment_complete ){
				do_action( 'yith_wcsc_payment_complete', $order->get_id(), $charge->id );
			}

			// Add order note
			$order->add_order_note( sprintf( __( 'Stripe Connect payment approved (ID: %s)', 'yith-stripe-connect-for-woocommerce' ), $charge->id ) );

			// Remove cart
			WC()->cart->empty_cart();

			// Return thank you page redirect
			return true;

		}


		public function credit_form_add_fields( $fields, $id ) {


			$cvc_field = '<p class="form-row form-row-last validate-required" >
			<label for="' . esc_attr( $this->id ) . '-card-cvc">' . esc_html__( 'Card code', 'woocommerce' ) . ' <span class="required">*</span></label>
    
			<input id="' . esc_attr( $this->id ) . '-card-cvc" class="input-text wc-credit-card-form-card-cvc" inputmode="numeric" autocomplete="off" autocorrect="no" autocapitalize="no" spellcheck="no" type="tel" maxlength="4" placeholder="' . esc_attr__( 'CVC', 'woocommerce' ) . '" ' . $this->field_name( 'card-cvc' ) . ' style="width:100px" />
			</p>';

			$default_fields = array(
				'card-number-field' => '<p class="form-row form-row-wide validate-required ">
				<label for="' . esc_attr( $this->id ) . '-card-number">' . esc_html__( 'Card number', 'woocommerce' ) . ' <span class="required">*</span></label>
				<input id="' . esc_attr( $this->id ) . '-card-number" class="input-text wc-credit-card-form-card-number" inputmode="numeric" autocomplete="cc-number" autocorrect="no" autocapitalize="no" spellcheck="no" type="tel" placeholder="&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;" ' . $this->field_name( 'card-number' ) . ' />
				
				</p>',
				'card-expiry-field' => '<p class="form-row form-row-first validate-required">
				<label for="' . esc_attr( $this->id ) . '-card-expiry">' . esc_html__( 'Expiry (MM/YY)', 'woocommerce' ) . ' <span class="required">*</span></label>
				<input id="' . esc_attr( $this->id ) . '-card-expiry" class="input-text wc-credit-card-form-card-expiry" inputmode="numeric" autocomplete="cc-exp" autocorrect="no" autocapitalize="no" spellcheck="no" type="tel" placeholder="' . esc_attr__( 'MM / YY', 'woocommerce' ) . '" ' . $this->field_name( 'card-expiry' ) . ' />
				</p>',
			);

			if ( $this->show_name_on_card == 'yes' ) {
				$default_fields = array_merge( array(
					'card-name-field' => '<p class="form-row form-row-wide">
						<label for="' . esc_attr( $this->id ) . '-card-name">' . apply_filters( 'yith_wccs_name_on_card_label', __( 'Name on Card', 'yith-stripe-connect-for-woocommerce' ) ) . ' <span class="required">*</span></label>
						
						<input id="' . esc_attr( $this->id ) . '-card-name" class="input-text wc-credit-card-form-card-name" type="text" autocomplete="off" placeholder="' . __( 'Name on Card', 'yith-stripe-connect-for-woocommerce' ) . '" ' . $this->field_name( 'card-name' ) . ' />

						</p>'
				), $default_fields );
			}
			if ( ! $this->supports( 'credit_card_form_cvc_on_saved_method' ) ) {
				$default_fields['card-cvc-field'] = $cvc_field;
			}

			return $default_fields;
		}

		/**
		 * Return the gateway icons.
		 *
		 * @return string
		 */
		public function get_icon() {
            $icon_html = apply_filters( 'yith_wc_stripe_connect_credit_cards_logos', '', $this->credit_cards_logo );
            $width = apply_filters('yith_wc_stripe_connect_credit_cards_logos_width','40px');
			foreach ( $this->credit_cards_logo as $logo_card ) {
				$icon_html .= '<img class="yith_wcsc_icon" src="' . YITH_WCSC_ASSETS_URL . 'images/' . esc_attr( $logo_card ) . '.svg" alt="' . $logo_card . '" width="' . $width . '" />';
			}

			return $icon_html;
		}

		/**
		 * Log to txt file
		 *
		 * @param $message
		 *
		 * @since 1.0.0
		 */
		public function log( $level, $message ) {
			if ( isset( $this->log, $this->log_enabled ) && $this->log_enabled ) {
				$this->log->log( $level, $message, array( 'source' => 'stripe-connect', '_legacy' => true ) );
			}
		}

		/**
		 * Initialise Gateway Settings Form Fields.
		 */
		public function init_form_fields() {
			$this->form_fields = include( YITH_WCSC_OPTIONS_PATH . 'settings-sc-gateway.php' );
		}

		public function init_stripe_connect_api() {
			if ( is_a( $this->api_handler, 'YITH_Stripe_Connect_API_Handler' ) ) {
				return;
			}
			$this->api_handler = YITH_Stripe_Connect_API_Handler::instance();
		}

		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

	}

}