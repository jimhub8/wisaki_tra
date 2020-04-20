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

use \Stripe\Error;

/**
 *
 *
 * @class      YITH_Stripe_Connect_Sources_Gateway
 * @package    Yithemes
 * @since      Version 1.1.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_Stripe_Connect_Sources_Gateway' ) ) {

	/**
	 * Class YITH_Stripe_Connect_Sources_Gateway
	 *
	 * This class replace YITH_Stripe_Connect_Gateway when the plugin YITH WooCommerce Subscription Premium from 1.4.6 is installed.
	 *
	 * @since 1.1.0
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	class YITH_Stripe_Connect_Source_Gateway extends YITH_Stripe_Connect_Gateway {

		/**
		 * Instance of YITH_Stripe_Connect_Source_Gateway
		 * @var null
		 */
		protected static $_instance = null;

		/**
		 * The domain of this site used to identifier the website from Stripe.
		 * @var string
		 */
		public $instance_url = '';

		/**
		 * Return the instance of Gateway
		 *
		 * @return null|YITH_Stripe_Connect_Gateway|YITH_Stripe_Connect_Source_Gateway
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
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
			parent::__construct();

			$this->save_cards   = true;
			$this->instance_url = preg_replace( '/http(s)?:\/\//', '', site_url() );

			$this->supports = array( 'products', 'tokenization', 'yith_subscriptions', 'yith_subscriptions_scheduling', 'yith_subscriptions_pause', 'yith_subscriptions_multiple', 'yith_subscriptions_payment_date', 'yith_subscriptions_recurring_amount' );

			//Pay the renew orders
			add_action( 'ywsbs_pay_renew_order_with_'.$this->id, array( $this, 'pay_renew_order' ), 10 , 2 );

			// token hooks - Update token when the customer edit them from My Account Page
			add_action( 'woocommerce_payment_token_deleted', array( $this, 'delete_token_from_stripe' ), 10, 2 );
			add_action( 'woocommerce_payment_token_set_default', array( $this, 'set_default_token_on_stripe' ), 10, 2 );

			add_filter( 'woocommerce_credit_card_form_fields', array( $this, 'credit_form_add_fields' ), 20, 2 );
		}

		/**
		 * Pay the order.
		 *
		 * If on cart there are subscription products proceed with this class, otherwise call the parent class.
		 *
		 * @param WC_Order $order
		 *
		 * @return array|bool|WP_Error
		 * @throws Error\Api
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @since 1.1.0
		 */
		public function pay( $order = null ) {

			if ( ! YITH_WC_Subscription()->cart_has_subscriptions() ) {
				return parent::pay( $order );
			}

			// Initialize SDK and set private key
			$this->init_stripe_connect_api();

			$this->token = isset( $_REQUEST['stripe_connect_source'] ) ? wc_clean( $_REQUEST['stripe_connect_source'] ) : '';

			// Card selected during payment
			$selected_card = $this->get_credit_card_num();

			// Set the token with card ID selected
			if ( $this->save_cards && 'new' != $selected_card && empty( $this->token ) ) {
				$this->token = $selected_card;
			}

			//without token it is not possible charge the customer
			if ( empty( $this->token ) ) {
				$error_msg = __( 'Please make sure that your card details have been entered correctly and that your browser supports JavaScript.', 'yith-stripe-connect-for-woocommerce' );

				if ( $this->test_live ) {
					$error_msg .= ' ' . __( 'Developers: Please make sure that you\'re including jQuery and that there are no JavaScript errors in the page.', 'yith-stripe-connect-for-woocommerce' );
				}

				$this->log( 'error', 'Wrong token ' . $this->token . ': ' . print_r( $_POST, true ) );

				throw new Error\Api( $error_msg );
			}

			//Get the Stripe customer from order
			$customer = $this->get_customer( $order );

			// retrieve card from token and store it as default payment method for next payment
			if ( 'new' == $selected_card && ! empty( $this->token ) ) {
				$card = $this->api_handler->get_card( $customer, $this->token );

				if ( $card ) {
					$this->api_handler->set_default_card( $customer, $card->id );
					$this->save_token( $card );
				}
			} elseif ( ! empty( $this->token ) ) {
				$this->api_handler->set_default_card( $customer, $this->token );
				$this->set_default_token( $this->token );
			}

			// get amount
			$amount        = $order->get_total();
			$order_id      = $order->get_id();
			$subscriptions = $order->get_meta( 'subscriptions' );

			// if we cannot retrieve subscriptions from order meta, check session
			if( empty( $subscriptions ) && ! is_null( WC()->session ) ){
				$order_args = WC()->session->get( 'ywsbs_order_args', array() );
				if( isset( $order_args['subscriptions'] ) ){
					$subscriptions = $order_args['subscriptions'];
				}

				WC()->session->set( 'ywsbs_order_args', array() );
			}

			foreach ( $subscriptions as $subscription_id ) {
				update_post_meta( $subscription_id, 'yith_stripe_connect_customer_id', $customer->id );
				update_post_meta( $subscription_id, 'yith_stripe_connect_source_id', $this->token );
			}

			if ( 0 == $amount ) {
				// Payment complete
				$order->payment_complete();
				return true;
			}

			if ( $amount * 100 < 50 ) {
				$error_msg = __( 'Sorry, the minimum order total allowed to use this payment method is 0.50.', 'yith-stripe-connect-for-woocommerce' );
				ywsbs_register_failed_payment( $order, $error_msg );
				return new WP_Error( 'stripe_error', $error_msg );
			}

			//Charge the customer
			//APPLY_FILTER: yith_wcsc_charge_description : filtering the charge description : blog name and order number are passed on filter
			$args = array(
				'amount'         => yith_wcsc_get_amount( $amount ),
				'description'    => apply_filters( 'yith_wcsc_charge_description', sprintf( __( '%s - Order %s', 'yith-stripe-connect-for-woocommerce' ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() ),
				'currency'       => $order->get_currency(),
				'source'         => $this->token,
				'transfer_group' => $order_id,
				'customer'       => $customer
			);

			$charge = $this->api_handler->create_charge( $args );

			$this->log( 'info', 'Stripe Connect Request: ' . print_r( $args, true ) );

			//Error during the payment
			if ( ! is_a( $charge, 'Stripe\Charge' ) && is_array( $charge ) && isset( $charge['error_charge'] ) ) {

				$this->log( 'error', 'Stripe Connect Response: A problem happens when tried to proceed the payment, we can\'t get the charge result from Stripe server. Probably card data wrong...' . print_r( $args, true ) );

				ywsbs_register_failed_payment( $order,  $charge['error_charge'] );

				return new WP_Error( 'stripe_error', $charge['error_charge'] );
			}

			$this->log( 'info', 'Stripe Connect Response: ' . print_r( $charge, true ) );

			foreach ( $subscriptions as $subscription_id ) {

				$this->log( 'info', sprintf( __( 'Stripe Connect processed successfully. Subscription %s. Order %s. (Transaction ID: %s)', 'yith-stripe-connect-for-woocommerce' ), $subscription_id, $order_id, $charge->id ) );

				update_post_meta( $subscription_id, 'transaction_id', $charge->id );

			}

			// Payment complete
			$is_payment_complete = $order->payment_complete( $charge->id );

			if ( $is_payment_complete ) {
				//DO_ACTION: yith_wcsc_payment_complete : do action after that the payment is completed : order id and charge id are passed
				do_action( 'yith_wcsc_payment_complete', $order->get_id(), $charge->id );
			}

			// Add order note
			$order->add_order_note( sprintf( __( 'Stripe Connect payment approved (ID: %s)', 'yith-stripe-connect-for-woocommerce' ), $charge->id ) );

			// Remove cart
			WC()->cart->empty_cart();

			// Return thank you page redirect
			return true;

		}

		/**
		 * Pay the renew order.
		 *
		 * It is triggered by ywsbs_pay_renew_order_with_{gateway_id} action
		 *
		 * @param WC_Order $order
		 *
		 * @return array|bool|WP_Error
		 * @throws Error\Api
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @since 1.1.0
		 */
		public function pay_renew_order( $order = null, $manually = false ) {

			if ( is_null( $order ) ) {
				return false;
			}

			$is_a_renew = $order->get_meta( 'is_a_renew' );
			$has_source = $order->get_meta( 'yith_stripe_connect_source_id' );

			if ( 'yes' !== $is_a_renew || empty( $has_source ) ) {
				return false;
			}

			// Initialize SDK and set private key
			$this->init_stripe_connect_api();

			$amount          = $order->get_total();
			$subscriptions   = $order->get_meta( 'subscriptions' );
			$order_id        = $order->get_id();
			$subscription_id = $subscriptions ? $subscriptions[0] : false;
			$general_failed_message = sprintf( __( 'Failed payment for order #%s',  'yith-stripe-connect-for-woocommerce' ), $order->get_order_number() );

			if ( ! $subscription_id ) {
				$error_msg = sprintf( __( 'Sorry, any subscription is found for this order: %s', 'yith-stripe-connect-for-woocommerce' ), $order_id );
				$this->log( 'error', $error_msg );
				return false;
			}

			if ( 0 == $amount ) {
				// Payment complete
				$order->payment_complete();
				return true;
			}

			if ( $amount * 100 < 50 ) {
				$error_msg = __( 'Sorry, the minimum order total allowed to use this payment method is 0.50.', 'yith-stripe-connect-for-woocommerce' );
				$this->log( 'error', $error_msg );
				if( $manually ){
					wc_add_notice( $general_failed_message, 'error');
				}else{
					ywsbs_register_failed_payment( $order,  $error_msg );
				}
				return false;
			}

			$user_id        = get_post_meta( $subscription_id, 'user_id', true );

			if ( $user_id != 0 ) {
				$local_customer = YITH_Stripe_Connect_Customer()->get_usermeta_info( $user_id );
				$stripe_user_id = $local_customer['id'];
				$source_id      = $this->get_valid_source_id( $user_id, $local_customer, $subscription_id );
			} else {
				$stripe_user_id = get_post_meta( $subscription_id, 'yith_stripe_connect_customer_id', true );
				$source_id      = get_post_meta( $subscription_id, 'yith_stripe_connect_source_id', true );
			}

			if ( ! $source_id ) {
				$error_msg = sprintf( __( 'Sorry, any card is registered to pay the order renew %s for subscription %s .', 'yith-stripe-connect-for-woocommerce' ), $order_id, $subscription_id );
				if( $manually ){
					wc_add_notice( $general_failed_message, 'error');
				}else{
					ywsbs_register_failed_payment( $order,  $error_msg );
				}

				$this->log( 'warning', $error_msg );
				return false;
			}

			$customer = $this->api_handler->get_customer( $stripe_user_id );

			$args = array(
				'amount'         => yith_wcsc_get_amount( $amount ),
				'description'    => apply_filters( 'yith_wcsc_charge_description', sprintf( __( '%s - Order %s', 'yith-stripe-connect-for-woocommerce' ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() ),
				'currency'       => yit_get_prop( $order, 'currency' ),
				'source'         => $source_id,
				'transfer_group' => yit_get_order_id( $order ),
				'customer'       => $stripe_user_id
			);

			$charge = $this->api_handler->create_charge( $args );

			$this->log( 'info', 'Stripe Connect Request: ' . print_r( $args, true ) );

			//Error during the payment
			if ( ! is_a( $charge, 'Stripe\Charge' ) && is_array( $charge ) && isset( $charge['error_charge'] ) ) {
				$this->log( 'error', 'Stripe Connect Response: A problem happens when tried to proceed the payment, we can\'t get the charge result from Stripe server. Probably card data wrong...' . print_r( $args, true ) );
				if( $manually ){
					wc_add_notice( $general_failed_message, 'error');
				}else{
					ywsbs_register_failed_payment( $order,  $charge['error_charge'] );
				}
				return false;
			}

			$this->log( 'info', 'Stripe Connect Response: ' . print_r( $charge, true ) );
			$this->log( 'info', sprintf( __( 'Stripe Connect processed successfully. Subscription #%s. Order #%s. (Transaction ID: %s)', 'yith-stripe-connect-for-woocommerce' ), $subscription_id, $order_id, $charge->id ) );

			// update renew order
			$order->update_meta_data( 'yith_stripe_connect_source_id', $source_id );
			$order->update_meta_data( 'yith_stripe_connect_customer_id', $customer->id );
			$order->save();

			// Payment complete
			$is_payment_complete = $order->payment_complete( $charge->id );

			if ( $is_payment_complete ) {
				do_action( 'yith_wcsc_payment_complete', $order->get_id(), $charge->id );
				if ( $manually ) {
					wc_add_notice( sprintf( __( 'Payment approved for order #%s', 'yith-stripe-connect-for-woocommerce' ), $order->get_order_number() ), 'success' );
				}
				// Add order note
				$order->add_order_note( sprintf( __( 'Stripe Connect payment approved (ID: %s)', 'yith-stripe-connect-for-woocommerce' ), $charge->id ) );
			}

			// Return thank you page redirect
			return true;
		}

		/**
		 * Get a valid token useful to pay the renew order.
		 *
		 * @param $user_id
		 * @param $local_customer
		 * @param $subscription_id
		 *
		 * @return bool|string
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @since 1.1.0
		 */
		public function get_valid_source_id( $user_id, $local_customer, $subscription_id ) {

			//Check first if the default payment token is valid
			$default_payment_method = WC_Payment_Tokens::get_customer_default_token( $user_id );

			if ( $default_payment_method->get_gateway_id() == $this->id ) {
				$token = $default_payment_method->get_token();
				//update the default source on Stripe Connect Customer
				if( isset( $local_customer['default_source'] ) && !empty( $local_customer['default_source'] ) && $token != $local_customer['default_source'] ){
					YITH_Stripe_Connect_Customer()->update_usermeta_info( $user_id, array(
						'default_source' => $token
					) );
				}
				return $token;
			}

			//Check if in local customer there's registered a valid token
			$registered_payments = WC_Payment_Tokens::get_customer_tokens( $user_id, $this->id );
			$source_id           = get_post_meta( $subscription_id, 'yith_stripe_connect_source_id', true );

			if ( isset( $local_customer['default_source'] ) ) {
				foreach ( $registered_payments as $registered_payment ) {
					$registered_token = $registered_payment->get_token();
					if ( $registered_token == $local_customer['default_source'] ) {
						return $registered_token;
					}
				}

				if ( $source_id == $local_customer['default_source'] ) {
					return false;
				}
			}

			//Check if in subscription there's registered a valid token
			if ( ! empty( $source_id ) ) {
				foreach ( $registered_payments as $registered_payment ) {
					$registered_token = $registered_payment->get_token();
					if ( $registered_token == $source_id ) {
						if( isset( $local_customer['default_source'] ) && !empty( $local_customer['default_source'] ) && $registered_token != $local_customer['default_source'] ){
							YITH_Stripe_Connect_Customer()->update_usermeta_info( $user_id, array(
								'default_source' => $registered_token
							) );
						}
						return $registered_token;
					}
				}
			}

			return false;
		}

		/**
		 * Save the token on db.
		 *
		 * @param \Stripe\Card $card
		 *
		 * @return bool|WC_Payment_Token|WC_Payment_Token_CC
		 * @throws Error\Api
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @since 1.1.0
		 */
		public function save_token( $card = null ) {

			if ( ! is_user_logged_in() || ! $this->save_cards ) {
				return false;
			}

			$this->init_stripe_connect_api();

			$user           = wp_get_current_user();
			$local_customer = YITH_Stripe_Connect_Customer()->get_usermeta_info( $user->ID );
			$customer       = ! empty( $local_customer['id'] ) ? $this->api_handler->get_customer( $local_customer['id'] ) : false;

			// add card
			if ( empty( $card ) ) {

				// get existing
				if ( $customer) {
					$card                      = $this->api_handler->create_card( $local_customer['id'], $this->token, 'source' );
				} // create new one
				else {
					$params = array(
						'source'      => $this->token,
						'email'       => $user->billing_email,
						'description' => $user->user_login . ' (#' . $user->ID . ' - ' . $user->user_email . ') ' . $user->billing_first_name . ' ' . $user->billing_last_name,
						'metadata'    => apply_filters( 'yith_wcstripe_connect_metadata', array(
							'user_id'  => $user->ID,
							'instance' => $this->instance_url
						), 'create_customer' )
					);

					$customer = $this->api_handler->create_customer( $params );
					foreach ( $customer->sources->data as $card ) {
						if ( $card->id == $customer->default_source ) {
							break;
						}
					}
				}
			}

			if ( empty( $card ) ) {
				throw new Error\Api( __( "Can't add credit card info.", 'yith-stripe-connect-for-woocommerce' ) );
			}

			$already_registered        = false;
			$already_registered_tokens = WC_Payment_Tokens::get_customer_tokens( $user->ID, $this->id );
			$registered_token          = false;

			if ( ! empty( $already_registered_tokens ) ) {
				foreach ( $already_registered_tokens as $registered_token ) {
					/**
					 * @var $registered_token \WC_Payment_Token
					 */
					if ( $registered_token->get_token() == $card->id ) {
						$already_registered = true;
						break;
					}
				}
			}

			if ( ! $already_registered ) {
				// save card
				$token   = new WC_Payment_Token_CC();
				$cart_id = $card->id;
				$token->set_token( $cart_id );
				$token->set_gateway_id( $this->id );
				$token->set_user_id( $user->ID );
				if ( 'source' === $card->object && 'card' === $card->type ) {
					$card = $card->card;
				}
				$token->set_card_type( strtolower( $card->brand ) );
				$token->set_last4( $card->last4 );
				$token->set_expiry_month( ( 1 === strlen( $card->exp_month ) ? '0' . $card->exp_month : $card->exp_month ) );
				$token->set_expiry_year( $card->exp_year );
				$token->set_default( true );
				$token->add_meta_data( 'fingerprint', $card->fingerprint );

				if ( ! $token->save() ) {
					throw new Error\Api( __( 'Credit card info not valid', 'yith-stripe-connect-for-woocommerce' ) );
				}

				// backard compatibility
				if ( $customer ) {
					YITH_Stripe_Connect_Customer()->update_usermeta_info( $customer->metadata->user_id, array(
						'id'             => $customer->id,
						'default_source' => $customer->default_source
					) );
				}

				//DO_ACTION : yith_wcstripe_connect_created_card : Do action after that the cart is created : cart id and customer are the arguments
				do_action( 'yith_wcstripe_connect_created_card', $cart_id, $customer );

				return $token;
			} else {
				return $registered_token;
			}
		}

		/**
		 * Add payment method via my account page.
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @since 1.1.0
		 */
		public function add_payment_method() {

			if ( empty( $_POST['stripe_connect_token'] ) && empty( $_POST['stripe_connect_source'] ) || ! is_user_logged_in() ) {
				$error_msg = __( 'Unable to add payment method to your account.', 'yith-stripe-connect-for-woocommerce' );
				$this->log( 'error', $error_msg );

				return new WP_Error( 'stripe_error', $error_msg );
			}

			$this->init_stripe_connect_api();
			$source = ! empty( $_POST['stripe_connect_source'] ) ? wc_clean( $_POST['stripe_connect_source'] ) : '';

			if ( ! empty( $source ) ) {
				$this->token = $source;
				$this->save_token();
			} else {
				$error_msg = __( 'Unable to add payment method to your account.', 'yith-stripe-connect-for-woocommerce' );
				$this->log( 'error', $error_msg );

				return new WP_Error( 'stripe_error', $error_msg );
			}

			return array(
				'result'   => 'success',
				'redirect' => wc_get_endpoint_url( 'payment-methods' ),
			);
		}

		/**
		 * Set one of the currently registered tokens as default
		 *
		 * @param $card_id string Card token
		 *
		 * @return bool Operation status
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @since 1.1.0
		 */
		public function set_default_token( $card_id ) {
			if ( ! is_user_logged_in() ) {
				return false;
			}

			$user                      = wp_get_current_user();
			$already_registered_tokens = WC_Payment_Tokens::get_customer_tokens( $user->ID, $this->id );

			if ( ! empty( $already_registered_tokens ) ) {
				foreach ( $already_registered_tokens as $registered_token ) {
					/**
					 * @var $registered_token \WC_Payment_Token
					 */
					if ( $registered_token->get_token() == $card_id ) {
						$registered_token->set_default( true );
						$registered_token->save();

						return true;
					}
				}
			}

			return false;
		}

		/**
		 * Get customer of Stripe account or create a new one if not exists
		 *
		 * @param $order WC_Order
		 *
		 * @return \Stripe\Customer
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @since 1.1.0
		 */
		public function get_customer( $order ) {

			$this->init_stripe_connect_api();

			if ( is_int( $order ) ) {
				$order = wc_get_order( $order );
			}

			$current_order_id = ( isset( $this->_current_order ) && $this->_current_order instanceof WC_Order ) ? $this->_current_order->get_id() : false;
			$order_id         = $order->get_id();

			if ( $current_order_id == $order_id && ! empty( $this->_current_customer ) ) {
				return $this->_current_customer;
			}

			$user_id  = is_user_logged_in() ? $order->get_user_id() : false;
			$local_customer = is_user_logged_in() ? YITH_Stripe_Connect_Customer()->get_usermeta_info( $user_id ) : false;
			$customer = isset( $local_customer['id'] ) ? $this->api_handler->get_customer( $local_customer['id'] ) : false;

			// get existing
			if ( $customer ) {
				$selected_card = $this->get_credit_card_num();

				if ( 'new' == $selected_card ) {
					$user = $order->get_user();

					$card        = $this->api_handler->create_card( $customer, $this->token, 'source' );
					$this->token = $card->id;

					try {
						//update the customer on Stripe with the billing email
						$customer = $this->api_handler->update_customer( $customer, array(
							'email'       => yit_get_prop( $order, 'billing_email' ),
							'description' => $user->user_login . ' (#' . $order->get_user_id() . ' - ' . $user->user_email . ') ' . yit_get_prop( $order, 'billing_first_name' ) . ' ' . yit_get_prop( $order, 'billing_last_name' ),
						) );

					} catch( Exception $e ) {
						YITH_Stripe_Connect_Customer()->delete_usermeta_info( $user_id );
						$this->get_customer( $order );
					}

					// update user meta
					YITH_Stripe_Connect_Customer()->update_usermeta_info( $user_id, array(
						'id'             => $customer->id,
						'default_source' => $customer->default_source
					) );

					do_action( 'yith_wcstripe_connect_created_card', $card->id, $customer );
				}

				if ( $current_order_id == $order_id ) {
					$this->_current_customer = $customer;
				}

				return $customer;

			} // create new one
			else {

				$user = is_user_logged_in() ? $order->get_user() : false;

				if ( is_user_logged_in() ) {
					$description = $user->user_login . ' (#' . $order->get_user_id() . ' - ' . $user->user_email . ') ' . yit_get_prop( $order, 'billing_first_name' ) . ' ' . yit_get_prop( $order, 'billing_last_name' );
				} else {
					$description = yit_get_prop( $order, 'billing_email' ) . ' (' . __( 'Guest', 'yith-stripe-connect-for-woocommerce' ) . ' - ' . yit_get_prop( $order, 'billing_email' ) . ') ' . yit_get_prop( $order, 'billing_first_name' ) . ' ' . yit_get_prop( $order, 'billing_last_name' );
				}

				$params = array(
					'source'      => $this->token,
					'email'       => yit_get_prop( $order, 'billing_email' ),
					'description' => $description,
					'metadata'    => apply_filters( 'yith_wcstripe_connect_metadata', array(
						'user_id'  => is_user_logged_in() ? $order->get_user_id() : false,
						'instance' => $this->instance_url
					), 'create_customer' )
				);

				$customer    = $this->api_handler->create_customer( $params );
				$this->token = $customer->default_source;

				// update user meta
				if ( is_user_logged_in() ) {
					YITH_Stripe_Connect_Customer()->update_usermeta_info( $user_id, array(
						'id'             => $customer->id,
						'default_source' => $customer->default_source
					) );
				}

				if ( $current_order_id == $order_id ) {
					$this->_current_customer = $customer;
				}

				return $customer;

			}

		}

		/**
		 * Return the method payment selected a saved card or a new one.
		 *
		 * @return mixed
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @since 1.1.0
		 */
		protected function get_credit_card_num() {

			$card_id = isset( $_POST[ 'wc-' . YITH_Stripe_Connect::$gateway_id . '-payment-token' ] ) ? $_POST[ 'wc-' . YITH_Stripe_Connect::$gateway_id . '-payment-token' ] : 'new';

			if ( 'new' != $card_id ) {
				$payment_token = WC_Payment_Tokens::get( $card_id );
				if ( $payment_token->get_user_id() === get_current_user_id()  ) {
					$card_id = $payment_token->get_token();
				}
			}

			return apply_filters( 'yith_stripe_connect_selected_card', $card_id );
		}

		/**
		 * Handle the card removing from stripe databases for the customer
		 *
		 * @param $token_id
		 * @param WC_Payment_Token_CC $token
		 *
		 * @return bool
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @since 1.1.0
		 */
		public function delete_token_from_stripe( $token_id, $token ) {

			if ( $token->get_gateway_id() != $this->id ) {
				return false;
			}

			try {

				// Initialize SDK and set private key
				$this->init_stripe_connect_api();

				$user_id     = $token->get_user_id();
				$customer_sc = YITH_Stripe_Connect_Customer()->get_usermeta_info( $user_id );

				// delete card
				$customer = $this->api_handler->delete_source( $customer_sc['id'], $token->get_token() );

				// ensure the default card is the same on stripe
				$default_token  = $customer->default_source;
				$payment_tokens = WC_Payment_Tokens::get_customer_tokens( $user_id );

				/** @var WC_Payment_Token_CC $payment_token */
				foreach ( $payment_tokens as $payment_token ) {
					if ( $payment_token->get_token() === $default_token && ! $payment_token->is_default() ) {
						$payment_token->set_default( true );
						$payment_token->save();
						break;
					}
				}

				YITH_Stripe_Connect_Customer()->update_usermeta_info( $user_id, array(
					'id'             => $customer->id,
					'default_source' => $customer->default_source
				) );

				return true;

			} catch( Error\Base $e ) {
				return false;
			}
		}

		/**
		 * Handle setting a token as default on Stripe
		 *
		 * @param $token_id
		 * @param WC_Payment_Token_CC $token
		 *
		 * @return bool
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @since 1.1.0
		 */
		public function set_default_token_on_stripe( $token_id, $token = null ) {
			if ( $token->get_gateway_id() != $this->id ) {
				return false;
			}

			if ( empty( $token ) ) {
				$token = WC_Payment_Tokens::get( $token_id );
			}

			try {

				// Initialize SDK and set private key
				$this->init_stripe_connect_api();

				$user_id = $token->get_user_id();
				$customer_sc = YITH_Stripe_Connect_Customer()->get_usermeta_info( $user_id );

				if ( empty( $customer_sc ) ) {
					return false;
				}

				// delete card
				$customer = $this->api_handler->set_default_card( $customer_sc['id'], $token->get_token() );

				// backard compatibility
				YITH_Stripe_Connect_Customer()->update_usermeta_info( $user_id, array(
					'id'             => $customer->id,
					'default_source' => $customer->default_source
				) );

				return true;

			} catch ( Error\Base $e ) {
				return false;
			}
		}

		/**
		 * Remove the checkbox from checkout.
		 *
		 * @return bool
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @since 1.1.0
		 */
		public function save_payment_method_checkbox() {
			return false;
		}

		/**
		 * Override the enqueue script of parent adding the support to stripe-js v3.
		 *
		 * @return mixed|void
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @since 1.1.0
		 */
		public function payment_scripts() {
			if ( ! $this->is_available() || ! ( is_checkout() || is_wc_endpoint_url( 'add-payment-method' ) ) ) {
				return;
			}

			$debug_enabled   = defined( 'WP_DEBUG' ) ? WP_DEBUG : false;
			$prefix          = ! $debug_enabled ? '.min' : '';
			$js_dependencies = array( 'jquery', 'stripe-js' );

			wp_register_script( 'stripe-js', 'https://js.stripe.com/v3/', array( 'jquery' ), false, true );

			wp_register_script( 'yith-stripe-connect-js', YITH_WCSC_ASSETS_URL . 'js/script-yith-sc-checkout-source' . $prefix . '.js', $js_dependencies, YITH_WCSC_VERSION, true );

			wp_localize_script( 'yith-stripe-connect-js', 'yith_stripe_connect_info', array(
				'public_key'     => $this->public_key,
				'card.name'      => __( 'A valid Name on Card is required.', 'yith-stripe-connect-for-woocommerce' ),
				'card.number'    => __( 'The credit card number seems to be invalid.', 'yith-stripe-connect-for-woocommerce' ),
				'card.cvc'       => __( 'The CVC number seems to be invalid.', 'yith-stripe-connect-for-woocommerce' ),
				'card.expire'    => __( 'The expiration date seems to be invalid.', 'yith-stripe-connect-for-woocommerce' ),
				'billing.fields' => __( 'You have to add extra information to checkout.', 'yith-stripe-connect-for-woocommerce' ),
			) );

			wp_register_style( 'yith-stripe-connect-css', YITH_WCSC_ASSETS_URL . 'css/style-yith-sc-checkout.css', null, YITH_WCSC_VERSION );

			wp_enqueue_script( 'yith-stripe-connect-js' );
			wp_enqueue_style( 'yith-stripe-connect-css' );

			wp_localize_script( 'yith-stripe-connect-js', 'yith_stripe_connect_source_info', array(
				'payment_type' => ( YITH_WC_Subscription()->cart_has_subscriptions() || is_wc_endpoint_url( 'add-payment-method' ) ) ? 'source' : 'token',
			) );
		}

		/**
		 * Override the default form for credit card
		 * @param $fields
		 * @param $id
		 *
		 * @return array
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function credit_form_add_fields( $fields, $id ) {


			$cvc_field = '<div class="form-row form-row-last validate-required" >
			<label for="' . esc_attr( $this->id ) . '-card-cvc">' . esc_html__( 'Card code', 'woocommerce' ) . ' <span class="required">*</span></label>
			<div id="yith-card-cvc-field-wrapper" class="yith-stripe-element">
			</div>
			
			</div>';

			$default_fields = array(
				'card-number-field' => '<div class="form-row form-row-wide validate-required yith-cc-number-container ">
				<label for="' . esc_attr( $this->id ) . '-card-number">' . esc_html__( 'Card number', 'woocommerce' ) . ' <span class="required">*</span></label>
				<div id="yith-card-number-field-wrapper" class="yith-stripe-element">
				</div>
				<i id="yith-stripe-cc" class="" alt="'. esc_html__( 'Credict Card', 'woocommerce' ).'"></i>
				</div>',
				'card-expiry-field' => '<div class="form-row form-row-first validate-required">
				<label for="' . esc_attr( $this->id ) . '-card-expiry">' . esc_html__( 'Expiry (MM/YY)', 'woocommerce' ) . ' <span class="required">*</span></label>
				<div  id="yith-card-expiry-field-wrapper" class="yith-stripe-element">
				</div>
				</div>',
			);

			if( $this->show_name_on_card == 'yes' ){
				$default_fields = array_merge (
					array(
						'card-name-field' => '<div class="form-row form-row-wide">
						<label for="' . esc_attr( $this->id ) . '-card-name">' . apply_filters( 'yith_wccs_name_on_card_label', __( 'Name on Card', 'yith-stripe-connect-for-woocommerce' ) ) . ' <span class="required">*</span></label>
						<div  id="yith-card-name-field-wrapper" class="yith-stripe-element">
						<input id="' . esc_attr( $this->id ) . '-card-name" class="input-text wc-credit-card-form-card-name" type="text" autocomplete="off" placeholder="' . __( 'Name on Card', 'yith-stripe-connect-for-woocommerce' ) . '" ' . $this->field_name( 'card-name' ) . ' />
</div>
						</div>'
					),
					$default_fields
				);
			}
			if ( ! $this->supports( 'credit_card_form_cvc_on_saved_method' ) ) {
				$default_fields['card-cvc-field'] = $cvc_field;
			}

			return $default_fields;
		}

	}
}