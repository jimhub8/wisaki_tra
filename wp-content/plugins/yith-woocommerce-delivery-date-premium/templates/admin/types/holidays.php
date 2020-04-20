<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$all_shipping_method = YITH_Delivery_Date_Processing_Method()->get_processing_method();
$all_carrier         = YITH_Delivery_Date_Carrier()->get_all_formatted_carriers();
$holidays_option     = get_option( $id, array() );

?>
<thead id="ywcdd_holiday_header_table">
<tr>
    <th class="onoff"><?php _e('Status','yith-woocommerce-delivery-date' );?></th>
    <th><?php _e( 'Holiday Name', 'yith-woocommerce-delivery-date' ); ?></th>
    <th><?php _e( 'Holiday for', 'yith-woocommerce-delivery-date' ); ?></th>
    <th><?php _e( 'From', 'yith-woocommerce-delivery-date' ); ?></th>
    <th><?php _e( 'To', 'yith-woocommerce-delivery-date' ); ?></th>
    <th><?php _e( 'Edit','yith-woocommerce-delivery-date' );?></th>
    <th></th>
</tr>
</thead>
<tbody id="ywcdd_holiday_body">
<?php if ( count( $holidays_option ) > 0 ):
	foreach ( $holidays_option as $holiday_id => $single_holiday ): ?>
        <tr data-holiday_id="<?php echo $holiday_id; ?>" align="top">
            <td class="forminp onoff">
				<?php
				$onoff_args = array(
					'id'    => $holiday_id,
					'class' => 'yith_holiday_onoff',
					'type'  => 'onoff',
					'value' => $single_holiday['enabled']
				);
				echo yith_plugin_fw_get_field( $onoff_args );
				?>
            </td>
            <td class="forminp">
                <div class="ywcdd_edit_row_holiday">
                    <input type="text" class="ywcdd_holiday_name"
                           value="<?php esc_attr_e( $single_holiday['event_name'] ); ?>"/>
                </div>
                <div class="ywcdd_row_holiday">
                    <span class="ywcdd_holiday_name"><?php echo $single_holiday['event_name']; ?></span>
                </div>
            </td>
            <td class="forminp">
                <div class="ywcdd_edit_row_holiday">
                    <select id="ywcdd_add_holiday_how" name="ywcdd_add_holiday_how"
                            class="ywcdd_how_holiday wc-enhanced-select" multiple="multiple">
                        <optgroup
                                label="<?php _e( 'Order Processing Method', 'yith-woocommerce-delivery-date' ); ?>">
							<?php foreach ( $all_shipping_method as $method ): ?>
                                <option value="<?php esc_attr_e( $method->ID ); ?>" <?php selected( true, in_array( $method->ID, $single_holiday['how_add_holiday'] ) ); ?>><?php echo get_the_title( $method->ID ); ?></option>
							<?php endforeach; ?>
                        </optgroup>
                        <optgroup label="<?php _e( 'Carrier', 'yith-woocommerce-delivery-date' ); ?>">
							<?php

							foreach ( $all_carrier as $carrier_id => $carrier_name ):?>
                                <option value="<?php esc_attr_e( $carrier_id ); ?>" <?php selected( true, in_array( $carrier_id, $single_holiday['how_add_holiday'] ) ); ?>><?php echo $carrier_name; ?></option>
							<?php endforeach; ?>
                        </optgroup>
                    </select>
                </div>
                <div class="ywcdd_row_holiday how_holiday_for">
					<?php foreach ( $single_holiday['how_add_holiday'] as $holiday_for ) {
						echo '<p>' . get_the_title( $holiday_for ) . '</p>';
					}
					?>
                </div>
            </td>
            <td class="forminp">
                <div class="ywcdd_edit_row_holiday">
                    <input type="text" class="ywcdd_datepicker holiday_from"
                           value="<?php echo $single_holiday['start_event']; ?>">
                </div>
                <div class="ywcdd_row_holiday start_event">
                    <p><?php echo $single_holiday['start_event']; ?></p>
                </div>
            </td>
            <td class="forminp">
                <div class="ywcdd_edit_row_holiday">
                    <input type="text" class="ywcdd_datepicker holiday_to"
                           value="<?php echo $single_holiday['end_event']; ?>">
                </div>
                <div class="ywcdd_row_holiday end_event">
                    <p><?php echo $single_holiday['end_event']; ?></p>
                </div>
            </td>
            <td class="forminp btn_column">
                <div class="ywcdd_edit_row_holiday">
                    <a class="button button-secondary ywcdd_update_holiday"><?php _e( 'Update', 'yith-woocommerce-delivery-date' ); ?></a>
                </div>
                <div class="ywcdd_row_holiday">
                    <a class="ywcdd_edit_holiday button button-secondary"
                       title="<?php _e( 'Edit', 'yith-woocommerce-delivery-date' ); ?>"><?php _e( 'Edit', 'yith-woocommerce-delivery-date' ); ?></a>
                </div>
            </td>
            <td>
                <a class="button button-secondary ywcdd_delete_holiday"><?php _e( 'Delete', 'yith-woocommerce-delivery-date' ); ?></a>

            </td>

        </tr>
	<?php endforeach; endif; ?>
</tbody>
<tfoot id="ywcdd_holiday_footer_table">
<tr valign="top">
    <td class="forminp" colspan="7">
        <div id="ywcdd_add_holiday_container">
            <a href=""
               class="ywcdd_add_holiday_btn button button-primary"><?php _e( '+ Add a new holiday', 'yith-woocommerce-delivery-date' ); ?></a>
            <div id="ywcdd_add_new_holiday">
                <p>
                    <label for="ywcdd_add_holiday_name"><?php _e( 'Holiday Name', 'yith-woocommerce-delivery-date' ); ?></label>
                    <input type="text" id="ywcdd_add_holiday_name" name="ywcdd_add_holiday_name"
                           class="ywcdd_holiday_name">
                </p>
                <p>
                    <label for="ywcdd_add_holiday_how"><?php _e( 'Holiday for', 'yith-woocommerce-delivery-date' ); ?></label>
                    <select id="ywcdd_add_holiday_how" name="ywcdd_add_holiday_how"
                            class="ywcdd_how_holiday wc-enhanced-select" multiple="multiple">
                        <optgroup
                                label="<?php _e( 'Processing Method', 'yith-woocommerce-delivery-date' ); ?>">
							<?php foreach ( $all_shipping_method as $method ): ?>
                                <option value="<?php esc_attr_e( $method->ID ); ?>"><?php echo get_the_title( $method->ID ); ?></option>
							<?php endforeach; ?>
                        </optgroup>
                        <optgroup label="<?php _e( 'Carrier', 'yith-woocommerce-delivery-date' ); ?>">
							<?php

							foreach ( $all_carrier as $carrier_id => $carrier_name ):?>
                                <option value="<?php esc_attr_e( $carrier_id ); ?>"><?php echo $carrier_name; ?></option>
							<?php endforeach; ?>
                        </optgroup>
                    </select>
                </p>
                <p>
                    <label for="ywcdd_add_holiday_from"><?php _e( 'From', 'yith-woocommerce-delivery-date' ); ?></label>
                    <input type="text" id="ywcdd_add_holiday_from" name="ywcdd_add_holiday_from"
                           class="ywcdd_datepicker holiday_from">
                </p>
                <p>
                    <label for="ywcdd_add_holiday_from"><?php _e( 'To', 'yith-woocommerce-delivery-date' ); ?></label>
                    <input type="text" id="ywcdd_add_holiday_to" name="ywcdd_add_holiday_to"
                           class="ywcdd_datepicker holiday_to">
                </p>
                <p>
                    <input type="submit" class="yith-add-new-holiday button button-primary"
                           value="<?php echo esc_attr( __( 'Add', 'yith-woocommerce-delivery-date' ) ) ?>"/>
                </p>
            </div>
        </div>
    </td>
</tr>
</tfoot>



