<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Payouts_Shortcodes' ) ) {

	class YITH_Payouts_Shortcodes {

		public function __construct() {
			add_shortcode( 'yith_payout_transactions', array( $this, 'payout_transaction' ) );
		}

		/**
		 *
		 */
		public function payout_transaction( $atts ) {

			$atts = shortcode_atts( array(
				'per_page'     => 10,
				'pagination'   => 'no',
				'current_page' => 1
			), $atts
			);

			extract( $atts );

			$current_page = empty( $current_page ) ? 1 : $current_page;

			if ( is_user_logged_in() ) {

				$query_args     = array();
				$user_id        = get_current_user_id();
				$receiver_email = apply_filters( 'yith_payout_receiver_email', $this->get_receiver_email( $user_id ), $user_id );

				if ( ! empty( $receiver_email ) ) {

					$query_args['receiver'] = $receiver_email;
					$query_args['fields']   = 'count';

					$tot_items = YITH_Payout_Items()->get_payout_items( $query_args );

					$query_args['fields'] = array(
						'payout_item_id',
						'transaction_id',
						'transaction_status',
						'amount',
						'currency'
					);
					$pages = 1;
					if ( 'yes' == $pagination && $tot_items > $per_page ) {
						$pages = ceil( $tot_items / $per_page );

						if ( $current_page > $pages ) {
							$current_page = $pages;
						}
						$query_args['paged']  = $current_page;
						$query_args['number'] = $per_page;
					}

					$items             = YITH_Payout_Items()->get_payout_items( $query_args );
					$additional_params = array(
						'count'          => $tot_items,
						'current_page'   => $current_page,
						'max_num_pages'  => $pages,
						'user_log_items' => $items,

					);
					$atts = array_merge( $atts, $additional_params );

					$atts['atts'] = $atts;

					ob_start();
					wc_get_template( 'view-payouts-list.php', $atts, '', YITH_PAYOUTS_TEMPLATE_PATH );
					$template = ob_get_contents();
					ob_end_clean();

					return $template;


				} else {
					$additional_params = array(
						'count'          => 0,
						'current_page'   => 1,
						'max_num_pages'  => 0,
						'user_log_items' => array(),

					);


					$atts         = array_merge( $atts, $additional_params );
					$atts['atts'] = $atts;

					ob_start();
					wc_get_template( 'view-payouts-list.php', $atts, '', YITH_PAYOUTS_TEMPLATE_PATH );
					$template = ob_get_contents();
					ob_end_clean();

					return $template;
				}

			}
		}

		/**
		 * get the paypal email
		 * @author Salvatore Strano
		 * @since 1.0.0
		 *
		 * @param int $user_id
		 *
		 * @return string
		 */
		public function get_receiver_email( $user_id ) {

			$receivers_opt = get_option( 'yith_payouts_receiver_list', array() );

			if ( $receivers_opt ) {
				foreach ( $receivers_opt as $receiver ) {

					if ( $user_id == $receiver['user_id'] ) {
						return $receiver['paypal_email'];
					}
				}
			}

			return '';
		}

	}
}

if ( ! function_exists( 'YITH_Payouts_Shortcodes' ) ) {
	/**
	 * @return YITH_Payouts_Shortcodes
	 */
	function YITH_Payouts_Shortcodes() {
		return new YITH_Payouts_Shortcodes();
	}
}

YITH_Payouts_Shortcodes();