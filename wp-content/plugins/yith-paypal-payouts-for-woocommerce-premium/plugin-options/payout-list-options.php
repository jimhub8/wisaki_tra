<?php
if( !defined( 'ABSPATH')){
	exit;
}

$payout_list = array(
	'payout-list' => array(

		'payouts_list' => array(
			'type'   => 'custom_tab',
			'action' => 'yith_payouts_list'
		)
	)
);

return $payout_list;