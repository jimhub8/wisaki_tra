<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$url = wp_unslash( $_SERVER['REQUEST_URI'] );
if ( ! empty( $_REQUEST['_wp_http_referer'] ) ) {


	$url = remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), $url );


	if ( ! empty( $_REQUEST['yith_payouts_ids'] ) ) {

		$payout_ids = implode( ',', $_REQUEST['yith_payouts_ids'] );

		$url = remove_query_arg( array( 'yith_payouts_ids' ), $url );
		$url = add_query_arg( array( 'yith_payouts_ids' => $payout_ids ), $url );
	}
	wp_redirect( $url );
	exit;
}


if ( ( isset( $_GET['page'] ) && 'yith_wc_paypal_payouts_panel' == $_GET['page'] ) && ( isset( $_GET['tab'] ) && 'payout-list' == $_GET['tab'] ) ) {

	$return_to_list = '';
	if ( isset( $_GET['show_payout_details'] ) && $_GET['show_payout_details'] !== '' ) {

		$back_to_list_args = array(
			'page' => 'yith_wc_paypal_payouts_panel',
			'tab'  => 'payout-list'
		);

		if ( isset( $_GET['payment_mode'] ) ) {

			$back_to_list_args['payment_mode'] = $_GET['payment_mode'];
		}

		$url = esc_url( add_query_arg( $back_to_list_args, admin_url( 'admin.php' ) ) );

		$return_to_list = sprintf( '<a href="%1$s" class="yith_payouts_return_link" title="%2$s">%2$s <img draggable="false" class="emoji" alt="⤴" src="https://s.w.org/images/core/emoji/2.3/svg/2934.svg"></a>', $url, __( 'Return to Payout List', 'yith-paypal-payouts-for-woocommerce' ) );

		if ( ! class_exists( 'YITH_PayOut_Items_List_Table' ) ) {

			require_once( YITH_PAYOUTS_INC . 'class.yith-payout-items-list-table.php' );
		}

		$payout_batch_id = $_GET['show_payout_details'];

		$payment_id = str_replace( 'commission_', '', $payout_batch_id );
		$payout_batch_id = 'payment_'.$payment_id;
		$page_title      = __( 'Payout Details', 'yith-paypal-payouts-for-woocommerce' ) . " ($payout_batch_id)";
		$table           = new YITH_PayOut_Items_List_Table( array(
			'singular' => _x( 'Payout Item', 'yith-paypal-payouts-for-woocommerce' ),
			//singular name of the listed records
			'plural'   => _x( 'Payout Items', 'yith-paypal-payouts-for-woocommerce' ),
		) );
	} else {

		if ( ! class_exists( 'YITH_PayOuts_List_Table' ) ) {

			require_once( YITH_PAYOUTS_INC . 'class.yith-payouts-list-table.php' );
		}

		$page_title = __( 'Payout List', 'yith-paypal-payouts-for-woocommerce' );
		$table      = new YITH_PayOuts_List_Table( array(
			'singular' => _x( 'Payout', 'yith-paypal-payouts-for-woocommerce' ),
			//singular name of the listed records
			'plural'   => _x( 'Payouts', 'yith-paypal-payouts-for-woocommerce' ),
		) );
	}
	?>
    <div class="wrap">
        <h2>
			<?php echo $page_title; ?>
        </h2>
		<?php echo $return_to_list; ?>
        <form method="get">
			<?php

			$table->prepare_items();
			$table->views();
			$table->search_box( __( 'Search Receiver', 'yith-paypal-payouts-for-woocommerce' ), 'search_receiver' );

			$table->display();
			?>
        </form>

    </div>

	<?php

}