<?php
class WCST_OrderPage
{
	
	var $track;
	function __construct() 
	{
		add_action( 'add_meta_boxes', array( &$this, 'woocommerce_metaboxes' ) );
		add_action( 'woocommerce_process_shop_order_meta', array( &$this, 'woocommerce_process_shop_ordermeta' ), 10, 2 ); //99: using 99 info are not embedded into emails
		add_action( 'woocommerce_process_shop_order_meta', array( &$this, 'late_order_meta_processing' ), 99, 2 ); //just used to swich order status 
		//add_action( 'admin_menu', array( &$this, 'ship_select_menu')); 
	
	}
	
	function woocommerce_process_shop_ordermeta( $post_id, $post ) 
	{
		$wcst_order_model = new WCST_Order();
		$wcst_order_model->save_shippings_info_metas( $post_id, $_POST);	
	}
	function late_order_meta_processing( $post_id, $post ) 
	{
		$wcst_order_model = new WCST_Order();
		$wcst_order_model->late_order_meta_processing( $post_id, $_POST);	
	}

	function woocommerce_metaboxes() 
	{
		global $wcst_html_helper;
		add_meta_box( 'woocommerce-order-ship', __('Tracking Code', 'woocommerce-shipping-tracking'), array( &$wcst_html_helper, 'render_shipping_companies_tracking_info_configurator_widget' ), 'shop_order', 'side', 'high');

	}
		
	function ship_select_menu(){
		
		if (!function_exists('current_user_can') || !current_user_can('manage_options') )
		return;
			
	}
} 
?>