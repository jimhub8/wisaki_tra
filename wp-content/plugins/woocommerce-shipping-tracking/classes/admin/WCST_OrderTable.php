<?php 
class WCST_OrderTable
{
	function __construct() 
	{
		add_filter( 'woocommerce_shop_order_search_fields', array( &$this, 'woocommerce_shop_order_search_tracking_number') );
		add_action( 'manage_edit-shop_order_columns', array( &$this, 'add_tracking_column'), 20, 1 );
		add_action( 'manage_shop_order_posts_custom_column', array( &$this, 'add_tacking_info_to_column'), 10,2);
		add_filter( 'manage_edit-product_columns', array(&$this, 'add_estimated_column'),15 );
		add_action( 'manage_product_posts_custom_column', array(&$this, 'add_estimation_info_to_column'), 10, 2 );
		add_action('restrict_manage_posts', array( &$this,'add_uploads_select_box_filter'));
		add_filter('parse_query',array( &$this,'filter_query_by_shipping_company')); 
	
	}
	public function add_uploads_select_box_filter()
	{
		global $typenow, $wp_query, $wcst_html_helper; 
		if ($typenow=='shop_order') 
		{
			$selected = isset($_GET['wcst_filter_by_shipping_company']) && $_GET['wcst_filter_by_shipping_company'] ? $_GET['wcst_filter_by_shipping_company']:"none";
			//onchange="this.form.submit()" ?>
			<select name="wcst_filter_by_shipping_company" >
				<option value="none" <?php if($selected == "none") echo 'selected="selected"';?>><?php _e('Select a shipping company', 'woocommerce-shipping-tracking') ?></option>
				<?php $wcst_html_helper->generic_shipping_comanies_dropdown_options($selected); ?>
			</select>
			<?php
		}
	}
	function filter_query_by_shipping_company($query) 
	{
		global $pagenow;
		$qv = &$query->query_vars;
		if ($pagenow=='edit.php' && 
		    isset($qv['post_type']) && $qv['post_type']=='shop_order' && isset($_GET['wcst_filter_by_shipping_company']) && $_GET['wcst_filter_by_shipping_company'] != 'none') 
		{
			 $qv['meta_query'][] = 
				array(
				 'relation' => 'OR',
				  array(
					'key' => '_wcst_order_trackurl',
					'compare' => '=',
					/*'compare' => 'NOT NULL',
					 'type' => 'CHAR' , */
					 'value' => $_GET['wcst_filter_by_shipping_company']
				  ),
				   array(
					'key' => '_wcst_additional_companies',
					'compare' => 'LIKE',
					 'value' => serialize('_wcst_order_trackurl').serialize($_GET['wcst_filter_by_shipping_company'])
				  )
			  );
			 // wcst_var_dump( $qv['meta_query']);
		}
	}
	function woocommerce_shop_order_search_tracking_number( $search_fields ) {

		$search_fields[] = '_wcst_order_trackno';
		$search_fields[] = '_wcst_additional_companies';
		$search_fields[] = '_wcst_order_trackname';
		return $search_fields;
	}
	//Order list columns
	function add_tracking_column($columns){ 
	
		wp_enqueue_style('wcst-info-tracking-box',  WCST_PLUGIN_PATH.'/css/wcst_orders_list.css');
		$columns["wcst_tracking_number"] = __('Tracking Number', 'woocommerce-shipping-tracking');
		
		return $columns;
		
	}
	//Order list columns
	function add_tacking_info_to_column($column, $order_id)
	{ 
		global $post, $woocommerce, $the_order, $wcst_product_model, $wcst_order_model;
			
			$order = wc_get_order($order_id); //new WC_Order( $order_id );
			/* if ( empty( $the_order ) || $the_order->id != $post->ID )
			{
				$the_order = new WC_Order( $post->ID );
			} */
				
			switch ( $column ) 
			{
				case "wcst_tracking_number" :
					
					$order_meta = $wcst_order_model->get_order_meta(WCST_Order::get_id($order)); //get_post_custom( WCST_Order::get_id($order) );
								
					//if(isset($order_meta['_wcst_order_trackno']) && isset($order_meta['_wcst_order_trackurl']))
					if(isset($order_meta['_wcst_order_trackno']) && isset($order_meta['_wcst_order_trackurl']) && isset($order_meta['_wcst_order_trackname']))
					{
					 	$this->admin_shipping_details_to_column($order_meta, $order);
					}
					if(isset($order_meta['_wcst_additional_companies']))
					{
												//old wc versions
						$additiona_companies = is_string($order_meta['_wcst_additional_companies'][0]) ? unserialize($order_meta['_wcst_additional_companies'][0]) : $order_meta['_wcst_additional_companies'];
						$this->admin_additional_shipping_details_to_column($additiona_companies, $order);
					}
				break; 
				
				
			}
			
	}
	//Product list columns
	function add_estimated_column($columns){ 
	
		$columns["wcst_estimated_rule_name"] =__('Est. shipping rule', 'woocommerce-shipping-tracking');
		
		return $columns;
		
	}
	//Product list columns
	function add_estimation_info_to_column( $column, $post_id ) 
	{ 
		global $post, $woocommerce, $the_order, $wcst_product_model;
	
			switch ( $column ) 
			{
					
				case "wcst_estimated_rule_name":
					$rule = $wcst_product_model->get_estimation_shippment_rule($post_id);
					if(isset($rule))
						echo '<a class="" target="_blank" href="'.admin_url().'admin.php?page=acf-options-estimated-shipping-configurator">'.
								$rule['name_id'].
								'</a></br>';
					break;
				
			}
			
	}
		
	function admin_shipping_details_to_column($order_meta , $order)
	{
		
		$urltrack =isset($order_meta['_wcst_order_track_http_url'][0]) ? $order_meta['_wcst_order_track_http_url'][0] : "#";
	
		if (/* $order_meta['_wcst_order_trackno'][0] != null &&  */$order_meta['_wcst_order_trackurl'][0] != null && $order_meta['_wcst_order_trackurl'][0] != 'NOTRACK' ) 
		{ ?>
			<div class="wcst_tracking_info_box"><STRONG><?php 
				echo $order_meta['_wcst_order_trackname'][0];
			?></STRONG><br/>
			<?php if($order_meta['_wcst_order_trackno'][0]): ?>
				<a target="_blank" href="<?php echo $urltrack;?>">#<?php echo $order_meta['_wcst_order_trackno'][0]; ?></a>
			<?php endif; ?>
			<?php if(isset($order_meta['_wcst_order_dispatch_date'][0]) && $order_meta['_wcst_order_dispatch_date'][0]): ?>
				<br/>
				<?php echo __('On: ','').$order_meta['_wcst_order_dispatch_date'][0];
			endif; ?>
			</div>
		<?php } 
		
	}
	function admin_additional_shipping_details_to_column($additiona_companies , $order)
	{
		foreach($additiona_companies as $order_meta)
		{
			$urltrack = isset($order_meta['_wcst_order_track_http_url']) ? $order_meta['_wcst_order_track_http_url'] : "#";
		
			if (/* $order_meta['_wcst_order_trackno'] != null &&  */$order_meta['_wcst_order_trackurl'] != null && $order_meta['_wcst_order_trackurl'] != 'NOTRACK' ) 
			{ ?>
				<div class="wcst_tracking_info_box"><STRONG><?php 
					echo $order_meta['_wcst_order_trackname'];
				?></STRONG><br/>
				<?php if($order_meta['_wcst_order_trackno']): ?>
					<a target="_blank" href="<?php echo $urltrack;?>">#<?php echo $order_meta['_wcst_order_trackno']; ?></a>
				<?php endif; ?>
				<?php if(isset($order_meta['_wcst_order_dispatch_date']) && $order_meta['_wcst_order_dispatch_date']): ?>
					<br/>
					<?php echo __('On: ','').$order_meta['_wcst_order_dispatch_date'];
				endif; ?>
				</div>
			<?php } 
		}
		
	}
}
?>