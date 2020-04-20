<?php
if(!defined('ABSPATH')){
    exit;
}

$formats = yith_get_delivery_date_format();
$delivery_format_opt = array();

$tax_option = function_exists( 'wc_get_product_tax_class_options' ) ? wc_get_product_tax_class_options() : array();

foreach( $formats as $key => $format ){

	$delivery_format_opt[$key] = date( $format );
}

$settings = array(
    'general-settings' => array(
    	'delivery_mode_section_start' => array(
    		'name' => __('Delivery Settings', 'yith-woocommerce-delivery-date' ),
    		'type' => 'title'	
    	),	
    	'delivery_mode' => array(
    			'name' => __('Show DatePicker', 'yith-woocommerce-delivery-date' ),
    			'type' => 'checkbox',
    			'id' => 'ywcdd_delivery_mode',
    			'desc' => __('If checked, the datepicker is always shown on frontend', 'yith-woocommerce-delivery-date' ),
    			'default' => 'no',
    			
    	)	,
	    'general_date_format'            => array(
		    'name' => __( 'Date Format', 'yith-woocommerce-delivery-date'),
		    'desc'  => __( 'Select a date format for your datepicker', 'yith-woocommerce-delivery-date' ),
		    'id'   => 'yith_delivery_date_format',
		    'type'  => 'select',
		    'options' => $delivery_format_opt,
		    'default' => 'mm/dd/y'
	    ),
    	'time_step' => array(
    	    'name' => __( 'Time increments (minutes)', 'yith-woocommerce-delivery-date' ),
		    'type' => 'number',
		    'id' => 'ywcdd_timeslot_step',
		    'desc' => __( 'Set how users will choose the delivery time: let them choose any type of increments, from 1 minute to 1 hour. Example: If you set 30 minutes, you can create time slots from 12:00 to 12:30, etc.', 'yith-woocommerce-delivery-date' ),
		    'custom_attributes'=> array('min' => 1,'max' => 60 ),
		    'default' => 30
	    ),
    	'delivery_mode_section_end' => array(
    			'type' => 'sectionend'
    	)	,

        'timeslot_fee_section_start' => array(
        	'name' => __('Time Slot Fee Settings', 'yith-woocommerce-delivery-date' ),
	        'type' => 'title'
        ),
        /*'timeslot_fee_label' => array(
        	'name' => __('Fee Label', 'yith-woocommerce-delivery-date'),
	        'desc' => __( 'Set a label for your fees', 'yith-woocoommerce-delivery-date'),
	        'type' =>'text',
	        'id' => 'ywcdd_fee_label',
	        'default' => 'Time Slot Fee'
        ),*/
        'timeslot_fee_taxable' => array(
        	'name' => __('Fee Taxable','yith-woocommerce-delivery-date'),
	        'desc' => __( 'Enable this option to set the fee to taxable. N.B. the time slot fees are always tax excluded, so if you set a $20 fee as taxable, the final value will be $20+tax', 'yith-woocommerce-delivery-date' ),
	        'type' => 'yith-field',
	        'yith-type' => 'onoff',
	        'default' => 'no',
	        'id' => 'ywcdd_fee_is_taxable'
        ),
        'timeslot_fee_tax_class' => array(
            'name' => __( 'Tax Class', 'yith-woocommerce-delivery-date' ),
	        'desc' => __( 'Select the tax that you want to apply to time slot fees', 'yith-woocommerce-delivery-date' ),
            'type' => 'yith-field',
            'yith-type' => 'select',
            'deps' => array(
	            'id' => 'ywcdd_fee_is_taxable',
	            'value' => 'yes',
	            'type' => 'disable'
            ),
	        'options' => $tax_option ,
	        'default' => current( array_keys( $tax_option )),
	        'id' => 'ywcdd_fee_tax_class'
        ),
        'timeslot_fee_section_end' => array(
	        'type' => 'sectionend'
        ),

        'add_event_into_calendar_start' => array(
            'type' => 'title',
            'name' => __('Event Calendar settings', 'yith-woocommerce-delivery-date')
        ),
        'add_event_into_calendar' => array(
            'type' => 'yith-field',
            'yith-type' => 'select-buttons',
            'name'  => __( 'Order status', 'yith-woocommerce-delivery-date' ),
            'desc' => __('Add events to the calendar when the order is marked with one or more of the following statuses', 'yith-woocommerce-delivery-date' ),
            'default' => array( 'completed', 'processing' ),
            'options' => ywcdd_get_order_status(),
            'id' => 'ywcdd_add_event_into_calendar'

        ),
        'add_event_into_calendar_end' =>    array(
            'type' => 'sectionend'
        ),



    )
);

return $settings;