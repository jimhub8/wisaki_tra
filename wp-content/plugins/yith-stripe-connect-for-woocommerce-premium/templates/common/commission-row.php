<tr id="<?php echo $index ?>"
    class="yith_wcsc_commission_row">
	<?php
	$view_text            = __( 'View', 'yith-stripe-connect-for-woocommerce' );
	$integration_class_icon = '';
	if ( ! empty( $affiliate_text ) ) {
		$integration_class_icon = '<span class="integration_commission_icon dashicons dashicons-admin-users" title="'. __('Affiliates Commission', 'yith-stripe-connect-for-woocommerce') .'"></span>';
	}

	if ( ! empty( $multivendor_text ) ) {
		$integration_class_icon = '<span class="integration_commission_icon dashicons dashicons-groups" title="'. __('Multi Vendor Commission', 'yith-stripe-connect-for-woocommerce') .'"></span>';
	}

	?>
    <td>
        <a href="#" data-commission="<?php echo $id_commission ?>" class="_commission dashicons dashicons-visibility view-info" title="<?php echo $view_text; ?>"><?php echo $view_text; ?></a>
    </td>
	<?php if ( is_admin() ) { ?>
        <td class="info-field _receivers_<?php echo $index ?>_receiver_info receiver-info">
            <span class="_commission commission_title"><strong> #<?php echo $id_commission . ' ' . $display_name ?></strong></span>
	        <?php echo $integration_class_icon ?>
        </td>
	<?php } ?>
    <td class="info-field _receivers_<?php echo $index ?>_product_info product-info">
        <?php if(!is_admin()){
	        echo $integration_class_icon;
        }?>
        <span class="_commission"><?php echo $product_info ?></span>
    </td>
    <td class="info-field _receivers_<?php echo $index ?>_commission_info commission-info">
		<?php
		if ( ! empty( $commission_text_detail ) ) {
			echo wc_help_tip( $commission_text_detail );
		}
		?>
        <span class="_commission" title="<?php echo $commission_text_detail ?> "><?php echo $commission_total ?></span>
    </td>
    <td class="info-field _receivers_<?php echo $index ?>_order_info order-info">
		<?php
		$order_link = '';
		if ( is_admin() ) {
			$order_link = get_edit_post_link( $order_id );
			?>
            <span class="_commission"><a href="<?php echo $order_link; ?>">#<?php echo $order_id ?></a></span>
			<?php
		} else {
			?>
            <span class="_commission">#<?php echo $order_id ?></span>
			<?php
		}
		?>
    </td>
    <td class="info-field _receivers_<?php echo $index ?>_purchased_date_info purchased-date-info">
        <span class="_commission"><?php echo $purchased_date; ?></span>
    </td>
    <td class="info-field _receivers_<?php echo $index ?>_commission_status_info commission-status-info">
        <span class="_commission commission_status commission_status_<?php echo $commission_status ?>" title="<?php echo $commission_status_text ?>"><?php if ( is_admin() ) {
		        echo $commission_status_text;
	        } else {
		        echo $commission_status_resumed;
	        } ?></span>
		<?php
		if ( ! empty( $note ) ) {
			echo wc_help_tip( $note );
		}
		?>
    </td>
	<?php if ( is_admin() ) { ?>
        <td class="info-field _receivers_<?php echo $index ?>_status_receiver_info status-receiver-info <? echo '_status_receiver_' . $receiver_status; ?>">
			<?php
			$receiver_status_text = '';
			if ( 'connect' == $receiver_status ) {
				$receiver_status_text = __( 'Connected', 'yith-stripe-connect-for-woocommerce' );
			}
			if ( 'disconnect' == $receiver_status ) {
				$receiver_status_text = __( 'Disconnected', 'yith-stripe-connect-for-woocommerce' );
			}
			?>
            <image class="_commission" src="<?php echo YITH_WCSC_ASSETS_URL . 'images/sc-icon-' . $receiver_status . '.svg' ?>" title="<?php echo $receiver_status_text; ?>"></image>
        </td>
	<?php } ?>
</tr>