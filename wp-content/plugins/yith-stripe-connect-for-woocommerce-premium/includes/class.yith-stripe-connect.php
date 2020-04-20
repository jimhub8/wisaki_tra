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
 * @class      YITH_Stripe_Connect
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_Stripe_Connect' ) ) {
	/**
	 * Class YITH_Stripe_Connect
	 *
	 * @author Francisco Javier Mateo <francisco.mateo@yithemes.com>
	 */
	class YITH_Stripe_Connect {
		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $version = YITH_WCSC_VERSION;

		/**
		 * Plugin DB version
		 *
		 * @const string
		 * @since 1.0.0
		 */
		const YITH_WCSC_DB_VERSION = '1.0.0';

		/**
		 * Main Instance
		 *
		 * @var YITH_Stripe_Connect
		 * @since  1.0.0
		 * @access protected
		 */
		protected static $_instance = null;

		/**
		 * Main Admin Instance
		 *
		 * @var YITH_Stripe_Connect_Admin
		 * @since 1.0.0
		 */
		public $admin = null;

		/**
		 * Main Frontpage Instance
		 *
		 * @var YITH_Stripe_Connect_Frontend
		 * @since 1.0.0
		 */
		public $frontend = null;

		/**
		 * Stripe Connect WC API
		 *
		 * @var YITH_Stripe_Connect_WC_API
		 * @since 1.0.0
		 */
		public $stripe_connect_wc_api = null;

		/**
		 * Stripe Connect Cron Job
		 *
		 * @var YITH_Stripe_Connect_Cron_Job
		 * @since 1.0.0
		 */
		public $stripe_connect_cron_job = null;

		/**
		 * Stripe gateway id
		 *
		 * @var string ID of specific gateway
		 * @since 1.0
		 */
		public static $gateway_id = 'yith-stripe-connect';

		/**
		 * Construct
		 *
		 * @author Francisco Javier Mateo <francisco.mateo@yithemes.com>
		 * @since  1.0.0
		 */
		public function __construct() {

			/* === Require Main Files === */
			$require = apply_filters( 'yith_wcsc_require_class',
				array(
					'common'   => array(
						'includes/functions.yith-wcsc.php',
						'includes/class.yith-stripe-connect-api-handler.php',
						'includes/class.yith-stripe-connect-gateway.php',
						'includes/class.yith-stripe-connect-receivers.php',
						'includes/class.yith-stripe-connect-commissions.php',
						'includes/class.yith-stripe-connect-wc-api.php',
						'includes/class.yith-stripe-connect-cron-job.php'
					),
					'frontend' => array(
						'includes/class.yith-stripe-connect-frontend.php'
					),
					'admin'    => array(
						'includes/class.yith-stripe-connect-admin.php',
						'includes/functions.yith-update.php'
					)
				) );

			$this->_require( $require );

			/* === Load Plugin Framework === */
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			add_action( 'plugins_loaded', array( $this, 'privacy_loader' ), 20 );

			/* === Load Plugin Integrations === */
			add_action( 'plugins_loaded', array( $this, 'load_integrations' ), 15 );

			/* === Plugins Init === */
			add_action( 'init', array( $this, 'init' ) );

			/* === Stripe Connect Gateway === */
			add_filter( 'woocommerce_payment_gateways', array( $this, 'add_stripe_connect_gateway' ) );
		}

		/**
		 * Main plugin Instance
		 *
		 * @return YITH_Stripe_Connect Main instance
		 * @author Francisco Javier Mateo <francisco.mateo@yithemes.com>
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Add the main classes file
		 *
		 * Include the admin and frontend classes
		 *
		 * @param $main_classes array The require classes file path
		 *
		 * @author Francisco Javier Mateo <francisco.mateo@yithemes.com>
		 * @since  1.0.0
		 *
		 * @return void
		 * @access protected
		 */
		protected function _require( $main_classes ) {
			foreach ( $main_classes as $section => $classes ) {
				foreach ( $classes as $class ) {
					if ( 'common' == $section || ( 'frontend' == $section && ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) || ( 'admin' == $section && is_admin() ) && file_exists( YITH_WCSC_PATH . $class ) ) {
						require_once( YITH_WCSC_PATH . $class );
					}
				}
			}
			do_action( 'yith_wcsc_require' );
		}

		/**
		 * Set plugins integrations...
		 *
		 * Stripe Connect is integrated with YITH Affiliates for WooCommerce and YITH Multivendor for WooCommerce
		 */
		public function load_integrations() {
			// Integration for YITH Affiliates for WooCommerce plugin
			if ( class_exists( 'YITH_WCAF' ) ) {
				add_filter( 'yith_wcaf_available_gateways', array( $this, 'add_wcaf_stripe_connect_gateway' ) );
			}

			if ( defined( 'YITH_YWSBS_PREMIUM' ) && version_compare( YITH_YWSBS_VERSION, '1.4.5', '>') ) {
				require_once( YITH_WCSC_PATH . 'includes/class.yith-stripe-connect-source-gateway.php' );
				require_once( YITH_WCSC_PATH . 'includes/class.yith-stripe-ywsbs-subscription.php' );
				require_once( YITH_WCSC_PATH . 'includes/class.yith-stripe-connect-customer.php' );

				YITH_Stripe_YWSBS_Subscription::instance();
			}
		}


		/**
		 * Load plugin framework
		 *
		 * @author Francisco Javier Mateo <francisco.mateo@yithemes.com>
		 * @since  1.0.0
		 * @return void
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
		 * Load plugin framework
		 *
		 * @author Francisco Javier Mateo <francisco.mateo@yithemes.com>
		 * @since  1.0.0
		 * @return void
		 */
		public function privacy_loader() {
			if( class_exists( 'YITH_Privacy_Plugin_Abstract' ) ) {
				require_once( YITH_WCSC_PATH . 'includes/class.yith-stripe-connect-privacy.php' );
				new YITH_Stripe_Connect_Privacy();
			}
		}

		/**
		 * Class Initialization
		 *
		 * Instance the admin class
		 *
		 * @author Francisco Javier Mateo <francisco.mateo@yithemes.com>
		 * @since  1.0.0
		 * @return void
		 * @access protected
		 */
		public function init() {
			$this->_install_tables();

			$this->_install_main_features();

			$this->stripe_connect_wc_api   = new YITH_Stripe_Connect_WC_API();
			$this->stripe_connect_cron_job = new YITH_Stripe_Connect_Cron_Job();

			if ( is_admin() ) {
				$this->admin = new YITH_Stripe_Connect_Admin();
			}

			if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				$this->frontend = new YITH_Stripe_Connect_Frontend();
			}
		}

		public function add_stripe_connect_gateway( $methods ) {
			$methods[] = 'YITH_Stripe_Connect_Gateway';

			return $methods;
		}

		public function add_wcaf_stripe_connect_gateway( $available_gateways ) {

			$wcaf_wcsc = array(
				'path'     => YITH_WCSC_PATH . 'includes/class.yith-wcaf-yith-wcsc-gateway.php',
				'label'    => __( 'Stripe Connect', 'yith-stripe-connect-for-woocommerce' ),
				'class'    => 'YITH_WCAF_YITH_WCSC',
				'mass_pay' => true
			);

			$available_gateways['yith-stripe-connect'] = $wcaf_wcsc;

			return $available_gateways;
		}

		protected function _install_tables() {
			global $wpdb;

			// adds tables name in global $wpdb
			$wpdb->yith_wcsc_receivers   = $wpdb->prefix . 'yith_wcsc_receivers';
			$wpdb->yith_wcsc_commissions = $wpdb->prefix . 'yith_wcsc_commissions';

			// skip if current db version is equal to plugin db version
			$current_db_version = get_option( 'yith_wcsc_db_version' );
			if ( $current_db_version == self::YITH_WCSC_DB_VERSION ) {
				return;
			} else {
				update_option( 'yith_wcsc_db_version', self::YITH_WCSC_DB_VERSION );
			}

			// assure dbDelta function is defined
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			// retrieve table charset
			$charset_collate = $wpdb->get_charset_collate();
			//*ID* | user_id | user_email | disabled | product_id | stripe_id | commission_value | commission_type | status_receiver | order_receiver
			// adds wcscs_receivers table
			$sql_receivers = "CREATE TABLE $wpdb->yith_wcsc_receivers (
                    ID bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    disabled INT,
                    user_id bigint(20) NOT NULL,
                    user_email VARCHAR (120) NOT NULL,
                    all_products INT,
                    product_id bigint(20) NOT NULL,
                    stripe_id VARCHAR (120),
                    commission_value DECIMAL,
                    commission_type VARCHAR (20),
                    status_receiver VARCHAR (120),
                    order_receiver bigint(20)
                ) $charset_collate;";
			dbDelta( $sql_receivers );
			//*ID* | receiver_id | user_id | order_id | order_item_id | product_id | commission | commission_status | pay_in | purchased_date
			$sql_commissions = "CREATE TABLE $wpdb->yith_wcsc_commissions (
                    ID bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    receiver_id bigint(20) NOT NULL,
                    user_id bigint (20) NOT NULL,
                    order_id bigint (20) NOT NULL,
                    order_item_id bigint(20) NOT NULL,
                    product_id bigint(20) NOT NULL,
                    commission DECIMAL(10,2) ,
                    commission_status VARCHAR (120) NOT NULL,
                    commission_type VARCHAR (20),
                    commission_rate DECIMAL ,
                    payment_retarded bigint (20),
                    purchased_date DATETIME,
                    note LONGTEXT,
                    integration_item LONGTEXT
                ) $charset_collate;";
			dbDelta( $sql_commissions );

			update_option( 'yith_wcsc_db_version', YITH_WCSC_DB_VERSION );
		}

		protected function _install_main_features() {
			$installed_plugin = apply_filters( 'yith_wcsc_istalled_plugin', get_option( 'yith_wcsc_installed' ) );

			//We add our endpoint each time that plugin is loaded.
			add_rewrite_endpoint( 'stripe-connect', EP_PAGES );

			if ( 'yes' != $installed_plugin ) {
				//Flush Rewrite Rules must run once time when plugin is installed.
				flush_rewrite_rules();
				update_option( 'yith_wcsc_installed', 'yes' );
			}
		}
	}
}

