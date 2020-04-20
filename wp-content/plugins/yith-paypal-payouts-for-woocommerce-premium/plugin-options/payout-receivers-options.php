<?php
if( !defined( 'ABSPATH' ) ){
	exit;
}

$payout_receivers = array(

	'payout-receivers' => array(

		'payout_receivers_section_start' => array(
			'type' => 'sectionstart'
		),
		'payout_receivers_section_title' => array(
			'type' => 'title',
			'name' => __( 'Receivers List', 'yith-paypal-payouts-for-woocommerce' )
		),

		'payout_receivers_list' => array(
			'type' => 'yith_payouts_receiver_list',
			'label' => '',
			'id' => 'yith_payouts_receiver_list',
			'default' => array()
		),
		'payout_receivers_section_end' => array(
			'type' => 'sectionend'
		)
	)
);

return $payout_receivers;