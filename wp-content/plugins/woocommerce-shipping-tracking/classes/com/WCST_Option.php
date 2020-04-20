<?php 
class WCST_Option
{
	var $option_cache;
	var $estimations_options_cache;
	public function __construct(){}
	
	public function get_messages($default = null , $lang_code = null )
	{
		//WPML
		global $sitepress;
		
		$options = get_option( 'wcst_template_messages');
		$result = isset($options) ? $options: $default;
		
		if(isset($result))
		{
			if(class_exists('SitePress') && ( (isset($lang_code) && $lang_code != $sitepress->get_default_language()) || ICL_LANGUAGE_CODE != $sitepress->get_default_language()))
			{
				$current_lang = isset($lang_code) ? $lang_code : ICL_LANGUAGE_CODE;
				//$current_lang = ICL_LANGUAGE_CODE;
				
				$result[$current_lang]['wcst_mail_message'] = (!isset($result[$current_lang]['wcst_mail_message']) || $result[$current_lang]['wcst_mail_message'] == "") ? "":stripslashes($result[$current_lang]['wcst_mail_message']);
				$result[$current_lang]['wcst_mail_message_additional_shippings'] = (!isset($result[$current_lang]['wcst_mail_message_additional_shippings']) || $result[$current_lang]['wcst_mail_message_additional_shippings'] == "") ? "":stripslashes($result[$current_lang]['wcst_mail_message_additional_shippings']);
				$result[$current_lang]['wcst_active_notification_text_before_tracking_info'] = (!isset($result[$current_lang]['wcst_active_notification_text_before_tracking_info']) || $result[$current_lang]['wcst_active_notification_text_before_tracking_info'] == "") ? "":stripslashes($result[$current_lang]['wcst_active_notification_text_before_tracking_info']);
					
				$result[$current_lang]['wcst_order_details_page_message'] =  (!isset($result[$current_lang]['wcst_order_details_page_message']) || $result[$current_lang]['wcst_order_details_page_message'] == "" )? "":stripslashes($result[$current_lang]['wcst_order_details_page_message']);
				$result[$current_lang]['wcst_order_details_page_additional_shippings'] = (!isset($result[$current_lang]['wcst_order_details_page_additional_shippings']) || $result[$current_lang]['wcst_order_details_page_additional_shippings'] == "") ? "":stripslashes($result[$current_lang]['wcst_order_details_page_additional_shippings']);
				
				return isset($result[$current_lang]) ? $result[$current_lang] : $default ;
			}
			else
			{
				  $result['wcst_mail_message'] = (!isset($result['wcst_mail_message']) || $result['wcst_mail_message'] == "") ? "":stripslashes($result['wcst_mail_message']);
				  $result['wcst_mail_message_additional_shippings'] = (!isset($result['wcst_mail_message_additional_shippings']) || $result['wcst_mail_message_additional_shippings'] == "") ? "":stripslashes($result['wcst_mail_message_additional_shippings']);
				  $result['wcst_active_notification_text_before_tracking_info'] = (!isset($result['wcst_active_notification_text_before_tracking_info']) || $result['wcst_active_notification_text_before_tracking_info'] == "") ? "":stripslashes($result['wcst_active_notification_text_before_tracking_info']);
					
				  $result['wcst_order_details_page_message'] =  (!isset($result['wcst_order_details_page_message']) || $result['wcst_order_details_page_message'] == "" )? "":stripslashes($result['wcst_order_details_page_message']);
				  $result['wcst_order_details_page_additional_shippings'] = (!isset($result['wcst_order_details_page_additional_shippings']) || $result['wcst_order_details_page_additional_shippings'] == "") ? "":stripslashes($result['wcst_order_details_page_additional_shippings']);
		
			}
		}
		return $result;
	}
	public function save_messages($value)
	{
		global $sitepress;
		$options = get_option('wcst_template_messages');
		if(class_exists('SitePress') && ICL_LANGUAGE_CODE != $sitepress->get_default_language())
		{
			if(isset($options) && is_array($options))
				$options[ICL_LANGUAGE_CODE] = $value;
			else
			{
				$options = array();
				$options[ICL_LANGUAGE_CODE] = $value;
			}
		}
		else			
		{
			if(isset($options) && is_array($options))
				$options = array_merge($options, $value);
			else
				$options = $value;
		}
		update_option( 'wcst_template_messages', $options);
	}
	public function save_general_options($options)
	{
		$wpml_helper = new WCST_Wpml();
		if(!isset($options['email_options']) )
			$options['email_options'] = array();
		if(!isset($options['email_options']['show_tracking_info_by_order_statuses']) )
			$options['email_options']['show_tracking_info_by_order_statuses'] = array();

		$order_statuses = wc_get_order_statuses();
		//wcst_var_dump($options['email_options']['show_tracking_info_by_order_statuses']);
		foreach($order_statuses as $order_status => $order_status_name)
		{
			$order_status = str_replace("wc-", "", $order_status);
			if(!isset($options['email_options']['show_tracking_info_by_order_statuses'][$order_status]) )
				$options['email_options']['show_tracking_info_by_order_statuses'][$order_status] = false;
			else
				$options['email_options']['show_tracking_info_by_order_statuses'][$order_status] = true;
		}
		
		//
		$old_estimated_shipping_label = $this->get_general_options('estimated_shipping_info_product_page_label', false);
		
		if($old_estimated_shipping_label != false)
		{
			$old_estimated_shipping_label[$wpml_helper->get_current_locale()] = isset($options['estimated_shipping_info_product_page_label'][$wpml_helper->get_current_locale()]) ? $options['estimated_shipping_info_product_page_label'][$wpml_helper->get_current_locale()] : "";
			$old_estimated_shipping_label[$wpml_helper->get_current_locale()] = stripslashes($old_estimated_shipping_label[$wpml_helper->get_current_locale()]);
			$options['estimated_shipping_info_product_page_label'] = $old_estimated_shipping_label;
		}
		
		//
		$old_tracking_shipment_button = $this->get_general_options('tracking_shipment_button', false);
		
		if($old_tracking_shipment_button != false)
		{
			$old_tracking_shipment_button[$wpml_helper->get_current_locale()] = isset($options['tracking_shipment_button'][$wpml_helper->get_current_locale()]) ? str_replace("%S", "%s",$options['tracking_shipment_button'][$wpml_helper->get_current_locale()]) : "";
			$old_tracking_shipment_button[$wpml_helper->get_current_locale()] = stripslashes($old_tracking_shipment_button[$wpml_helper->get_current_locale()]);
			$options['tracking_shipment_button'] = $old_tracking_shipment_button;
		}
		
		//
		$estimated_shipping_info_out_of_stock = $this->get_general_options('estimated_shipping_info_out_of_stock', false);
		
		if($estimated_shipping_info_out_of_stock != false)
		{
			$estimated_shipping_info_out_of_stock[$wpml_helper->get_current_locale()] = isset($options['estimated_shipping_info_out_of_stock'][$wpml_helper->get_current_locale()]) ? $options['estimated_shipping_info_out_of_stock'][$wpml_helper->get_current_locale()]: "";
			$estimated_shipping_info_out_of_stock[$wpml_helper->get_current_locale()] = stripslashes($estimated_shipping_info_out_of_stock[$wpml_helper->get_current_locale()]);
			$options['estimated_shipping_info_out_of_stock'] = $estimated_shipping_info_out_of_stock;
		}
		
		//
		$active_notification_email_subject = $this->get_general_options('active_notification_email_subject', false);
		
		if($active_notification_email_subject != false)
		{
			$active_notification_email_subject[$wpml_helper->get_current_locale()] = isset($options['active_notification_email_subject'][$wpml_helper->get_current_locale()]) ? $options['active_notification_email_subject'][$wpml_helper->get_current_locale()]: "";
			$active_notification_email_subject[$wpml_helper->get_current_locale()] = stripslashes($active_notification_email_subject[$wpml_helper->get_current_locale()]);
			$options['active_notification_email_subject'] = $active_notification_email_subject;
		}
		
		//
		$active_notification_email_heading = $this->get_general_options('active_notification_email_heading', false);
		
		if($active_notification_email_heading != false)
		{
			$active_notification_email_heading[$wpml_helper->get_current_locale()] = isset($options['active_notification_email_heading'][$wpml_helper->get_current_locale()]) ? $options['active_notification_email_heading'][$wpml_helper->get_current_locale()]: "";
			$active_notification_email_heading[$wpml_helper->get_current_locale()] = stripslashes($active_notification_email_heading[$wpml_helper->get_current_locale()]);
			$options['active_notification_email_heading'] = $active_notification_email_heading;
		}
		
		$this->option_cache = null;
		
		update_option('wcst_general_options', $options);
	}
	public function get_csv_separator()
	{
		$options = isset($this->option_cache) ? $this->option_cache : get_option('wcst_general_options');
		$this->option_cache = $options;
		return isset($options['csv_separator']) ? $options['csv_separator'] : ',';
	}
	public function get_email_show_tracking_info_by_order_status($status)
	{
		$options = get_option('wcst_general_options');
		if(!isset($options['email_options'])) 
			$options['email_options'] = array();
		if(!isset($options['email_options']['show_tracking_info_by_order_statuses']) )
			$options['email_options']['show_tracking_info_by_order_statuses'] = array();
		
		$order_statuses = wc_get_order_statuses();
		foreach($order_statuses as $order_status => $order_status_name)
		{
			$order_status = str_replace("wc-", "", $order_status);
			
			//Backward compatibility: force setting the default status for complete order status
			if($order_status == 'completed' && !isset($options['email_options']['show_tracking_info_by_order_statuses'][$order_status]))
				$options['email_options']['show_tracking_info_by_order_statuses'][$order_status] = true;
			//To be safe: force set false for non existing statuses 
			elseif(!isset($options['email_options']['show_tracking_info_by_order_statuses'][$order_status]) )
				$options['email_options']['show_tracking_info_by_order_statuses'][$order_status] = false;
			/* else
				$options['email_options']['show_tracking_info_by_order_statuses'][$order_status] = true; */
		}
		//custom statuses
		if(isset($options['email_options']['show_tracking_info_by_order_statuses']['custom_statuses']) && $options['email_options']['show_tracking_info_by_order_statuses']['custom_statuses'] != "")
		{
			try{
				$custom_statuses = @explode(",",$options['email_options']['show_tracking_info_by_order_statuses']['custom_statuses']);
				foreach((array)$custom_statuses as $custom_status)
				{
					$custom_status = str_replace("wc-", "", $custom_status);
					//temporaly create array element for custom_status
					$options['email_options']['show_tracking_info_by_order_statuses'][$custom_status] = true;
				}
			}catch(Exception $e){}
		}
		
		return isset($options['email_options']['show_tracking_info_by_order_statuses'][$status]) ? $options['email_options']['show_tracking_info_by_order_statuses'][$status] : false;
	}
	public function save_checkout_options($new_values)
	{
		global $sitepress;
		$options = get_option('wcst_checkout_options');
		if(!isset($options) || !is_array($options))
			$options = array();
		
		
		$temp_value = array();
		if(class_exists('SitePress') && ICL_LANGUAGE_CODE != $sitepress->get_default_language())
		{
			$temp_value = array();
			$temp_value[ICL_LANGUAGE_CODE] = array();
			$temp_value[ICL_LANGUAGE_CODE]['messages'] = $new_values['messages'];
			$temp_value['options'] = $new_values['options'];
		}
		else $temp_value = $new_values;
		
		$options = array_merge($options, $temp_value);
		update_option( 'wcst_checkout_options', $options);
	}
	public function get_checkout_options($default = null , $lang_code = null)
	{
		//WPML
		global $sitepress;
		$options = get_option( 'wcst_checkout_options');
		$result = isset($options) ? $options: $default;
		if(isset($result))
		{
			if(class_exists('SitePress') && ( (isset($lang_code) && $lang_code != $sitepress->get_default_language()) || ICL_LANGUAGE_CODE != $sitepress->get_default_language()))
			{
				$current_lang = isset($lang_code) ? $lang_code : ICL_LANGUAGE_CODE;
				//$current_lang = ICL_LANGUAGE_CODE;
				$result['messages'] = !empty($result[$current_lang]['messages']) ? $result[$current_lang]['messages'] : array();
			}
			$result['messages']['date_range'] = (!isset($result['messages']['date_range']) || $result['messages']['date_range'] == "") ? __('Select a date range', 'woocommerce-shipping-tracking'):stripslashes($result['messages']['date_range']);
			$result['messages']['time_range'] = (!isset($result['messages']['time_range']) || $result['messages']['time_range'] == "") ? __('Select a time range', 'woocommerce-shipping-tracking'):stripslashes($result['messages']['time_range']);
				
			$result['messages']['time_secondary_range'] =  (!isset($result['messages']['time_secondary_range']) || $result['messages']['time_secondary_range'] == "" )? __('Select a secondary time range', 'woocommerce-shipping-tracking'):stripslashes($result['messages']['time_secondary_range']);
			$result['messages']['title'] = (!isset($result['messages']['title']) || $result['messages']['title'] == "") ? __('Select a preferred delivery date and time', 'woocommerce-shipping-tracking'):stripslashes($result['messages']['title']);
			$result['messages']['note'] = (!isset($result['messages']['note']) || $result['messages']['note'] == "") ? "":stripslashes($result['messages']['note']);	
		}
		return $result;
	}
	public function get_option($option_name = 'wcst_options', $option_key = null, $default_value = null)
	{
		$result =  get_option( $option_name );
		if($option_key && isset($result))
			return isset($result[$option_key]) ? $result[$option_key] : $default_value;
		
		return isset($result) ? $result : $default_value;
	}
	public function get_general_options($option_name = null, $default_value = null)
	{
		$options = isset($this->option_cache) ? $this->option_cache : get_option('wcst_general_options');
		$this->option_cache = $options;
		
		if(isset($option_name))
		{
			$options = isset($options[$option_name]) ? $options[$option_name] : $default_value; 
		}
		
		return $options;
	}
	public function get_sql_date_format_according_to_date_option()
	{
		$options = isset($this->option_cache) ? $this->option_cache : get_option('wcst_general_options');
		$this->option_cache = $options;
		
		$date_format = 'd/m/Y';
		if($options['date_format'])
			switch($options['date_format'])
			{
				case 'mm/dd/yyyy': $date_format = 'm/d/Y'; break;
				case 'yyyy/mm/dd': $date_format = 'Y/m/d'; break;
				case 'dd.mm.yyyy': $date_format = 'd.m.Y'; break;
				case 'mm.dd.yyyy': $date_format = 'm.d.Y'; break;
				case 'yyyy.mm.dd': $date_format = 'Y.m.d'; break;
				case 'dd-mm-yyyy': $date_format = 'd-m-Y'; break;
				case 'mm-dd-yyyy': $date_format = 'm-d-Y'; break;
				case 'yyyy-mm-dd': $date_format = 'Y-m-d'; break;
				case 'mmmm dd, yyyy': $date_format = 'F j, Y'; break;
			}
			
		return $date_format;
	}
	public function save_delivery_estimations($data)
	{
		$data_to_save = array();
		$data_to_save['method_estimate_from'] = isset($data['method_estimate_from']) ? $data['method_estimate_from'] : array();
		$data_to_save['method_estimate_to'] = isset($data['method_estimate_to']) ? $data['method_estimate_to'] : array();
		
		update_option( 'wcst_delivery_estimations', $data_to_save);
	}
	public function get_delivery_estimations()
	{
		return get_option( 'wcst_delivery_estimations');
	}
	public function cl_acf_set_language() 
	{
	  return acf_get_setting('default_language');
	}
	public function get_estimations_options($option_name = null, $default_value = null)
	{
		
		add_filter('acf/settings/current_language',  array(&$this, 'cl_acf_set_language'), 100);
		
		$all_data = isset($this->estimations_options_cache) ? $this->estimations_options_cache : array();
		
		if(empty($all_data))
		{
			$wordpres_offset = get_option('gmt_offset') * 60;
			$server_offset = date('Z')/60;
			$all_data['hour_offset'] = $server_offset == 0 || $server_offset == $wordpres_offset ? $wordpres_offset : $wordpres_offset - $server_offset; 
			$all_data['estimated_shipping'] = array(); 
		
			if( have_rows('wcst_shippings', 'option') )
				while ( have_rows('wcst_shippings', 'option') ) 
				{
					the_row();
					$estimated_shipping = array();
					$estimated_shipping['non_working_days'] = array();
					$estimated_shipping['name_id'] = get_sub_field('wcst_name_id', 'option'); //Check if value exists: if( $value )
					$estimated_shipping['products'] = get_sub_field('wcst_products', 'option'); 
					$estimated_shipping['tags'] = get_sub_field('wcst_tags', 'option'); 
					$estimated_shipping['categories'] = get_sub_field('wcst_categories', 'option'); 
					$estimated_shipping['children_categories'] = get_sub_field('wcst_children_categories', 'option'); 
					$estimated_shipping['working_days'] = get_sub_field('wcst_working_days', 'option'); 
					$estimated_shipping['day_cut_off_hour'] = get_sub_field('wcst_day_cut_off_hour', 'option'); 
					$estimated_shipping['days_delay'] = get_sub_field('wcst_days_delay', 'option') ? get_sub_field('wcst_days_delay', 'option') : 0; 
					$estimated_shipping['elastic_date'] = get_sub_field('wcst_elastic_date', 'option');
					$estimated_shipping['elastic_date_day_offset'] = get_sub_field('wcst_elastic_date_day_offset', 'option');
				
					//backwardcompatibility
					$estimated_shipping['tags'] = $estimated_shipping['tags'] ? $estimated_shipping['tags'] : array();
					$estimated_shipping['products'] = $estimated_shipping['products'] ? $estimated_shipping['products'] : array();
					$estimated_shipping['categories'] = $estimated_shipping['categories'] ? $estimated_shipping['categories'] : array();
					$estimated_shipping['elastic_date'] = $estimated_shipping['elastic_date'] ? $estimated_shipping['elastic_date'] : false;
					$estimated_shipping['elastic_date_day_offset'] = $estimated_shipping['elastic_date_day_offset'] ? $estimated_shipping['elastic_date_day_offset'] : 1;
					
					if( have_rows('wcst_non_working_days', 'option') )
						while ( have_rows('wcst_non_working_days', 'option') ) 
						{
							the_row();
							$non_working_day = array();
							$non_working_day['day'] = get_sub_field('wcst_day', 'option'); 
							$non_working_day['month'] = get_sub_field('wcst_month', 'option'); 
							
							$estimated_shipping['non_working_days'][] = $non_working_day;
						}
						
					$all_data['estimated_shipping'][] = $estimated_shipping;
				}
				
			remove_filter('acf/settings/current_language', array(&$this,'cl_acf_set_language'), 100);
			
			$this->estimations_options_cache = $all_data;
		}
		
		if(isset($option_name))
		{
			return isset($all_data[$option_name]) ? $all_data[$option_name] : $default_value;
		}
		
		/* Format
		  ["non_working_days"]=>
			  array(2) {
				[0]=>
				array(2) {
				  ["day"]=>
				  string(1) "1"
				  ["month"]=>
				  string(1) "3"
				}
				[1]=>
				array(2) {
				  ["day"]=>
				  string(1) "1"
				  ["month"]=>
				  string(2) "11"
				}
			  }
			  ["name_id"]=>
			  string(4) "Test"
			  ["products"]=>
			  array(1) {
				[0]=>
				int(12)
			  }
			  ["categories"]=>
			  array(1) {
				[0]=>
				int(11)
			  },
			  ["children_categories"]=>
				  string(13) "selected_only"
			  ["working_days"]=>
			  array(2) {
				[0]=>
				string(1) "2"
				[1]=>
				string(1) "5"
			  }
			  ["day_cut_off_hour"]=>
			  string(1) "0"
			  ["days_delay"]=>
			  string(1) "0"
			}
			*/
		
		return $all_data;
	}
}
?>