<?php 
class WCST_OrderTableFragment
{
	var $rates;
	var $estimations;
	public function __construct()
	{
		//add_filter( 'woocommerce_package_rates', array(&$this, 'get_all_shipping_rates') );
		add_filter('woocommerce_cart_shipping_method_full_label', array(&$this, 'display_estimated_date'), 10, 2); //woocommerce_after_shipping_rate
	}
	public function display_estimated_date($label, $method )
	{
		global $woocommerce;
		//wcst_var_dump($method);
		
		if(!isset($this->estimations))
		{
			$options = new WCST_Option();
			$this->estimations = $options->get_delivery_estimations();
		}
		$method_estimate_from = isset($this->estimations['method_estimate_from']) ? $this->estimations['method_estimate_from'] : array();
		$method_estimate_to = isset($this->estimations['method_estimate_to']) ? $this->estimations['method_estimate_to'] : array(); 
		
		$current_method_days_from = isset( $method_estimate_from[ $method->id]  ) ? $method_estimate_from[ $method->id  ] : 0;
		$current_method_days_to = isset( $method_estimate_to[ $method->id ] ) ? $method_estimate_to[ $method->id ] : 0;
		
		if ( ! $current_method_days_from && ! $current_method_days_to ) 
			return $label;
		
		$label .= '<br /><small class="wcst_estimated_shipping_delivery" data-min="'.$current_method_days_from.'" data-max="'.$current_method_days_to.'">';
		if ( ! empty( $current_method_days_from ) && ! empty( $current_method_days_to ) ) 
			$label .= sprintf( __( 'Delivery: %1$s - %2$s days', 'woocommerce-shipping-tracking' ), $current_method_days_from, $current_method_days_to);
		elseif ( empty( $current_method_days_from ) && ! empty( $current_method_days_to ) ) 
			$label .= sprintf( __( 'Delivery: up to %1$s day(s)', 'woocommerce-shipping-tracking' ), $current_method_days_to );
		elseif ( ! empty( $current_method_days_from ) && empty( $current_method_days_to ) ) 
			$label .= sprintf( __( 'Delivery: at least %1$s day(s)', 'woocommerce-shipping-tracking' ), $current_method_days_from);

		$label .= '</small>';
	    
		return $label;
	}
	function get_all_shipping_rates( $rates ) 
	{
 
		/* $free_rates = array();
	 
		foreach ( $rates as $rate_id => $rate ) {
			 
			if ( 0 === (int) $rate->cost ) {
				$free_rates[ $rate_id ] = $rate;
				// uncomment this `break;` if you only want to show the first free rate
				// break;
			}
		}
	 
		return ! empty( $free_rates ) ? $free_rates : $rates; */
		$this->rates = $rates;
		return $rates;
	}
}
?>