<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$all_time_slot = get_post_meta( $post_id, $metakey, true );
$carrier_worksday = array_keys( YITH_Delivery_Date_Carrier()->get_work_days( $post_id ) );

$all_time_slot = empty( $all_time_slot ) ? array() : $all_time_slot;
$index         = 0;
$days          = yith_get_worksday();
$days = wp_array_slice_assoc( $days, $carrier_worksday );

$label_fee = sprintf('%s (%s)', __('Fee','yith-woocommerce-delivery-date'), get_woocommerce_currency_symbol() );

?>
<div id="<?php echo $metakey; ?>">
	<?php foreach ( $all_time_slot as $key => $slot ):
		$time_slot_enabled = ! empty( $slot['enabled'] ) ? $slot['enabled'] : 'yes';
		$time_from = $slot['timefrom'];
		$time_to = $slot['timeto'];
		$max_order = $slot['max_order'];
		$fee = $slot['fee'];

		$fee_name = ! empty( $slot['fee_name'] ) ? $slot['fee_name'] : '';
		$slot_name = ! empty( $slot['slot_name'] ) ? $slot['slot_name'] : $time_from . ' - ' . $time_to;
		$override_days = $slot['override_days'];
		$day_selected = isset( $slot['day_selected'] ) ? $slot['day_selected'] : array();
		?>
        <div class="ywcdd_list_row ywcdd_list_row_close" data-index="<?php echo $index; ?>"
             data-item_key="<?php echo $post_id; ?>">
            <div class="ywcdd_list_title">
                <h3><?php echo $slot_name; ?></h3>
                <span class="ywcdd_time_slot__toggle"><span
                            class="dashicons dashicons-arrow-up-alt2"></span></span>
                <span class="ywcdd_time_slot__enabled">
                            <?php

                            $onoff_args = array(
	                            'id'    => $key,
	                            'class' => 'ywcdd_enable_rule_slot',
	                            'type'  => 'onoff',
	                            'value' => $time_slot_enabled
                            );
                            echo yith_plugin_fw_get_field( $onoff_args );
                            ?>
                        </span>
            </div>
            <div class="ywcdd_list_content">
                <div class="yith_time_slot_field">
                    <div class="yith_time_slot_field_title">
						<?php _e( 'Time slot name', 'yith-woocommerce-delivery-date' ); ?>
                    </div>
                    <div class="yith_time_slot_field_content">
                        <input type="text" class="yith_time_slot_name" value="<?php echo $slot_name; ?>"/>
                    </div>
                </div>
                <div class="ywcdd_timeslot_from_to_row">
                    <div class="yith_time_slot_field">
                        <div class="yith_time_slot_field_title">
							<?php _e( 'Time from', 'yith-woocommerce-delivery-date' ); ?>
                        </div>
                        <div class="yith_time_slot_field_content">
                            <input type="text" class="yith_timepicker_from yith_timepicker"
                                   placeholder="<?php _e( 'Time From', 'yith-woocommerce-delivery-date' ); ?>" required
                                   value="<?php echo $time_from; ?>"/>
                        </div>
                    </div>
                    <div class="yith_time_slot_field ">
                        <div class="yith_time_slot_field_title">
							<?php _e( 'Time to', 'yith-woocommerce-delivery-date' ); ?>
                        </div>
                        <div class="yith_time_slot_field_content">
                            <input type="text" class="yith_timepicker_to yith_timepicker"
                                   placeholder="<?php _e( 'Time To', 'yith-woocommerce-delivery-date' ); ?>" required
                                   value="<?php echo $time_to; ?>"/>
                        </div>
                    </div>
                </div>
                <div class="yith_time_slot_field">
                    <div class="yith_time_slot_field_title">
						<?php _e( 'Lockout', 'yith-woocommerce-delivery-date' ); ?>
                    </div>
                    <div class="yith_time_slot_field_content">
                        <input type="number" class="yith_max_tot_order" min="0" step="1"
                               placeholder="<?php _e( 'Lockout', 'yith-woocommerce-delivery-date' ); ?>"
                               value="<?php echo $max_order; ?>"/>
                        <span class="description"><?php _e( 'Max number of orders accepted for this time slot', 'yith-woocommerce-delivery-date' ); ?></span>
                    </div>
                </div>
                <div class="ywcdd_fee_row">
                    <div class="yith_time_slot_field fee_name">
                        <div class="yith_time_slot_field_title">
							<?php _e( 'Fee Name', 'yith-woocommerce-delivery-date' ); ?>
                        </div>
                        <div class="yith_time_slot_field_content">
                            <input type="text" class="yith_fee_name" value="<?php echo $fee_name; ?>"/>
                        </div>
                    </div>
                    <div class="yith_time_slot_field">
                        <div class="yith_time_slot_field_title">
							<?php _e( 'Fee Price', 'yith-woocommerce-delivery-date' ); ?>
                        </div>
                        <div class="yith_time_slot_field_content">
                            <input type="number" class="yith_fee" min="0" step="any"
                                   placeholder="<?php echo $label_fee; ?>" value="<?php echo $fee; ?>"/>
                        </div>
                    </div>
                    <span class="description"><?php _e( 'Set a fee for this time slot. If you don\'t need a fee, leave these fields empty.', 'yith-woocommerce-delivery-date' ); ?></span>
                </div>
                <div class="yith_time_slot_field override_working_days">
                    <div class="yith_time_slot_field_title">
						<?php _e( 'Set Workdays', 'yith-woocommerce-delivery-date' ); ?>
						<?php
						$onoff_args = array(
							'id'    => 'yith_override_day_'.$index,
							'class' => 'yith_override_day',
							'type'  => 'onoff',
							'value' => $override_days
						);
						echo yith_plugin_fw_get_field( $onoff_args );

						$hide_workday_select = 'yes' !== $override_days ? 'style ="display:none;"' :'' ;
						?>
                    </div>
                    <div class="yith_time_slot_field working_day_container" <?php echo $hide_workday_select;?> >
                        <div class="yith_time_slot_field_title">
							<?php _e( 'Workday', 'yith-woocommerce-delivery-date' ); ?>
                        </div>
                        <div class="yith_time_slot_field_content">
                            <?php
                                $select_args = array(
                                        'id' => 'ywcdd_dayworkselect_'.$index,
                                        'type' => 'select-buttons',
                                        'class' => "yith_dayworkselect wc-enhanced-select",
                                        'options' => $days,
                                        'value' => $day_selected
                                );

                           echo yith_plugin_fw_get_field( $select_args );
                            ?>
                            <span class="description"><?php _e( 'If enabled, this time slot will be available only for the follow selected workdays', 'yith-woocommerce-delivery-date' ); ?></span>
                        </div>
                    </div>
                </div>

                <div class="ywcdd_action_container">
                    <input type="submit" class="button button-primary yith_update_time_slot"
                           value="<?php _e( 'Save', 'yith-woocommerce-delivery-date' ); ?>">
                    <input type="submit" class="button button-secondary ywcdd_delete_time_slot"
                           value="<?php _e( 'Delete', 'yith-woocommerce-delivery-date' ); ?>">
                </div>
            </div>
        </div>

		<?php $index ++;
	endforeach; ?>

</div>
