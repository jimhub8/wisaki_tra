<?php 
/***** Active Plugin ********/
require_once( get_template_directory().'/lib/class-tgm-plugin-activation.php' );

add_action( 'tgmpa_register', 'emarket_register_required_plugins' );
function emarket_register_required_plugins() {
  $plugins = array(
    array(
      'name'               => esc_html__( 'WooCommerce', 'emarket' ), 
      'slug'               => 'woocommerce', 
      'required'           => true, 
      'version'			   => '3.9.1'
      ),
    
    array(
     'name'               => esc_html__( 'Revslider', 'emarket' ), 
     'slug'               => 'revslider', 
     'source'             => get_template_directory() . '/lib/plugins/revslider.zip', 
     'required'           => true, 
     'version'            => '6.1.8'
     ),

    array(
     'name'               => esc_html__( 'Visual Composer', 'emarket' ), 
     'slug'               => 'js_composer', 
     'source'             => get_template_directory() . '/lib/plugins/js_composer.zip', 
     'required'           => true, 
     'version'            => '6.1'
     ), 
	 
	 array(
     'name'               => esc_html__( 'Elementor', 'emarket' ), 
     'slug'               => 'elementor',
     'required'           => true, 
     'version'            => '2.8.5'
     ), 
	 
	 array(
     'name'               => esc_html__( 'Elementor Pro', 'emarket' ), 
     'slug'               => 'elementor-pro', 
     'source'             => get_template_directory() . '/lib/plugins/elementor-pro.zip', 
     'required'           => true, 
     'version'            => '2.8.3'
     ), 

    array(
      'name'     		 => esc_html__( 'SW Core', 'emarket' ),
      'slug'      		 => 'sw_core',
      'source'        	 => get_template_directory() . '/lib/plugins/sw_core.zip', 
      'required'  		 => true,   
      'version'			 => '1.0.7'
      ),

    array(
      'name'     		 => esc_html__( 'SW WooCommerce', 'emarket' ),
      'slug'      		 => 'sw_woocommerce',
      'source'         	 => get_template_directory() . '/lib/plugins/sw_woocommerce.zip', 
      'required'  		 => true,
      'version'			 => '1.2.3'
      ),
	
	 array(
      'name'     		 => esc_html__( 'Sw Vendor Slider', 'emarket' ),
      'slug'      		 => 'sw_vendor_slider',
      'source'         	 => get_template_directory() . '/lib/plugins/sw_vendor_slider.zip', 
      'required'  		 => true,
      'version'			 => '1.0.7'
      ),
	  
    array(
      'name'     		 => esc_html__( 'SW Woocommerce Swatches', 'emarket' ),
      'slug'      		 => 'sw_wooswatches',
      'source'         	 => get_template_directory() . '/lib/plugins/sw_wooswatches.zip', 
      'required'  		 => true,
      'version'			 => '1.0.9'
      ),

    array(
      'name'     		 => esc_html__( 'SW Ajax Woocommerce Search', 'emarket' ),
      'slug'      		 => 'sw_ajax_woocommerce_search',
      'source'         	 => get_template_directory() . '/lib/plugins/sw_ajax_woocommerce_search.zip', 
      'required'  		 => true,
      'version'			 => '1.2.1'
      ),

    array(
      'name'     		 => esc_html__( 'Sw Product Bundles', 'emarket' ),
      'slug'      		 => 'sw-product-bundles',
      'source'         	 => get_template_directory() . '/lib/plugins/sw-product-bundles.zip', 
      'required'  		 => true,
      'version'			 => '2.0.17'
      ),

    array(
      'name'               => esc_html__( 'One Click Demo Import', 'emarket' ), 
      'slug'               => 'one-click-demo-import', 
      'source'             => get_template_directory() . '/lib/plugins/one-click-demo-import.zip', 
      'required'           => true, 
      ),

    array(
      'name'     			 => esc_html__( 'WordPress Importer', 'emarket' ),
      'slug'      		 => 'wordpress-importer',
      'required' 			 => true,
      ), 
    array(
      'name'      		 => esc_html__( 'MailChimp for WordPress Lite', 'emarket' ),
      'slug'     			 => 'mailchimp-for-wp',
      'required' 			 => false,
      ),
    array(
      'name'      		 => esc_html__( 'Contact Form 7', 'emarket' ),
      'slug'     			 => 'contact-form-7',
      'required' 			 => false,
      ),
    array(
      'name'      		 => esc_html__( 'YITH Woocommerce Compare', 'emarket' ),
      'slug'      		 => 'yith-woocommerce-compare',
      'required'			 => false
      ),
    array(
      'name'     			 => esc_html__( 'YITH Woocommerce Wishlist', 'emarket' ),
      'slug'      		 => 'yith-woocommerce-wishlist',
      'required' 			 => false
      ), 
    array(
      'name'     			 => esc_html__( 'WordPress Seo', 'emarket' ),
      'slug'      		 => 'wordpress-seo',
      'required'  		 => false,
      ),

    );
	if( emarket_options()->getCpanelValue('developer_mode') ): 
	 $plugins[] = array(
		  'name'               => esc_html__( 'Less Compile', 'emarket' ), 
		  'slug'               => 'lessphp', 
		  'source'             => get_template_directory() . '/lib/plugins/lessphp.zip', 
		  'required'           => true, 
		  'version'			 => '4.0.1'
	  );
	endif;
	$config = array();

	tgmpa( $plugins, $config );

	}
	add_action( 'vc_before_init', 'emarket_vcSetAsTheme' );
	function emarket_vcSetAsTheme() {
	  vc_set_as_theme();
}