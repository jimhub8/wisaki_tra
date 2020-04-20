<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'YITH_Delivery_Date_Product_Frontend' ) ) {

	class YITH_Delivery_Date_Product_Frontend {

		/**
		 * @var YITH_Delivery_Date_Product_Frontend unique instance
		 */
		protected static $_instance;

		public function __construct() {

			$position = get_option( 'ywcdd_ddm_where_show_delivery_message', 15 );

			if ( ! is_admin() ) {
				if ( $position > 0 ) {
					add_action( 'woocommerce_single_product_summary', array( $this, 'show_date_info' ), $position );
				}

				add_filter( 'woocommerce_available_variation', array( $this, 'add_variation_data' ), 15, 3 );
				add_action( 'wp_enqueue_scripts', array( $this, 'include_scripts' ), 20 );
				add_filter( 'yith_delivery_date_show_date_info', array(
					$this,
					'check_for_custom_product_type'
				), 10, 2 );
			}
		}

		/**
		 * @return YITH_Delivery_Date_Product_Frontend
		 * @since 2.0.0
		 * @author YITH
		 */
		public static function get_instance() {

			if ( is_null( self::$_instance ) ) {

				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**show all info about shipping and delivery date
		 * @author YITH
		 * @since 2.0.0
		 *
		 */
		public function show_date_info() {

			global $product;

			if ( ! $product->is_downloadable() && ! $product->is_virtual() && apply_filters( 'yith_delivery_date_show_date_info', true, $product ) ) {
				$this->get_date_info( $product );
			}
		}


		/**
		 * @param WC_Product $product
		 */
		public function get_date_info( $product ) {
			list( $shipping_id, $processing_date, $process_method_id ) = $this->get_min_processing_date( $product );
			list( $carrier_id, $delivery_date ) = $this->get_min_delivery_date( $process_method_id, $processing_date );
			$last_useful_shipping_date = YITH_Delivery_Date_Manager()->get_last_shipping_date( $delivery_date, $process_method_id, $carrier_id );
			$timelimit                 = $this->get_order_within_information( $process_method_id );

			$args = array(
				'shipping_id'         => $shipping_id,
				'processing_id'       => $process_method_id,
				'carrier_id'          => $carrier_id,
				'first_shipping_date' => $processing_date,
				'last_shipping_date'  => $last_useful_shipping_date,
				'delivery_date'       => $delivery_date,
				'time_limit'          => $timelimit
			);

			wc_get_template( 'woocommerce/single-product/show_date_info.php', $args, YITH_DELIVERY_DATE_TEMPLATE_PATH, YITH_DELIVERY_DATE_TEMPLATE_PATH );
		}


		/**
		 * calculate the "purchase within" value for receive the order in a specific date
		 *
		 * @param int $processing_method_id
		 *
		 * @return string
		 * @author Salvatore Strano
		 *
		 */
		public function get_order_within_information( $processing_method_id ) {
			$now                = current_time( 'Y-m-d H:i' );
			$now_time_stamp     = strtotime( $now );
			$wDay               = strtolower( date( "D", $now_time_stamp ) );
			$shipping_work_days = YITH_Delivery_Date_Processing_Method()->get_work_days( $processing_method_id );
			$is_a_work_day      = isset( $shipping_work_days[ $wDay ] );
			$is_a_holiday       = YITH_Delivery_Date_Calendar()->is_holiday( $processing_method_id, $now_time_stamp );
			$limit              = '';
			if ( $is_a_work_day && ! $is_a_holiday ) {
				$timelimit           = ! empty( $shipping_work_days[ $wDay ]['timelimit'] ) ? $shipping_work_days[ $wDay ]['timelimit'] : '23:59';
				$timelimit_timestamp = strtotime( $timelimit );

				if ( $timelimit_timestamp > $now_time_stamp ) {
					$limit     = date( 'Y-m-d', $now_time_stamp ) . " {$timelimit}";
					$datetime1 = new DateTime( $limit );
					$datetime2 = new DateTime( $now );
					$interval  = $datetime1->diff( $datetime2 );
					$hours     = $interval->h;
					$minutes   = $interval->i;

					$hours_label   = _nx( 'hour', 'hours', $hours, '[Part of]: 4 hours and 50 minutes', 'yith-woocommerce-delivery-date' );
					$minutes_label = _nx( 'minute', 'minutes', $minutes, '[Part of]: 4 hours and 50 minutes', 'yith-woocommerce-delivery-date' );

					$limit = sprintf( '%s %s %s %s %s', $interval->format( '%H' ), $hours_label, _x( 'and', '[Part of]: 4 hours and 50 minutes', 'yith-woocommerce-delivery-date' ), $interval->format( '%I' ), $minutes_label );
				}
			}

			return $limit;
		}

		/**
		 * get the shipping method for current customer
		 * @return array
		 * @since 2.0.0
		 * @author YITH
		 */
		public function get_customer_shipping_method() {
			$customer_packing = array(
				'destination' => array(
					'country'  => WC()->customer->get_shipping_country(),
					'state'    => WC()->customer->get_shipping_state(),
					'postcode' => WC()->customer->get_shipping_postcode()
				)
			);
			$zone1            = WC_Shipping_Zones::get_zone_matching_package( $customer_packing );

			$shipping_methods = $zone1->get_shipping_methods( true );

			return $shipping_methods;
		}

		/**
		 * return the available order processing methods for the customer
		 *
		 * @param array $shipping_methods
		 *
		 * @return array
		 * @author  YITH
		 * @since 2.0.0
		 *
		 */
		public function get_order_processing_methods( $shipping_methods ) {
			$order_processing_method = array();
			foreach ( $shipping_methods as $shipping_id => $shipping_method ) {
				if ( ! empty( $shipping_method->instance_settings['select_process_method'] ) ) {
					$order_processing_method[ $shipping_id ] = $shipping_method->instance_settings['select_process_method'];
				}
			}

			return array_unique( $order_processing_method );
		}

		/**calculate the minimum processing date for the product
		 *
		 * @param WC_Product $product
		 *
		 * @return array
		 * @author YITH
		 * @since 2.0.0
		 *
		 */
		public function get_min_processing_date( $product ) {

			$min              = false;
			$min_shipping_id  = false;
			$min_process_id   = false;
			$shipping_methods = $this->get_customer_shipping_method();
			$processing_ids   = $this->get_order_processing_methods( $shipping_methods );

			$product_day = $this->get_custom_base_day_for_product( $product );

			foreach ( $processing_ids as $shipping_id => $processing_id ) {

				if ( $product_day > 0 ) {
					$processing_date = YITH_Delivery_Date_Manager()->get_first_shipping_date( $processing_id, array( 'min_working_day' => $product_day ) );
				} else {
					$processing_date = YITH_Delivery_Date_Manager()->get_first_shipping_date( $processing_id );
				}
				if ( ! $min || $processing_date < $min ) {
					$min             = $processing_date;
					$min_shipping_id = $shipping_id;
					$min_process_id  = $processing_id;
				}
			}

			return array( $min_shipping_id, $min, $min_process_id );
		}

		public function get_min_delivery_date( $processing_id, $processing_date ) {

			$carriers = YITH_Delivery_Date_Processing_Method()->get_carriers( $processing_id );

			$min            = false;
			$min_carrier_id = false;
			if ( $carriers ) {
				foreach ( $carriers as $carrier_id ) {
					$delivery_date = YITH_Delivery_Date_Manager()->get_first_delivery_date( $carrier_id, array( 'shipping_date' => $processing_date ) );

					if ( ! $min || $delivery_date < $min ) {
						$min            = $delivery_date;
						$min_carrier_id = $carrier_id;
					}
				}
			}

			return array( $min_carrier_id, $min );
		}

		/**
		 * get the processing date for the product
		 *
		 * @param WC_Product $product
		 * @param int $qty
		 *
		 * @return int
		 */
		public function get_custom_base_day_for_product( $product, $qty = 1 ) {

			if ( ! $product instanceof WC_Product ) {
				global $product;
			}

			$product_id      = $product->get_id();
			$product_base_id = false;


			if ( $product->is_type( 'variation' ) ) {
				/**
				 * @var WC_Product_Variation $product
				 */
				$product_base_id = $product->get_parent_id();
			}

			$base_day = $this->get_need_day_for_product_rule( $product_id, $qty );

			if ( - 1 == $base_day && $product_base_id ) {
				$base_day = $this->get_need_day_for_product_rule( $product_base_id, $qty );
			}

			//after checked for product rule, try with product category rules
			if ( - 1 == $base_day ) {

				$product_id = $product_base_id ? $product_base_id : $product_id;

				$base_day = $this->get_need_day_for_product_categories_rule( $product_id, $qty );
			}


			return $base_day;
		}

		/**
		 * return how processing day are needs for a product
		 *
		 * @param int $product_id
		 * @param int $qty
		 *
		 * @return int
		 */
		public function get_need_day_for_product_rule( $product_id, $qty ) {

			$product_rules = get_option( 'yith_new_shipping_day_prod', array() );
			$need_day      = - 1;

			if ( count( $product_rules ) > 0 && isset( $product_rules[ $product_id ] ) ) {
				$is_enabled = isset( $product_rules[ $product_id ]['enabled'] ) ? $product_rules[ $product_id ]['enabled'] : 'yes';

				if ( yith_plugin_fw_is_true( $is_enabled ) ) {

					$rules = $product_rules[ $product_id ]['need_process_day'];

					// this is a compatibility with old version , where need_process_day was a number
					if ( ! is_array( $rules ) && is_numeric( $rules ) ) {
						$need_day = $rules;
					} else {

						foreach ( $rules as $rule ) {

							if ( ( ! empty( $rule['from'] ) && $rule['from'] <= $qty ) && ( empty( $rule['to'] ) || ( $qty <= $rule['to'] ) ) ) {

								$need_day = ! empty( $rule['day'] ) ? $rule['day'] : - 1;
								break;
							}
						}
					}
				}

			}

			return $need_day;
		}

		/**
		 * @param int $product_id
		 * @param int $qty
		 *
		 * @return int
		 */
		public function get_need_day_for_product_categories_rule( $product_id, $qty ) {
			$need_day = - 1;

			$category_rules = get_option( 'yith_new_shipping_day_cat', array() );

			if ( count( $category_rules ) > 0 ) {
				$terms    = wp_get_post_terms( $product_id, 'product_cat' );
				$term_ids = wp_list_pluck( $terms, 'term_id' );
				foreach ( $terms as $term ) {

					if ( $term->parent == 0 && in_array( $term->parent, $term_ids ) ) {

						continue;
					}
					if ( isset( $category_rules[ $term->term_id ] ) ) {
						$is_enabled = isset( $category_rules[ $term->term_id ]['enabled'] ) ? $category_rules[ $term->term_id ]['enabled'] : 'yes';

						if ( yith_plugin_fw_is_true( $is_enabled ) ) {
							$rules = $category_rules[ $term->term_id ]['need_process_day'];
							if ( ! is_array( $rules ) && is_numeric( $rules ) ) {
								$cat_day = $rules;
							} else {

								foreach ( $rules as $rule ) {

									if ( ( ! empty( $rule['from'] ) && $rule['from'] <= $qty ) && ( empty( $rule['to'] ) || ( $qty <= $rule['to'] ) ) ) {

										$cat_day = ! empty( $rule['day'] ) ? $rule['day'] : - 1;

									}
								}
							}

							$need_day = max( $need_day, $cat_day );
						}

					}
				}


			}

			return $need_day;
		}

		/**
		 * get date info for variation products
		 *
		 * @param array $variation_data
		 * @param WC_Product_Variable $variable_product
		 * @param WC_Product_Variation $variation_product
		 *
		 * @return array
		 * @author YITH
		 * @since 2.0.0
		 *
		 */
		public function add_variation_data( $variation_data, $variable_product, $variation_product ) {

			ob_start();
			$this->get_date_info( $variation_product );
			$info = ob_get_contents();
			ob_end_clean();

			$variation_data['ywcdd_date_info'] = $info;

			return $variation_data;
		}

		/**
		 * include product scripts
		 * @author YITH
		 * @since 2.0.0
		 */
		public function include_scripts() {

			wp_register_script( 'ywcdd_single_product', YITH_DELIVERY_DATE_ASSETS_URL . 'js/' . yit_load_js_file( 'yith_deliverydate_single_product.js' ), array( 'jquery' ), YITH_DELIVERY_DATE_VERSION, true );
			wp_register_style( 'ywcdd_single_product', YITH_DELIVERY_DATE_ASSETS_URL . 'css/yith_deliverydate_single_product.css', array(), YITH_DELIVERY_DATE_VERSION );
			if ( is_product() ) {
				wp_enqueue_script( 'ywcdd_single_product' );
				wp_enqueue_style( 'ywcdd_single_product' );
			}
		}

		/**
		 * @param bool $show
		 * @param WC_Product $product
		 *
		 * @return bool
		 */
		public function check_for_custom_product_type( $show, $product ) {

			$check_type = array( 'booking', 'gift-card', 'ywf_deposit' );

			if ( in_array( $product->get_type(), $check_type ) ) {
				$show = false;
			}

			return $show;
		}

	}
}

if ( ! function_exists( 'YITH_Delivery_Date_Product_Frontend' ) ) {
	function YITH_Delivery_Date_Product_Frontend() {
		return YITH_Delivery_Date_Product_Frontend::get_instance();
	}
}
YITH_Delivery_Date_Product_Frontend();
