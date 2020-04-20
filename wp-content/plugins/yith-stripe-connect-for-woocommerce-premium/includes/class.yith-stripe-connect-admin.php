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
 * @class      YITH_Stripe_Connect_Admin
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Francisco Javier Mateo <francisco.mateo@yithemes.com>
 *
 */

if ( ! class_exists( 'YITH_Stripe_Connect_Admin' ) ) {
	/**
	 * Class YITH_Stripe_Connect_Admin
	 *
	 * @author Francisco Javier Mateo <francisco.mateo@yithemes.com>
	 */
	class YITH_Stripe_Connect_Admin {
		/**
		 * @var Panel page
		 */
		protected $_panel_page = 'yith_wcsc_panel';

		/**
		 * @var bool Show the premium landing page
		 */
		public $show_premium_landing = false; //TODO Here set true for free version

		/**
		 * @var doc_url
		 */
		protected $doc_url = 'https://docs.yithemes.com/yith-stripe-connect-for-woocommerce/';

		/**
		 * @var string Official plugin documentation
		 */
		protected $_official_documentation = 'https://docs.yithemes.com/yith-stripe-connect-for-woocommerce/';

		/**
		 * @var $_premium string Premium tab template file name
		 */
		protected $_premium = 'premium.php';

		/**
		 * @var string Premium version landing link
		 */
		protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-stripe-connect/';


		/**
		 * Stripe Connect Handler Instance
		 *
		 * @var YITH_Stripe_Connect_API_Handler
		 * @since  1.0.0
		 * @access protected
		 */
		protected $_stripe_connect_api_hanlder = null;

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
		 * Stripe Connect Gateway Instance
		 *
		 * @var YITH_Stripe_Connect_Gateway
		 * @since  1.0.0
		 * @access protected
		 */
		protected $_stripe_connect_gateway = null;

		protected $_require_key_test_message = '';
		protected $_require_key_live_message = '';

		/**
		 * Construct
		 *
		 * @author Francisco Javier Mateo <francisco.mateo@yithemes.com>
		 * @since  1.0.0
		 */
		public function __construct() {

			/* === Set admin Ajax calls === */
			add_action( 'wp_ajax_print_receiver_row_action', array( $this, 'print_receiver_row_action' ) );
			add_action( 'wp_ajax_save_receivers_action', array( $this, 'save_receivers_action' ) );
			add_action( 'wp_ajax_redirect_uri_done', array( $this, 'save_redirect_uri_done' ) );
			add_action( 'wp_ajax_webhook_done', array( $this, 'save_webhook_done' ) );


			/* === Action links and meta === */
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCSC_PATH . 'init.php' ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			/* === Register panel === */
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );
			add_action( 'yith_wcsc_receiver_panel', array( $this, 'get_receiver_panel' ) );
			add_action( 'yith_wcsc_commissions_panel', array( $this, 'get_commissions_panel' ) );


			add_action( 'yith_wcsc_premium', array( $this, 'premium_tab' ) );

			/* === Print YITH Settings === */
			add_action( 'yith_wcsc_gateway_advanced_settings_tab', array( $this, 'print_panel' ) );

			/* === Meta Box === */
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10 );


			// Enqueue Scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			$this->_stripe_connect_api_hanlder = YITH_Stripe_Connect_API_Handler::instance();
			$this->_stripe_connect_receivers   = YITH_Stripe_Connect_Receivers::instance();
			$this->_stripe_connect_commissions = YITH_Stripe_Connect_Commissions::instance();
			$this->_stripe_connect_gateway     = YITH_Stripe_Connect_Gateway::instance();


			if ( class_exists( 'WC_Stripe' ) ) {
				add_action( 'admin_notices', array( $this, 'print_wc_stripe_message' ) );
			}

			// Checks the keys for test mode...
			if ( 'yes' == $this->_stripe_connect_gateway->test_live ) {
				$this->_require_key_test_message = sprintf( __( '<b>%s -</b> you have enable Test live. This field is required: ', 'yith-stripe-connect-for-woocommerce' ), 'YITH Stripe Connect for WooCommerce' );

				$test_mode_client_id = $this->_stripe_connect_gateway->get_option( 'api-dev-client-id' );
				$public_test_key     = $this->_stripe_connect_gateway->get_option( 'api-public-test-key' );
				$secret_test_key     = $this->_stripe_connect_gateway->get_option( 'api-secret-test-key' );

				if ( empty( $test_mode_client_id ) ) {
					add_action( 'admin_notices', array( $this, 'print_wc_stripe_test_mode_client_id_required' ) );
				}
				if ( empty( $public_test_key ) ) {
					add_action( 'admin_notices', array( $this, 'print_wc_stripe_public_test_key_required' ) );
				}
				if ( empty( $secret_test_key ) ) {
					add_action( 'admin_notices', array( $this, 'print_wc_stripe_secret_test_key_required' ) );
				}
			} else { // Check the keys for live mode...
				$this->_require_key_live_message = sprintf( __( '<b>%s -</b> Need fill the following field to work: ', 'yith-stripe-connect-for-woocommerce' ), 'YITH Stripe Connect for WooCommerce' );

				$prod_client_id  = $this->_stripe_connect_gateway->get_option( 'api-prod-client-id' );
				$public_live_key = $this->_stripe_connect_gateway->get_option( 'api-public-live-key' );
				$secret_live_key = $this->_stripe_connect_gateway->get_option( 'api-secret-live-key' );

				if ( empty( $prod_client_id ) ) {
					add_action( 'admin_notices', array( $this, 'print_wc_stripe_prod_mode_client_id_required' ) );
				}
				if ( empty( $public_live_key ) ) {
					add_action( 'admin_notices', array( $this, 'print_wc_stripe_public_live_key_required' ) );
				}
				if ( empty( $secret_live_key ) ) {
					add_action( 'admin_notices', array( $this, 'print_wc_stripe_secret_live_key_required' ) );
				}

			}

			add_action( 'admin_notices', array( $this, 'print_wc_stripe_connect_uri_webhook_message' ) );

			// register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
		}

		/**
		 * action_links function.
		 *
		 * @access public
		 *
		 * @param mixed $links
		 *
		 * @return array
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, 'yith_wcsc_panel', true );
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
		 *
		 * @return   Array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WCSC_INIT' ) {
			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug'] = YITH_WCSC_SLUG;
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @since    1.0.0
		 * @author   Francisco Javier Mateo <francisco.mateo@yithemes.com>
		 * @use      /Yit_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {

			if ( ! empty( $this->_panel ) ) {
				return;
			}
			$admin_tabs = apply_filters( 'yith_wcsc_admin_tabs', array(
					'settings'    => _x( 'Settings', 'tab name', 'yith-stripe-connect-for-woocommerce' ),
					//@since 1.0.0
					'receiver'    => _x( 'Receivers', 'tab name', 'yith-stripe-connect-for-woocommerce' ),
					//@since 1.0.0
					'commissions' => _x( 'Commissions', 'tab name', 'yith-stripe-connect-for-woocommerce' ),
					//@since 1.0.0
				)
			);
			if ( $this->show_premium_landing ) {
				$admin_tabs['premium'] = __( 'Premium Version', 'yith-stripe-connect-for-woocommerce' );
			}
			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => __( 'Stripe Connect', 'yith-stripe-connect-for-woocommerce' ),
				'menu_title'       => __( 'Stripe Connect', 'yith-stripe-connect-for-woocommerce' ),
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YITH_WCSC_OPTIONS_PATH
			);

			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once( YITH_WACP_DIR . '/plugin-fw/lib/yit-plugin-panel-wc.php' );
			}

			/* === Fixed: not updated theme/old plugin framework  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once( 'plugin-fw/lib/yit-plugin-panel-wc.php' );
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		public function get_receiver_panel() {
			global $pagenow;
			$this->enqueue_receivers_scripts();

			$receivers_args = array();
			$context        = '';
			$product_id     = '';
			if ( isset($_GET['post']) && ( $pagenow == 'post.php' && get_post_type( $_GET['post'] ) == 'product' || $pagenow == 'post-new.php' && $_GET['post_type'] == 'product' ) ) {
				$context                        = 'product_edit_page';
				$product_id                     = $_GET['post'];
				$receivers_args['product_id']   = $product_id;
				$receivers_args['all_products'] = true;
			}
			$args = array(
				'context'         => $context,
				'count_receivers' => $this->_stripe_connect_receivers->get_receivers_count( $receivers_args ),
				'current_page'    => isset( $_GET['current_page'] ) ? abs( (int) $_GET['current_page'] ) : 1,
				'items_per_page'  => $this->_stripe_connect_receivers->items_per_page,
				'receivers'       => $this->_stripe_connect_receivers->get_receivers( $receivers_args, true )
			);

			yith_wcsc_get_template( 'receivers-panel', $args, 'admin' );
		}

		public function get_commissions_panel() {
			$this->enqueue_commissions_scripts();

			$commissions_args = array(
				'product_id' => isset( $_GET['yith_wcs_product'] ) ? $_GET['yith_wcs_product'] : '',
				'user_id'    => isset( $_GET['yith_wcs_user'] ) ? $_GET['yith_wcs_user'] : '',
				'date_from'  => isset( $_GET['yith_wcsc_date_from'] ) ? $_GET['yith_wcsc_date_from'] : '',
				'date_to'    => isset( $_GET['yith_wcsc_date_to'] ) ? $_GET['yith_wcsc_date_to'] : '',
				'day'        => isset( $_GET['yith_wcsc_day'] ) ? $_GET['yith_wcsc_day'] : '',
				'month_year' => isset( $_GET['yith_wcsc_month_year'] ) ? $_GET['yith_wcsc_month_year'] : '',
				'orderby'    => isset( $_GET['yith_wcsc_orderby'] ) ? $_GET['yith_wcsc_orderby'] : 'ID',
				'order'      => isset( $_GET['yith_wcsc_order'] ) ? $_GET['yith_wcsc_order'] : 'DESC'
			);

			$args = array(
				'count_commissions' => $this->_stripe_connect_commissions->get_commissions_count( $commissions_args ),
				'current_page'      => isset( $_GET['current_page'] ) ? abs( (int) $_GET['current_page'] ) : 1,
				'items_per_page'    => $this->_stripe_connect_commissions->items_per_page,
				'commissions'       => $this->_stripe_connect_commissions->get_commissions( $commissions_args, true ),
			);

			yith_wcsc_get_template( 'commissions-panel', $args, 'common' );
		}

		/**
		 * Premium Tab Template
		 *
		 * Load the premium tab template on admin page
		 *
		 * @return   void
		 * @since    1.0.0
		 * @author   Francisco Javier Mateo <francisco.mateo@yithemes.com>
		 * @return void
		 */
		public function premium_tab() {
			$premium_tab_template = YITH_WCSC_PATH . 'templates/admin/' . $this->_premium;
			if ( file_exists( $premium_tab_template ) && $this->show_premium_landing ) {
				include_once( $premium_tab_template );
			}
		}

		/**
		 * Get the premium landing uri
		 *
		 * @since   1.0.0
		 * @author  Francisco Javier Mateo <francisco.mateo@yithemes.com>
		 * @return  string The premium landing link
		 */
		public function get_premium_landing_uri() {
			return defined( 'YITH_REFER_ID' ) ? $this->_premium_landing . '?refer_id=' . YITH_REFER_ID : $this->_premium_landing . '?refer_id=1030585';
		}

		/**
		 * Sidebar links
		 *
		 * @return   array The links
		 * @since    1.0.0
		 * @author   Francisco Javier Mateo <francisco.mateo@yithemes.com>
		 */
		public function get_sidebar_link() {
			$links = array(
				array(
					'title' => __( 'Plugin documentation', 'yith-stripe-connect-for-woocommerce-for-woocommerce' ),
					//@since 1.0.0
					'url'   => $this->_official_documentation,
				),
				array(
					'title' => __( 'Help Center', 'yith-stripe-connect-for-woocommerce-for-woocommerce' ),
					//@since 1.0.0
					'url'   => 'https://support.yithemes.com/hc/en-us/categories/202568518-Plugins',
				),
			);

			return $links;
		}

		/**
		 * Print custom tab of settings for Stripe Connect sub panel
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_panel() {
			$panel_template = YITH_WCSC_PATH . 'templates/admin/settings-tab.php';

			if ( ! file_exists( $panel_template ) ) {
				return;
			}

			global $current_section;
			$current_section = 'yith-stripe-connect';

			WC_Admin_Settings::get_settings_pages();

			if ( ! empty( $_POST ) ) {
				$gateways = WC()->payment_gateways()->payment_gateways();
				$gateways[ YITH_Stripe_Connect::$gateway_id ]->process_admin_options();
			}

			include_once( $panel_template );

		}

		/** Add our custom metaboxes that allows us add our custom receivers on product page */
		public function add_meta_boxes( $post_type ) {

			if ( 'product' == $post_type && current_user_can( 'administrator' ) ) { // Only administrator can see the metabox on product edit page.
				$title = __( 'Stripe Connect Receivers', 'yith-stripe-connect-for-woocommerce' ); //@since 1.0.0
				add_meta_box( 'stripe-connect-receiver', $title, array(
					$this,
					'get_receiver_panel'
				), $post_type );
			}
		}

		/**
		 * Enqueue Scripts
		 *
		 * Register and enqueue scripts for Admin
		 *
		 * @author     Francisco Javier Mateo <francisco.mateo@yithemes.com>
		 * @since      1.0
		 * @return void
		 */
		public function enqueue_scripts() {
			$debug_enabled = defined( 'WP_DEBUG' ) ? WP_DEBUG : false;
			$prefix        = ! $debug_enabled ? '.min' : '';

			$data_to_js = array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			);

            $current_page = isset($_GET['page']) ? $_GET['page'] : '';
            $section =  isset($_GET['section']) ? $_GET['section'] : '';

			wp_register_script( 'yith-wcsc-admin', YITH_WCSC_ASSETS_URL . 'js/script-yith-sc-admin' . $prefix . '.js', array( 'jquery', 'select2' ), YITH_WCSC_VERSION, true );
			wp_localize_script( 'yith-wcsc-admin', 'yith_wcsc_admin', $data_to_js );

            if ( 'yith_wcsc_panel' == $current_page || 'yith-stripe-connect' == $section ) {
	            wp_enqueue_script( 'yith-wcsc-admin' );
            }
		}

		public function enqueue_receivers_scripts() {
			global $pagenow;
			$debug_enabled   = defined( 'WP_DEBUG' ) ? WP_DEBUG : false;
			$prefix          = ! $debug_enabled ? '.min' : '';
			$js_dependencies = array(
				'jquery',
				'jquery-ui-sortable'
			);
            $data_to_js = array();
			if ( $pagenow == 'post.php' && isset( $_GET['post'] ) && ( get_post_type( $_GET['post'] ) == 'product' || $pagenow == 'post-new.php' && $_GET['post_type'] == 'product' ) ) {
				$data_to_js['context']    = 'product_edit_page';
				$data_to_js['product_id'] = $_GET['post'];
			}

			wp_register_style( 'yith-wcsc-receivers-style', YITH_WCSC_ASSETS_URL . 'css/style-yith-sc-receivers.css', null, YITH_WCSC_VERSION );
			wp_register_script( 'yith-wcsc-receivers-script', YITH_WCSC_ASSETS_URL . 'js/script-yith-sc-receivers' . $prefix . '.js', $js_dependencies, YITH_WCSC_VERSION, true );

			wp_localize_script( 'yith-wcsc-receivers-script', 'yith_wcsc_receivers', $data_to_js );

			wp_enqueue_style( 'yith-wcsc-receivers-style' );
			wp_enqueue_script( 'yith-wcsc-receivers-script' );

		}

		public function enqueue_commissions_scripts() {
			$this->_stripe_connect_commissions->enqueue_scripts();
		}

		public function print_receiver_row_action() {
			if ( isset( $_POST['index'] ) ) {
				$context    = '';
				$product_id = '';
				if ( isset( $_POST['context'] ) & isset( $_POST['product_id'] ) ) {
					$context                      = $_POST['context'];
					$product_id                   = $_POST['product_id'];
					$receivers_args['product_id'] = $product_id;
				}

				$args = array(
					'context'      => $context,
					'index'        => $_POST['index'],
					'receiver_row' => array(
						'product_id' => $product_id
					)
				);

				yith_wcsc_get_template( 'receiver-row', $args, 'admin' );
			}
			die();
		}

		public function save_receivers_action() {
			$receivers_to_save   = explode( ",", $_POST['_receivers_to_save'] );
			$receivers_to_remove = explode( ",", $_POST['_receivers_to_remove'] );

			$created = array();
			foreach ( $receivers_to_save as $receiver_to_save ) {
				$receiver = isset( $_POST['_receivers'][ $receiver_to_save ]['ID'] ) ? $_POST['_receivers'][ $receiver_to_save ] : array();
				if ( ! empty( $receiver ) ) {
					$stripe_user_id              = get_user_meta( $receiver['user_id'], 'stripe_user_id', true );
					$receiver['status_receiver'] = $stripe_user_id ? 'connect' : 'disconnect';
					$receiver['stripe_id']       = $stripe_user_id;

					if ( 'new' != $receiver['ID'] ) {
						$this->_stripe_connect_receivers->update( $receiver['ID'], $receiver );
					} else {
						$inserted  = $this->_stripe_connect_receivers->insert( $receiver );
						$created[] = array(
							'index' => $receiver_to_save,
							'id'    => $inserted
						);
					}
				}
			}

			foreach ( $receivers_to_remove as $receiver_to_remove ) {
				$this->_stripe_connect_receivers->delete( $receiver_to_remove );
			}

			wp_send_json( $created );
			die();
		}

		public function save_redirect_uri_done() {
			$value = update_option( 'yith_wcsc_redirected_uri', 'yes' );
			wp_send_json_success( $value );
		}

		public function save_webhook_done() {
			$value = update_option( 'yith_wcsc_webhook_defined', 'yes' );
			wp_send_json_success( $value );
		}

		public function print_wc_stripe_message() {
			?>
            <div class="notice notice-warning is-dismissible">
                <p><?php _e( 'Seems that you have WooCommerce Stripe Gateway activated. Please, disable it for proper operation.', 'yith-stripe-connect-for-woocommerce' ); ?></p>
            </div>
			<?php
		}

		public function print_wc_stripe_test_mode_client_id_required() {
			?>
            <div class="notice notice-warning is-dismissible">
                <p><?php echo $this->_require_key_test_message . __( '<b>Test Mode Client ID</b>', 'yith-stripe-connect-for-woocommerce' ); ?></p>
            </div>
			<?php
		}

		public function print_wc_stripe_public_test_key_required() {
			?>
            <div class="notice notice-warning is-dismissible">
                <p><?php echo $this->_require_key_test_message . __( '<b>Publishable test key</b>', 'yith-stripe-connect-for-woocommerce' ); ?></p>
            </div>
			<?php
		}

		public function print_wc_stripe_secret_test_key_required() {
			?>
            <div class="notice notice-warning is-dismissible">
                <p><?php echo $this->_require_key_test_message . __( '<b>Secret test key</b>', 'yith-stripe-connect-for-woocommerce' ); ?></p>
            </div>
			<?php
		}

		public function print_wc_stripe_prod_mode_client_id_required() {
			?>
            <div class="notice notice-warning is-dismissible">
                <p><?php echo $this->_require_key_live_message . __( '<b>Live Mode Client ID</b>', 'yith-stripe-connect-for-woocommerce' ); ?></p>
            </div>
			<?php
		}

		public function print_wc_stripe_public_live_key_required() {
			?>
            <div class="notice notice-warning is-dismissible">
                <p><?php echo $this->_require_key_live_message . __( '<b>Publishable live key</b>', 'yith-stripe-connect-for-woocommerce' ); ?></p>
            </div>
			<?php
		}

		public function print_wc_stripe_secret_live_key_required() {
			?>
            <div class="notice notice-warning is-dismissible">
                <p><?php echo $this->_require_key_live_message . __( '<b>Secret live key</b>', 'yith-stripe-connect-for-woocommerce' ); ?></p>
            </div>
			<?php
		}

		public function print_wc_stripe_connect_uri_webhook_message() {
			$current_page = isset( $_GET['page'] ) ? $_GET['page'] : '';
			$section      = isset( $_GET['section'] ) ? $_GET['section'] : '';
			if ( 'yith_wcsc_panel' == $current_page || 'yith-stripe-connect' == $section ) {

				if ( 'yes' != get_option( 'yith_wcsc_webhook_defined' ) ) {
					?>
                    <div class="notice notice-warning yith_wcsc_message yith_wcsc_message_webhook">
                        <p><?php echo sprintf( __( '<b>%s -</b> Define the following <b>Webhook</b> %s in your <a href="%s" target="_blank">Stripe Dashboard > API > Webhooks</a> (Endpoints receiving events from Connect applications) section.', 'yith-stripe-connect-for-woocommerce' ), 'YITH Stripe Connect for WooCommerce', '<code>' . esc_url( site_url( '/wc-api/sc_webhook_event' ) ) . '</code>', 'https://dashboard.stripe.com/account/webhooks' ); ?></p>
                        <p>
                            <a class="button-primary"> <?php echo __( 'Done', 'yith-stripe-connect-for-woocommerce' ); ?> </a>
                        </p>
                    </div>
					<?php
				}
				if ( 'yes' != get_option( 'yith_wcsc_redirected_uri' ) ) {
					?>
                    <div class="notice notice-warning yith_wcsc_message yith_wcsc_message_redirect_uri">
                        <p><?php echo sprintf( __( '<b>%s -</b> Define the following <b>Redirect URI</b> %s in your <a href="%s" target="_blank">Stripe Dashboard > Connect > Settings ></a> <b>Redirect URIs</b> section.', 'yith-stripe-connect-for-woocommerce' ), 'YITH Stripe Connect for WooCommerce', '<code>' . esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ) . 'stripe-connect' . '</code>', 'https://dashboard.stripe.com/account/applications/settings' ); ?></p>
                        <p>
                            <a class="button-primary"> <?php echo __( 'Done', 'yith-stripe-connect-for-woocommerce' ); ?> </a>
                        </p>

                    </div>
					<?php
				}
			}
		}

		/* === LICENCE HANDLING METHODS === */

		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YITH_WCSC_PATH . 'plugin-fw/licence/lib/yit-licence.php';
				require_once YITH_WCSC_PATH . 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}

			YIT_Plugin_Licence()->register( YITH_WCSC_INIT, YITH_WCSC_SECRET_KEY, YITH_WCSC_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once( YITH_WCSC_PATH . 'plugin-fw/lib/yit-upgrade.php' );
			}

			YIT_Upgrade()->register( YITH_WCSC_SLUG, YITH_WCSC_INIT );
		}
	}

}