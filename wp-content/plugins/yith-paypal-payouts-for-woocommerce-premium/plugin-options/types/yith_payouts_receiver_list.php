<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$default = isset( $option['default'] ) ? $option['default'] : array();

$id = $option['id'];

$values = get_option( $id , $default );

?>


<div id="<?php echo $id ?>-container" <?php echo yith_field_deps_data( $option ); ?>
     class="yith-plugin-fw-metabox-field-row">
    <table class="widefat yith_payouts_receiver_table striped">
        <thead>
        <tr>
            <th><?php _e( 'User', 'yith-paypal-payouts-for-woocommerce' ); ?></th>
            <th><?php _e( 'PayPal Email', 'yith-paypal-payouts-for-woocommerce' ); ?></th>
            <th><?php _e( 'Commission', 'yith-paypal-payouts-for-woocommerce' ); ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
		<?php
		if ( is_array( $values ) && count( $values ) > 0 ) {
                $i =0 ;
                foreach( $values as $receiver ){

                    include( 'yith_payouts_single_receiver.php' );
                    $i++;
                }
		}
		?>
        </tbody>
        <tfoot>
        <tr>
            <th colspan="4">
                <a href="#" class="button insert"><?php _e( 'Add new Receiver', 'yith-paypal-payouts-for-woocommerce' ); ?></a>
            </th>
        </tr>
        </tfoot>
    </table>
</div>