<?php 
class WCST_ExtraDelivery
{
	public function __construct()
	{
		//Attach new info
		add_action( 'woocommerce_after_checkout_billing_form', array( &$this, 'add_additional_form_checkout_page' ), 10, 1 ); //Checkout page
		
		//After Checkout
		add_action('woocommerce_checkout_order_processed', array( &$this, 'save_additional_shipping_info_after_checkout' )); //After checkout
		
		add_action( 'woocommerce_admin_order_data_after_billing_address', array( &$this,'after_shipping_address_meta_box_admin_order_page')); //Order details
		add_action( 'woocommerce_process_shop_order_meta', array( &$this, 'on_save_order_details_admin_order_page' ), 5, 2 );//save order	
		
		//Order details page
		add_action( 'woocommerce_order_details_after_order_table', array( &$this, 'add_order_details_page_addon' ) );
		
		//Email
		//add_action('woocommerce_email_customer_details', array( &$this, 'woocommerce_include_extra_fields_in_emails' ), 10, 3);
		add_action('woocommerce_email_after_order_table', array( &$this, 'woocommerce_include_extra_fields_in_emails' ), 10, 5);
	}
	
	private function  enqueue_scripts($options)
	{
		wp_enqueue_style('wcst-datepicker-classic', WCST_PLUGIN_PATH.'/css/datepicker/classic.css');   
		wp_enqueue_style('wcst-datepicker-date-classic', WCST_PLUGIN_PATH.'/css/datepicker/classic.date.css');   
		wp_enqueue_style('wcst-datepicker-time-classic', WCST_PLUGIN_PATH.'/css/datepicker/classic.time.css');   
		wp_enqueue_style('wcst-delivery-date', WCST_PLUGIN_PATH.'/css/wcst_delivery_date.css');  
		
		wp_enqueue_script('wcst-ui-picker', WCST_PLUGIN_PATH.'/js/datepicker/picker.js', array( 'jquery' ));
		wp_enqueue_script('wcst-ui-datepicker', WCST_PLUGIN_PATH.'/js/datepicker/picker.date.js', array( 'jquery' ));
		wp_enqueue_script('wcst-ui-timepicker', WCST_PLUGIN_PATH.'/js/datepicker/picker.time.js', array( 'jquery' ));
		wp_register_script('wcst-frontend-delivery-date', WCST_PLUGIN_PATH.'/js/wcst-frontend-delivery-date.js', array( 'jquery' ));
		$js_options = array(
			'just_one_date_field' => isset($options['options']['just_one_date_field']) ? 'true' : 'false'
		);
		wp_localize_script( 'wcst-frontend-delivery-date', 'wcst', $js_options );
		wp_enqueue_script( 'wcst-frontend-delivery-date' );
	}
	public function add_order_details_page_addon( $order )
	{
		global $wcst_time_model;
		$this->save_post_data(WCST_Order::get_id($order));
		
		$options = new WCST_Option();
		$order_model = new WCST_Order();
		$delivery_info = $order_model->get_delivery_and_times(WCST_Order::get_id($order));
		$messages_and_options = $options->get_checkout_options();
		$show_on_order_details_page = isset($messages_and_options['options']['show_on_order_details_page']) ? true : false;
		
		$general_options = $options->get_general_options();
		$date_format = isset($general_options['date_format']) ? $general_options['date_format'] : "dd/mm/yyyy";
		
		//Retrieve min delivery date 
		$min_date = $this->get_min_delivery_date(false, WCST_Order::get_id($order));
		
		if($show_on_order_details_page)
		{
			$this->enqueue_scripts($messages_and_options);
			include WCST_PLUGIN_ABS_PATH.'template/order_details_extra_delivery_info.php';
		}
	}
	
	public function add_additional_form_checkout_page($checkout)
	{
		$options = new WCST_Option();
		//$order_model = new WCST_Order();
		$messages_and_options = $options->get_checkout_options();
		//wccm_var_dump($messages_and_options);
		$show_on_checkout_page = isset($messages_and_options['options']['show_on_checkout_page']) ? true : false;
		
		//Retrieve min delivery date 
		$min_date = $this->get_min_delivery_date();
		//var_dump($min_date);
		$general_options = $options->get_general_options();
		$date_format = isset($general_options['date_format']) ? $general_options['date_format'] : "dd/mm/yyyy";
		
		if($show_on_checkout_page)
		{
			$this->enqueue_scripts($messages_and_options);
			include WCST_PLUGIN_ABS_PATH.'template/checkout_form.php';
		}
	}
	private function get_min_delivery_date($is_checkout = true, $order_id = 0)
	{
		global $wcst_product_model,$wcst_time_model;
		$min_date = null;
		
		//Retrieve min delivery date 
		if($is_checkout)
			foreach(WC()->cart->get_cart( ) as $item)
			{
				$estimated_date = $wcst_time_model->get_available_date($wcst_product_model->get_estimation_shippment_rule($item['product_id']), true);	
				$min_date = !isset($min_date) || $min_date > $estimated_date ? $estimated_date : $min_date;
			}
		else
		{
			$wc_order = wc_get_order($order_id); //new WC_Order( $order_id);
			$products = $wc_order->get_items();
			foreach($products as $item)
			{
				$order_item_id = version_compare( WC_VERSION, '2.7', '<' )  ? $item['item_meta']['_product_id'][0] : $item->get_product_id();
				$estimated_date = $wcst_time_model->get_available_date($wcst_product_model->get_estimation_shippment_rule($order_item_id), true);
				$min_date = !isset($min_date) || $min_date > $estimated_date ? $estimated_date : $min_date;
			}
		}
		$result = isset($min_date) ? $min_date : new DateTime();
		$result = $result->modify('+1 day'); //Shipping rate
		
		return array('day' => $result->format("j"), 'month' => $result->format("n")-1, 'year' => $result->format("Y"));
	}
	public function after_shipping_address_meta_box_admin_order_page()
	{
		global $post;
		$options = new WCST_Option();
		$order_model = new WCST_Order();
		$delivery_info = $order_model->get_delivery_and_times($post->ID);
		$messages_and_options = $options->get_checkout_options();
		//wccm_var_dump($delivery_info);
		
		wp_enqueue_style('wcst-datepicker-classic', WCST_PLUGIN_PATH.'/css/datepicker/classic.css');   
		wp_enqueue_style('wcst-datepicker-date-classic', WCST_PLUGIN_PATH.'/css/datepicker/classic.date.css');   
		wp_enqueue_style('wcst-datepicker-time-classic', WCST_PLUGIN_PATH.'/css/datepicker/classic.time.css');  
		wp_enqueue_style('wcst-order-date-picker', WCST_PLUGIN_PATH.'/css/datepicker/classic.time.css');  
		wp_enqueue_style('wcst-order-detail-ui', WCST_PLUGIN_PATH.'/css/wcst_admin_order_details_page.css');  
		
		wp_enqueue_script('wcst-ui-picker', WCST_PLUGIN_PATH.'/js/datepicker/picker.js', array( 'jquery' ));
		wp_enqueue_script('wcst-ui-datepicker', WCST_PLUGIN_PATH.'/js/datepicker/picker.date.js', array( 'jquery' ));
		wp_enqueue_script('wcst-ui-timepicker', WCST_PLUGIN_PATH.'/js/datepicker/picker.time.js', array( 'jquery' ));
		wp_enqueue_script('wcst-order-datail-delivery-date', WCST_PLUGIN_PATH.'/js/wcst-order-datail-delivery-date.js', array( 'jquery' ));
		
		$general_options = $options->get_general_options();
		$date_format = isset($general_options['date_format']) ? $general_options['date_format'] : "dd/mm/yyyy";
		?>
		<script>
			//var wcst_date_format_2 = "<?php echo $date_format; ?>";
		</script>
		<?php
		//if(isset($delivery_info))
		{
			if(isset($messages_and_options['options']['date_range']))
			{
				?>
				<p class="wcst_extra_delivery_row form-row form-row-wide">
					<strong><?php _e('Delivery date range: ', 'woocommerce-shipping-tracking');  ?></strong><br/>
					<input type="text" class="wcst_input_date" name="wcst_delivery[date_start_range]" value="<?php if(isset($delivery_info['date_start_range'])) echo $delivery_info['date_start_range']; ?>"></input>
					<input type="text" class="wcst_input_date" name="wcst_delivery[date_end_range]" value="<?php if(isset($delivery_info['date_end_range'])) echo $delivery_info['date_end_range']; ?>"></input>
				</p>
				<?php
			}
			if(isset($messages_and_options['options']['time_range']))
			{
				?>
				<p class="wcst_extra_delivery_row  form-row form-row-wide">
					<strong><?php _e('Delivery time range: ', 'woocommerce-shipping-tracking'); ?></strong><br/>
					<input type="text" class="wcst_input_time" name="wcst_delivery[time_start_range]" value="<?php if(isset($delivery_info['time_start_range'])) echo $delivery_info['time_start_range']; ?>"></input>
					<input type="text" class="wcst_input_time" name="wcst_delivery[time_end_range]" value="<?php if(isset($delivery_info['time_end_range'])) echo $delivery_info['time_end_range']; ?>"></input>
				</p>
				<?php
			}
			if(isset($messages_and_options['options']['time_range']) && isset($messages_and_options['options']['time_secondary_range']))
			{
				?>
				<p class="wcst_extra_delivery_row form-row form-row-wide">
					<strong><?php  _e('Delivery secondary time range: ', 'woocommerce-shipping-tracking'); ?></strong><br/>
					<input type="text" class="wcst_input_time" name="wcst_delivery[time_secondary_start_range]" value="<?php if(isset($delivery_info['time_secondary_start_range'])) echo $delivery_info['time_secondary_start_range']; ?>"></input>
					<input type="text" class="wcst_input_time" name="wcst_delivery[time_secondary_end_range]" value="<?php if(isset($delivery_info['time_secondary_end_range'])) echo $delivery_info['time_secondary_end_range']; ?>"></input>
				</p>
				<?php
			}
		}
	}
	
	public function save_additional_shipping_info_after_checkout($order_id)
	{
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) 
		  return $order_id;
	 
		$this->save_post_data($order_id);
	}
	public function on_save_order_details_admin_order_page( $order_id, $post )
	{
		$this->save_post_data($order_id);
	}
	private function save_post_data($order_id)
	{
		if(isset($_POST['wcst_delivery']))
		{
			$order_model = new WCST_Order();
			$order_model->save_delivery_date_and_time($order_id, $_POST['wcst_delivery']);
		}
	}
	public function woocommerce_include_extra_fields_in_emails($order, $sent_to_admin = false, $plain_text = "", $email = null)
	{
		if(!isset($order))
			return;
		
		$order_model = new WCST_Order();
		//$options = new WCST_Option();
		
		//$messages_and_options = $options->get_checkout_options();
		$delivery_info = $order_model->get_delivery_and_times(WCST_Order::get_id($order));
		
		if(!empty($delivery_info))
		{
			include WCST_PLUGIN_ABS_PATH.'template/email_extra_delivery_info.php';
		}
	}
}
?>