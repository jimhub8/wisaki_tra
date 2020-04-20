<?php
if( !defined('ABSPATH')){
    exit;
}

$meta_boxes_options = array(
    'label' => __( 'Time Slots', 'yith-woocommerce-delivery-date' ),
    'pages' => 'yith_carrier', //or array( 'post-type1', 'post-type2')
    'context' => 'normal', //('normal', 'advanced', or 'side')
    'priority' => 'default',
    'tabs' => array(
        'time_slot_settings' => array(
            'label' => __('Time Slot', 'yith-woocommerce-delivery-date'),
            'fields' => array(
                'ywcdd_addtimeslot' => array(
                    'label' => __('Add time slot', 'yith-woocommerce-delivery-date'),
                    'type' => 'addtimeslot',
                    'desc' => ''
                    ),
                )

        )
    )
);

return $meta_boxes_options;