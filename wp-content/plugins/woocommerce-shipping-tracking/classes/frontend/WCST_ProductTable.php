<?php 
class WCST_ProductTable
{
	public function __construct()
	{
		add_filter( 'woocommerce_cart_item_name', array(&$this, 'add_estimated_shipping_date_to_product'), 99, 3 ); 
	}
	function add_estimated_shipping_date_to_product( $url, $cart_item, $cart_item_key ) 
	{ 
		$options_controller = new WCST_Option();
		$estimated_shipping_info_cart_checkout_pages_automaic_display = $options_controller->get_general_options('estimated_shipping_info_cart_checkout_pages_automaic_display', 'no');
				
		if($estimated_shipping_info_cart_checkout_pages_automaic_display == 'yes' && (@is_cart() || @is_shop))
		{
			$product_id = $cart_item['variation_id'] != 0 ? $cart_item['variation_id'] : $cart_item['product_id'];
			$estimated_date = do_shortcode('[wcst_show_estimated_date product_id="'.$product_id .'"]');
			if($estimated_date != "N/A" && $estimated_date != "")
			{
				$wpml_helper = new WCST_Wpml();
				$estimated_shipping_info_product_page_label = $options_controller->get_general_options('estimated_shipping_info_product_page_label');
				$estimated_shipping_info_product_page_label = isset($estimated_shipping_info_product_page_label[$wpml_helper->get_current_locale()]) ? $estimated_shipping_info_product_page_label[$wpml_helper->get_current_locale()] : __('Estimated shipping date:', 'woocommerce-shipping-tracking');
		
				$url .="<br/><strong>".$estimated_shipping_info_product_page_label."</strong> ".$estimated_date;
			}
			//wcst_var_dump($url);
		}
		return $url; 
	}
}
?>