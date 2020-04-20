<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * args = array(
 *  'type' => 'category' || 'product'
 * )
 */

if ( isset( $type ) ) {

	if ( 'product' == $type ) {

		$option     = get_option( 'yith_new_shipping_day_prod', array() );
		$id         = 'ywcdd_custom_shipping_product_wrapper';
		$name_field = "yith_new_shipping_day_prod";

	} else {
		$option     = get_option( 'yith_new_shipping_day_cat', array() );


		$id         = 'ywcdd_custom_shipping_category_wrapper';
		$name_field = "yith_new_shipping_day_cat";
	}
	?>


        <div id="<?php echo $id; ?>">
            <form method="post">
			<?php
			$index = 0;
			foreach ( $option as $key => $value ): ?>
            <div class="ywcdd_list_row ywcdd_list_row_close" data-index="<?php echo $index; ?>"
                 data-item_key="<?php echo $key; ?>">
                <div class="ywcdd_list_title">
					<?php
					if ( 'product' == $type ) {
						$product = wc_get_product( $value['product'] );
						$title   = $product->get_formatted_name();
					} else {
						$category = get_term_by( 'id', $value['category'], 'product_cat' );
						$title    = $category->name;
					}
					?>
                    <h3><?php echo $title; ?></h3>
                    <span class="ywcdd_custom_processing_method__toggle"><span
                                class="dashicons dashicons-arrow-up-alt2"></span></span>
                    <span class="ywcdd_custom_processing_method__enabled">
                            <?php
                            if ( ! isset( $value['enabled'] ) ) {
	                            $enabled = 'yes';
                            } else {
	                            $enabled = $value['enabled'];
                            }
                            $onoff_args = array(
	                            'id'    => 'ywcdd_enable_rule_' .$type.'_'. $key,
	                            'name' => $name_field.'[enabled]',
	                            'type'  => 'onoff',
	                            'value' => $enabled
                            );
                            echo yith_plugin_fw_get_field( $onoff_args );
                            ?>
                        </span>
                </div>

                <div class="ywcdd_list_content">
					<?php
					if ( isset( $value['need_process_day'] ) && ! is_array( $value['need_process_day'] ) ) {
						$day   = $value['need_process_day'];
						$day_value = array(
							array(
								'from' => 1,
								'to'   => '',
								'day'  => $day
							)
						);
					}else{
						$day_value =  $value['need_process_day'];

                    }
					$template_args = array(
						'args' => array(
							'name'   => $name_field,
							'values' => $day_value,
							'type'   => $type,
                            'btn_class' => 'ywcdd_update'
						)
					);

					wc_get_template( 'admin/quantity-range-field.php', $template_args, '', YITH_DELIVERY_DATE_TEMPLATE_PATH )

					?>
                </div>
            </div>
				<?php
				$index ++;
				endforeach; ?>
            </form>
            </div>


	<?php
}