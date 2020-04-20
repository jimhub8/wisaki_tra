<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*return apply_filters(
    'yith_wcdd_general_calendar_options',
    array(
        'general-calendar' => array(
            'general_calendar' => array(
                'type' => 'custom_tab',
                'action' => 'yith_wcdd_general_calendar_tab',
                'hide_sidebar'  => true
            )

        )
    )
);*/

$calendar_settings = array(
	'general-calendar' => array(
		'calendar_holidays_section_start' => array(
			'name' => __( 'Holidays', 'yith-woocommerce-delivery-date' ),
			'type' => 'title'
		),
		'calendar_holidays_option' => array(
			'id' => 'ywcdd_holidays_option',
			'type' => 'holidays'
		),
		'calendar_holidays_section_end' => array(
			'type' => 'sectionend'
		),
		'calendar_section_start' => array(
			'name' => __( 'Calendar', 'yith-woocommerce-delivery-date' ),
			'type' => 'title'
		),
		'calendar_display' => array(
			'type' => 'calendar'
		),
		'calendar_section_end' => array(
			'type' => 'sectionend'
		),
		'color_label_section_start' => array(
			'name' => __('Calendar Customization', 'yith-woocommerce-delivery-date'),
			'type' => 'title',
		),
		'calendar_color_shipp' => array(
			'name'=>__('Shipping Event Color','yith-woocommerce-delivery-date'),
			'type'=> 'color',
			'id' => 'ywcdd_shipping_to_carrier_color',
			'default' => '#ff643e'
		)		,
		'calendar_color_delivery' => array(
			'name'=>__('Delivery Event Color','yith-woocommerce-delivery-date'),
			'type'=> 'color',
			'id' => 'ywcdd_delivery_day_color',
			'default' => '#a3c401'
		)		,
		'calendar_color_holiday' => array(
			'name'=>__('Holiday Event Color','yith-woocommerce-delivery-date'),
			'type'=> 'color',
			'id' => 'ywcdd_holiday_color',
			'default' => '#1197C1'
		),
		'color_label_section_end' =>    array(
			'type' => 'sectionend'
		),

	)
);

return $calendar_settings;