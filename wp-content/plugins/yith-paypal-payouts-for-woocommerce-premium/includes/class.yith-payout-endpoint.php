<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Payout_Endpoints' ) ) {

	class YITH_Payout_Endpoints {

		protected $query_vars = array();

		public function __construct() {

			add_action( 'init', array( $this, 'add_woocommerce_query_vars' ), 10 );
			add_action( 'init', array( $this, 'rewrite_rules' ), 20 );
			add_filter( 'woocommerce_account_menu_items', array( $this, 'add_account_menu_items' ), 20 );

			$endpoint_slug = $this->get_payouts_slug();
			add_action( 'woocommerce_account_' . $endpoint_slug . '_endpoint', array( $this,'show_payouts_content' ) );
			if( !is_admin() ){
				add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0, 1 );
			}
			$this->init_query_vars();


		}


		/**
		 * add endpoint
		 * @author Salvatore Strano
		 * @since 1.0.0
		 */
		public function add_woocommerce_query_vars(){

			foreach ( $this->query_vars as $key => $value ) {

				add_rewrite_endpoint( $value, EP_ROOT|EP_PAGES );
			}
		}

		/**
		 * flush permalink after add the endpoint
		 * @author Salvatore Strano
		 * @since 1.0.0
		 */
		public function rewrite_rules(){

			$rewrite = get_option('yith_payouts_rewrite_rule', true );

			if( $rewrite ){

				flush_rewrite_rules();
				update_option('yith_payouts_rewrite_rule', false);

			}
		}

		/**
		 * init query vars
		 * @author Salvatore Strano
		 * @since 1.0.0
		 *
		 */
		public function init_query_vars() {

			$endpoints = apply_filters( 'yith_payouts_get_endpoints', array( 'payouts' => 'payouts' ) );

			foreach ( $endpoints as $key => $endpoint ) {

				$this->query_vars[ $key ] = $endpoint;
			}


		}

		/**
		 * add End point item in my account menu
		 * @author Salvatore Strano
		 * @since 1.0.0
		 * @param array $menu_items
		 * @return array
		 */
		public function add_account_menu_items( $menu_items ){

			$menu_items['payouts'] = __( 'Payouts', 'yith-paypal-payouts-for-woocommerce' );
			return $menu_items;
		}

		/**
		 * add query vars
		 * @author Salvatore Strano
		 * @since 1.0.0
		 * @param array $vars
		 * @return array
		 */
		public function add_query_vars( $vars ){
			foreach ( $this->query_vars as $key => $value ) {

				$vars[] = $key;
			}

			return $vars;
		}

		/**
		 *
		 * @author Salvatore Strano
		 * @since 1.0.0
		 * @param $context
		 * @return string
		 */
		public function get_payouts_slug( $context = 'view' ){
			if( 'view' == $context ) {
				return apply_filters( 'yith_get_payouts_slug', 'payouts' );
			}
			else{
				return 'payouts';
			}
		}

		/**
		 * show the payouts content
		 * @param string $value
		 * @author Salvatore Strano
		 * @since 1.0.0
		 */
		public function show_payouts_content( $value ){

			if( !is_user_logged_in() ){
				wp_redirect( esc_url( wc_get_page_permalink( 'myaccount' ) ) );
				exit;
			}

			echo do_shortcode('[yith_payout_transactions pagination="yes" current_page="'.$value.'"]');

		}

	}
}

if( !function_exists( 'YITH_Payout_Endpoints')){

	/**
	 * @return YITH_Payout_Endpoints
	 */
	function YITH_Payout_Endpoints(){
		return new YITH_Payout_Endpoints();
	}
}
YITH_Payout_Endpoints();
