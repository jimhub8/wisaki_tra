<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'YITH_Delivery_Date_Admin' ) ) {

	class YITH_Delivery_Date_Admin {

		protected static $_instance;


		public function __construct() {

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 20 );

			//manage time slot, priority 15 after woocommerce init option
			add_action( 'admin_init', array( $this, 'add_time_slot' ), 15 );

			add_action( 'wp_ajax_update_time_slot', array( $this, 'update_time_slot' ) );
			add_action( 'wp_ajax_delete_time_slot', array( $this, 'delete_time_slot' ) );
			// manage custom product shipping day
			add_action( 'wp_ajax_update_category_day', array( $this, 'update_category_day' ) );
			add_action( 'wp_ajax_enable_disable_category_day', array( $this, 'enable_disable_category_day' ) );
			add_action( 'wp_ajax_delete_category_day', array( $this, 'delete_category_day' ) );
			add_action( 'wp_ajax_enable_disable_product_day', array( $this, 'enable_disable_product_day' ) );
			add_action( 'wp_ajax_update_product_day', array( $this, 'update_product_day' ) );
			add_action( 'wp_ajax_delete_product_day', array( $this, 'delete_product_day' ) );

			//manage calendar ( custom holidays )
			add_action( 'wp_ajax_enable_disable_holidays', array( $this, 'enable_disable_holidays' ) );
			add_action( 'wp_ajax_add_holidays', array( $this, 'add_holidays' ) );
			add_action( 'wp_ajax_update_holidays', array( $this, 'update_holidays' ) );
			add_action( 'wp_ajax_delete_holidays', array( $this, 'delete_holidays' ) );

			//add custom tab in plugin panel
			add_action( 'yith_wcdd_timeslot_panel', array( $this, 'add_timeslot_table_field' ) );
			add_action( 'yith_wcdd_shippingday_panel', array( $this, 'show_shippingday_panel' ) );
			add_action( 'yith_wcdd_general_calendar_tab', array( $this, 'show_calendar_panel' ) );

			add_action( 'wp_ajax_update_processing_type_option', array( $this, 'update_processing_type_option' ) );
			add_action( 'wp_ajax_update_processing_method_table', array( $this, 'update_processing_method_table' ) );


			//add admin notices
			add_action( 'admin_notices', array( $this, 'show_admin_notices' ) );

			add_action( 'ywcdd_show_processing_method_tab', array( $this, 'add_processing_method_tab' ) );
			add_action( 'ywcdd_show_carrier_tab', array( $this, 'add_carrier_tab' ) );
		}

		/**
		 * @author YITHEMES
		 * @since 1.0.0
		 * @return YITH_Delivery_Date_Admin
		 */
		public static function get_instance() {

			if ( is_null( self::$_instance ) ) {

				self::$_instance = new self();
			}

			return self::$_instance;
		}


		/**
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function show_calendar_panel() {

			wc_get_template( 'calendar.php', array(), '', YITH_DELIVERY_DATE_TEMPLATE_PATH . 'admin/' );
		}


		/**
		 * add style and script in admin
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function enqueue_admin_scripts() {

			global $post;
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';


			$is_delivery_panel_page         = ( isset( $_GET['page'] ) && 'yith_delivery_date_panel' === $_GET['page'] );
			$is_carrier_post_type           = ( isset( $post ) && 'yith_carrier' === get_post_type( $post->ID ) ) || ( isset( $_GET['post_type'] ) && 'yith_carrier' === $_GET['post_type'] );
			$is_processing_method_post_type = ( isset( $post ) && 'yith_proc_method' === get_post_type( $post->ID ) ) || ( isset( $_GET['post_type'] ) && 'yith_proc_method' === $_GET['post_type'] );

			if ( $is_delivery_panel_page || $is_carrier_post_type || $is_processing_method_post_type ) {

				wp_register_script( 'ywcdd_timepicker', YITH_DELIVERY_DATE_ASSETS_URL . 'js/timepicker/jquery.timepicker' . $suffix . '.js', array( 'jquery' ), YITH_DELIVERY_DATE_VERSION, true );
				wp_register_style( 'ywcdd_timepicker', YITH_DELIVERY_DATE_ASSETS_URL . 'css/timepicker/jquery.timepicker.css', array(), YITH_DELIVERY_DATE_VERSION );

				//Calendar ASSETS
				wp_register_script( 'moment', YITH_DELIVERY_DATE_ASSETS_URL . 'js/fullcalendar/moment.min.js', array( 'jquery' ), '3.0.0', true );
				wp_register_script( 'ywcdd_fullcalendar', YITH_DELIVERY_DATE_ASSETS_URL . 'js/fullcalendar/fullcalendar.min.js', array(
					'jquery',
					'moment',
					'jquery-ui-datepicker'
				), '3.0.0', true );
				wp_register_script( 'ywcdd_fullcalendar_language', YITH_DELIVERY_DATE_ASSETS_URL . 'js/fullcalendar/locale-all.js', array(
					'jquery',
					'moment',
					'ywcdd_fullcalendar'
				), '3.0.0', true );
				wp_register_style( 'ywcdd_fullcalendar_style', YITH_DELIVERY_DATE_ASSETS_URL . 'css/fullcalendar/fullcalendar.min.css', array(), '3.0.0' );


			}

			if ( $is_delivery_panel_page ) {

				wp_enqueue_script( 'ywcdd_timepicker' );
				wp_enqueue_script( 'ywcdd_fullcalendar' );
				wp_enqueue_script( 'ywcdd_fullcalendar_language' );

				wp_enqueue_style( 'ywcdd_timepicker' );
				wp_enqueue_style( 'ywcdd_fullcalendar_style' );


				wp_register_script( 'yith_delivery_date_panel', YITH_DELIVERY_DATE_ASSETS_URL . 'js/yith_deliverydate_admin' . $suffix . '.js', array( 'jquery' ), YITH_DELIVERY_DATE_VERSION, true );

				$params = array(
					'ajax_url'     => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
					'actions'      => array(
						'update_processing_type_option' => 'update_processing_type_option',
						'update_time_slot'              => 'update_time_slot',
						'delete_time_slot'              => 'delete_time_slot',
						'enable_disable_category_day'   => 'enable_disable_category_day',
						'update_category_day'           => 'update_category_day',
						'delete_category_day'           => 'delete_category_day',
						'enable_disable_product_day'    => 'enable_disable_product_day',
						'update_product_day'            => 'update_product_day',
						'delete_product_day'            => 'delete_product_day',
						'update_processing_method_table' => 'update_processing_method_table'
					),
					'empty_row'    => sprintf( '<tr class="no-items"><td class="colspanchange" colspan="6">%s</td></tr>', __( 'No item found.', 'yith-woocommerce-delivery-date' ) ),
					'timeformat'   => 'H:i',
					'timestep'     => get_option( 'ywcdd_timeslot_step', 30 ),
					'dateformat'   => get_option( 'date_format' ),
					'plugin_nonce' => YITH_DELIVERY_DATE_SLUG,
				);
				wp_enqueue_script( 'yith_delivery_date_panel' );
				wp_localize_script( 'yith_delivery_date_panel', 'yith_delivery_parmas', $params );

				wp_enqueue_style( 'yith_delivery_date_panel_css', YITH_DELIVERY_DATE_ASSETS_URL . 'css/yith_delivery_date_admin.css', array(), YITH_DELIVERY_DATE_VERSION );

				wp_enqueue_script( 'yith_wcdd_calendar', YITH_DELIVERY_DATE_ASSETS_URL . 'js/yith_deliverydate_calendar' . $suffix . '.js', array( 'jquery' ), YITH_DELIVERY_DATE_VERSION, true );

				$locale = substr( get_locale(), 0, 2 );

				$timezone_format = 'Y-m-d H:i:s';

				$now    = strtotime( date_i18n( $timezone_format ) );
				$now    = strtotime( 'midnight', $now );
				$params = array(
					'starday'           => date( 'Y-m-d', $now ),
					'dateformat'        => 'yy-mm-dd',
					'calendar_language' => $locale,
					'ajax_url'          => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
					'actions'           => array(
						'add_holidays'            => 'add_holidays',
						'delete_holidays'         => 'delete_holidays',
						'enable_disable_holidays' => 'enable_disable_holidays',
						'update_holidays'         => 'update_holidays'
					)
				);

				wp_localize_script( 'yith_wcdd_calendar', 'ywcdd_calendar_params', $params );

			}
			if ( $is_carrier_post_type ) {
				wp_enqueue_style( 'yith_delivery_date_panel_css', YITH_DELIVERY_DATE_ASSETS_URL . 'css/yith_delivery_date_admin.css', array(), YITH_DELIVERY_DATE_VERSION );

				wp_register_script( 'yith_wcdd_carrier', YITH_DELIVERY_DATE_ASSETS_URL . 'js/yith_deliverydate_carrier' . $suffix . '.js', array(
					'jquery',
					'jquery-blockui',
					'wc-enhanced-select'
				), YITH_DELIVERY_DATE_VERSION );
				wp_register_style( 'ywcdd_carrier_metaboxes', YITH_DELIVERY_DATE_ASSETS_URL . 'css/yith_carrier_metaboxes.css', array(), YITH_DELIVERY_DATE_VERSION );
				wp_enqueue_style( 'ywcdd_timepicker' );
			}

			if ( $is_processing_method_post_type ) {

				wp_register_script( 'yith_wcdd_processing_method', YITH_DELIVERY_DATE_ASSETS_URL . 'js/yith_deliverydate_processing_method' . $suffix . '.js', array(
					'jquery',
					'jquery-blockui'
				), YITH_DELIVERY_DATE_VERSION );
				wp_register_style( 'ywcdd_processing_method_metaboxes', YITH_DELIVERY_DATE_ASSETS_URL . 'css/yith_processing_method_metaboxes.css', array(), YITH_DELIVERY_DATE_VERSION );
				wp_enqueue_style( 'ywcdd_timepicker' );
			}
		}

		/**
		 * add time slot
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function add_time_slot() {


			if ( isset( $_POST['yith_new_timeslot'] ) ) {

				$timefrom  = $_POST['yith_new_timeslot']['timefrom'];
				$timeto    = $_POST['yith_new_timeslot']['timeto'];
				$max_order = $_POST['yith_new_timeslot']['max_order'];
				$fee       = $_POST['yith_new_timeslot']['fee'];
				$override  = 'no';
				$days      = array();

				if ( $timefrom !== '' && $timeto !== '' ) {

					$timeslots = get_option( 'yith_delivery_date_time_slot', array() );

					$id      = uniqid( 'ywcdd_gen_timeslot_' );
					$newslot = array(
						'timefrom'      => $timefrom,
						'timeto'        => $timeto,
						'max_order'     => $max_order,
						'fee'           => $fee,
						'override_days' => $override,
						'day_selected'  => $days
					);

					$timeslots[ $id ] = $newslot;

					update_option( 'yith_delivery_date_time_slot', $timeslots );
				}
			}
		}

		/**
		 * update time slot via ajax
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function update_time_slot() {

			if ( isset( $_POST['plugin_nonce'] ) && YITH_DELIVERY_DATE_SLUG === $_POST['plugin_nonce'] && isset( $_POST['slot_action'] ) && 'update_slot' === $_POST['slot_action'] ) {

				$time_from     = $_POST['ywcdd_time_from'];
				$time_to       = $_POST['ywcdd_time_to'];
				$max_order     = $_POST['ywcdd_max_order'];
				$fee           = $_POST['ywcdd_fee'];
				$item_id       = $_POST['item_id'];
				$override_days = $_POST['override_days'];
				$days          = isset( $_POST['ywcdd_day'] ) ? $_POST['ywcdd_day'] : array();


				$time_slots = get_option( 'yith_delivery_date_time_slot' );

				if ( ! empty( $time_slots ) && isset( $time_slots[ $item_id ] ) ) {

					$single_slot                  = $time_slots[ $item_id ];
					$single_slot['timefrom']      = $time_from;
					$single_slot['timeto']        = $time_to;
					$single_slot['max_order']     = $max_order;
					$single_slot['fee']           = $fee;
					$single_slot['override_days'] = $override_days;
					$single_slot['day_selected']  = $days;
					$time_slots[ $item_id ]       = $single_slot;

					update_option( 'yith_delivery_date_time_slot', $time_slots );
				}


				wp_send_json( array( 'result' => 'ok' ) );
			}
		}

		/**
		 * delete time slot via ajax
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function delete_time_slot() {

			if ( isset( $_POST['plugin_nonce'] ) && YITH_DELIVERY_DATE_SLUG === $_POST['plugin_nonce'] && isset( $_POST['slot_action'] ) && 'delete_slot' === $_POST['slot_action'] ) {

				$item_id    = $_POST['item_id'];
				$time_slots = get_option( 'yith_delivery_date_time_slot' );

				if ( ! empty( $time_slots ) && isset( $time_slots[ $item_id ] ) ) {

					$new_time_slots = array();
					foreach ( $time_slots as $key => $slot ) {
						if ( $key != $item_id ) {

							$new_time_slots[ $key ] = $slot;
						}
					}
					update_option( 'yith_delivery_date_time_slot', $new_time_slots );
				}

			}
			wp_send_json( array( 'result' => 'ok' ) );

		}

		/**
		 * enable or disable the single custom processing days for categories
		 * @author YITH
		 * @since 2.0.0
		 */
		public function enable_disable_category_day() {

			if ( isset( $_POST['ywcdd_category_id'] ) ) {

				$category_id   = $_POST['ywcdd_category_id'];
				$enable        = $_POST['ywcdd_category_enable'];
				$category_days = get_option( 'yith_new_shipping_day_cat', array() );

				if ( isset( $category_days[ $category_id ] ) ) {

					$category_days[ $category_id ]['enabled'] = $enable;

					update_option( 'yith_new_shipping_day_cat', $category_days );
				}

				wp_send_json( array( 'result' => true ) );
			}
		}

		/**
		 * update process category day
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 */
		public function update_category_day() {
			if ( ! empty( $_POST['ywcdd_category_id'] ) ) {

				$category_id = $_POST['ywcdd_category_id'];
				$args        = array();
				$arg         = isset( $_POST['ywcdd_args'] ) ? $_POST['ywcdd_args'] : '';
				parse_str( $arg, $args );

				$quantity_days = isset( $args['yith_new_shipping_day_cat']['need_process_day'] ) ? $args['yith_new_shipping_day_cat']['need_process_day'] : array();


				$category_day = get_option( 'yith_new_shipping_day_cat', array() );

				if ( isset( $category_day[ $category_id ] ) ) {
					$category_day[ $category_id ]['need_process_day'] = $quantity_days;
					$category_day[ $category_id ]['enabled']          = isset( $args['yith_new_shipping_day_cat']['enabled'] );

				} else {
					$category_day[ $category_id ] = array(
						'category'         => $category_id,
						'need_process_day' => $quantity_days,
						'enabled'          => 'yes'
					);

				}

				update_option( 'yith_new_shipping_day_cat', $category_day );

				if ( isset( $_POST['ywcdd_action'] ) && 'add' === $_POST['ywcdd_action'] ) {
					$type = 'category';
					ob_start();
					include( YITH_DELIVERY_DATE_TEMPLATE_PATH . '/admin/custom-processing-day-view.php' );
					$template = ob_get_contents();
					ob_end_clean();

					wp_send_json( array( 'template' => $template ) );
				}

			}
		}

		/**
		 * delete process category day
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function delete_category_day() {

			if ( isset( $_POST['ywcdd_category_id'] ) ) {

				$category_id  = $_POST['ywcdd_category_id'];
				$category_day = get_option( 'yith_new_shipping_day_cat', array() );

				if ( isset( $category_day[ $category_id ] ) ) {
					unset( $category_day[ $category_id ] );
					update_option( 'yith_new_shipping_day_cat', $category_day );
				}
			}
		}

		/**
		 * enable or disable single custom processing product day
		 * @since 2.0.0
		 * @author YITH
		 */
		public function enable_disable_product_day() {
			if ( isset( $_POST['ywcdd_product_id'] ) ) {

				$product_id   = $_POST['ywcdd_product_id'];
				$enable       = $_POST['ywcdd_product_enable'];
				$product_days = get_option( 'yith_new_shipping_day_prod', array() );

				if ( isset( $product_days[ $product_id ] ) ) {

					$product_days[ $product_id ]['enabled'] = $enable;

					update_option( 'yith_new_shipping_day_prod', $product_days );
				}

				wp_send_json( array( 'result' => true ) );
			}
		}

		/**
		 * update process product day
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function update_product_day() {

			if ( ! empty( $_POST['ywcdd_product_id'] ) ) {

				$product_id = $_POST['ywcdd_product_id'];
				$args       = array();
				$arg        = isset( $_POST['ywcdd_args'] ) ? $_POST['ywcdd_args'] : '';
				parse_str( $arg, $args );
				$quantity_days = isset( $args['yith_new_shipping_day_prod']['need_process_day'] ) ? $args['yith_new_shipping_day_prod']['need_process_day'] : array();

				$product_day = get_option( 'yith_new_shipping_day_prod', array() );

				if ( isset( $product_day[ $product_id ] ) ) {
					$product_day[ $product_id ]['need_process_day'] = $quantity_days;
					$product_day[ $product_id ]['enabled']          = isset( $args['yith_new_shipping_day_prod']['enabled'] );

				} else {
					$product_day[ $product_id ] = array(
						'product'          => $product_id,
						'need_process_day' => $quantity_days,
						'enabled'          => 'yes'
					);
				}

				update_option( 'yith_new_shipping_day_prod', $product_day );

				if ( isset( $_POST['ywcdd_action'] ) && 'add' === $_POST['ywcdd_action'] ) {
					$type = 'product';
					ob_start();
					include( YITH_DELIVERY_DATE_TEMPLATE_PATH . '/admin/custom-processing-day-view.php' );
					$template = ob_get_contents();
					ob_end_clean();
					wp_send_json( array( 'template' => $template ) );
				}


			}
		}

		/**
		 * delete process product day
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function delete_product_day() {


			if ( isset( $_POST['ywcdd_product_id'] ) ) {

				$product_id  = $_POST['ywcdd_product_id'];
				$product_day = get_option( 'yith_new_shipping_day_prod', array() );

				if ( isset( $product_day[ $product_id ] ) ) {
					unset( $product_day[ $product_id ] );
					update_option( 'yith_new_shipping_day_prod', $product_day );
				}

				wp_send_json( array( 'removed' => true ) );
			}

		}

		//CALENDAR

		public function enable_disable_holidays() {

			if ( isset( $_POST['ywcdd_holiday_id'] ) ) {

				$holiday_id    = $_POST['ywcdd_holiday_id'];
				$enabled       = $_POST['ywcdd_holiday_enabled'];
				$all_holidays  = get_option( 'ywcdd_holidays_option', array() );
				$delete_option = isset( $_POST['ywcdd_delete_holiday'] );

				if ( isset( $all_holidays[ $holiday_id ] ) ) {
					$holiday                     = $all_holidays[ $holiday_id ];
					$holiday['enabled']          = $enabled;
					$all_holidays[ $holiday_id ] = $holiday;

					if ( $delete_option ) {
						unset( $all_holidays[ $holiday_id ] );
					}
					$start_event     = $holiday['start_event'];
					$end_event       = $holiday['end_event'];
					$event_name      = $holiday['event_name'];
					$how_add_holiday = $holiday['how_add_holiday'];

					update_option( 'ywcdd_holidays_option', $all_holidays );
					if ( 'no' == $enabled ) {
						YITH_Delivery_Date_Calendar()->delete_event_by_date( $start_event, $end_event, $how_add_holiday );
					} else {
						foreach ( $how_add_holiday as $who ) {

							YITH_Delivery_Date_Calendar()->add_calendar_event( $who, $event_name, 'holiday', $start_event, $end_event );
						}
					}
					$all_events = YITH_Delivery_Date_Calendar()->get_calendar_events();

					wp_send_json( array( 'result' => $all_events ) );
				}
			}
		}

		/**
		 * add new holidays to calendar
		 */
		public function add_holidays() {

			if ( isset( $_POST['ywcdd_add_holidays'] ) && 'add_new_holidays' == $_POST['ywcdd_add_holidays'] ) {

				$how_add_holiday = isset( $_POST['ywcdd_how_add'] ) ? $_POST['ywcdd_how_add'] : array();
				$event_name      = isset( $_POST['ywcdd_event_name'] ) ? $_POST['ywcdd_event_name'] : '';
				$start_event     = isset( $_POST['ywcdd_start_event'] ) ? $_POST['ywcdd_start_event'] : '';
				$end_event       = isset( $_POST['ywcdd_end_event'] ) ? $_POST['ywcdd_end_event'] : '';

				foreach ( $how_add_holiday as $who ) {

					if ( $who == 'carrier_default' ) {
						$who = - 1;
					}

					YITH_Delivery_Date_Calendar()->add_calendar_event( $who, $event_name, 'holiday', $start_event, $end_event );
				}

				$unique_id = 'ywcdd_holiday_' . uniqid();

				$all_holidays_opts = get_option( 'ywcdd_holidays_option', array() );

				$new_holiday = array(
					'enabled'         => 'yes',
					'event_name'      => $event_name,
					'start_event'     => $start_event,
					'end_event'       => $end_event,
					'how_add_holiday' => $how_add_holiday
				);

				$all_holidays_opts[ $unique_id ] = $new_holiday;
				update_option( 'ywcdd_holidays_option', $all_holidays_opts );

				$all_holiday = YITH_Delivery_Date_Calendar()->get_calendar_events();

				ob_start();
				wc_get_template( 'holidays.php', array( 'id' => 'ywcdd_holidays_option' ), '', YITH_DELIVERY_DATE_TEMPLATE_PATH . '/admin/types/' );
				$holiday_list = ob_get_contents();
				ob_end_clean();
				wp_send_json( array( 'result' => $all_holiday, 'list' => $holiday_list ) );
			}
		}

		/**
		 * @author YITH
		 * @since 2.0.0
		 * delete a holiday
		 */
		public function delete_holidays() {

			if ( isset( $_POST['ywcdd_event_id'] ) ) {

				$event_id = $_POST['ywcdd_event_id'];
				$res      = YITH_Delivery_Date_Calendar()->delete_event_by_id( $event_id );

				$result = $res ? 'deleted' : 'error';

				wp_send_json( array( 'result' => $result ) );
			}
		}

		/**
		 * update holiday
		 * /**
		 * @author YITH
		 * @since 2.0.0
		 *
		 */
		public function update_holidays() {

			if ( isset( $_POST['ywcdd_holiday_id'] ) ) {
				$holiday_id = $_POST['ywcdd_holiday_id'];
				$from       = $_POST['ywcdd_from'];
				$to         = $_POST['ywcdd_to'];
				$enabled    = $_POST['ywcdd_holiday_enabled'];
				$event_for = $_POST['ywcdd_holiday_for'];
				$event_name = $_POST['ywcdd_event_name'];

				$all_holidays = get_option( 'ywcdd_holidays_option', array() );

				if ( isset( $all_holidays[ $holiday_id ] ) ) {
					$single_holiday                = $all_holidays[ $holiday_id ];
					$old_from                      = $single_holiday['start_event'];
					$old_to                        = $single_holiday['end_event'];
					$how_add_holiday               = $single_holiday['how_add_holiday'];
					$single_holiday['enabled']     = $enabled;
					$single_holiday['event_name'] = $event_name;
					$single_holiday['start_event'] = $from;
					$single_holiday['end_event']   = $to;
					$single_holiday['how_add_holiday'] = $event_for;

					$all_holidays[ $holiday_id ] = $single_holiday;

					YITH_Delivery_Date_Calendar()->delete_event_by_date( $old_from,$old_to,$how_add_holiday );
					if( 'yes' == $enabled ) {
						foreach ( $event_for as $who ) {

							YITH_Delivery_Date_Calendar()->add_calendar_event( $who, $single_holiday['event_name'], 'holiday', $from, $to );
						}
					}

					update_option( 'ywcdd_holidays_option', $all_holidays );

					$all_holiday = YITH_Delivery_Date_Calendar()->get_calendar_events();

					wp_send_json( array('result' => $all_holiday ) );
				}


			}
		}

		public function show_admin_notices() {

			$messages = array();
			if ( isset( $_GET['page'] ) && 'yith_delivery_date_panel' == $_GET['page'] ) {

				$tot_post = wp_count_posts( 'yith_proc_method' );
				$tot_post = $tot_post->publish;

				if ( $tot_post == 0 ) {
					$post_url     = admin_url( 'post-new.php' );
					$params       = array( 'post_type' => 'yith_proc_method' );
					$new_post_url = esc_url( add_query_arg( $params, $post_url ) );
					$message      = sprintf( '%s <a href="%s" class="page-title-action" style="top:0;font-size:11px;">%s</a>', __( 'In order to use the plugin, it is essential to create at least a Processing Method', 'yith-woocommerce-delivery-date' ),
						$new_post_url, __( 'Add new Processing Method', 'yith-woocommerce-delivery-date' ) );

					$message = array( 'type' => 'warning', 'message' => $message, 'url' => '' );

					$messages[] = $message;
				}

			}

			if ( count( $messages ) > 0 ) {

				foreach ( $messages as $message ) {

					wc_get_template( '/admin/notices/admin-notice-' . $message['type'] . '.php', array(
						'message' => $message['message'],
						'url'     => $message['url']
					),

						YITH_DELIVERY_DATE_TEMPLATE_PATH, YITH_DELIVERY_DATE_TEMPLATE_PATH );
				}
			}
		}

		/**
		 * Show the Processing method tab
		 * @author YITH
		 * @since 2.0.0
		 */
		public function add_processing_method_tab() {

			include_once( YITH_DELIVERY_DATE_TEMPLATE_PATH . 'admin/processing-method-tab.php' );
		}

		/**
		 * show the Carrier tab
		 * @author YITH
		 * @since 2.0.0
		 */
		public function add_carrier_tab() {
			include_once( YITH_DELIVERY_DATE_TEMPLATE_PATH . 'admin/carrier-tab.php' );
		}

		public function update_processing_type_option() {

			if ( isset( $_POST['ywcdd_processing_type'] ) ) {

				update_option( 'ywcdd_processing_type', $_POST['ywcdd_processing_type'] );

				wp_send_json( array( 'result' => true ) );
			}
		}

		public function update_processing_method_table(){

			if ( ! class_exists( 'YITH_Processing_Method_Table' ) ) {
				include_once( YITH_DELIVERY_DATE_INC . 'admin-tables/class.yith-delivery-date-processing-method-table.php' );
			}

			$processing_table = new YITH_Processing_Method_Table();
			$processing_table->ajax_response();
		}


	}
}
/**
 * @return YITH_Delivery_Date_Admin
 */
function YITH_Delivery_Date_Admin() {
	return YITH_Delivery_Date_Admin::get_instance();
}

YITH_Delivery_Date_Admin();