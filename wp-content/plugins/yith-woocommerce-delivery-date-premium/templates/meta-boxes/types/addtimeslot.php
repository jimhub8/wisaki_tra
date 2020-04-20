<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post;
extract( $args );

$label_fee = sprintf( '%s (%s)', __( 'Fee', 'yith-woocommerce-delivery-date' ), get_woocommerce_currency_symbol() );
?>
<div id="<?php esc_attr_e( $id ); ?>-container">
    <div class="ywcdd_add_time_slot">
        <a id="yith_add_time_slot"
           class="button button-primary"><?php _e( 'Add a time slot', 'yith-woocommerce-delivery-date' ); ?></a>
    </div>
    <div class="yith-new-time-slot">
        <label><?php _e( 'Add new time slot', 'yith-woocommerce-delivery-date' ); ?></label>
        <div class="yith_time_slot_field">
            <div class="yith_time_slot_field_title">
				<?php _e( 'Time slot name', 'yith-woocommerce-delivery-date' ); ?>
            </div>
            <div class="yith_time_slot_field_content">
                <input type="text" id="yith_time_slot_name" name="yith_add_time_slot_name"/>
            </div>
        </div>
        <div class="yith_time_slot_field timefrom_field">
            <div class="yith_time_slot_field_title">
				<?php _e( 'Time from', 'yith-woocommerce-delivery-date' ); ?>
            </div>
            <div class="yith_time_slot_field_content">
                <input type="text" id="yith_timepicker_from" class="yith_timepicker"
                       placeholder="<?php _e( 'Time From', 'yith-woocommerce-delivery-date' ); ?>"  name="yith_add_time_slot_from"/>
            </div>
        </div>
        <div class="yith_time_slot_field ">
            <div class="yith_time_slot_field_title">
				<?php _e( 'Time to', 'yith-woocommerce-delivery-date' ); ?>
            </div>
            <div class="yith_time_slot_field_content">
                <input type="text" id="yith_timepicker_to" class="yith_timepicker"
                       placeholder="<?php _e( 'Time To', 'yith-woocommerce-delivery-date' ); ?>" name="yith_add_time_slot_to"/>
            </div>
        </div>
        <div class="yith_time_slot_field">
            <div class="yith_time_slot_field_title">
				<?php _e( 'Lockout', 'yith-woocommerce-delivery-date' ); ?>
            </div>
            <div class="yith_time_slot_field_content">
                <input type="number" id="yith_max_tot_order" min="0" step="1"
                       placeholder="<?php _e( 'Lockout', 'yith-woocommerce-delivery-date' ); ?>" name="yith_add_time_slot_lockout"/>
                <span class="description"><?php _e( 'Max number of orders accepted for this time slot', 'yith-woocommerce-delivery-date' ); ?></span>
            </div>
        </div>

        <div class="ywcdd_fee_row">
            <div class="yith_time_slot_field fee_name">
                <div class="yith_time_slot_field_title">
					<?php _e( 'Fee Name', 'yith-woocommerce-delivery-date' ); ?>
                </div>
                <div class="yith_time_slot_field_content">
                    <input type="text" id="yith_fee_name" name="yith_add_time_slot_fee_name"/>
                </div>
            </div>
            <div class="yith_time_slot_field">
                <div class="yith_time_slot_field_title">
					<?php _e( 'Fee Price', 'yith-woocommerce-delivery-date' ); ?>
                </div>
                <div class="yith_time_slot_field_content">
                    <input type="number" id="yith_fee" min="0" step="any" placeholder="<?php echo $label_fee; ?>" name="yith_add_time_slot_fee"/>
                </div>
            </div>
            <span class="description"><?php _e( 'Add a fee for this time slot. If you don\'t need a fee, leave these fields empty.', 'yith-woocommerce-delivery-date' ); ?></span>
        </div>

        <input type="submit" id="yith_save_time_slot" class="button button-primary"
               value="<?php _e( 'Save', 'yith-woocommerce-delivery-date' ); ?>"/>
        <input type="hidden" id="yith_carrier_id" value="<?php echo $post->ID; ?>" name="yith_add_time_slot_id"/>
        <input type="hidden" id="yith_metakey" value="<?php echo $id; ?>" name="yith_metakey"/>
    </div>
    <div class="ywcdd_carrier_table">
		<?php
		$post_id = $post->ID;
		$metakey = $id;
		include_once( YITH_DELIVERY_DATE_TEMPLATE_PATH . 'meta-boxes/carrier-time-slot-view.php' );
		?>
    </div>
</div>
