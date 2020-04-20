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

/**
 *
 *
 * @class      YITH_Stripe_Connect_Frontend
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Francisco Javier Mateo <francisco.mateo@yithemes.com>
 *
 */

if ( ! class_exists( 'YITH_Stripe_Connect_Frontend' ) ) {
	/**
	 * Class YITH_Stripe_Connect_Frontend
	 *
	 * @author Francisco Javier Mateo <francisco.mateo@yithemes.com>
	 */
	class YITH_Stripe_Connect_Frontend {

		/**
		 * Stripe Connect Handler Instance
		 *
		 * @var YITH_Stripe_Connect_API_Handler
		 * @since  1.0.0
		 * @access protected
		 */
		protected $_stripe_connect_api_handler = null;

		/**
		 * Stripe Connect Handler Instance
		 *
		 * @var YITH_Stripe_Connect_Receivers
		 * @since  1.0.0
		 * @access protected
		 */
		protected $_stripe_connect_receivers = null;

		/**
		 * Stripe Connect Handler Instance
		 *
		 * @var YITH_Stripe_Connect_Commissions
		 * @since  1.0.0
		 * @access protected
		 */
		protected $_stripe_connect_commissions = null;

		/**
		 * Construct
		 *
		 * @author Francisco Javier Mateo <francisco.mateo@yithemes.com>
		 * @since  1.0.0
		 */
		public function __construct() {

			/*** Starting with Stripe Connecet on Account Page... ***/

			// We just add our custom menu item 'Stripe Connect' on Account page...
			add_filter( 'woocommerce_account_menu_items', array( $this, 'add_stripe_connect_account_menu_item' ) );

			// We define the content for our Stripe Connect Account Page...
			add_action( 'woocommerce_account_stripe-connect_endpoint', array( $this, 'stripe_connect_account_page' ) );

			// Ajax calls
			add_action( 'wp_ajax_disconnect_stripe_connect', array( $this, 'disconnect_stripe_connect' ) );

			$this->_stripe_connect_api_handler = YITH_Stripe_Connect_API_Handler::instance();
			$this->_stripe_connect_receivers   = YITH_Stripe_Connect_Receivers::instance();
			$this->_stripe_connect_commissions = YITH_Stripe_Connect_Commissions::instance();
		}

		public function add_stripe_connect_account_menu_item( $items ) {
			$items['stripe-connect'] = _x( 'Stripe Connect', 'No need translation...', 'yith-stripe-connect-for-woocommerce' );
			return apply_filters('yith_wcsc_account_menu_item',$items);
		}

		public function stripe_connect_account_page() {
			$this->enqueue_scripts_for_account_page();

			if ( isset( $_GET['scope'] ) & isset( $_GET['code'] ) ) { //The page has loaded from Stripe Platform, some user want connect with us.
				$code    = $_GET['code'];
				$user_id = get_current_user_id();

				$this->_stripe_connect_receivers->connect_by_user_id_and_access_code( $user_id, $code );

			} elseif ( isset( $_GET['error_description'] ) ) { // The page has loaded from Stripe Platform, some user canceled the connect process.
				
			}

			$current_status = yith_wcsc_get_stripe_user_status( get_current_user_id() );
			$OAuth_link     = '';

			$commissions_args = array(
				'product_id' => isset( $_GET['yith_wcs_product'] ) ? $_GET['yith_wcs_product'] : '',
				'user_id'    => get_current_user_id(),
				'date_from'  => isset( $_GET['yith_wcsc_date_from'] ) ? $_GET['yith_wcsc_date_from'] : '',
				'date_to'    => isset( $_GET['yith_wcsc_date_to'] ) ? $_GET['yith_wcsc_date_to'] : '',
				'day'        => isset( $_GET['yith_wcsc_day'] ) ? $_GET['yith_wcsc_day'] : '',
				'month_year' => isset( $_GET['yith_wcsc_month_year'] ) ? $_GET['yith_wcsc_month_year'] : ''
			);

			$commissions = $this->_stripe_connect_commissions->get_commissions( $commissions_args, true );

			if ( 'disconnect' == $current_status ) {
				$OAuth_link = $this->_stripe_connect_api_handler->get_OAuth_link();
			} else if ( 'connect' == $current_status ) {

			}
			$args = array(
				'current_status'    => $current_status,
				'OAuth_link'        => $OAuth_link,
				'count_commissions' => $this->_stripe_connect_commissions->get_commissions_count( $commissions_args ),
				'current_page'      => isset( $_GET['current_page'] ) ? abs( (int) $_GET['current_page'] ) : 1,
				'items_per_page'    => $this->_stripe_connect_commissions->items_per_page,
				'commissions'       => $commissions
			);

			$args = apply_filters( 'yith_wcsc_connect_account_template_args', $args );

			yith_wcsc_get_template( 'stripe-connect-account', $args, 'frontend' );
		}

		public function enqueue_scripts_for_account_page() {
			$debug_enabled   = defined( 'WP_DEBUG' ) ? WP_DEBUG : false;
			$prefix          = ! $debug_enabled ? '.min' : '';
			$js_dependencies = array(
				'jquery',
				'jquery-blockui'
			);
			$data_to_js      = array(
				'ajaxurl'                          => admin_url( 'admin-ajax.php' ),
				'disconnect_stripe_connect_action' => 'disconnect_stripe_connect',
				'OAuth_link'                       => $this->_stripe_connect_api_handler->get_OAuth_link(),
				'messages'                         => array(
					'connect_to'    => __( 'Connect with Stripe', 'yith-stripe-connect-for-woocommerce' ),
					'disconnect_to' => __( 'Disconnect from Stripe', 'yith-stripe-connect-for-woocommerce' )
				)
			);

			$data_to_js = apply_filters( 'yith_wcsc_account_page_script_data', $data_to_js );


			wp_register_style( 'yith-wcsc-account-page-style', YITH_WCSC_ASSETS_URL . 'css/style-yith-sc-account.css', null, YITH_WCSC_VERSION );
			wp_register_script( 'yith-wcsc-account-page-script', YITH_WCSC_ASSETS_URL . 'js/script-yith-sc-account' . $prefix . '.js', $js_dependencies, YITH_WCSC_VERSION, true );

			wp_localize_script( 'yith-wcsc-account-page-script', 'yith_wcsc_account_page_script', $data_to_js );

			wp_enqueue_style( 'yith-wcsc-account-page-style' );
			wp_enqueue_script( 'yith-wcsc-account-page-script' );
		}

		public function disconnect_stripe_connect() {
			$user_id = get_current_user_id();

			$result = $this->_stripe_connect_receivers->disconnect_by_user_id( $user_id );


			return wp_send_json( $result );
		}


	}

}