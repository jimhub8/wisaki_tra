<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Payout' ) ) {


	/**
	 * Main class for the commission
	 *
	 * @class      YITH_Payout
	 * @package    YIThemes
	 * @since      Version 1.0.0
	 * @author     Your Inspiration Themes
	 * @category   Class
	 *
	 * @property   int $id The ID of commission
	 * @property   int $payout_batch_id payout batch id
	 * @property   int $order_id The order ID of payout
	 * @property   string $payout_status The status of payout (one between 'pending', 'unpaid' and 'paid')
	 * @property   string $payout_mode , how this payout has been created ( only instant for now )
	 * @property   string $last_edit When was the last update
	 * @property   string $last_edit_gmt When was the last update
	 */
	class YITH_Payout {

		public $id = 0;

		protected $_data = array();

		protected $_order = null;

		protected $_changed = false;

		protected static $_instance = array();

		/**
		 * Main plugin Instance
		 *
		 * @static
		 *
		 * @param bool|int $payout_id
		 *
		 * @return YITH_Payout Main instance
		 * @since  1.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public static function instance( $payout_id = false ) {
			if ( ! isset( self::$_instance[ $payout_id ] ) || is_null( self::$_instance[ $payout_id ] ) ) {
				self::$_instance[ $payout_id ] = new self( $payout_id );
			}

			return self::$_instance[ $payout_id ];
		}

		/**
		 * Constructor
		 *
		 * @param bool $payout_id
		 *
		 * @return YITH_Payout
		 * @since  1.0.0
		 * @access public
		 */
		public function __construct( $payout_id = false ) {
			if ( ! $payout_id ) {
				return $this;
			}

			// populate instance by data from database
			$this->_populate( $payout_id  );

			// When leaving or ending page load, store data
			add_action( 'shutdown', array( $this, 'save_data' ), 10 );

			return $this;
		}

		/**
		 * Save data function.
		 */
		public function save_data() {
			if ( ! $this->_changed || empty( $this->_data ) ) {
				return;
			}

			global $wpdb;
			$this->last_edit     = current_time( 'mysql' );
			$this->last_edit_gmt = current_time( 'mysql', 1 );
			$wpdb->update( $wpdb->yith_payouts, $this->_data, array( 'ID' => $this->id ) );


		}

		/**
		 * __set function.
		 *
		 * @param mixed $property
		 *
		 * @return bool
		 */
		public function __isset( $property ) {
			return isset( $this->_data[ $property ] );
		}

		/**
		 * __get function.
		 *
		 * @param string $property
		 *
		 * @return string
		 */
		public function __get( $property ) {
			return isset( $this->_data[ $property ] ) ? $this->_data[ $property ] : '';
		}

		/**
		 * __set function.
		 *
		 * @param mixed $property
		 * @param mixed $value
		 */
		public function __set( $property, $value ) {
			switch ( $property ) {
				case 'order_id' :
					$this->_order = null;
					$value        = intval( $value );
					break;

			}

			$this->_data[ $property ] = $value;
			$this->_changed           = true;
		}

		/**
		 * Retrieve the record of a payout
		 *
		 * @param $payout_id
		 *
		 * @since 1.0
		 */
		protected function _populate( $payout_id ) {
			global $wpdb;
			$this->_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->yith_payouts WHERE sender_batch_id = %s", $payout_id ), ARRAY_A );

			if ( ! empty( $this->_data ) ) {
				$this->id = $this->_data['ID'];


			}
		}

		/**
		 * Add new record to DB
		 *
		 * @param array $args
		 *
		 * @return int
		 */
		public function add( $args = array() ) {
			global $wpdb;

			$defaults = array(
				'payout_batch_id' => '',
				'order_id'     => 0,
				'sender_batch_id' => '',
				'payout_status'      => 'unprocessed',
				'payout_mode'    => 'instant',

			);


			$args = wp_parse_args( $args, $defaults );

			$wpdb->insert( $wpdb->yith_payouts, (array) $args );

			return $wpdb->insert_id;
		}

		/**
		 * Remove the payout of this instance from database
		 *
		 * @since 1.0
		 */
		public function remove() {
			if ( ! $this->id ) {
				return;
			}

			global $wpdb;
			$wpdb->delete( $wpdb->yith_payouts, array( 'ID' => $this->id ) );
		}

		/**
		 * Detect if payout ID exists
		 *
		 * @return bool
		 * @since 1.0
		 */
		public function exists() {
			return ! empty( $this->_data );
		}

	}
}

/**
 * Main instance of plugin
 *
 * @return YITH_Payout
 * @since  1.0
 */
if ( ! function_exists( 'YITH_Payout' ) ) {
	/**
	 * @param bool $payout_id
	 *
	 * @return YITH_Payout
	 */
	function YITH_Payout( $payout_id = false ) {
		return YITH_Payout::instance(  $payout_id  );
	}
}
