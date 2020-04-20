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

use \Stripe\Stripe;
use \Stripe\Charge;
use \Stripe\Account;
use \Stripe\OAuth;
use \Stripe\Error;
use \Stripe\Customer;
use \Stripe\Plan;
use \Stripe\Subscription;
use \Stripe\Invoice;
use \Stripe\Source;
/**
 *
 *
 * @class      YITH_Stripe_Connect_API_Handler
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_Stripe_Connect_API_Handler' ) ) {

	/**
	 * Class YITH_Stripe_Connect_API_Handler
	 *
	 * @author Francisco Javier Mateo <francisco.mateo@yithemes.com>
	 */
	class YITH_Stripe_Connect_API_Handler {

		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0
		 */
		public $version = YITH_WCSC_VERSION;

		/**
		 * StripeObject Instance
		 *
		 * @var StripeObject
		 * @since  1.0
		 * @access protected
		 */
		protected static $_instance = null;

		public $_test_live = null;

		public $_env = null;

		/**
		 * Main Instance
		 *
		 * @var YITH_Stripe_Connect_Gateway
		 * @since  1.0
		 * @access protected
		 */
		protected $_stripe_connect_gateway = null;

		/**
		 * Construct
		 *
		 * @author Francisco Mateo
		 * @since  1.0
		 */
		public function __construct() {
			require_once( YITH_WCSC_VENDOR_PATH . 'autoload.php' );

			// Gets all Payments Gateways defined on WooCommerce.
			$payment_gateways = WC()->payment_gateways->payment_gateways();
			// Filter the Gateways and get our YITH Stripe Connect Gateway. We get the Gateway object to gets better their data, settings for example.ff
			$this->_stripe_connect_gateway = $payment_gateways['yith-stripe-connect'];

			$this->_test_live = $this->_stripe_connect_gateway->get_option( 'test-live' );
			$this->_env       = ( $this->_test_live == 'yes' ) ? 'dev' : 'prod';

			$this->init_handler();
		}

		/**
		 * Main plugin Instance
		 *
		 * @return YITH_Stripe_Connect_API_Handler Main instance
		 * @author Francisco Mateo
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		public function init_handler() {
			$secret_api_key = ('yes' ==$this->_test_live) ?  $this->_stripe_connect_gateway->get_option( 'api-secret-test-key' ) : $this->_stripe_connect_gateway->get_option( 'api-secret-live-key' );
			Stripe::setApiKey( $secret_api_key );
		}

		public function create_account( $args = array() ) {
			try {
				$acct = Account::create( $args );
			} catch ( Exception $e ) {
				return false;
			}

			return $acct;
		}

		public function retrieve_account( $id ) {
			try {
				$acct = Account::retrieve( $id );

				return $acct;
			} catch ( Exception $e ) {
				return false;
			}
		}


		public function create_charge( $args = array() ) {
			try {
				$charge = Charge::create( $args );
			} catch ( Exception $e ) {
				return array( 'error_charge' => $e->getMessage() );
			}

			return $charge;
		}

		public function create_transfer( $args = array() ) {
			try {
				$transfer = \Stripe\Transfer::create( $args );
			} catch ( Exception $e ) {
				return array( 'error_transfer' => $e->getMessage() );
			}

			return $transfer;
		}

		public function authorize_account( $stripe_user_email ) {
			try {
				$client_id       = $this->_stripe_connect_gateway->get_option( 'api-' . $this->_env . '-client-id' );
				$user_authorized = OAuth::authorizeUrl( array(
					'client_id'   => $client_id,
					'stripe_user' => $stripe_user_email,
				) );
				$this->_stripe_connect_gateway->log( 'info', 'Authorize Account: Account with client_id:"' . $client_id . '" and stripe_user_email:"' . $stripe_user_email . '" authorized' );

				return $user_authorized;
			} catch ( Exception $e ) {
				$this->_stripe_connect_gateway->log( 'error', 'Authorize Account: Could not be authorize account...' . $e->getMessage() );

				return false;
			}
		}

		public function get_OAuth_link() {
			try {
				$args       = array(
					'client_id'    => $this->_stripe_connect_gateway->get_option( 'api-' . $this->_env . '-client-id' ),
					'redirect_uri' => wc_get_page_permalink( 'myaccount' ) . 'stripe-connect',
					'scope'        => 'read_write'
				);
				$OAuth_link = OAuth::authorizeUrl( $args );
			} catch ( Exception $e ) {
				return false;
			}

			return $OAuth_link;
		}

		public function get_OAuth_token( $code ) {
			try {
				$client_id = $this->_stripe_connect_gateway->get_option( 'api-' . $this->_env . '-client-id' );
				$args  = array(
					'client_id'  => $client_id,
					'code'       => $code,
					'grant_type' => 'authorization_code',
				);
				$token = OAuth::token( $args );

				return $token;
			} catch ( Exception $e ) {
				return false;
			}
		}

		public function deauthorize_account( $stripe_user_id ) {
			try {
				$client_id         = $this->_stripe_connect_gateway->get_option( 'api-' . $this->_env . '-client-id' );
				$user_deauthorized = OAuth::deauthorize( array(
					'client_id'      => $client_id,
					'stripe_user_id' => $stripe_user_id
				), array()
				);

				$this->_stripe_connect_gateway->log( 'info', 'Deauthorize Account: Account with client_id:"' . $client_id . '" deauthorized' );

				return $user_deauthorized;
			} catch ( Exception $e ) {
				if ( $e instanceof Error\OAuth\InvalidClient ) {
					$this->_stripe_connect_gateway->log( 'warning', 'Deauthorize Account: Account with client_id:"' . $client_id . '" have been deauthorized previously' );

					return $e;
				}
				$this->_stripe_connect_gateway->log( 'error', 'Deauthorize Account: Could not be deauthorize account...' . $e->getMessage() );

				return false;
			}
		}

		/**
		 * New customer
		 *
		 * @param $params
		 *
		 * @since 1.0.0
		 * @return Customer
		 */
		public function create_customer( $params  ) {
			return Customer::create( $params );
		}

		/**
		 * Retrieve customer
		 *
		 * @param $customer Customer object or ID
		 *
		 * @since 1.0.0
		 * @return Customer
		 */
		public function get_customer( $customer ) {
			if ( is_a( $customer, '\Stripe\Customer' ) ) {
				return $customer;
			}

			return Customer::retrieve( $customer );
		}

		/**
		 * Update customer
		 *
		 * @param $customer Customer object or ID
		 * @param $params
		 *
		 * @since 1.0.0
		 * @return Customer
		 */
		public function update_customer( $customer, $params ) {
			$customer = $this->get_customer( $customer );

			// edit
			foreach ( $params as $key => $value ) {
				$customer->{$key} = $value;
			}

			// save
			return $customer->save();
		}

		/**
		 * Create a card
		 *
		 * @param $customer Customer object or ID
		 * @param $token
		 *
		 * @return Customer
		 *
		 * @since 1.0.0
		 */
		public function create_card( $customer, $token, $type = 'card' ) {
			$customer = $this->get_customer( $customer );

			$result = $customer->sources->create(
				array(
					$type => $token
				)
			);

			do_action( 'yith_wcstripe_connect_card_created', $customer, $token, $type );

			return $result;
		}


		/**
		 * Retrieve a card object for the customer
		 *
		 * @param $customer Customer object or ID
		 * @param $card_id
		 *
		 * @return Customer
		 *
		 * @since 1.0.0
		 */
		public function get_card ( $customer, $card_id, $params = array() ) {
			$card = $customer->sources->retrieve( $card_id, $params );

			return $card;
		}

		/**
		 * Se the default card for the customer
		 *
		 * @param $customer Customer object or ID
		 * @param $card_id
		 *
		 * @return Customer
		 *
		 * @since 1.0.0
		 */
		public function set_default_card( $customer, $card_id ) {
			$result = $this->update_customer( $customer, array(
				'default_source' => $card_id
			) );

			do_action( 'yith_wcstripe_connect_card_set_default', $customer, $card_id );

			return $result;
		}

		/**
		 *  Remove a source from a customer.
		 *
		 * @param $customer Customer object or ID
		 * @param $source_id
		 *
		 * @return Customer
		 *
		 * @since 1.1.0
		 */
		public function delete_source( $customer_id, $source_id ){
			$customer = $this->get_customer( $customer_id );
			/**@var \Stripe\Source $source */
			$source = $customer->sources->retrieve($source_id);
			$source->delete();

			return $customer;
		}
	}
}
