<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$show_shipping_date = get_option( 'ywcdd_ddm_enable_shipping_message', 'no' );
$show_delivery_date = get_option( 'ywcdd_ddm_enable_delivery_message', 'no' );


$last_shipping_date_string = '';
$time_limit_string         = '';
$delivery_date_string      = '';
if ( 'yes' == $show_shipping_date ) {
	$last_shipping_date_string = get_option( 'ywcdd_ddm_shipping_message', '' );

}
if ( 'yes' == $show_delivery_date ) {
	$delivery_date_string = get_option( 'ywcdd_ddm_delivery_message', '' );
	if ( '' === $time_limit ) {
		$delivery_date_string = get_option( 'ywcdd_ddm_time_limit_alternative_txt', '' );
	}
}

$formats        = yith_get_delivery_date_format();
$current_format = get_option( 'yith_delivery_date_format', 'yy-mm-dd' );
$format         = isset( $formats[ $current_format ] ) ? $formats[ $current_format ] : 'Y-m-d';

$bg_shipping = get_option( 'ywcdd_dm_customization_ready_bg', '#eff3f5' );
$bg_delivery = get_option( 'ywcdd_dm_customization_customer_bg', '#ffdea5' );

?>
<style>
    #ywcdd_info_shipping_date {
        background: <?php echo $bg_shipping;?>
    }

    #ywcdd_info_first_delivery_date {
        background: <?php echo $bg_delivery;?>
    }
</style>
<div id="ywcdd_info_single_product">
	<?php if ( ''!== $last_shipping_date_string ):
		$last_shipping_date = "<span class='ywcdd_date_info shipping_date'>" . date_i18n( $format, $last_shipping_date ) . '</span>';
		$last_shipping_date_string = str_replace( '{shipping_date}', $last_shipping_date, $last_shipping_date_string );
		?>
        <div id="ywcdd_info_shipping_date">
            <span class="ywcdd_shipping_icon"></span>
            <span class="ywcdd_shipping_message">
			    <?php echo $last_shipping_date_string; ?>
            </span>
        </div>
	<?php endif; ?>
	<?php if ( '' !== $delivery_date_string ):
		$delivery_date = "<span class='ywcdd_date_info delivery_date'>" . date_i18n( $format, $delivery_date ) . "</span>";

		$time_limit           = "<span class='ywcdd_date_info time_limit'>" . $time_limit . '</span>';
		$delivery_date_string = str_replace( '{delivery_date}', $delivery_date, $delivery_date_string );
		$delivery_date_string = str_replace( '{time_limit}', $time_limit, $delivery_date_string );
		?>
        <div id="ywcdd_info_first_delivery_date">
            <span class="ywcdd_delivery_icon"></span>
            <span class="ywcdd_delivery_message">
			    <?php echo $delivery_date_string; ?>
            </span>
        </div>
	<?php endif; ?>
</div>
