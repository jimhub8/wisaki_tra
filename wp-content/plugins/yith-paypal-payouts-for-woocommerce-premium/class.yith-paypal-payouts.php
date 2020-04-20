<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_PayPal_PayOuts' ) ) {

	class YITH_PayPal_PayOuts {

		/**
		 * @var YITH_PayPal_PayOuts single instance
		 */
		protected static $instance;

		public function __construct() {

			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			add_action( 'plugins_loaded', array( $this, 'load_privacy_class' ), 20 );
			if ( is_admin() ) {

				$this->payouts_admin = new YITH_PayPal_PayOuts_Admin();
			}

			add_action( 'init', array( $this, 'load_payouts_classes' ), 20 );
			add_action( 'wp_enqueue_scripts', array( $this, 'include_frontend_scripts' ), 20 );
		}

		/**
		 * unique instance for the class
		 * @author Salvatore Strano
		 * @since 1.0.0
		 * @return YITH_PayPal_PayOuts
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}


		/**
		 * load the plugin framework
		 *
		 * @since  1.0
		 * @access public
		 * @return void
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}
		}

		/**
		 * load PayOut Service Class
		 * @author Salvatore Strano
		 * @since  1.0.0
		 */
		public function load_payouts_classes() {

			require_once( YITH_PAYOUTS_INC . 'abstract-yith-payouts-service.php' );
			require_once( YITH_PAYOUTS_INC . 'class.yith-payouts-service.php' );
			YITH_PayOuts_Service();

		}

		/**
		 * register style
		 * @author Salvatore Strano
		 * @since 1.0.0
		 */
		public function include_frontend_scripts() {

			wp_register_style( 'yith_payouts_style', YITH_PAYOUTS_ASSETS_URL . 'css/yith_payouts_frontend.css', YITH_PAYOUTS_VERSION );
		}

		/**
		 * load privacy class
		 * @author Salvatore Strano
		 * @since 1.0.0
		 */
		public function load_privacy_class(){

			require_once( YITH_PAYOUTS_INC . 'class.yith-payouts-privacy.php' );

			new YITH_Payouts_Privacy();
		}
	}
}

/**
 * @return YITH_PayPal_PayOuts
 */
function YITH_PayPal_Payouts() {
	return YITH_PayPal_PayOuts::get_instance();
}