<?php
/*
 * This file belongs to the YIT Framework.
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
 * @class      YITH_Stripe_Connect_WC_API
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Francsico Mateo
 *
 */

if ( ! class_exists( 'YITH_Stripe_Connect_WC_API' ) ) {
	/**
	 * Class YITH_Stripe_Connect_WC_API
	 *
	 * @author Francsico Mateo
	 */
	class YITH_Stripe_Connect_WC_API {

		public function __construct() {
			add_action( 'woocommerce_api_sc_webhook_event', array( $this, 'sc_webhook_event' ) );
		}

		public function account_application_deauthorized( $event ) {
			//$stripe_event = Stripe\Event::retrieve( $event->id );
			$stripe_user_id           = $event->account;
			$stripe_connect_gateway   = YITH_Stripe_Connect_Gateway::instance();
			$stripe_connect_receivers = YITH_Stripe_Connect_Receivers::instance();

			$users = get_users( array(
				'meta_key'   => 'stripe_user_id',
				'meta_value' => $stripe_user_id
			) );

			$user = ! empty( $users ) ? $users[0] : '';

			if ( ! empty( $user ) ) {
				$user_id                    = $user->id;
				$acc_deleted_from_site      = delete_user_meta( $user_id, 'stripe_user_id' );
				$acc_deleted_access_token   = delete_user_meta( $user_id, 'stripe_access_token' );
				$acc_deleted_from_receivers = $stripe_connect_receivers->update_by_user_id( $user_id, array( 'stripe_id' => '', 'status_receiver' => 'disconnect' ) );

				if ( ! $acc_deleted_from_site & ! $acc_deleted_access_token & ! $acc_deleted_from_receivers ) {
					$stripe_connect_gateway->log( 'error', sprintf( __( 'account.application.deauthorized Stripe Webhook event is disconnect for %s,
					 but it could not be removed from the server', 'yith-stripe-connect-for-woocommerce' ), $user->display_name ) );
				} else {
					$stripe_connect_gateway->log( 'info', sprintf( __( 'account.application.deauthorized Stripe Webhook event is disconnect for %s',
                        'yith-stripe-connect-for-woocommerce' ), $user->display_name ) );
				}
			}
		}

		public function sc_webhook_event() {
			$input      = @file_get_contents( "php://input" );
			$event_json = json_decode( $input );

			if ( empty( $event_json ) ) {
				die();
			}

			$type_method = str_replace( '.', '_', $event_json->type );

			if ( method_exists( $this, $type_method ) ) {
				call_user_func( array( $this, $type_method ), $event_json );
			} else {
				die();
			}
			die();
		}


	}
}