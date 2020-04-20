<?php 
class WCST_Tracking_info_displayer
{
	public function __construct()
	{
		$option = new WCST_Option();
		//add_action( 'woocommerce_order_details_after_order_table', array( &$this, 'track_page_shipping_details' ) );
		//add_action( 'woocommerce_view_order', array( &$this, 'track_page_shipping_details' ) );
		add_action( $option->get_option('wcst_general_options', 'order_details_page_positioning', 'woocommerce_order_details_after_order_table'), array( &$this, 'track_page_shipping_details' ) );
		add_action( 'woocommerce_email_before_order_table', array( &$this, 'email_shipping_details' ) );
		add_action( 'wcst_email_render_tracking_info', array( &$this, 'email_shipping_details' ) );
		add_filter( 'wc_gzdp_email_invoice_text', array( &$this, 'email_shipping_details_germanized_pro' ),10,2 ); //Germanized pro
	}
	function track_page_shipping_details( $order )
	{
		global $wcst_order_model;
		
		$order_id = !is_numeric($order) ? WCST_Order::get_id($order) : $order;
		$order = !is_numeric($order) ? $order : wc_get_order($order_id); //new WC_Order($order_id);
		$order_meta = $wcst_order_model->get_order_meta($order_id) /* get_post_custom( $order_id ) */;
				
		if(/* isset($order_meta['_wcst_order_trackno']) &&  */isset($order_meta['_wcst_order_trackurl']) && isset($order_meta['_wcst_order_trackname']))
			$this->shipping_details( $order_meta, $order);
					
	} 
	function email_shipping_details_germanized_pro( $text, $invoice  )
	{
		global $wcst_order_model;
	    $order_id = get_post_meta($invoice->id, '_invoice_order', true);
	    //wcst_var_dump( $order_id);
	    $shipping_info = "";
	    if($order_id)
	    {
	       $order_meta = $wcst_order_model->get_order_meta($order_id); //get_post_custom(  $order_id );		
	       $order = wc_get_order($order_id); //new WC_Order($order_id);
    		if(/* isset($order_meta['_wcst_order_trackno']) && */ isset($order_meta['_wcst_order_trackurl']) && isset($order_meta['_wcst_order_trackname']))
    		{
    		    ob_start();
    			$this->shipping_details($order_meta, $order, true);
    			//wcst_var_dump($order_meta);
    			$result = ob_get_contents();    			
    			ob_end_clean();  
    			$shipping_info = "<br/><br/>".$result;
    		}
	    }
			
		return $text.$shipping_info ;
					
	} 
	function email_shipping_details( $order ) 
	{
				
		global $wcst_order_model;
		$order_meta = $wcst_order_model->get_order_meta(WCST_Order::get_id($order));; //get_post_custom(WCST_Order::get_id($order) );
		//$render_tracking_info_anyway = isset($order_meta['_wcst_track_without_tracking_code']) && $order_meta['_wcst_track_without_tracking_code'];
		
		//if(($render_tracking_info_anyway || (isset($order_meta['_wcst_order_trackno']) && isset($order_meta['_wcst_order_trackurl'])))			
		if(/* isset($order_meta['_wcst_order_trackno']) && */ isset($order_meta['_wcst_order_trackurl']) && isset($order_meta['_wcst_order_trackname']))
			$this->shipping_details($order_meta, $order, true);
				
	}
	function active_notification($order_meta, $order, $tracking_code_to_show_on_email = array())
	{
		$options_model = new WCST_Option();
		$lang = isset($order_meta['wpml_language']) ? $order_meta['wpml_language'][0] : null; 
		$messages = $options_model->get_messages( null, $lang );
		include WCST_PLUGIN_ABS_PATH.'/template/email_notification_text_before_tracking_info.php';
		$this->shipping_details($order_meta, $order, true, $tracking_code_to_show_on_email);
	}
	function shipping_details($order_meta, $order, $is_email = false, $tracking_code_to_show_on_email = array())
	{
			global $wcst_order_model, $wcst_time_model, $wcst_shortcodes;
			$options_model = new WCST_Option();
			//$options = get_option( 'wcst_options' );
			//$options = $options_model->get_option( 'wcst_options' );
			$options =  $options_model->get_option( 'wcst_general_options', 'email_options');
			$urltrack = $order_meta['_wcst_order_track_http_url'][0];
			$shipping_company_name =  $order_meta['_wcst_order_trackname'][0];
			$default_message = WCST_AdminMenu::get_default_message();
			$default_message_additional = WCST_AdminMenu::get_default_message_additional_shippings();
			$order_meta_additional_shippings = null;
			//$order_details_page_url = '<a href="'.wc_get_endpoint_url( 'view-order', $order->get_id(), wc_get_page_permalink( 'myaccount' ) ).'">'.__('Order page','woocommerce-shipping-tracking').'</a>';
			$order_details_page_url = wc_get_endpoint_url( 'view-order', $order->get_id(), wc_get_page_permalink( 'myaccount' ) );
			
			if(isset($order_meta['_wcst_additional_companies']))
			{
				$order_meta_additional_shippings = is_string($order_meta['_wcst_additional_companies'][0]) ? unserialize(array_shift($order_meta['_wcst_additional_companies'])) : $order_meta['_wcst_additional_companies'];
			}
		
			if ( (/*$order_meta['_wcst_order_trackno'][0] != null &&*/ $order_meta['_wcst_order_trackurl'][0] != null &&  $order_meta['_wcst_order_trackurl'][0] != 'NOTRACK') || 
				isset($order_meta_additional_shippings)) 
			{
				if($is_email )
				{
					//if(!isset($options['include_only_if_order_completed']) || $order->get_status( ) == "completed") //$order->has_status( 'complete' )
					if( !empty($tracking_code_to_show_on_email) || 
						($options_model->get_email_show_tracking_info_by_order_status($order->get_status( )) && ! $wcst_order_model->is_email_tracking_info_embedding_disabled(WCST_Order::get_id($order))))
					{
						$lang = isset($order_meta['wpml_language']) ? $order_meta['wpml_language'][0] : null; 
						include WCST_PLUGIN_ABS_PATH.'/template/email.php';
					}
				}
				else
				{
					wp_enqueue_style('wcst-style', WCST_PLUGIN_PATH.'/css/wcst_style.css');
					include WCST_PLUGIN_ABS_PATH.'/template/order_details.php';
				}
			}
			//wp_die(); 
		}
}
?>