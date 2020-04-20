<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WC_Save_For_Later' ) ) {

	class YITH_WC_Save_For_Later {
		/**static instance of the class
		 * @var YITH_WC_Save_For_Later
		 */
		protected static $instance;

		/** db version
		 * @var string
		 */
		protected $_db_version = '1.0.1';
		/**
		 * @var Panel
		 */
		protected $_panel;
		/**
		 * @var Panel Page
		 */
		protected $_panel_page = 'yith_wc_save_for_later_panel';
		/**
		 * @var string
		 */
		protected $_premium = 'premium.php';


		/**
		 * @var array, contains information about the products in the "save list"
		 */
		protected $savelists;

		protected $_suffix;


		public function __construct() {

			if ( ! defined( 'YWSFL_DB_VERSION' ) ) {
				define( 'YWSFL_DB_VERSION', $this->_db_version );
			}

			$this->_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			// Load Plugin Framework
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			add_action( 'plugins_loaded', array( $this, 'load_privacy_class' ), 20 );
			//Add action links
			add_filter( 'plugin_action_links_' . plugin_basename( YWSFL_DIR . '/' . basename( YWSFL_FILE ) ), array(
				$this,
				'action_links'
			), 5 );
			//add row meta
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );
			//  Add action menu
			add_action( 'yith_wc_save_for_later_premium', array( $this, 'premium_tab' ) );
			add_action( 'admin_menu', array( $this, 'add_menu_page' ), 5 );

			add_action( 'wp_enqueue_scripts', array( $this, 'include_free_style_and_script' ), 20 );

			//initialize the user list if logged
			add_action( 'init', array( $this, 'initialize_user_list' ), 0 );

			//print the link in product name column
			add_filter( 'woocommerce_cart_item_name', array( $this, 'print_add_link_in_list' ), 15, 3 );
			//print the save list in cart page
			add_action( 'woocommerce_after_cart', array( $this, 'print_product_in_list' ) );
			add_action( 'woocommerce_cart_is_empty', array( $this, 'print_product_in_list' ) );

			//Add product in savelist
			add_action( 'init', array( $this, 'add_to_saveforlater' ) );
			add_action( 'wp_ajax_add_to_saveforlater', array( $this, 'add_to_saveforlater_ajax' ) );
			add_action( 'wp_ajax_nopriv_add_to_saveforlater', array( $this, 'add_to_saveforlater_ajax' ) );

			//Remove product in savelist
			add_action( 'init', array( $this, 'remove_from_savelist' ) );
			add_action( 'wp_ajax_remove_from_savelist', array( $this, 'remove_from_savelist_ajax' ) );
			add_action( 'wp_ajax_nopriv_remove_from_savelist', array( $this, 'remove_from_savelist_ajax' ) );

			//remove product to cart, after "save list"
			add_action( 'wp_ajax_remove_to_cart_after_save_list', array( $this, 'remove_to_cart_after_save_list' ) );
			add_action( 'wp_ajax_nopriv_remove_to_cart_after_save_list', array(
				$this,
				'remove_to_cart_after_save_list'
			) );

			//remove product in save list, after add to cart
			add_action( 'woocommerce_add_to_cart', array( $this, 'remove_from_savelist_after_add_to_cart' ), 10, 2 );


			/*GDPR integration*/


			add_filter( 'wp_privacy_personal_data_exporters', array(
				$this,
				'register_export_save_for_later_list'
			), 10, 2 );
			add_filter( 'wp_privacy_personal_data_erasers', array(
				$this,
				'register_eraser_save_for_later_list'
			), 10, 2 );

			global $wpdb;
			$wpdb->yith_wsfl_table = YITH_WSFL_Install()->_table_name;

			if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
				$this->create_table();
				add_action( 'plugins_loaded', 'ywsfl_add_gutenberg_block', 20 );
			}

		}

		/**Update the user savelist if logged
		 * @author YITHEMES
		 * @since 1.0.0
		 * @use init
		 */
		public function initialize_user_list() {

			if ( is_user_logged_in() ) {

				$this->savelists['user_id'] = get_current_user_id();

				$cookie = yith_getcookie( 'yith_wsfl_savefor_list' );

				foreach ( $cookie as $item ) {
					$this->savelists['product_id']   = $item['product_id'];
					$this->savelists['quantity']     = $item['quantity'];
					$this->savelists['variation_id'] = isset( $item['variation_id'] ) ? $item['variation_id'] : - 1;
					$this->add();
				}
				yith_destroycookie( 'yith_wsfl_savefor_list' );
			}
			// update cookie from old version to new one
			$this->_update_cookies();
			$this->_destroy_serialized_cookies();
		}

		/**add a product to savelist
		 * @author YITHEMES
		 * @since 1.0.0
		 * @return string
		 */
		public function add() {
			global $wpdb;
			$user_id      = isset( $this->savelists['user_id'] ) ? $this->savelists['user_id'] : - 1;
			$product_id   = isset( $this->savelists['product_id'] ) ? $this->savelists['product_id'] : - 1;
			$quantity     = isset( $this->savelists['quantity'] ) ? $this->savelists['quantity'] : 1;
			$variation_id = isset( $this->savelists['variation_id'] ) ? $this->savelists['variation_id'] : - 1;

			if ( $product_id == - 1 ) {
				return "error";
			}

			if ( $this->is_product_in_savelist( $product_id, $variation_id ) ) {
				return "exists";
			}

			if ( $user_id != - 1 ) {

				$args = array(
					'product_id'   => $product_id,
					'user_id'      => $user_id,
					'quantity'     => $quantity,
					'variation_id' => $variation_id,
					'date_added'   => date( 'Y-m-d H:i:s' )
				);

				$res = $wpdb->insert( YITH_WSFL_Install()->_table_name, $args );

			} else {
				$cookie = array(
					'product_id'   => $product_id,
					'quantity'     => $quantity,
					'variation_id' => $variation_id
				);

				$savelist_cookie = yith_getcookie( 'yith_wsfl_savefor_list' );

				$savelist_cookie[] = $cookie;


				yith_setcookie( 'yith_wsfl_savefor_list', $savelist_cookie );

				$res = true;
			}

			if ( $res ) {

				return "true";
			} else {
				return "error";
			}

		}

		/** remove product to savelist
		 * @author YITHEMES
		 * @since 1.0.0
		 * @return string
		 */
		public function remove() {

			global $wpdb;
			$user_id    = isset( $this->savelists['user_id'] ) ? $this->savelists['user_id'] : - 1;
			$product_id = isset( $this->savelists['product_id'] ) ? $this->savelists['product_id'] : - 1;


			if ( $product_id == - 1 ) {
				return "errors";
			}

			if ( is_user_logged_in() ) {

				$sql       = "DELETE FROM {$wpdb->yith_wsfl_table} WHERE {$wpdb->yith_wsfl_table}.user_id=%d AND {$wpdb->yith_wsfl_table}.product_id=%d";
				$sql_parms = array(
					$user_id,
					$product_id
				);

				$result = $wpdb->query( $wpdb->prepare( $sql, $sql_parms ) );

				if ( $result ) {
					return "true";
				} else {
					return "false";
				}
			} else {
				$savelist_cookie = yith_getcookie( 'yith_wsfl_savefor_list' );

				foreach ( $savelist_cookie as $key => $item ) {
					if ( $item['product_id'] == $product_id ) {
						unset( $savelist_cookie[ $key ] );
					}
				}
				yith_setcookie( 'yith_wsfl_savefor_list', $savelist_cookie );

				return "true";
			}
		}

		/**check if a product is in savelist
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param $product_id
		 *
		 * @return bool
		 */
		public function is_product_in_savelist( $product_id, $variation_id = - 1 ) {
			$exist = false;

			if ( is_user_logged_in() ) {
				global $wpdb;

				$user_id = get_current_user_id();

				$query = "SELECT COUNT(*) as cnt
                             FROM {$wpdb->yith_wsfl_table}
                             WHERE {$wpdb->yith_wsfl_table}.product_id=%d AND {$wpdb->yith_wsfl_table}.user_id=%d";

				$parms = array(
					$product_id,
					$user_id
				);

				if ( $variation_id > 0 ) {

					$query .= " AND {$wpdb->yith_wsfl_table}.variation_id=%d";

					$parms[] = $variation_id;
				}

				$results = $wpdb->get_var( $wpdb->prepare( $query, $parms ) );

				return (bool) ( $results > 0 );
			} else {
				$cookie = yith_getcookie( 'yith_wsfl_savefor_list' );

				foreach ( $cookie as $key => $item ) {
					if ( $item['product_id'] == $product_id ) {
						$exist = true;
					}
				}

				return $exist;
			}

		}

		/**return all product in savelist for user_id
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param array $args
		 *
		 * @return array|mixed
		 */
		public function get_savelist_by_user( $args = array() ) {

			global $wpdb;

			$default = array(
				'user_id'    => ( is_user_logged_in() ) ? get_current_user_id() : false,
				'product_id' => false,
				'id'         => false, // only for table select
				'limit'      => false,
				'offset'     => 0
			);

			$args = wp_parse_args( $args, $default );
			extract( $args );

			if ( ! empty( $user_id ) ) {
				$query = "SELECT *
                             FROM {$wpdb->yith_wsfl_table}
                             WHERE {$wpdb->yith_wsfl_table}.user_id=%d";

				$query_params = array( $user_id );

				if ( ! empty( $product_id ) ) {
					$query          .= " AND {$wpdb->yith_wsfl_table}.product_id=%d";
					$query_params[] = $product_id;
				}

				if ( ! empty( $id ) ) {
					$query          .= " AND {$wpdb->yith_wsfl_table}.ID=%d";
					$query_params[] = $id;
				}

				if ( ! empty( $limit ) ) {
					$query .= " LIMIT " . $offset . ", " . $limit;
				}

				$savelist = $wpdb->get_results( $wpdb->prepare( $query, $query_params ), ARRAY_A );

			} else {
				$savelist = yith_getcookie( 'yith_wsfl_savefor_list' );

				if ( ! empty( $limit ) ) {
					$savelist = array_slice( $savelist, $offset, $limit );
				}
			}

			return $savelist;
		}

		/**create the table for savelist
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function create_table() {
			$curr_db_version = get_option( 'ywsfl_db_version' );

			if ( $curr_db_version == '1.0.0' ) {

				add_action( 'init', array( YITH_WSFL_Install(), 'update' ) );
				do_action( 'ywsfl_installed' );
				do_action( 'ywsfl_updated' );
			} elseif ( $curr_db_version != $this->_db_version || ! YITH_WSFL_Install()->is_table_created() ) {
				add_action( 'init', array( YITH_WSFL_Install(), 'init' ) );
				do_action( 'ywsfl_installed' );
			}
		}

		/**add a new product in savelist
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function add_to_saveforlater() {
			if ( isset( $_GET['save_for_later'] ) ) {
				$this->savelists['product_id'] = $_GET['save_for_later'];

				$res = $this->add();
			}
		}

		/**call ajax for add a new product in savelist
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function add_to_saveforlater_ajax() {

			$this->savelists['product_id'] = isset( $_POST['save_for_later'] ) ? $_POST['save_for_later'] : - 1;
			$page_url                      = isset( $_POST['page_url'] ) ? $_POST['page_url'] : '';
			$return                        = $this->add();
			$message                       = '';
			if ( $return == 'true' ) {
				$message = __( 'Product added', 'yith-woocommerce-save-for-later' );
			} elseif ( $return == 'exists' ) {
				$message = __( 'Product already in Save for later', 'yith-woocommerce-save-for-later' );
			}


			wp_send_json(
				array(
					'result'   => $return,
					'message'  => $message,
					'template' => YITH_WSFL_Shortcode::saveforlater( array( 'current_page' => $page_url ) )
				)
			);
		}

		/**remove a product from savelist
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function remove_from_savelist() {

			if ( isset( $_GET['remove_from_savelist'] ) ) {

				$this->savelists['product_id'] = $_GET['remove_from_savelist'];
				$this->remove();
			}
		}

		/** call ajax for remove a product from savelist
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function remove_from_savelist_ajax() {

			$this->savelists['product_id']   = isset( $_REQUEST['remove_from_savelist'] ) ? $_REQUEST['remove_from_savelist'] : - 1;
			$this->savelists['variation_id'] = isset( $_REQUEST['variation_id'] ) ? $_REQUEST['variation_id'] : - 1;
			$result                          = $this->remove();
			$message                         = '';

			if ( $result == "true" ) {
				$message = __( 'Product deleted from Save for later', 'yith-woocommerce-save-for-later' );
			} else {
				$message = __( 'No product', 'yith-woocommerce-save-for-later' );
			}

			wp_send_json(
				array(
					'result'   => $result,
					'message'  => $message,
					'template' => YITH_WSFL_Shortcode::saveforlater()
				)
			);

		}

		/**print a "Save for later" link in cart table
		 * @author YITHEMES
		 * @since 1.0.0
		 * @use woocommerce_cart_item_name
		 *
		 * @param $product_name
		 * @param $cart_item
		 * @param $cart_item_key
		 *
		 * @return string
		 */
		public function print_add_link_in_list( $product_name, $cart_item, $cart_item_key ) {

			if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				$product_id = apply_filters( 'ywsfl_add_to_save_for_later_list', $cart_item['product_id'], $cart_item );

				$query_args         = array(
					'save_for_later' => $product_id
				);
				$save_for_later_url = esc_url( add_query_arg( $query_args, function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : WC()->cart->get_cart_url() ) );
				//   $save_for_later_url = esc_url(add_query_arg('save_for_later', $product_id), get_permalink(wc_get_page_id('myaccount')));
				$text_link = get_option( 'ywsfl_text_add_button' );
				$href      = '<div class="saveforlater_button" >
                                <a href="' . $save_for_later_url . '" rel="nofollow" class="add_saveforlater" title="Save for Later">' . $text_link . '</a>
                            </div>';

				return apply_filters( 'ywsfl_saveforlater_link', $product_name . $href, $product_name, $cart_item, $cart_item_key );
			} else {
				return $product_name;
			}
		}


		/**print the product list in "Save For later"
		 * @author YITHEMES
		 * @since 1.0.0
		 * @use woocommerce_after_cart,woocommerce_cart_is_empty
		 */
		public function print_product_in_list() {
			echo YITH_WSFL_Shortcode::saveforlater();
		}


		/**call ajax for remove product from cart, after save list
		 * @author YITHEMES
		 * @since 1.0.0
		 * @use wp_ajax_remove_to_cart_after_save_list,wp_ajax_nopriv_remove_to_cart_after_save_list
		 */
		public function remove_to_cart_after_save_list() {
			$cart = WC()->cart;
			$res  = false;

			$product_id   = ( isset( $_REQUEST['variation_id'] ) && $_REQUEST['variation_id'] > 0 ) ? $_REQUEST['variation_id'] : $_REQUEST['product_id'];
			$cart_itemkey = '';
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

				if ( $cart_item['product_id'] == $product_id || $cart_item['variation_id'] == $product_id ) {
					$res          = $cart->remove_cart_item( $cart_item_key );
					$cart_itemkey = $cart_item_key;
					break;
				}
			}

			wp_send_json(
				array(
					'result'        => $res,
					'cart_item_key' => $cart_itemkey
				)
			);
		}

		/**remove product form save list, after click "add to cart"
		 * @author YITHEMES
		 * @since 1.0.0
		 * @use woocommerce_add_to_cart
		 */
		public function remove_from_savelist_after_add_to_cart( $cart_item_key, $product_id ) {
			global $yith_wsfl_is_savelist;


			if ( isset( $_REQUEST['remove_to_cart_after_save_list'] ) ) {
				$this->savelists['product_id'] = $_REQUEST['remove_to_cart_after_save_list'];

			} elseif ( ! $yith_wsfl_is_savelist && isset( $_REQUEST['add-to-cart'] ) ) {
				$this->savelists['product_id']   = $_REQUEST['add-to-cart'];
				$this->savelists['variation_id'] = isset( $_REQUEST['variation_id'] ) ? $_REQUEST['variation_id'] : - 1;
			} else {
				$this->savelists['product_id']   = $product_id;
				$this->savelists['variation_id'] = isset( $_REQUEST['variation_id'] ) ? $_REQUEST['variation_id'] : - 1;
			}
			$this->remove();

		}

		/**include style and script
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 */
		public function include_free_style_and_script() {

			wp_register_style( 'ywsfl_free_frontend', YWSFL_ASSETS_URL . 'css/ywsfl_frontend.css' );
			wp_enqueue_style( 'ywsfl_free_frontend' );

			$this->enqueue_scripts();
		}

		/**
		 * Enqueue plugin scripts.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue_scripts() {

			wp_register_script( 'yith_wsfl_free', YWSFL_ASSETS_URL . 'js/yith_free_wsfl' . $this->_suffix . '.js', array( 'jquery' ), YWSFL_VERSION, true );
			$current_page   = is_cart() ? 'cart' : 'other';
			$yith_wsfl_l10n = array(
				'ajax_url'          => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
				'is_user_logged_in' => is_user_logged_in(),
				'ajax_loader_url'   => YWSFL_ASSETS_URL . 'assets/images/ajax-loader.gif',
				'labels'            => array(
					'cookie_disabled'       => __( 'We are sorry, but this feature is available only if cookies are enabled in your browser.', 'yith-woocommerce-save-for-later' ),
					'added_to_cart_message' => sprintf( '<div class="woocommerce-message">%s</div>', __( 'Product correctly added to cart', 'yith-woocommerce-save-for-later' ) )
				),
				'current_page'      => $current_page,
				'actions'           => array(
					'add_to_savelist_action'                      => 'add_to_saveforlater',
					'remove_from_savelist_action'                 => 'remove_from_savelist',
					'remove_from_cart_after_add_save_list_action' => 'remove_to_cart_after_save_list',

				)
			);

			wp_localize_script( 'yith_wsfl_free', 'yith_wsfl_l10n', $yith_wsfl_l10n );
		}


		/**
		 * Destroy serialize cookies, to prevent major vulnerability
		 * @author YITHEMES
		 * @return void
		 * @since 1.0.0
		 */
		private function _destroy_serialized_cookies() {
			$name = 'yith_wsfl_savefor_list';

			if ( isset( $_COOKIE[ $name ] ) && is_serialized( stripslashes( $_COOKIE[ $name ] ) ) ) {
				$_COOKIE[ $name ] = json_encode( array() );
				yith_destroycookie( $name );
			}
		}

		/**
		 * Update old savelist cookies
		 * @author YITHEMES
		 * @return void
		 * @since 1.0.0
		 */
		private function _update_cookies() {
			$cookie     = yith_getcookie( 'yith_wsfl_savefor_list' );
			$new_cookie = array();

			if ( ! empty( $cookie ) ) {
				foreach ( $cookie as $item ) {
					$new_cookie[] = array(
						'product_id'   => $item['product_id'],
						'quantity'     => isset( $item['quantity'] ) ? $item['quantity'] : 1,
						'variation_id' => isset( $item['variation_id'] ) ? $item['variation_id'] : - 1,

					);
				}

				yith_setcookie( 'yith_wsfl_savefor_list', $new_cookie );
			}
		}


		/**Returns single instance of the class
		 * @author YITHEMES
		 * @since 1.0.0
		 * @return YITH_WC_Save_For_Later
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}


		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     /Yit_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function add_menu_page() {
			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs = apply_filters( 'ywsfl_add_plugin_tab', array(
				'general'         => __( 'Settings', 'yith-woocommerce-save-for-later' ),
				'premium-landing' => __( 'Premium Version', 'yith-woocommerce-save-for-later' )
			) );

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => __( 'Save for later', 'yith-woocommerce-save-for-later' ),
				'menu_title'       =>  'Save for later',
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YWSFL_DIR . '/plugin-options'
			);

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );

		}

		/**load plugin_fw
		 * @author YITHEMES
		 * @since 1.0.0
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
		 * Premium Tab Template
		 *
		 * Load the premium tab template on admin page
		 *
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return  void
		 */
		public function premium_tab() {
			$premium_tab_template = YWSFL_TEMPLATE_PATH . '/admin/' . $this->_premium;
			if ( file_exists( $premium_tab_template ) ) {
				include_once( $premium_tab_template );
			}
		}

		/**
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $links | links plugin array
		 *
		 * @return   mixed Array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return mixed
		 * @use plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {

			$is_premium = defined( 'YWSFL_INIT' );
			$links = yith_add_action_links( $links, $this->_panel_page , $is_premium );

			return $links;
		}

		/**
		 * plugin_row_meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $new_row_meta_args
		 * @param $plugin_meta
		 * @param $plugin_file
		 * @param $plugin_data
		 * @param $status
		 *
		 * @return   array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YWSFL_FREE_INIT' ) {

			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug'] = YWSFL_SLUG;
			}

			return $new_row_meta_args;

		}

		/**
		 * register export action
		 * @author Salvatore Strano
		 * @since 1.0.8
		 *
		 * @param array $exporters
		 *
		 * @return array
		 */
		public function register_export_save_for_later_list( $exporters ) {

			$exporters['ywsfl-export-list'] = array(
				'exporter_friendly_name' => __( 'Save for Later', 'yith-woocommerce-save-for-later' ),
				'callback'               => array( $this, 'export_save_for_later_list' )
			);

			return $exporters;
		}

		/**
		 * export save for later list
		 * @author  Salvatore Strano
		 * @since 1.0.8
		 *
		 * @param $email_address
		 * @param int $page
		 *
		 * @return array
		 */
		public function export_save_for_later_list( $email_address, $page = 1 ) {
			$data_to_export = array();

			$user = get_user_by( 'email', $email_address );

			$save_list = array();
			if ( $user instanceof WP_User ) {

				$user_id = $user->ID;

				global $wpdb;

				$table_name = $wpdb->prefix . 'ywsfl_list';
				$query      = $wpdb->prepare( "SELECT * FROM {$table_name} WHERE user_id = %d", $user_id );

				$list = $wpdb->get_results( $query, ARRAY_A );


				if ( count( $list ) > 0 ) {
					$save_list = array();
					$items     = array();
					foreach ( $list as $data ) {

						$product_id   = $data['product_id'];
						$variation_id = $data['variation_id'];
						$qty          = $data['quantity'];

						if ( $variation_id > 0 ) {
							$product = wc_get_product( $variation_id );
						} else {

							$product = wc_get_product( $product_id );
						}

						$items[] = $product->get_formatted_name() . ' x ' . $qty;
					}

					$save_list[] = array(
						'name'  => __( 'Products in list', 'yith-woocommerce-save-for-later' ),
						'value' => implode( ',', $items )
					);
				}

			}
			$data_to_export[] = array(
				'group_id'    => 'ywfsl_list',
				'group_label' => __( 'Save For Later', 'yith-woocommerce-save-for-later' ),
				'data'        => $save_list,
				'item_id'     => 'save_list'
			);

			return array(
				'data' => $data_to_export,
				'done' => true
			);

		}

		/**
		 * @author  Salvatore Strano
		 * @since 1.0.8
		 *
		 * @param $erasers
		 *
		 * @return array
		 */
		public function register_eraser_save_for_later_list( $erasers ) {

			$erasers['ywsfl-export-list'] = array(
				'eraser_friendly_name' => __( 'Save for later', 'yith-woocommerce-review-reminder' ),
				'callback'             => array( $this, 'eraser_save_for_later_list' ),
			);

			return $erasers;

		}


		/**
		 * @author Salvatore Strano
		 * @since 1.0.8
		 *
		 * @param string $email_address
		 * @param int $page
		 *
		 * @return array
		 */
		public function eraser_save_for_later_list( $email_address, $page = 1 ) {

			$response = array(
				'items_removed'  => false,
				'items_retained' => false,
				'messages'       => array(),
				'done'           => true,
			);

			$user = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.

			if ( ! $user instanceof WP_User ) {
				return $response;
			}

			$user_id = $user->ID;

			global $wpdb;

			$deleted = $wpdb->delete( $wpdb->prefix . 'ywsfl_list', array( 'user_id' => $user_id ), array( '%d' ) );

			if ( $deleted > 0 ) {
				$response['items_removed'] = true;
				$response['messages'][]    = sprintf( '%d %s', $deleted, _n( 'Item removed from Save For Later list', 'Items removed from Save For Later list', $deleted, 'yith-woocommerce-save-for-later' ) );
			}


			return $response;
		}

		public function load_privacy_class() {

			require_once( YWSFL_INC . 'class.yith-wsfl-privacy-policy.php' );
		}
	}

}