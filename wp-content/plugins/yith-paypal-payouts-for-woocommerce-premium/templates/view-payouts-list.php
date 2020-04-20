<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wp_enqueue_style( 'yith_payouts_style' );
$table_column = array(
	'payout_item_id'     => __( 'Payout item ID', 'yith-paypal-payouts-for-woocommerce' ),
	'transaction_id'     => __( 'Transaction ID', 'yith-paypal-payouts-for-woocommerce' ),
	'transaction_status' => __( 'Transaction Status', 'yith-paypal-payouts-for-woocommerce' ),
	'amount'             => __( 'Amount', 'yith-paypal-payouts-for-woocommerce' ),

);
?>
<div class="yith_payouts_container">
	<?php
	if ( count( $user_log_items ) > 0 ) { ?>
        <table class="shop_table shop_table_responsive my_account_orders">
            <thead>
            <tr>
				<?php foreach ( $table_column as $column ) : ?>
                    <th><?php echo $column; ?></th>
				<?php endforeach; ?>
            </tr>
            </thead>
            <tbody>
			<?php
			foreach ( $user_log_items as $single_row ) { ?>
                <tr class="order">
					<?php
					foreach ( $single_row as $column_name => $item ) {

						if ( 'currency' == $column_name ) {
							continue;
						}
						$output = '';
						$title  = '';
						switch ( $column_name ) {

							case 'amount':
								$currency = $single_row['currency'];
								$output   = wc_price( $item, array( 'currency' => $currency ) );
								break;
							case 'transaction_status' :
								$status = YITH_Payout_Items()->get_transaction_status();
								$output = isset( $status[ $item ] ) ? $status[ $item ] : $item;
								break;
							case 'transaction_id':
							case 'payout_item_id':
								$output = $item;
								break;


						} ?>
                        <td class="<?php echo $column_name; ?>"
                            data-title="<?php echo $table_column[ $column_name ]; ?>">
							<?php echo $output; ?>
                        </td>
						<?php
					} ?>
                </tr>
				<?php
			}
			?>
            </tbody>
        </table>
		<?php if ( 1 < $max_num_pages ) : ?>
            <div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
				<?php if ( 1 !== intval( $current_page ) ) : ?>
                    <a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button"
                       href="<?php echo esc_url( wc_get_endpoint_url( 'payouts', $current_page - 1 ) ); ?>"><?php _e( 'Previous', 'woocommerce' ); ?></a>
				<?php endif; ?>

				<?php if ( intval( $max_num_pages ) !== intval( $current_page ) ) : ?>
                    <a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button"
                       href="<?php echo esc_url( wc_get_endpoint_url( 'payouts', $current_page + 1 ) ); ?>"><?php _e( 'Next', 'woocommerce' ); ?></a>
				<?php endif; ?>
            </div>
		<?php endif; ?>
		<?php
	} else { ?>
        <div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
            <a class="woocommerce-Button button"
               href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
				<?php _e( 'Go to the shop', 'yith-paypal-payouts-for-woocommerce' ) ?>
            </a>
			<?php _e( 'No payout has been made yet.', 'yith-paypal-payouts-for-woocommerce' ); ?>

        </div>
		<?php

	}
	?>
</div>