<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings = array(
	'processing-method' => array(
		'processing_method_tab' => array(
			'type'   => 'custom_tab',
			'action' => 'ywcdd_show_processing_method_tab'
		)
	)

);

return $settings;