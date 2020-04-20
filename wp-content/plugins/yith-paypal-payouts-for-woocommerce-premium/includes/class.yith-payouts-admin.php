<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_PayPal_PayOuts_Admin' ) ) {

	class YITH_PayPal_PayOuts_Admin {

		/**
		 * @var YIT_Panel $_panel
		 */
		protected $_panel;

		/**
		 * @var string paypal payouts panel page
		 */
		protected $_panel_page = 'yith_wc_paypal_payouts_panel';


		public function __construct() {
			//Add action links
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_PAYOUTS_DIR . '/' . basename( YITH_PAYOUTS_FILE ) ), array(
				$this,
				'action_links'
			) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			//  Add action menu
			add_action( 'admin_menu', array( $this, 'add_menu_page' ), 5 );

			add_action( 'woocommerce_admin_field_yith_payouts_receiver_list', array( $this, 'add_custom_field_types' ) );
			add_action( 'woocommerce_admin_field_webook-info', array( $this, 'add_custom_field_webhook' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'include_admin_scripts' ), 20 );

			//add a new row in the receiver table
			add_action( 'wp_ajax_add_receiver_row', array( $this, 'add_receiver_row' ) );
			add_action( 'wp_ajax_get_payout_item_details', array( $this, 'get_payout_details' ) );

			add_action( 'yith_payouts_list', array( $this, 'show_payouts_list_table' ), 10 );

			//Add action for register and update plugin
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );


		}

		/**
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $links | links plugin array
		 *
		 * @return array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return mixed
		 * @use plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {

			$links = yith_add_action_links( $links, $this->_panel_page,true );

			return $links;
		}

		/**
		 * plugin_row_meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $plugin_meta
		 * @param $plugin_file
		 * @param $plugin_data
		 * @param $status
		 * @param  $init_file
		 *
		 * @return   array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_PAYOUTS_INIT' ) {

			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug'] = YITH_PAYOUTS_SLUG;
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;
		}

		public function add_menu_page() {

			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs = array(
				'general-settings' => __( 'General Settings', 'yith-paypal-payouts-for-woocommerce' ),
				'payout-receivers' => __( 'Payout Receivers Settings', 'yith-paypal-payouts-for-woocommerce' ),
				'payout-list'      => __( 'Payouts List', 'yith-paypal-payouts-for-woocommerce' ),
				'privacy-settings'      => __( 'Privacy Settings', 'yith-paypal-payouts-for-woocommerce' ),
			);


			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => __( 'PayPal Payouts', 'yith-paypal-payouts-for-woocommerce' ),
				'menu_title'       => 'PayPal Payouts',
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YITH_PAYOUTS_DIR . '/plugin-options'
			);

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/**
		 * @param string $field_template
		 * @param array $field
		 * @author Salvatore Strano
		 * @since 1.0.0
		 */
		public function add_custom_field_types( $option ) {


			$option['option'] = $option;

			wc_get_template( 'yith_payouts_receiver_list.php', $option, YITH_PAYOUTS_DIR . '/plugin-options/types/' , YITH_PAYOUTS_DIR . '/plugin-options/types/' );

		}

		/**
		 * register scripts and style in admin
		 * @author Salvatore Strano
		 * @since 1.0.0
		 *
		 */
		public function include_admin_scripts() {

			$script_args = array(
				'admin_url' => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
				'actions'   => array(
					'add_receiver_row' => 'add_receiver_row'
				)
			);

			wp_register_script( 'yith_payouts_admin_panel', YITH_PAYOUTS_ASSETS_URL . 'js/' . yit_load_js_file( 'yith_payouts_admin.js' ), array( 'jquery' ), YITH_PAYOUTS_VERSION,true );
			wp_localize_script( 'yith_payouts_admin_panel', 'payouts_admin', $script_args );

			wp_register_style( 'yith_payouts_admin_style', YITH_PAYOUTS_ASSETS_URL.'css/yith_payouts_admin.css', array(), YITH_PAYOUTS_VERSION );
			if ( isset( $_GET['page'] ) && 'yith_wc_paypal_payouts_panel' == $_GET['page'] ) {

				wp_enqueue_script( 'yith_payouts_admin_panel' );
				wp_enqueue_style( 'yith_payouts_admin_style' );
			}

			$script_args['actions']  = array(
				'payouts_get_payout_item_details' => 'get_payout_item_details'
			);

			wp_register_script( 'yith_payouts_preview_modal', YITH_PAYOUTS_ASSETS_URL . 'js/' . yit_load_js_file( 'yith_payout_modal_preview.js' ), array( 'jquery', 'wp-util', 'underscore', 'backbone', 'jquery-blockui', 'wc-backbone-modal' ), YITH_PAYOUTS_VERSION,true );
			wp_localize_script( 'yith_payouts_preview_modal', 'payouts_modal', $script_args );
			if( isset( $_GET['page'] ) && 'yith_wc_paypal_payouts_panel' == $_GET['page'] && isset( $_GET['show_payout_details'] ) ){

				wp_enqueue_script( 'yith_payouts_preview_modal' );
			}

		}

		/**
		 * Add new row into receiver table
		 * @author Salvatore Strano
		 * @since 1.0.0
		 *
		 */
		public function add_receiver_row(){

			if( isset( $_REQUEST['yith_action'] ) && 'add_new_receiver' == $_REQUEST['yith_action'] ) {

				$i = isset( $_REQUEST['i'] ) ? $_REQUEST['i'] : 0;

				$args = array(
					'i' => $i,
					'receiver' => array()
				);

				ob_start();
				wc_get_template( 'yith_payouts_single_receiver.php', $args, YITH_PAYOUTS_DIR.'plugin-options/types/', YITH_PAYOUTS_DIR.'plugin-options/types/' );

				$template = ob_get_contents();
				ob_end_clean();

				wp_send_json( array( 'result' => $template ) );
			}
		}

		/**
		 *
		 */
		public function show_payouts_list_table(){
			ob_start();
			require_once( YITH_PAYOUTS_TEMPLATE_PATH.'/admin/payouts-list.php' );
			$template = ob_get_contents();
			ob_end_clean();
			echo $template;
		}

		/** Register plugins for activation tab
		 * @return void
		 * @since    1.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {

				require_once YITH_PAYOUTS_DIR . 'plugin-fw/licence/lib/yit-licence.php';
				require_once YITH_PAYOUTS_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YITH_PAYOUTS_INIT, YITH_PAYOUTS_SECRET_KEY, YITH_PAYOUTS_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since    1.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {

				require_once( YITH_PAYOUTS_DIR . 'plugin-fw/lib/yit-upgrade.php' );
			}
			YIT_Upgrade()->register( YITH_PAYOUTS_SLUG, YITH_PAYOUTS_INIT );
		}

		public function get_payout_details(){


			if( isset( $_GET['payout_item_id'] ) ){

				include_once 'class.yith-payout-items-list-table.php';
				wp_send_json_success( YITH_PayOut_Items_List_Table::payout_item_preview_get_payout_item_details( $_GET['payout_item_id'] ) );
			}
			die();
		}


	}
}