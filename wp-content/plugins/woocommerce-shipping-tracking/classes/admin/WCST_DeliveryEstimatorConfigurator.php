<?php 
class WCST_DeliveryEstimatorConfigurator
{
	public function __construct()
	{
	}
	public function render_page()
	{
		$options = new WCST_Option();
		
		//Set
		if(isset($_POST) && !empty($_POST))
			$options->save_delivery_estimations($_POST);
		
		//Get
		$estimations = $options->get_delivery_estimations();
		$method_estimate_from = isset($estimations['method_estimate_from']) ? $estimations['method_estimate_from'] : array();
		$method_estimate_to = isset($estimations['method_estimate_to']) ? $estimations['method_estimate_to'] : array(); 
		
		//Js & Css
		wp_enqueue_style('wcst-common', WCST_PLUGIN_PATH.'/css/wcst_common.css');  
		wp_enqueue_style('wcst-delivery-estimator-configurator', WCST_PLUGIN_PATH.'/css/wcst_delivery_estimator_configurators.css'); 
		?>
		<div class="wcst_wrap white-box">
			<h2 class="wcst_section_title wcst_small_margin_top"><?php _e( 'Estimated delivery times', 'woocommerce-shipping-tracking' );?></h2> 
			<p><?php _e( 'Here you can define estimation for delivery time for each Shipping rate you have defined. Estimation can be associated only to Default WooCommerce shipping rates and to the one created with <strong>Table Rate Shipping for WooCommerce</strong> plugin.', 'woocommerce-shipping-tracking' );?></p>
			<p><?php _e( 'How will the delivery estimation will be displayed? According which fields you will fill you will have different output. Follows an example:', 'woocommerce-shipping-tracking' );?>
				<ol>
					<li><?php _e('Filling both <strong>From</strong> and <strong>To</strong> fields: <strong><i>x - y days</i></strong>',''); ?></li>
					<li><?php _e('Filling only <strong>From</strong> field (<strong>To</strong> field empty): <strong><i>at least x day(s)</i></strong>',''); ?></li>
					<li><?php _e('Filling only <strong>To</strong> field (<strong>To</strong> field empty): <strong><i>up to y day(s)</i></strong>',''); ?></li>
				</ol>
			</p>
			<form method="post">
				<table class="form-table">
				<tbody>
					<tr valign="top">
						<!-- <th scope="row" class="titledesc"><?php esc_html_e( 'Estimate Ranges', 'woocommerce-shipping-tracking' ) ?></th> -->
						<td class="forminp">
							<?php $zones = WC_Shipping_Zones::get_zones(); ?>
							<?php //World zones ?>
							<?php if ( ! empty( $zones ) ) : ?>
							<?php foreach ( $zones as $zone_id => $zone_data ) : ?>
								<?php 
									$zone = WC_Shipping_Zones::get_zone( $zone_id ); 
									$zone_methods = $zone->get_shipping_methods(); 
									//wcst_var_dump(get_class_methods ('WC_Shipping_Table_Rate'));
									
									if ( ! empty( $zone_methods ) ) :
								?>
								<table class="wc_shipping widefat wp-list-table" cellspacing="0">
								<thead>
									<tr style="background: #f7f7f7;">
										<th colspan="4" class="first-head" style="text-align: center; ">
											<?php echo sprintf( '<a href="%1$s">%2$s</a>', esc_url( admin_url( 'admin.php?page=wc-settings&tab=shipping&zone_id=' . $zone->get_id() ) ), $zone->get_zone_name() ); ?>
											<?php esc_html_e( 'Methods', 'woocommerce-shipping-tracking' ); ?>
										</th>
									</tr>
									<tr>
										<th class="name" style="padding-left: 2% !important"><?php esc_html_e( 'Name', 'woocommerce-shipping-tracking' ); ?></th>
										<th class="type"><?php esc_html_e( 'Label', 'woocommerce-shipping-tracking' ); ?></th>
										<th class="day-from"><?php esc_html_e( 'From (days)', 'woocommerce-shipping-tracking' ); ?> <?php echo wc_help_tip( __( 'The earliest estimated arrival. Can be left blank.', 'woocommerce-shipping-tracking' ) ); ?></th>
										<th class="day-to"><?php esc_html_e( 'To (days)', 'woocommerce-shipping-tracking' ); ?> <?php echo wc_help_tip( __( 'The latest estimated arrival. Can be left blank.', 'woocommerce-shipping-tracking' ) ); ?></th>
									</tr>
								</thead>
								<tbody>
								<?php foreach ( $zone->get_shipping_methods() as $instance_id => $method ) : ?>
									<?php 
									
									//Support to new Table Shipping Rating plugin rates (CodeCanyon)
									if(get_class($method) == 'BE_Table_Rate_Method'):
										$be_table_rates = get_option( $method->id . '_options-' . $method->instance_id );
										//wcst_var_dump($be_table_rates);
										//wcst_var_dump(get_class_methods('BE_Table_Rate_Method'));
										foreach($be_table_rates['settings'] as $be_rate):
												//wcst_var_dump($be_rate);
												$method_tile = $be_rate['title'];
												$shipping_rate_id = $instance_id."-".$be_rate['option_id'];
											?>
												<tr>
													<td style="padding-left: 2%" class="name">
														<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=shipping&instance_id=' . $instance_id ) ); ?>" target="_blank" ><?php echo esc_html( $method_tile ); ?></a>
													</td>
													<td class="type">
														<?php echo esc_html( $method_tile); ?>
													</td>
													<td class="day-from">
														<input type="number" step="1" min="0" name="method_estimate_from[<?php echo esc_attr( $method->id."_".$shipping_rate_id ); ?>]" value="<?php echo isset( $method_estimate_from[ $method->id."_".$shipping_rate_id ] ) ? $method_estimate_from[ $method->id."_".$shipping_rate_id ] : ''; ?>" />
													</td>
													<td class="day-to">
														<input type="number" step="1" min="0" name="method_estimate_to[<?php echo esc_attr( $method->id."_".$shipping_rate_id ); ?>]" value="<?php echo isset( $method_estimate_to[ $method->id."_".$shipping_rate_id ] ) ? $method_estimate_to[ $method->id."_".$shipping_rate_id ] : ''; ?>" />
													</td>
												</tr>
											<?php 
										endforeach; //shipping_rates
										

									//Support to Woo Table Shipping Rating plugin
									elseif(method_exists($method, 'get_shipping_rates')):
									//wcst_var_dump($method->get_shipping_rates());
									
										$shipping_rates = $method->get_shipping_rates();
										foreach($shipping_rates as $shipping_rate):
												//wcst_var_dump($shipping_rates);
												//wcst_var_dump($zone_methods[$shipping_rate->shipping_method_id]->title);
										
												$method_tile = $zone_methods[$shipping_rate->shipping_method_id]->title; //$shipping_rate->rate_label;
												$method_sub_title = $shipping_rate->rate_label;
												$shipping_rate_id = $instance_id.":".$shipping_rate->rate_id;
											?>
												<tr>
													<td style="padding-left: 2%" class="name">
														<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=shipping&instance_id=' . $instance_id ) ); ?>" target="_blank" ><?php echo esc_html( $method_tile ); ?></a>
													</td>
													<td class="type">
														<?php echo $method_sub_title != "" ? esc_html( $method_sub_title) : esc_html_e( 'N/A', 'woocommerce-shipping-tracking' );; ?>
													</td>
													<td class="day-from">
														<input type="number" step="1" min="0" name="method_estimate_from[<?php echo esc_attr( $method->id.":".$shipping_rate_id ); ?>]" value="<?php echo isset( $method_estimate_from[ $method->id.":".$shipping_rate_id ] ) ? $method_estimate_from[ $method->id.":".$shipping_rate_id ] : ''; ?>" />
													</td>
													<td class="day-to">
														<input type="number" step="1" min="0" name="method_estimate_to[<?php echo esc_attr( $method->id.":".$shipping_rate_id ); ?>]" value="<?php echo isset( $method_estimate_to[ $method->id.":".$shipping_rate_id ] ) ? $method_estimate_to[ $method->id.":".$shipping_rate_id ] : ''; ?>" />
													</td>
												</tr>
											<?php 
										endforeach; //shipping_rates
									//Native WooCommerce methods
									else:
										$method_tile = $method->get_title();
									?>
										<tr>
											<td style="padding-left: 2%" class="name">
												<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=shipping&instance_id=' . $instance_id ) ); ?>" target="_blank" ><?php echo esc_html( $method_tile ); ?></a>
											</td>
											<td class="type">
												<?php echo esc_html( $method_tile ); ?>
											</td>
											<td class="day-from">
												<input type="number" step="1" min="0" name="method_estimate_from[<?php echo esc_attr( $method->id.":".$instance_id ); ?>]" value="<?php echo isset( $method_estimate_from[ $method->id.":".$instance_id ] ) ? $method_estimate_from[ $method->id.":".$instance_id ] : ''; ?>" />
											</td>
											<td class="day-to">
												<input type="number" step="1" min="0" name="method_estimate_to[<?php echo esc_attr( $method->id.":".$instance_id ); ?>]" value="<?php echo isset( $method_estimate_to[ $method->id.":".$instance_id ] ) ? $method_estimate_to[ $method->id.":".$instance_id ] : ''; ?>" />
											</td>
										</tr>
								<?php endif;
								endforeach; ?>
								</tbody>
								</table>
								<?php endif; ?>
							<?php endforeach; ?>
							<?php endif; ?>
							
							<?php $world_zone =  WC_Shipping_Zones::get_zone( 0 ); ?>
							<?php $world_zone_methods = $world_zone->get_shipping_methods(); ?>
							<?php 
								//Rest of the world
								if ( ! empty( $world_zone_methods ) ) : ?>
								<table class="wc_shipping widefat wp-list-table" cellspacing="0">
								<thead>
									<tr style="background: #f7f7f7;">
										<th class="first-head" colspan="4" style="text-align: center; ">
											<?php $zone_name = __( 'Rest of the World', 'woocommerce-shipping-tracking' ); ?>
											<?php echo sprintf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( admin_url( 'admin.php?page=wc-settings&tab=shipping&zone_id=0' ) ), $zone_name ); ?>
											<?php esc_html_e( 'Methods', 'woocommerce-shipping-tracking' ); ?>
										</th>
									</tr>
									<tr>
										<th class="name" style="padding-left: 2% !important"><?php esc_html_e( 'Name', 'woocommerce-shipping-tracking' ); ?></th>
										<th class="type"><?php esc_html_e( 'Type', 'woocommerce-shipping-tracking' ); ?></th>
										<th class="day-from"><?php esc_html_e( 'From (days)', 'woocommerce-shipping-tracking' ); ?> <?php echo wc_help_tip( __( 'The earliest estimated arrival. Can be left blank.', 'woocommerce-shipping-tracking' ) ); ?></th>
										<th class="day-to"><?php esc_html_e( 'To (days)', 'woocommerce-shipping-tracking' ); ?> <?php echo wc_help_tip( __( 'The latest estimated arrival. Can be left blank.', 'woocommerce-shipping-tracking' ) ); ?></th>
									</tr>
								</thead>
								<tbody>
								<?php 
									foreach ( $world_zone_methods as $instance_id => $method ) : ?>
									<tr>
										<td style="padding-left: 2%" class="name">
											<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=shipping&instance_id=' . $instance_id ) ); ?>" target="_blank"><?php echo esc_html( $method->get_title() ); ?></a>
										</td>
										<td class="type">
											<?php echo esc_html( $method->get_method_title() ); ?>
										</td>
										<td class="day-from">
											<input type="number" step="1" min="0" name="method_estimate_from[<?php echo esc_attr( $method->id.":".$instance_id ); ?>]" value="<?php echo isset( $method_estimate_from[$method->id.":".$instance_id] ) ? $method_estimate_from[ $method->id.":".$instance_id ] : ''; ?>" />
										</td>
										<td class="day-to">
											<input type="number" step="1" min="0" name="method_estimate_to[<?php echo esc_attr( $method->id.":".$instance_id ); ?>]" value="<?php echo isset( $method_estimate_to[$method->id.":".$instance_id ] ) ? $method_estimate_to[ $method->id.":".$instance_id ] : ''; ?>" />
										</td>
									</tr>
								<?php endforeach; ?>
								</tbody>
								</table>
								<?php endif; ?>
								<?php 
									$methods = WC()->shipping->get_shipping_methods(); 
									unset( $methods['flat_rate'], $methods['free_shipping'], $methods['local_pickup'] );
									$other_methods = false;
									 if(is_a($method, 'BE_Table_Rate_Shipping') && isset($method->table_rates))
											   foreach($method->table_rates as $table_rate)
													$no_other_methods = true;
									
									if ( ! empty( $methods ) && $other_methods) :
									//Table rate shipping
								?>
								<table class="wc_shipping widefat wp-list-table" cellspacing="0">
								<thead>
									<tr style="background: #f7f7f7;">
										<th class="first-head" colspan="4" style="text-align: center; "><?php esc_html_e( 'Other Methods', 'woocommerce-shipping-tracking' ); ?></th>
									</tr>
									<tr>
										<th class="name" style="padding-left: 2% !important"><?php esc_html_e( 'Name', 'woocommerce-shipping-tracking' ); ?></th>
										<th class="id"><?php esc_html_e( 'ID', 'woocommerce-shipping-tracking' ); ?></th>
										<th class="day-from"><?php esc_html_e( 'From (days)', 'woocommerce-shipping-tracking' ); ?> <?php echo wc_help_tip( __( 'The earliest estimated arrival. Can be left blank.', 'woocommerce-shipping-tracking' ) ); ?></th>
										<th class="day-to"><?php esc_html_e( 'To (days)', 'woocommerce-shipping-tracking' ); ?> <?php echo wc_help_tip( __( 'The latest estimated arrival. Can be left blank.', 'woocommerce-shipping-tracking' ) ); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ( $methods as $method_id => $method ) : 
											//wcst_var_dump($method->table_rates );
											//wcst_var_dump(get_class($method) );
										   if(is_a($method, 'BE_Table_Rate_Shipping') && isset($method->table_rates))
											   foreach($method->table_rates as $table_rate):
										?>
										<tr>
											<td style="padding-left: 2%" class="name">
												<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=shipping&section=' . $method_id ) ); ?>" target="_blank">
												<?php echo esc_html( $table_rate['title'] ); ?>
												</a>
											</td>
											<td class="id">
												<?php echo esc_attr( $method->id."_".$table_rate['identifier'] ); ?>
											</td>
											<td class="day-from">
												<input type="number" step="1" min="0" name="method_estimate_from[<?php echo esc_attr($method->id."_".$table_rate['identifier'] ); ?>]" value="<?php echo isset( $method_estimate_from[ $method->id."_".$table_rate['identifier']] ) ? $method_estimate_from[$method->id."_".$table_rate['identifier'] ] : ''; ?>" />
											</td>
											<td width="1%" class="day-to">
												<input type="number" step="1" min="0" name="method_estimate_to[<?php echo esc_attr( $method->id."_".$table_rate['identifier'] ); ?>]" value="<?php echo isset( $method_estimate_to[ $method->id."_".$table_rate['identifier']] ) ? $method_estimate_to[ $method->id."_".$table_rate['identifier']] : ''; ?>" />
											</td>
										</tr>
									<?php  endforeach;
									endforeach; ?>
								</tbody>
								</table>
								<?php endif; ?>
							</table>
						</td>
					</tr>
				</tbody>
				</table>
				<p>
					<input type="submit" value="<?php _e('Save', 'woocommerce-shipping-tracking');?>" class="button-primary" id="save-button" name="Submit">
				</p>
			</form>
		</div>
		<?php
	}
}
?>