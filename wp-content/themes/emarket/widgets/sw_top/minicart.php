<?php 
	do_action( 'before' ); 
?>
<?php if ( class_exists( 'WooCommerce' ) && !emarket_options()->getCpanelValue( 'disable_cart' ) ) { ?>
<?php
	$emarket_page_header = ( get_post_meta( get_the_ID(), 'page_header_style', true ) != '' ) ? get_post_meta( get_the_ID(), 'page_header_style', true ) : emarket_options()->getCpanelValue('header_style');
	if( $emarket_page_header == 'style8' ||  $emarket_page_header == 'style9' || $emarket_page_header == 'style10' ){
		get_template_part( 'woocommerce/minicart-ajax-style3' ); 
	}
	elseif( $emarket_page_header == 'style6' ){
		get_template_part( 'woocommerce/minicart-ajax-style2' ); 
	}else{
		get_template_part( 'woocommerce/minicart-ajax' ); 
	}
	
?>
<?php } ?>