<?php 
class WCST_HtmlHelper
{
	public function __construct()
	{
	}
	function generic_shipping_comanies_dropdown_options($selected = "")
	{
		$option_model = new WCST_Option();
		$options = $option_model->get_option();
		$shipping_companies = WCST_AdminMenu::get_shipping_companies_list();
		$custom_companies = get_option( 'wcst_user_defined_companies');
		foreach( $shipping_companies as $k => $v )
		{
			if (isset($options[$k]) == '1') 
			{
				echo '<option value="'.$k.'" ';
				if ( $selected === $k) {
					echo 'selected="selected"';
				}
				echo '>'.$v.'</option>';  
			}
			
		}
		//Custom companies
		if(isset($custom_companies) && is_array($custom_companies))
			foreach( $custom_companies as $index => $custom_company )
			{
				if (isset($options[$index]) == '1') 
				{
					echo '<option value="'.$index.'" ';
					if ( $selected === $index.'')
					{
						echo 'selected="selected"';
					}
					echo '>'.$custom_company['name'].'</option>';  
				}
			}
	}
	function shipping_dropdown_options($data, $options, $already_shifted = false, $part = '')
		{ 
		 
			if ($part == '0' || $part == '' ) {
				$part = '';
			}
			
			$no_company_selected = 0;
			foreach($data as $key => $value)
				if(strpos('_wcst_order_trackurl', $key) !== false)
					$no_company_selected++;
			$no_company_selected = $no_company_selected > 0 ? false:true;
			
			if(!$already_shifted)
			{
				if(isset($data['_wcst_order_trackurl'.$part][0]))
					$data['_wcst_order_trackurl'.$part] = $data['_wcst_order_trackurl'.$part][0];
				else
					$data['_wcst_order_trackurl'.$part] = null;
			}
			$favorite = isset($options['favorite']) ? $options['favorite'] : "-1";
			$shipping_companies = WCST_AdminMenu::get_shipping_companies_list();
			$custom_companies = get_option( 'wcst_user_defined_companies');
			
			foreach( $shipping_companies as $k => $v )
			{
				if (isset($options[$k]) == '1') 
				{
					echo '<option value="'.$k.'" ';
					if ( ($no_company_selected && $favorite === $k) || (isset($data['_wcst_order_trackurl'.$part]) && $data['_wcst_order_trackurl'.$part] == $k)) {
						echo 'selected="selected"';
					}
					echo '>'.$v.'</option>';  
				}
				
			}
			//Custom companies
			if(isset($custom_companies) && is_array($custom_companies))
				foreach( $custom_companies as $index => $custom_company )
				{
					if (isset($options[$index]) == '1') 
					{
						echo '<option value="'.$index.'" ';
						if ( ($no_company_selected && $favorite===(string)$index) || (isset($data['_wcst_order_trackurl'.$part]) && $data['_wcst_order_trackurl'.$part] == $index.''))
						{
							echo 'selected="selected"';
						}
						echo '>'.$custom_company['name'].'</option>';  
					}
				}
			
		}
		function render_shipping_companies_tracking_info_configurator_widget($post = null) 
		{
			global $wcst_order_model, $wcst_time_model;
			$option_model = new WCST_Option();
			$general_options = $option_model->get_general_options();
			$date_format = isset($general_options['date_format']) ? $general_options['date_format'] : "dd/mm/yyyy";
			$admin_order_details_autofocus = isset($general_options['admin_order_details_autofocus']) ? $general_options['admin_order_details_autofocus'] : "no";
			$is_order_details_page = isset($post);
			$wc_order = $is_order_details_page ? wc_get_order($post->ID) : false;
			$data = $is_order_details_page ? $wcst_order_model->get_order_meta($post->ID) /* get_post_custom( $post->ID ) */ : array();
			
			//dispatch date managment
			$dispatch_date_automatic_fill_with_today_date = $option_model->get_general_options('dispatch_date_automatic_fill_with_today_date', 'no');
			if($dispatch_date_automatic_fill_with_today_date == 'yes' && !isset($data['_wcst_order_dispatch_date'][0]))
				$dispatch_date = current_time($option_model->get_sql_date_format_according_to_date_option());
			else 
			{
				$dispatch_date = isset($data['_wcst_order_dispatch_date'][0]) ? $data['_wcst_order_dispatch_date'][0] : "";
				$dispatch_date = $wcst_time_model->format_data($dispatch_date);
			}
			//wcst_var_dump($dispatch_date);
			
			$is_email_embedding_disabled = isset($post) ? $wcst_order_model->is_email_tracking_info_embedding_disabled($post->ID) : false;
			$options = $option_model->get_option();//get_option( 'wcst_options' );
			$style1 = 'style="display: none"';
			$btn1 = '';
			$active_notification_description = __('Clicking on the "Update" button, the plugin will send a notification email containing the tracking codes for which this option has been checked.', 'woocommerce-shipping-tracking');;
			$track_without_code_description = __('Tracking info will be showed even if no tracking code has been entered. Use the Custom text textarea to give more details about the shipping.', 'woocommerce-shipping-tracking');;
			//wcst_var_dump($data);
			if( isset( $data['_wcst_order_trackno1'][0]) && $data['_wcst_order_trackno1'][0] != '' ){
				$style1 = '';
				$btn1 = 'style="display: none"';
			}
			$index_additional_companies = 0;
			if(isset($data['_wcst_additional_companies']))
			{
										//old wc versions
				$additiona_companies = is_string($data['_wcst_additional_companies'][0]) ? unserialize(array_shift($data['_wcst_additional_companies'])) : $data['_wcst_additional_companies'];
				//var_dump($additiona_companies); //additiona_companies
			}
			
			wp_enqueue_style('wcst-datepicker-classic', WCST_PLUGIN_PATH.'/css/datepicker/classic.css');   
			wp_enqueue_style('wcst-datepicker-date-classic', WCST_PLUGIN_PATH.'/css/datepicker/classic.date.css');   
			wp_enqueue_style('wcst-datepicker-time-classic', WCST_PLUGIN_PATH.'/css/datepicker/classic.time.css');  
			wp_enqueue_style('wcst-order-detail', WCST_PLUGIN_PATH.'/css/datepicker/classic.time.css'); 
			wp_enqueue_style('wcst-shipping-companies-info-widget',  WCST_PLUGIN_PATH.'/css/wcst_shipping_companies_tracking_info_configurator_widget.css');
			
			wp_enqueue_script('wcst-ui-picker', WCST_PLUGIN_PATH.'/js/datepicker/picker.js', array( 'jquery' ));
			wp_enqueue_script('wcst-ui-datepicker', WCST_PLUGIN_PATH.'/js/datepicker/picker.date.js', array( 'jquery' ));
			wp_enqueue_script('wcst-ui-datepicker', WCST_PLUGIN_PATH.'/js/datepicker/picker.date.js', array( 'jquery' ));
			wp_enqueue_script('wcst-add-additional-company', WCST_PLUGIN_PATH. '/js/wcst-additional-companies.js' ,	array( 'jquery' ));
			wp_register_script('wcst-order-details', WCST_PLUGIN_PATH.'/js/wcst-order-details.js',	array( 'jquery' ));
			$js_options = array(
					'autofocus' => $admin_order_details_autofocus
				);
			wp_localize_script( 'wcst-order-details', 'wcst_options', $js_options );
			wp_enqueue_script( 'wcst-order-details' );
			
			?>
			<p>
			<?php _e('Add, edit or remove the tracking info. Once done click on the "Save Order" button to update order tracking info.', 'woocommerce-shipping-tracking'); ?>
			</p>
			<div class="wcst_shipping_info_box">
				<ul class="totals">
					<li>
						<label  style="display:block; clear:both; font-weight:bold;"><?php _e('Shipping Company:', 'woocommerce-shipping-tracking'); ?></label>
						<select style="margin-bottom:15px;" id="_wcst_order_trackurl" name="_wcst_order_trackurl" >
							<option value="NOTRACK" <?php if ( isset($data['_wcst_order_trackurl'][0]) && $data['_wcst_order_trackurl'][0] == 'NOTRACK') {
								echo 'selected="selected"';
							} ?>><?php _e('No Tracking', 'woocommerce-shipping-tracking'); ?></option>
							<?php $this->shipping_dropdown_options( $data, $options ); ?>
						</select>
					</li>
					<li>
						<label style="display:block; clear:both; font-weight:bold;"><?php _e('Tracking Number:', 'woocommerce-shipping-tracking'); ?></label>
						<p>	<?php _e('In case the tracking URL requires multiple codes, insert them by separating using the "," character. Example: "code1,code2,code2"', 'woocommerce-shipping-tracking');?></p>
						<input style="margin-bottom:15px;" type="text" id="_wcst_order_trackno" name="_wcst_order_trackno" placeholder="<?php _e('Enter Tracking No', 'woocommerce-shipping-tracking'); ?>" value="<?php if (isset($data['_wcst_order_trackno'][0])) echo $data['_wcst_order_trackno'][0]; ?>" class="wcst_tracking_code_input" />
						<!--<input class="wcst_no_tracking_code_checkbox" id="" type="checkbox" value="true" data-target="#_wcst_order_trackno" name="_wcst_track_without_tracking_code" <?php if (isset($data['_wcst_track_without_tracking_code'][0]) && $data['_wcst_track_without_tracking_code'][0]) echo 'checked="checked"' ?>><?php _e('Shipping has no tracking code', 'woocommerce-shipping-tracking'); ?></input>
						<span class="wcst_description"><?php echo $track_without_code_description; ?></span>-->
					</li>
					<li>
						<label  style="display:block; clear:both; font-weight:bold;"><?php _e('Dispatch date', 'woocommerce-shipping-tracking'); ?></label>
						<input style="margin-bottom:15px;" type="text" class="wcst_dispatch_date" id="_wcst_order_dispatch_date" name="_wcst_order_dispatch_date" placeholder="<?php _e('19/02/15 or 15th December 2015', 'woocommerce-shipping-tracking'); ?>" value="<?php echo $dispatch_date; ?>"  />
					</li>
					<li>
						<label style="display:block; clear:both; font-weight:bold;"><?php _e('Custom text', 'woocommerce-shipping-tracking'); ?></label>
						<textarea style="margin-bottom:15px;" type="text"  name="_wcst_custom_text" placeholder="<?php _e('Info about the shipped item(s) or whatever you want', 'woocommerce-shipping-tracking'); ?>" rows="4"><?php if (isset($data['_wcst_custom_text'][0])) echo $data['_wcst_custom_text'][0]; ?></textarea>
					</li>
					<li>
						<input class="wcst_send_shipping_notification_email_checkbox" id="wcst_send_shipping_notification_email_default" type="checkbox" value="true" data-id="default" name="wcst_send_shipping_notification_email[default]"><?php _e('Send a notification email', 'woocommerce-shipping-tracking'); ?></input>
						<span class="wcst_description"><?php echo $active_notification_description; ?></span>
					</li>		
					
				</ul>
			</div>
			<h4 id="wcst_additional_tracking_boxes_title"><?php _e('Additional tracking codes', 'woocommerce-shipping-tracking'); ?></h4>
			<div id="wcst-additional-shippings">
				<?php if(isset($additiona_companies))
						foreach($additiona_companies as $company):
					//var_dump($company);
					$dispatch_date = isset($company['_wcst_order_dispatch_date']) ? $company['_wcst_order_dispatch_date'] : "";
					$dispatch_date = $wcst_time_model->format_data($dispatch_date);
					?>
					<div id="wcst-additiona-shipping-box-<?php echo $index_additional_companies?>" class="wcst_shipping_info_box">
						<ul class="totals">
							<li>
								<label style="display:block; clear:both;"><?php _e('Shipping Company:', 'woocommerce-shipping-tracking'); ?></label>								
								<select style="margin-bottom:15px;" name="_wcst_order_additional_shipping[<?php echo $index_additional_companies?>][trackurl]"  >
									<option value="NOTRACK" <?php if ( isset($company['_wcst_order_trackurl']) && $company['_wcst_order_trackurl'] == 'NOTRACK') {
										echo 'selected="selected"';
									} ?>><?php _e('No Tracking', 'woocommerce-shipping-tracking'); ?></option>
									<?php $this->shipping_dropdown_options( $company, $options, true ); ?>
								</select>
							</li>
							<li>
								<label style="display:block; clear:both;"><?php _e('Tracking Number:', 'woocommerce-shipping-tracking'); ?></label>
								<input style="margin-bottom:15px;"type="text" id="wcst_tracking_code_input_<?php echo $index_additional_companies?>" name="_wcst_order_additional_shipping[<?php echo $index_additional_companies?>][trackno]" placeholder="<?php _e('Enter Tracking No', 'woocommerce-shipping-tracking'); ?>" value="<?php if (isset($company['_wcst_order_trackno'])) echo $company['_wcst_order_trackno']; ?>" class="wcst_tracking_code_input" />
								<!-- <input class="wcst_no_tracking_code_checkbox" id="" type="checkbox" value="true" data-target="#wcst_tracking_code_input_<?php echo $index_additional_companies?>" name="_wcst_order_additional_shipping[<?php echo $index_additional_companies?>][track_without_tracking_code]" <?php if (isset($company['_wcst_track_without_tracking_code']) && $company['_wcst_track_without_tracking_code']) echo 'checked="checked"' ?>><?php _e('Shipping has no tracking code', 'woocommerce-shipping-tracking'); ?></input>
								<span class="wcst_description"><?php echo $track_without_code_description; ?></span> -->
							</li>
							<li>
								<label style="display:block; clear:both;"><?php _e('Dispatch date', 'woocommerce-shipping-tracking'); ?></label>
								<input style="margin-bottom:15px;" type="text" class="wcst_dispatch_date" name="_wcst_order_additional_shipping[<?php echo $index_additional_companies?>][order_dispatch_date]" placeholder="<?php _e('19/02/16 or 15th Dec 2016', 'woocommerce-shipping-tracking'); ?>" value="<?php echo $dispatch_date; ?>"  />
							</li>
							<li>
								<label style="display:block; clear:both;"><?php _e('Custom text', 'woocommerce-shipping-tracking'); ?></label>
								<textarea style="margin-bottom:15px;" type="text" class="wcst_custom_text" name="_wcst_order_additional_shipping[<?php echo $index_additional_companies?>][custom_text]" placeholder="<?php _e('Info about the shipped item(s) or whatever you want', 'woocommerce-shipping-tracking'); ?>" rows="4"><?php if (isset($company['_wcst_custom_text'])) echo $company['_wcst_custom_text']; ?></textarea>
							</li>
							<li>
								<input class="" id="wcst_send_shipping_notification_email_<?php echo $index_additional_companies?>" type="checkbox" value="true" data-id="<?php echo $index_additional_companies?>" name="wcst_send_shipping_notification_email[<?php echo $index_additional_companies?>]"><?php _e('Send a notification email', 'woocommerce-shipping-tracking'); ?></input>
								<span class="wcst_description"><?php echo $active_notification_description; ?></span>
							</li>	
						</ul>
						<button class="button wcst-remove-shipping" data-id="<?php echo $index_additional_companies?>"> <?php _e('Remove', 'woocommerce-shipping-tracking'); ?></button>
					</div>
				<?php $index_additional_companies++; endforeach; ?>
			</div>
			<div class="clear"></div>
			<button class="button" id="wcst-additional-shipping-button"><?php _e('Add another tracking code', 'woocommerce-shipping-tracking'); ?></button>
			
			<div class="<?php if($is_order_details_page) echo 'wcst_option_container'; ?>">
				<h4 class="wcst_option_title"><?php _e('Disable email embedding', 'woocommerce-shipping-tracking'); ?> 
					<!-- <div class="wcst_tooltip dashicons dashicons-editor-help"><span class="wcst_tooltiptext"><?php _e('This option overrides the <strong>General options -> Email options</strong> settings allowing you to not embed tracking info in any WooCommerce email', 'woocommerce-shipping-tracking'); ?></span></div>-->
				</h4>
				<span class="wcst_description"><?php _e('This overrides the <strong>General option -> Email options</strong> settings allowing you to not embed any tracking info into WooCommerce emails', 'woocommerce-shipping-tracking'); ?></span>
				<select name="_wcst_order_disable_email">
					<option value="no"><?php _e('No', 'woocommerce-shipping-tracking'); ?></option>
					<option value="disable_email_embedding" <?php if($is_email_embedding_disabled) echo 'selected="selected"';?>><?php _e('Yes', 'woocommerce-shipping-tracking'); ?></option>
				</select>
			</div>
			
			<?php if($is_order_details_page && $wc_order && $wc_order->get_status() != 'completed'): ?>
			<div class="wcst_option_container">
				<h4 class="wcst_option_title"><?php _e('Switch order status to completed', 'woocommerce-shipping-tracking'); ?></h4>
				<span class="wcst_description"><?php _e('This saves you some time. Check the following option to set the order status as "Completed" :)', 'woocommerce-shipping-tracking'); ?></span>
				<input type="checkbox" name="_wcst_switch_order_to_completed" value="yes"><?php _e('Yes', 'woocommerce-shipping-tracking'); ?></input>
			</div>
			<?php endif; ?>
			
			<script>
			var wcst_index = <?php echo $index_additional_companies; ?>;
			var wcst_date_format = "<?php echo $date_format; ?>";
			<?php 
			//dispatch date managment
			$dispatch_date = $dispatch_date_automatic_fill_with_today_date == 'yes' ? current_time($option_model->get_sql_date_format_according_to_date_option()) : "";
			?>
			var wcst_default_dispatch_date = "";
			function wcst_get_template(index)
			{
				var wcst_add_shipping_company_template = '<div id="wcst-additiona-shipping-box-'+index+'" class="wcst_shipping_info_box">';
					wcst_add_shipping_company_template += '	<ul class="totals">';
					wcst_add_shipping_company_template += '		<li>';
					wcst_add_shipping_company_template += '			<label style="display:block; clear:both;"><?php echo str_replace("'","\'",__('Shipping Company:', 'woocommerce-shipping-tracking')); ?></label>';
					wcst_add_shipping_company_template += '			<select style="margin-bottom:15px;" name="_wcst_order_additional_shipping['+index+'][trackurl]" >';
					wcst_add_shipping_company_template += '				<option value="NOTRACK"><?php echo str_replace("'","\'",__('No Tracking', 'woocommerce-shipping-tracking')); ?></option>';
					wcst_add_shipping_company_template += '				<?php $this->shipping_dropdown_options( $data, $options); ?>';
					wcst_add_shipping_company_template += '			</select>';
					wcst_add_shipping_company_template += '		</li>';
					wcst_add_shipping_company_template += '		<li>';
					wcst_add_shipping_company_template += '			<label style="display:block; clear:both;"><?php echo str_replace("'","\'", __('Tracking Number:', 'woocommerce-shipping-tracking')); ?></label>';
					wcst_add_shipping_company_template += '			<input style="margin-bottom:15px;" type="text" id="wcst_tracking_code_input_'+index+'" name="_wcst_order_additional_shipping['+index+'][trackno]" placeholder="<?php echo str_replace("'","\'",__('Enter Tracking No', 'woocommerce-shipping-tracking')); ?>" value="" class="wcst_tracking_code_input" />';
					//wcst_add_shipping_company_template += '			<input class="wcst_no_tracking_code_checkbox" id="" type="checkbox" value="true" data-target="#wcst_tracking_code_input_'+index+'" name="_wcst_order_additional_shipping['+index+'][track_without_tracking_code]"><?php _e('Shipping has no tracking code', 'woocommerce-shipping-tracking'); ?></input>';
					//wcst_add_shipping_company_template += '			<span class="wcst_description"><?php echo $track_without_code_description; ?></span>';					
					wcst_add_shipping_company_template += '		</li>';
					wcst_add_shipping_company_template += '		<li>';
					wcst_add_shipping_company_template += '			<label style="display:block; clear:both;"><?php echo str_replace("'","\'",__('Dispatch date', 'woocommerce-shipping-tracking')); ?></label>';
					wcst_add_shipping_company_template += '			<input style="margin-bottom:15px;" class="wcst_dispatch_date" type="text" name="_wcst_order_additional_shipping['+index+'][order_dispatch_date]" placeholder="<?php echo str_replace("'","\'",__('19/02/15 or 15th December 2015', 'woocommerce-shipping-tracking')); ?>" value="<?php echo $dispatch_date; ?>" />';
					wcst_add_shipping_company_template += '		</li>';
					wcst_add_shipping_company_template += '		<li>';
					wcst_add_shipping_company_template += '			<label style="display:block; clear:both;"><?php echo str_replace("'","\'",__('Custom text', 'woocommerce-shipping-tracking')); ?></label>';
					wcst_add_shipping_company_template += '			<textarea style="margin-bottom:15px;" type="text" class="wcst_custom_text" name="_wcst_order_additional_shipping['+index+'][custom_text]" placeholder="<?php echo str_replace("'","\'",__('Info about the shipped item(s) or whatever you want', 'woocommerce-shipping-tracking')); ?>" rows="4" />';
					wcst_add_shipping_company_template += '		</li>';
					wcst_add_shipping_company_template += '	<li>';
					wcst_add_shipping_company_template += '			<input class="" id="wcst_send_shipping_notification_email_'+index+'" type="checkbox" value="true" data-id="'+index+'" name="wcst_send_shipping_notification_email['+index+']"><?php _e('Send a notification email', 'woocommerce-shipping-tracking'); ?></input>';
					wcst_add_shipping_company_template += '			<span class="wcst_description"><?php echo $active_notification_description; ?></span>';
					wcst_add_shipping_company_template += '		</li>';	
					wcst_add_shipping_company_template += ' 	</ul>';
					wcst_add_shipping_company_template += ' 	<button class="button wcst-remove-shipping" data-id="'+index+'"> <?php echo str_replace("'","\'",__('Remove', 'woocommerce-shipping-tracking')); ?></button>';
					wcst_add_shipping_company_template += '	</div>';
				return wcst_add_shipping_company_template;
			}
			</script>
			<?php 
		}	
}
?>