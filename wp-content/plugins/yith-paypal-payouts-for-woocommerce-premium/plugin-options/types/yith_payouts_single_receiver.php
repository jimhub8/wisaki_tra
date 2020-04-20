<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$field_name = "yith_payouts_receiver_list[$i]";
?>
<tr>
    <td>
		<?php

		$customer_select2 = array(
			'id'               => "yith_paypal_receiver_list_" . $i . "_user",
			'name'             => $field_name . '[user_id]',
			'class'            => 'wc-customer-search',
			'data-placeholder' => __( 'Search for users', 'yith-paypal-payouts-for-woocommerce' ),
			'data-allow_clear' => true,
			'data-multiple'    => false,
		);

		if ( isset( $receiver['user_id'] ) ) {

			$user      = get_user_by( 'id', $receiver['user_id'] );
			$user_name = '#' . $user->ID . '-' . esc_html( $user->display_name );

			$customer_select2['data-selected'] = array( $receiver['user_id'] => $user_name );
			$customer_select2['value']         = $receiver['user_id'];
		}
		yit_add_select2_fields( $customer_select2 );
		?>
    </td>
    <td>
        <?php
            $email = isset( $receiver['paypal_email'] )? $receiver['paypal_email'] : '';
        ?>
        <input type="email" required name="<?php echo $field_name;?>[paypal_email]" value="<?php echo $email;?>" placeholder="<?php _e( 'Enter a valid email','yith-paypal-payouts-fo-woocommerce' );?>">
    </td>
    <td>
        <?php
            $commission_rate = isset( $receiver['commission_rate'] ) ? $receiver['commission_rate']  : '';
        ?>
        <input type="number" min="0" max="100" step="1" name="<?php echo $field_name;?>[commission_rate]" value="<?php echo $commission_rate;?>" placeholder="<?php _e( 'Insert commission rate', 'yith-paypal-payouts-fo-woocommerce' );?>">
    </td>
    <td width="1%"><a href="#" class="delete"><?php _e( 'Remove', 'yith-paypal-payouts-for-woocommerce' ); ?></a></td>
</tr>