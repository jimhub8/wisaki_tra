<?php
class WCST_CheckoutPage
{
	function __construct()
	{
		add_action( 'init', array( &$this, 'init' ));
	}
	function init()
	{
		add_action('woocommerce_new_order_item', array( &$this, 'update_order_item_meta' ),10,3);
	}
	function update_order_item_meta($item_id, $values, $cart_item_key)
	{
		if(!is_a( $values, 'WC_Order_Item_Product' ))
			return;
		
		$options_controller = new WCST_Option();
		/* if ( is_a( $values, 'WC_Order_Item_Product' ) ) 
		{
			$values = $values->legacy_values;
			//$cart_item_key = $values->legacy_cart_item_key;
		}  */
		//wcst_var_dump(get_class($values));
		
		if($options_controller->get_general_options('estimated_shipping_report_info_on_order_details', false))
		{
			$product_id = $values->get_variation_id() != 0 ? $values->get_variation_id() :  $values->get_product_id();
			$product = wc_get_product($product_id);
			$wpml_helper = new WCST_Wpml();
			$estimated_shipping_info_product_page_label = $options_controller->get_general_options('estimated_shipping_info_product_page_label');
			$estimated_shipping_info_product_page_label = isset($estimated_shipping_info_product_page_label[$wpml_helper->get_current_locale()]) ? $estimated_shipping_info_product_page_label[$wpml_helper->get_current_locale()] : __('Estimated shipping date:', 'woocommerce-shipping-tracking');
			$estimated_shipping_info_product_page_show_text_for_out_of_stock = $options_controller->get_general_options('estimated_shipping_info_product_page_show_text_for_out_of_stock');
			$estimated_shipping_info_product_page_show_text_for_out_of_stock = isset($estimated_shipping_info_product_page_show_text_for_out_of_stock) ? $estimated_shipping_info_product_page_show_text_for_out_of_stock : "yes";
			
			if($estimated_shipping_info_product_page_show_text_for_out_of_stock == 'no' && (!isset($product) || $product == false || !$product->is_in_stock( ) || (WCST_Order::get_manage_stock($product) != 'no' && isset($stock_quantity) && $stock_quantity < 1)))
			{
				return;
			}
			else
			{
				$estimated_shipping_info_product_page_label = str_replace(":", "", $estimated_shipping_info_product_page_label);
				wc_add_order_item_meta($item_id, $estimated_shipping_info_product_page_label, do_shortcode('[wcst_show_estimated_date product_id="'.$product_id.'"]'), true);
			}
		}
		
	}
}
?>