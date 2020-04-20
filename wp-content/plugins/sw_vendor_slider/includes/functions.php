<?php
/*
** Name: Functions of vendor slider
** Author: WpThemeGo
*/

include_once( SVTHEME . '/sw-vendor-slider-widget.php' );
include_once( SVTHEME . '/sw-vendor-slider-shortcodes.php' );
if ( class_exists( 'Vc_Manager' ) ) {
	require_once ( SVTHEME . '/visual-map.php' );
}

function sw_vendor_override_check( $path, $file ){
	$paths = '';
	if( locate_template( 'sw_vendor_slider/'.$path . '/' . $file ) ){
		$paths = get_template_part( 'sw_vendor_slider/' . $path . '/' . $file );
	}else{
		$paths = SVTHEME . '/' . $path . '/' . $file . '.php';
	}
	return $paths;
}