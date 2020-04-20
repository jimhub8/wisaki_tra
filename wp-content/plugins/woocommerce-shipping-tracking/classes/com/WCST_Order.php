<?php 
class WCST_Order
{
	var $tracking_key_array = array('_wcst_order_trackno','_wcst_order_dispatch_date', '_wcst_track_without_tracking_code',
									'_wcst_custom_text','_wcst_order_trackname','_wcst_order_trackurl',
									'_wcst_order_track_http_url','_wcst_order_disable_email');
	var $tracking_additional_company_key = '_wcst_additional_companies';
	
	public function __construct()
	{
		add_action('wp_ajax_wcst_get_order_list', array(&$this, 'ajax_get_order_partial_list'));
		add_action('wp_ajax_wcst_upload_tracking_csv', array(&$this, 'process_csv_upload_ajax'));
	}
	public static function get_shipping_postcode($order)
	{
		if(version_compare( WC_VERSION, '2.7', '<' ))
			return $order->shipping_postcode;
		
		return $order->get_shipping_postcode();
	}
	public static function get_billing_postcode($order)
	{
		if(version_compare( WC_VERSION, '2.7', '<' ))
			return $order->billing_postcode;
		
		return $order->get_billing_postcode();
	}
	public static function get_id($order)
	{
		if(version_compare( WC_VERSION, '2.7', '<' ))
			return $order->id;
		
		return $order->get_id();
	}
	public static function get_manage_stock($order)
	{
		if(version_compare( WC_VERSION, '2.7', '<' ))
			return $order->manage_stock;
		
		return $order->get_manage_stock();
	}
	public static function get_billing_email($order)
	{
		if(version_compare( WC_VERSION, '2.7', '<' ))
			return $order->billing_email;
		
		return $order->get_billing_email();
	}
	function process_csv_upload_ajax()
	{
		
		$csv_array = explode("<#>", $_POST['csv']);
		$merge_data = isset($_POST['merge_data']) && $_POST['merge_data'] == 'yes' ? true : false;
		$result = $this->process_csv_data_and_update_orders($csv_array, $merge_data);
	
		foreach((array)$result as $message)
				echo $message;
		wp_die();
	}
	public function load_csv_data_from_url_and_import($csv_url)
	{
		$data = file_get_contents($csv_url);
		if( $data == false)
		{
			$error_message = sprintf(__('Automatic bulk importer task failed because could not load the following file: %s', 'woocommerce-shipping-tracking'), $csv_url);
			$wcst_email_model->send_error_email_to_admin($error_message);
			return;
		}
		$rows = explode("\n",$data);
		$this->process_csv_data_and_update_orders($rows);
	}
	private function process_csv_data_and_update_orders($csv_array = null, $merge_data = false)
	{
		global $wcst_time_model;
		$options_controller = new WCST_Option();
		$customerAdded = 0;
		$messages = array(); 
		$order_statuses = wc_get_order_statuses();
		$allowed_email_notification_statuses = array("send_email_new_order,send_email_cancelled_order",
													 "send_email_customer_processing_order",
													 "send_email_customer_completed_order",
													 "send_email_customer_refunded_order",
													 "send_email_customer_invoice",
													 "send_active_notification" );
		$columns_names = array("order_id",
								"order_status",
								"force_email_notification",
								"dispatch_date",
								"custom_text",
								"tracking_info");
		$colum_index_to_name = array();
		
		$row = 1;
		$updated = 0;
		if($csv_array != null)
		{
			//while (($data = fgetcsv($handle)) !== FALSE) 
			foreach($csv_array as $csv_row)
			{
				$csv_row = trim($csv_row);
				if(empty($csv_row) || $csv_row == "")
					continue;
				$data = str_getcsv($csv_row, $options_controller->get_csv_separator());
				$num = count($data);
				$order = array();
				
				for ($c=0; $c < $num; $c++) 
				{						
					if($row == 1)
					{
						foreach( $columns_names as $title)
							if($title == $data[$c])
									$colum_index_to_name[$c] = $title;
					}
					else
					{
						if(isset($colum_index_to_name[$c]))
						{
							$order[$colum_index_to_name[$c]] = $data[$c];
						}
					}
					
				}
				if(empty($colum_index_to_name))
				{
					array_push( $messages, '<span class="error_message">'.__("The file hasn't a valid header row, import process stopped. Please check the csv file structure.", 'woocommerce-shipping-tracking').'</span><br/>' );
					return $messages;
				}
				
				if($order != null)
				{
					if(count($colum_index_to_name) != count($order))
					{
						array_push( $messages, '<span class="error_message">'.sprintf(__("Row number %s has less columns than expected so it has not been imported. Please check.", 'woocommerce-shipping-tracking'), $row).'</span><br/>' );
						continue;
					}
					
					//Order id
					if(isset($order['order_id']))
					{
						$order['order_id'] = apply_filters('wcst_bulk_import_order_id_processing', $order['order_id']);
					}
					$is_order_id_valid =   !isset($order['order_id'] ) || $order['order_id'] == "" || empty($order['order_id'] ) || !is_numeric($order['order_id']) ? false : true; 
					if($is_order_id_valid)
					{
						//$order_object = new WC_Order($order['order_id']);
						//$is_order_id_valid =  !isset($order_object->post) ? false : $is_order_id_valid;
						$order_object = wc_get_order($order['order_id']);
						$is_order_id_valid = is_bool($order_object) ? false : $is_order_id_valid;
					}
					if(!$is_order_id_valid)
						array_push( $messages, '<span class="error_message">'.sprintf(__("Order %s (row number %s): the id is not valid.", 'woocommerce-shipping-tracking'), $order[ 'order_id' ], $row).'</span><br/>' );
					
					//Order status
					$is_status_valid = !isset($order['order_status'] ) || $order['order_status'] == "" || empty($order['order_status'] ) ? false : true; 
					if($is_status_valid)
						$order['order_status'] = trim(strtolower($order['order_status']));
					
					foreach($order_statuses as $code => $status_name)
						if($order['order_status'] == $code)
							$is_status_valid = true;
					
					if(!$is_status_valid && $order['order_status']!="")	
					{
						//array_push( $messages, new WP_Error('order', sprintf(__("Order %s selected status was not valid.", 'woocommerce-shipping-tracking'), $order[ 'order_id' ])));
						array_push( $messages, '<br/><span class="error_message">'.sprintf(__("Order %s (row number %s): selected status was not valid. Its status has been left unchanged.", 'woocommerce-shipping-tracking'), $order[ 'order_id' ], $row).'</span><br/>' );
						//wcst_var_dump($messages);
					}
					
					//Forced email notification
					$is_notification_email_status_valid = !isset($order['force_email_notification'] ) || $order['force_email_notification'] == "" || empty($order['force_email_notification'] ) ? true : false; 
					$is_notification_email_status_valid = in_array( $order['force_email_notification'], $allowed_email_notification_statuses) ? true : $is_notification_email_status_valid;
					
					if(!$is_notification_email_status_valid)	
						array_push( $messages, '<span class="error_message">'.sprintf(__("Order %s (row number %s): notification email status was not valid. No notification has been sent.", 'woocommerce-shipping-tracking'), $order[ 'order_id' ], $row).'</span><br/>' );
				
					//Track info
					$tracking_info_strings = explode("|", $order['tracking_info']);
					$company_id_and_tracking_code = array();
					foreach((array)$tracking_info_strings as $tracking_info_string)
					{
						$temp = explode(":", $tracking_info_string);
						array_push($company_id_and_tracking_code, array('company_id' => strtoupper($temp[0]), 'tracking_code' => $temp[1]));
					}
				
					//Custom text
					$custom_texts = explode("|",$order['custom_text']);
					//Dispatch date
					$dispatch_dates = explode("|",$order['dispatch_date']);
					
					if(is_array($dispatch_dates) && $dispatch_dates[0] != "")
						foreach($dispatch_dates as $dispatch_date)
							if(!$wcst_time_model->is_valid_date_format($dispatch_date))
									array_push( $messages, '<span class="error_message">'.sprintf(__("Order %s (row number %s): Check dispatch date(s), an invalid format has been used. Please use the yyyy-mm-dd format.", 'woocommerce-shipping-tracking'), $order[ 'order_id' ], $row).'</span><br/>' );
					
					if(empty($company_id_and_tracking_code))
						array_push( $messages, '<span class="error_message">'.sprintf(__("Order %s (row number %s): tacking info is not valid.", 'woocommerce-shipping-tracking'), $order[ 'order_id' ], $row).'</span><br/>' );
					else
					{
						//Save info 
						global $wcst_email_model;
						$send_active_notification = $is_notification_email_status_valid && $order['force_email_notification'] == "send_active_notification";
						$meta_data_array = array();
						$meta_data_array['_wcst_order_trackurl'] = $company_id_and_tracking_code[0]['company_id'];
						$meta_data_array['_wcst_order_trackno'] = $company_id_and_tracking_code[0]['tracking_code'];
						$meta_data_array['_wcst_order_dispatch_date'] = $dispatch_dates[0];
						$meta_data_array['_wcst_custom_text'] = $custom_texts[0];
						
						if($send_active_notification)
						{
							$meta_data_array['wcst_send_shipping_notification_email'] = array();
							$meta_data_array['wcst_send_shipping_notification_email']['default'] = true;
						}
						
						if(count($company_id_and_tracking_code) > 1 )
						{
							$meta_data_array['_wcst_order_additional_shipping'] = array();
							for($i = 1; $i < count($company_id_and_tracking_code); $i++)
							{
								$additiona_company = array();
							
								$additiona_company['trackurl'] = isset($company_id_and_tracking_code[$i]['company_id']) ? $company_id_and_tracking_code[$i]['company_id'] : "";
								$additiona_company['trackno'] = isset($company_id_and_tracking_code[$i]['tracking_code']) ? $company_id_and_tracking_code[$i]['tracking_code'] : "";
								$additiona_company['order_dispatch_date'] = isset($dispatch_dates[$i]) ? $dispatch_dates[$i] : "";
								$additiona_company['custom_text'] = isset($custom_texts[$i]) ? $custom_texts[$i] : "";
								array_push($meta_data_array['_wcst_order_additional_shipping'], $additiona_company);
								if($send_active_notification)
									$meta_data_array['wcst_send_shipping_notification_email'][] = true;
							}
							
						}
						
						//wcst_var_dump($meta_data_array);
						if($is_order_id_valid)
						{
							$this->save_shippings_info_metas($order['order_id'], $meta_data_array, $merge_data);
							
			
							if($is_status_valid && $order['order_status'] != "" )
							{
								foreach($order_statuses as $code => $status_name)
									if($order['order_status'] == $code)
										$order_object->update_status($order['order_status']);
							}
							if($is_notification_email_status_valid && $order['force_email_notification'] != "" && $order['force_email_notification'] != "send_active_notification")
							{
								//wcst_var_dump($order['force_email_notification']);
								$wcst_email_model->force_status_email_sending($order['force_email_notification'], $order_object);
							} 
						}
					}
				}
				$row++;
			}
			//array_push( $messages, sprintf(__('Updated %d orders!', 'woocommerce-shipping-tracking'),  $updated  ));
			
		}
		//wcst_var_dump($messages);
		return $messages;
	}
	public function get_delivery_and_times($order_id)
	{
		$result =  get_post_meta($order_id, '_wcst_order_delivery_datetimes' , true);
		return isset($result) && $result != "" ? $result : array();
	}
	public function save_delivery_date_and_time($order_id, $date_and_time)
	{
		return update_post_meta($order_id, '_wcst_order_delivery_datetimes',$date_and_time);
	}
	public function get_order_statuses($get_codes = false)
	{
		$result = array('version'=>0, 'statuses'=>array());
		if(function_exists( 'wc_get_order_statuses' ))
		{
			
			$result['version'] = 2.2;
			//[slug] => name
			if(!$get_codes)
				$result['statuses'] = wc_get_order_statuses();
			else foreach(wc_get_order_statuses() as $code => $name)
				$result['statuses'][] = $code;
		}
		else
		{
			$args = array(
				'hide_empty'   => false, 
				'fields'            => 'id=>name', 
			);
			$result['version'] = 2.1;
			//[id] => name
			$result['statuses'] =  get_terms('shop_order_status', $args);
		}
		return $result;
	}
	public function ajax_get_order_partial_list()
	{
		$resultCount = 50;
		$search_string = isset($_GET['search_string']) ? $_GET['search_string'] : null;
		$page = isset($_GET['page']) ? $_GET['page'] : null;
		$offset = isset($page) ? ($page - 1) * $resultCount : null;
		$orders = $this->get_order_list($search_string ,$offset, $resultCount);
		 echo json_encode( $orders);
		 wp_die();
	}
	public function get_orders_by_user_id($user_id)
	{
		$args =  array(
				'numberposts' => -1,
				'meta_key'    => '_customer_user',
				'meta_value'  => $user_id,
				//'post_type'   => 'shop_order',
				'post_type'   => wc_get_order_types(),
				//'post_status' => $this->get_order_statuses()['statuses']
				'post_status' => array_keys( wc_get_order_statuses() )
			 );
			 
		return get_posts($args);	
	}
	private function get_order_list($search_string = null, $offset = null, $resultCount  = null)
	{
		global $wpdb;
		$statuses = $this->get_order_statuses(true);
		$statuses_names = $this->get_order_statuses();
		$limit_query = isset($offset) && isset($resultCount) ? " LIMIT {$resultCount} OFFSET {$offset}": "";
		$additional_select = $additional_join = $additional_where = "";
		if($search_string)
		{
			$offset = null;
			$limit_query = "";
		}
		$additional_join = " LEFT JOIN {$wpdb->postmeta} AS billing_name_meta  ON billing_name_meta.post_id = orders.ID 
							 LEFT JOIN {$wpdb->postmeta} AS billing_last_name_meta  ON billing_last_name_meta.post_id = orders.ID
							 LEFT JOIN {$wpdb->postmeta} AS billing_email_meta  ON billing_email_meta.post_id = orders.ID
							 LEFT JOIN {$wpdb->postmeta} AS shipping_name_meta  ON shipping_name_meta.post_id = orders.ID
							 LEFT JOIN {$wpdb->postmeta} AS shipping_last_name_meta  ON shipping_last_name_meta.post_id = orders.ID
							 LEFT JOIN {$wpdb->postmeta} AS customer_id_meta  ON customer_id_meta.post_id = orders.ID
							 LEFT JOIN {$wpdb->postmeta} AS order_number_formatted ON order_number_formatted.post_id = orders.ID  AND (order_number_formatted.meta_key = '_order_number_formatted')
							 LEFT JOIN {$wpdb->postmeta} AS order_number ON order_number.post_id = orders.ID AND (order_number.meta_key = '_order_number')
							";
		$additional_where = " AND billing_name_meta.meta_key = '_billing_first_name' 
							  AND billing_last_name_meta.meta_key = '_billing_last_name' 
							  AND billing_email_meta.meta_key = '_billing_email' 
							  AND shipping_name_meta.meta_key = '_shipping_first_name' 
							  AND shipping_last_name_meta.meta_key = '_shipping_last_name' 
							  AND customer_id_meta.meta_key = '_customer_user' 
		";
		
		 $query_string = "SELECT orders.ID as order_id, orders.post_date as order_date, orders.post_status as order_status, order_number_formatted.meta_value as order_number_formatted, order_number.meta_value as order_number
							 FROM {$wpdb->posts} AS orders {$additional_join}
							 WHERE orders.post_status IN ('".implode("','", $statuses['statuses'])."') 
							 AND orders.post_type = 'shop_order' {$additional_where} ";
		if($search_string)
				$query_string .=  " AND ( orders.ID LIKE '%{$search_string}%' OR  
										  orders.post_date LIKE '%{$search_string}%' OR 
										  orders.post_status LIKE '%{$search_string}%' OR
										  billing_name_meta.meta_value LIKE '%{$search_string}%' OR 
										  billing_last_name_meta.meta_value LIKE '%{$search_string}%' OR 
										  billing_email_meta.meta_value LIKE '%{$search_string}%' OR 
										  shipping_name_meta.meta_value LIKE '%{$search_string}%' OR 
										  shipping_last_name_meta.meta_value LIKE '%{$search_string}%' OR 
										  customer_id_meta.meta_value LIKE '%{$search_string}%' OR
										  order_number_formatted.meta_value LIKE '%{$search_string}%' OR
										  order_number.meta_value LIKE '%{$search_string}%' 
										  )";
		
		$query_string .=  " GROUP BY orders.ID ORDER BY orders.post_date DESC ".$limit_query ;
		 $wpdb->query('SET SQL_BIG_SELECTS=1');
		$results = $wpdb->get_results($query_string );
		//wcst_var_dump($query_string);
		//wcst_var_dump($results);
		$bad_char = array('"', "'");
		foreach((array)$results as $key => $result)
		{
			$order = wc_get_order($result->order_id); //new WC_Order($result->order_id);
			$user = $order->get_customer_id() > 0 ? get_userdata($order->get_customer_id()) : null;
			$results[$key]->billing_name_and_last_name = str_replace($bad_char, "", $order->get_billing_first_name()." ".$order->get_billing_last_name());
			$results[$key]->shipping_name_and_last_name = str_replace($bad_char, "",$order->get_shipping_first_name()." ".$order->get_shipping_last_name());
			//$results[$key]->shipping_address = method_exists($order, 'get_formatted_shipping_address') ? str_replace($bad_char, "",$order->get_formatted_shipping_address()) : "";
			$results[$key]->user_login = isset($user) ? $user->user_login: "Guest";
			$results[$key]->user_id = $order->get_customer_id() ;
			$results[$key]->user_email = /* isset($order->get_billing_email()) ? */ $order->get_billing_email() /* : "N/A" */;
			$results[$key]->order_status = $statuses_names['statuses'][$result->order_status];
		}
		//wcst_var_dump($results);
		
		if(isset($offset) && isset($resultCount))
		{
			$query_string = "SELECT COUNT(*) as tot
							 FROM {$wpdb->posts} AS orders
							 WHERE orders.post_type = 'shop_order' ";
			$num_order = $wpdb->get_col($query_string);
			$num_order = isset($num_order[0]) ? $num_order[0] : 0;
			$endCount = $offset + $resultCount;
			$morePages = $num_order > $endCount;
			$results = array(
				  "results" => $results,
				  "pagination" => array(
					  "more" => $morePages
				  )
			  );
		}
		else
			$results = array(
				  "results" => $results,
				  "pagination" => array(
					  "more" => false
				  )
			  );
		
		return $results;
	}
	public function is_email_tracking_info_embedding_disabled($order_id)
	{
		$result = get_post_meta( $order_id, '_wcst_order_disable_email', true);
		//wcst_var_dump($result);
		return isset($result) && $result == 'disable_email_embedding' ? true : false;
	}
	public function get_order_single_meta($order_id, $meta_name, $single = true)
	{
		$order = wc_get_order($order_id); //new WC_Order($order_id); 
		return  version_compare( WC_VERSION, '2.7', '<' ) ? get_post_meta($order_id, $meta_name,  $single) : $order->get_meta($meta_name,  $single);
	}
	public function get_order_meta($order_id)
	{
		if(version_compare( WC_VERSION, '2.7', '<' ))
			return  get_post_custom( $order_id );
		
		$meta_to_return = array();
		$order = wc_get_order($order_id);// new WC_Order($order_id); 
		if(!isset($order) || $order == false)
			return $meta_to_return;
		
		$meta = $order->get_meta_data();
		
		foreach((array)$meta as $current_meta)
			foreach((array)$current_meta->value as $value)
				if(in_array($current_meta->key, $this->tracking_key_array) || $current_meta->key == $this->tracking_additional_company_key || $current_meta->key == 'wpml_language')
				{
					/* if(!isset($meta_to_return[$current_meta->key]))
						$meta_to_return[$current_meta->key] = array(); */
					$meta_to_return[$current_meta->key][] = $value;
				}
		return $meta_to_return;
	}
	public function save_shippings_info_metas($post_id, $data_to_save, $merge = false)
	{
		global $wcst_shipping_company_model, $wcst_email_model, $wcst_tracking_info_displayer;
		
		$wpml_helper = new WCST_Wpml();
		$options_controller = new WCST_Option();
		$options = $options_controller->get_general_options();
			
		$order = wc_get_order($post_id);// new WC_Order($post_id);
		$addtional_companies_counter = 0;
		$additional_companies = array();
		$shipping_postcode = WCST_Order::get_shipping_postcode($order);
		$billing_postcode = WCST_Order::get_billing_postcode($order);
		$post_code = isset($shipping_postcode) && $shipping_postcode != "" ? $shipping_postcode : $billing_postcode;
		$data_to_save['_wcst_order_trackno'] = isset($data_to_save['_wcst_order_trackno']) ? $data_to_save['_wcst_order_trackno'] : "";
		 
		$info = WCST_shipping_companies_url::get_company_url(stripslashes( $data_to_save['_wcst_order_trackurl'] ), stripslashes( $data_to_save['_wcst_order_trackno'] ), $post_code );
		//add_post_meta( $post_id, '_order_key', uniqid('order_') );
		
		$overwrite_data = true;
		if($merge)
		{
			$primary_shipping_company = version_compare( WC_VERSION, '2.7', '<' ) ? get_post_meta($post_id, '_wcst_order_trackno', true) : $order->get_meta_data('_wcst_order_trackno', $post_id);
			$is_primary_shipping_company_valid = false;
			
			foreach($primary_shipping_company as $primary_shipping)
				{
					if( $primary_shipping->key == '_wcst_order_trackurl' && $primary_shipping->value != 'NOTRACK')
						$is_primary_shipping_company_valid = true;
				}
			
			//if already exists a valid primary shipping company, the added one becomes an additional company
			if($primary_shipping_company && $is_primary_shipping_company_valid)
			{
				$overwrite_data = false;
				$temp_array = array();
				foreach($this->tracking_key_array as $tracking_key)
					if($tracking_key == '_wcst_order_trackname')
						$temp_array[$tracking_key] = $wcst_shipping_company_model->get_company_name_by_id($data_to_save['_wcst_order_trackurl']);
					else if($tracking_key == '_wcst_order_track_http_url')
						$temp_array[$tracking_key] = $info['urltrack'] ;
					else if(isset($data_to_save[$tracking_key]))
						$temp_array[$tracking_key] = $data_to_save[$tracking_key];
				$additional_companies[] = $temp_array;
			}
			
		}
		
		if($overwrite_data)
		{
			$data_to_save['_wcst_order_disable_email'] = !isset($data_to_save['_wcst_order_disable_email']) ? "no" : $data_to_save['_wcst_order_disable_email'];
			if(version_compare( WC_VERSION, '2.7', '<' ))
			{
				update_post_meta( $post_id, '_wcst_order_trackno', stripslashes( $data_to_save['_wcst_order_trackno'] ));
				update_post_meta( $post_id, '_wcst_track_without_tracking_code', isset($data_to_save['_wcst_track_without_tracking_code']) ? true : false);
				update_post_meta( $post_id, '_wcst_order_dispatch_date', stripslashes( $data_to_save['_wcst_order_dispatch_date'] ));
				update_post_meta( $post_id, '_wcst_custom_text', stripslashes( $data_to_save['_wcst_custom_text'] ));
				update_post_meta( $post_id, '_wcst_order_trackname', stripslashes( $wcst_shipping_company_model->get_company_name_by_id($data_to_save['_wcst_order_trackurl']) ));
				update_post_meta( $post_id, '_wcst_order_trackurl', stripslashes( $data_to_save['_wcst_order_trackurl'] ));
				update_post_meta( $post_id, '_wcst_order_track_http_url', stripslashes( $info['urltrack'] ));
				update_post_meta( $post_id, '_wcst_order_disable_email', $data_to_save['_wcst_order_disable_email']);
			}
			else
			{
				//wcst_var_dump($data_to_save['_wcst_order_trackno']);
				$order->update_meta_data('_wcst_order_trackno', stripslashes( $data_to_save['_wcst_order_trackno'] ));
				$order->update_meta_data('_wcst_track_without_tracking_code', isset($data_to_save['_wcst_track_without_tracking_code']) ? true : false);
				$order->update_meta_data('_wcst_order_dispatch_date', stripslashes( $data_to_save['_wcst_order_dispatch_date'] ));
				$order->update_meta_data('_wcst_custom_text', trim(stripslashes( $data_to_save['_wcst_custom_text'] )));
				$order->update_meta_data('_wcst_order_trackname', stripslashes( $wcst_shipping_company_model->get_company_name_by_id($data_to_save['_wcst_order_trackurl']) ));
				$order->update_meta_data('_wcst_order_trackurl', stripslashes( $data_to_save['_wcst_order_trackurl'] ));
				$order->update_meta_data('_wcst_order_track_http_url', stripslashes( $info['urltrack'] ));
				$order->update_meta_data('_wcst_order_disable_email', $data_to_save['_wcst_order_disable_email']);
				$order->save( );
			}
		}
		
			
		//******************* additional companies ************************************
		$skip_additiona_company_further_operation = false;
		if(!$merge && !isset($data_to_save['_wcst_order_additional_shipping']))
		{
			if(version_compare( WC_VERSION, '2.7', '<' ))
				delete_post_meta( $post_id, '_wcst_additional_companies' );
			else 
			{
				//$order->delete_meta_data( array('_wcst_additional_companies')); 
				$order->update_meta_data(  '_wcst_additional_companies', null);
				$order->save( );
			}
			$skip_additiona_company_further_operation = true;
		}
		
		if(!$skip_additiona_company_further_operation)
		{
			if(isset($data_to_save['_wcst_order_additional_shipping']))
				foreach($data_to_save['_wcst_order_additional_shipping'] as $additional_company)
				{
					$additional_company['trackno'] = isset( $additional_company['trackno']) ?  $additional_company['trackno'] : "";
					$temp = array();
					$info = WCST_shipping_companies_url::get_company_url(stripslashes( $additional_company['trackurl'] ), stripslashes( $additional_company['trackno'] ), $post_code );
					$temp['_wcst_order_trackno'] = $additional_company['trackno'] ;
					$temp['_wcst_track_without_tracking_code'] = isset($additional_company['track_without_tracking_code']) ? true : false ;
					$temp['_wcst_custom_text'] = trim($additional_company['custom_text']) ;
					$temp['_wcst_order_dispatch_date'] = $additional_company['order_dispatch_date'] ;
					$temp['_wcst_order_trackname'] = stripslashes( $wcst_shipping_company_model->get_company_name_by_id($additional_company['trackurl']) );
					$temp['_wcst_order_trackurl'] = stripslashes( $additional_company['trackurl']);
					$temp['_wcst_order_track_http_url'] = stripslashes( $info['urltrack']);
					//wcst_var_dump($temp);
					array_push($additional_companies, $temp);
				}
			//wp_die();
			if($merge)
			{
				$old_additional = $this->get_order_single_meta($post_id, '_wcst_additional_companies');
				if(isset($data_to_save['wcst_send_shipping_notification_email'])) //reset due to merge
				{
					$data_to_save['wcst_send_shipping_notification_email'] = array();
					$tmp_counter = $old_additional ? count($old_additional) : 0;
					foreach($additional_companies as $tmp_additional_company)
						$data_to_save['wcst_send_shipping_notification_email'][$tmp_counter++] = true;
					
				}
				
				$additional_companies  = $old_additional ? array_merge($old_additional , $additional_companies) : $additional_companies;
			}
			
			
			if(version_compare( WC_VERSION, '2.7', '<' ))
				update_post_meta( $post_id, '_wcst_additional_companies', $additional_companies );
			else 
			{
				$order->update_meta_data('_wcst_additional_companies', $additional_companies); 
				$order->save( );
			}
			
		}
		//Active email notification managment 
		if(isset($data_to_save['wcst_send_shipping_notification_email']))
		{
			$order_meta = $this->get_order_meta(WCST_Order::get_id($order));
			ob_start();
			$wcst_tracking_info_displayer->active_notification($order_meta, $order, $data_to_save['wcst_send_shipping_notification_email']);
			$message =  ob_get_contents();
			ob_end_clean(); 
			
			$active_notification_email_subject = isset($options['active_notification_email_subject']) && isset($options['active_notification_email_subject'][$wpml_helper->get_current_locale()]) ? $options['active_notification_email_subject'][$wpml_helper->get_current_locale()] : __('Your products have been shipped', 'woocommerce-shipping-tracking');
			$active_notification_email_heading = isset($options['active_notification_email_heading']) && isset($options['active_notification_email_heading'][$wpml_helper->get_current_locale()]) ? $options['active_notification_email_heading'][$wpml_helper->get_current_locale()] : get_bloginfo('name');
			
			//shortcodes
			$active_notification_email_subject = str_replace("[order_id]", WCST_Order::get_id($order), $active_notification_email_subject);
			$active_notification_email_heading = str_replace("[order_id]", WCST_Order::get_id($order), $active_notification_email_heading);
			
			$wcst_email_model->send_active_notification_email_with_tracking_codes($this->get_billing_email($order), $message, $active_notification_email_subject, $active_notification_email_heading, $order);
		}
		
		
	}
	public function late_order_meta_processing($order_id, $posted_data)
	{
		$order = wc_get_order($order_id);
		
		if(!isset($order) || $order == false )
			return;
		
		if(isset($posted_data['_wcst_switch_order_to_completed']))
		{
			//$order->set_status("completed", '', false);
			$order->update_status("completed", '', true);
		} 
		//wp_die();
	}
}
?>