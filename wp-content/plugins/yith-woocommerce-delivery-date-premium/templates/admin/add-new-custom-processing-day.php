<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( 'category' == $type ) {
	$args       = array(
		'id'               => 'ywcdd_product_cat_search',
		'class'            => 'wc-product-search',
		'name'             => 'yith_new_shipping_day_cat[category]',
		'data-action'      => 'ywcdd_search_product_category',
		'data-multiple'    => false,
		'data-placeholder' => __( 'Search for a category&hellip;', 'yith-woocommerce-delivery-date' ),
		'style'            => 'width:300px;'

	);
	$name_field = "yith_new_shipping_day_cat";
} else {
	$args       = array(
		'id'               => 'ywcdd_product_search',
		'class'            => 'wc-product-search',
		'name'             => 'yith_new_shipping_day_prod[product]',
		'data-multiple'    => false,
		'data-placeholder' => __( 'Search for a product&hellip;', 'yith-woocommerce-delivery-date' ),
		'style'            => 'width:300px;'

	);
	$name_field = "yith_new_shipping_day_prod";

	$json_encode = '<div class="ywcdd_quantity_item">';
	$json_encode .= "<input type='number' class='ywcdd_from' name='" . $name_field . "[day_for_quantity][index][from]'> ";
	$json_encode .= __( 'To', 'yith-woocommerce-delivery-date' );
	$json_encode .= " <input type='number' class='ywcdd_to' name='" . $name_field . "[day_for_quantity][index][to]'> ";
	$json_encode .= __( 'set', 'yith-woocommerce-delivery-date' );
	$json_encode .= " <input type='number' class='ywcdd_day' name='" . $name_field . "[day_for_quantity][index][day]'> ";
	$json_encode .= __( 'days', 'yith-woocommerce-delivery-date' );
	$json_encode .= '</div>';

	$json_encode = esc_attr( $json_encode );
}
?>
<div class="yith_wcdd_panel_processing_day">
    <form id="plugin-fw-wc" class="processing-day-table" method="post">
        <div id="ywcdd_form_add_<?php echo $type; ?>" class="ywcdd_form_processing_day">
            <div class="wrap">
                <label for="<?php echo $args['id']; ?>"><?php _e( 'Add new', 'yith-woocommerce-delivery-date' ); ?></label>
				<?php yit_add_select2_fields( $args );

				$template_args = array(
					'args' => array(
						'name'   => $name_field,
						'values' => array(),
						'type'   => $type
					)
				);

				wc_get_template( 'admin/quantity-range-field.php', $template_args, '', YITH_DELIVERY_DATE_TEMPLATE_PATH )
				?>

            </div>
        </div>
        <div class="ywcdd_add_new_button">
            <input type="submit" class="yith-add-new-<?php echo $type; ?>-day button button-primary"
                   value="<?php echo esc_attr( __( 'Add new', 'yith-woocommerce-delivery-date' ) ) ?>"/>
        </div>
    </form>
</div>
