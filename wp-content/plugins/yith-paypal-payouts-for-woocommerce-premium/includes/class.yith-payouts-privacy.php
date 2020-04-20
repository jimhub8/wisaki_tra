<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Payouts_Privacy' ) && class_exists( 'YITH_Privacy_Plugin_Abstract' ) ) {

	class YITH_Payouts_Privacy extends YITH_Privacy_Plugin_Abstract {

		public function __construct() {

			$plugin_info = get_plugin_data( YITH_PAYOUTS_FILE );

			$name = $plugin_info['Name'];

			parent::__construct( $name );

			add_action( 'admin_init', array( $this, 'privacy_personal_data_init' ), 99 );
		}

		public function privacy_personal_data_init() {
			// set up vendors data exporter

			add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_exporter' ) );

			// set up vendors data eraser
			add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_eraser' ) );
		}

		/**
		 * @param array $exporters
		 */
		public function register_exporter( $exporters ) {

			$exporter_commission = get_option( 'yith_payouts_export_payout', 'yes' );

			if ( 'yes' == $exporter_commission ) {

				$exporters['yith_payouts_exporter'] = array(
					'exporter_friendly_name' => __( 'User commission data', 'yith-paypal-payouts-for-woocommerce' ),
					'callback'               => array( $this, 'exporter_user_payouts' )

				);
			}

			$exporters['yith_payouts_user_exporter'] = array(
				'exporter_friendly_name' => __( 'User data in PayPal Payouts', 'yith-paypal-payouts-for-woocommerce' ),
				'callback'               => array( $this, 'exporter_user_data' )
			);

			return $exporters;
		}


		/**
		 * @param string $user_email
		 */
		public function exporter_user_data( $user_email ) {

			$user = get_user_by( 'email', $user_email );

			$data_to_export = array();
			if ( $user instanceof WP_User ) {

				$user_id      = $user->ID;
				$paypal_email = $this->get_paypal_email( $user_id );

				if ( ! empty( $paypal_email ) ) {
					$data = array( array( 'name' => 'PayPal Email', 'value' => $paypal_email ) );

					$data_to_export[] = array(
						'group_id'    => 'yith_payouts_user_info',
						'group_label' => __( 'PayPal Payouts', 'yith-paypal-payouts-for-woocommerce' ),
						'data'        => $data,
						'item_id'     => 'paypal_payouts_email'
					);
				}
			}

			return array(
				'data' => $data_to_export,
				'done' => true
			);
		}

		/**
		 * @param string $user_email
		 *
		 * @return array
		 */
		public function exporter_user_payouts( $user_email, $page ) {

			$user           = get_user_by( 'email', $user_email );
			$data_to_export = $personal_data = array();
			$number         = 50;
			$page           = (int) $page;
			$offset         = $number * ( $page - 1 );
			$done           = true;
			if ( $user instanceof WP_User ) {

				$user_id      = $user->ID;
				$paypal_email = $this->get_paypal_email( $user_id );

				if ( ! empty( $paypal_email ) ) {


					$query_args = array(
						'receiver' => $paypal_email,
						'number'   => $number,
						'paged'    => $page,
						'fields'   => array( 'ID', 'payout_batch_id', 'payout_item_id','transaction_id', 'transaction_status', 'amount', 'fee', 'currency' )
					);

					$payout_commissions = YITH_Payout_Items()->get_payout_items( $query_args );

					$labels = array(
						'ID'                 => __( 'ID', 'yith-paypal-payouts-for-woocommerce' ),
						'payout_item_id'     => __( 'Payout Item ID', 'yith-paypal-payouts-for-woocommerce' ),
						'payout_batch_id'    => __( 'Payout Batch ID', 'yith-paypal-payouts-for-woocommerce' ),
						'transaction_id'     => __( 'Transaction ID', 'yith-paypal-payouts-for-woocommerce' ),
						'transaction_status' => __( 'Transaction status', 'yith-paypal-payouts-for-woocommerce' ),
						'amount'             => __( 'Transaction value', 'yith-paypal-payouts-for-woocommerce' ),
						'fee'                => __( 'Transaction Fee', 'yith-paypal-payouts-for-woocommerce' ),
						'currency'           => __( 'Transaction Currency', 'yith-paypal-payouts-for-woocommerce' )

					);
					if ( 0 < count( $payout_commissions ) ) {

						foreach ( $payout_commissions as $commission ) {
							$id = $commission['ID'];
							foreach ( $commission as $label => $value ) {

								$personal_data[] = array(
									'name'  => $labels[ $label ],
									'value' => $value
								);
							}

							$data_to_export[] = array(
								'group_id'    => 'yith_payouts_data',
								'group_label' => __( 'User Payouts Data (PayPal Payouts)', 'yith-paypal-payouts-for-woocommerce' ),
								'item_id'     => 'paypal_payout-' . $id,
								'data'        => $personal_data,
							);
						}

						$done = $number > count( $payout_commissions );
					} else {
						$done = true;
					}
				}
			}

			return array(
				'data' => $data_to_export,
				'done' => $done
			);
		}

		/**
		 * @param array $erasers
		 *
		 * @return array
		 */
		public function register_eraser( $erasers ) {

			$eraser_commission = get_option( 'yith_payouts_eraser_payout', 'no' );

			if ( 'yes' == $eraser_commission ) {

				$erasers['yith_payouts_eraser'] = array(
					'eraser_friendly_name' => __( 'User Payouts data', 'yith-paypal-payouts-for-woocommerce' ),
					'callback'             => array( $this, 'eraser_user_payouts' )
				);
			}


			$eraser_user_info = get_option( 'yith_payouts_eraser_user_data', 'no' );

			if ( 'yes' == $eraser_user_info ) {
				$erasers['yith_payouts_user_info_eraser'] = array(
					'eraser_friendly_name' => __( 'User info data', 'yith-paypal-payouts-for-woocommerce' ),
					'callback'             => array( $this, 'eraser_user_info' )
				);
			}

			return $erasers;
		}

		/**
		 * @param string $user_email
		 * @param int $page
		 *
		 * @return array
		 */
		public function eraser_user_payouts( $user_email, $page ) {

			$user     = get_user_by( 'email', $user_email );
			$number   = 50;
			$page     = (int) $page;
			$offset   = $number * ( $page - 1 );
			$response = array(
				'items_removed'  => false,
				'items_retained' => false,
				'messages'       => array(),
				'done'           => true,
			);


			if ( $user instanceof WP_User ) {

				$user_id      = $user->ID;
				$paypal_email = $this->get_paypal_email( $user_id );

				if ( ! empty( $paypal_email ) ) {
					$query_args = array(
						'receiver' => $paypal_email,
						'number'   => $number,
						'paged'    => $page,
						'fields'   => array( 'ID', 'transaction_id', 'transaction_status', 'amount', 'fee', 'currency' )
					);

					$payout_commissions = YITH_Payout_Items()->get_payout_items( $query_args );


					if ( 0 < count( $payout_commissions ) ) {

						foreach ( $payout_commissions as $commission ) {
							$id = $commission['ID'];

							 YITH_Payout_Items::anonymize_email_transaction( $id );
						}
						$message                   = _x( 'Removed User information From PayPal Payouts Items', '[GDPR Message]', 'yith-paypal-payouts-for-woocommerce' );
						$response['done']          = $number > count( $payout_commissions );
						$response['messages'][]    = sprintf( '%s (%s/%s)', $message, $offset, ( $offset + $number ) );
						$response['items_removed'] = true;
					} else {
						$response['done'] = true;
					}
				}
			}

			return $response;
		}

		/**
		 * @param string $user_email
		 *
		 * @return array
		 */
		public function eraser_user_info( $user_email ) {

			$user     = get_user_by( 'email', $user_email );
			$response = array(
				'items_removed'  => false,
				'items_retained' => false,
				'messages'       => array(),
				'done'           => true,
			);
			if ( $user instanceof WP_User ) {

				$user_id      = $user->ID;
				$paypal_email = function_exists( 'wp_privacy_anonymize_data' ) ? wp_privacy_anonymize_data( 'email' ) : 'deleted@email.com';

				$users_option = get_option( 'yith_payouts_receiver_list', array() );

				foreach( $users_option as $key => $option ){

					if( $user_id == $option['user_id'] ){
						$users_option[$key]['paypal_email'] = $paypal_email;
						break;
					}
				}
				update_option( 'yith_payouts_receiver_list', $users_option );

				$response['messages'][]    = __( 'PayPal email removed', 'yith-paypal-payouts-for-woocommerce' );
				$response['items_removed'] = true;
			}

			return $response;
		}

		/**
		 * Gets the message of the privacy to display.
		 * To be overloaded by the implementor.
		 *
		 * @return string
		 */
		public function get_privacy_message( $section ) {

			$message = '';
			switch ( $section ) {
				case 'collect_and_store':
					$message = '<p>' . __( 'We collect information about you during the registration and checkout process on our store.', 'yith-paypal-payouts-for-woocommerce' ) . '</p>' .
					           '<p>' . __( 'While you visit our site, we’ll track:', 'yith-paypal-payouts-for-woocommerce' ) . '</p>' .
					           '<ul>' .
					           '<li>' . __( 'User information: we will use these data to allows them to sell products on this website in exchange of a commission fee on each sale.', 'yith-paypal-payouts-for-woocommerce' ) . '</li>' .
					           '<li>' . __( 'The information required are the following: paypal email and commission rate', 'yith-paypal-payouts-for-woocommerce' ) . '</li>' .
					           '</ul>';
					break;

				case 'has_access':
					$message = '<p>' . __( 'Members of our team have access to the information you provide us. For example, both Administrators and Shop Managers can access:', 'yith-paypal-payouts-for-woocommerce' ) . '</p>' .
					           '<ul>' .
					           '<li>' . __( 'User information', 'yith-paypal-payouts-for-woocommerce' ) . '</li>' .
					           '<li>' . __( 'Data concerning commissions earned by the user', 'yith-paypal-payouts-for-woocommerce' ) . '</li>' .
					           '<li>' . __( 'Data about payments', 'yith-paypal-payouts-for-woocommerce' ) . '</li>' .
					           '</ul>' .
					           '<p>' . __( 'Our team members have access to this information to help fulfill orders, process orders and support you.', 'yith-paypal-payouts-for-woocommerce' ) . '</p>';
					break;

				case 'payments':
					$message = '<p>' . __( 'We send payments to vendors through PayPal. When processing payments, some of your data will be passed to PayPal, including information required to process or support the payment, such as the purchase total and billing information.', 'woocommerce' ) . '</p>' .
					           '<p>' . __( 'Please see the <a href="https://www.paypal.com/us/webapps/mpp/ua/privacy-full">PayPal Privacy Policy</a> for more details.', 'woocommerce' ) . '</p>';
					break;

				case 'share':
					$message = '<p>' . __( 'We share information with third parties who help us provide our orders and store services to you.', 'woocommerce' ) . '</p>';
					break;

			}

			return $message;
		}

		/**
		 * return paypal email by user_id
		 * @author Salvatore Strano
		 * @since 1.0.0
		 *
		 * @param $user_id
		 *
		 * @return string
		 */
		public function get_paypal_email( $user_id ) {
			$users_option = get_option( 'yith_payouts_receiver_list', array() );

			$paypal_email = '';

			foreach ( $users_option as $option ) {

				if ( $user_id == $option['user_id'] ) {
					$paypal_email = $option['paypal_email'];
					break;
				}
			}

			return $paypal_email;
		}
	}
}