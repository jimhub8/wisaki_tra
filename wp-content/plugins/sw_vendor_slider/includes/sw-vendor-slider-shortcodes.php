<?php
/*
** Name: Shorcodes of vendor slider
** Author: WpThemeGo
*/

class SW_Vendor_Shortcode {
	private $snumber = 1;
	
	function __construct(){
		add_shortcode( 'sw_vendor', array( $this, 'sw_vendor_shortcode_callback' ) );
		add_shortcode( 'sw_vendor_dokan', array( $this, 'sw_vendor_dk_shortcode_callback' ) );
		add_shortcode( 'sw_vendor_wcm', array( $this, 'sw_vendor_wcm_shortcode_callback' ) );
	}
	
	/*
	** Generate ID
	*/
	public function generateID() {
		return'sw_vendor_slider_' . (int) $this->snumber++;
	}
	
	function sw_vendor_shortcode_callback( $atts, $content = '' ){
		extract( shortcode_atts(
			array(
				'title1' => '',
				'category' => 0,
				'image' => '',
				'banner_links' => '',
				'length' => 0,
				'item_row'=> 1,
				'columns' => 4,
				'columns1' => 4,
				'columns2' => 3,
				'columns3' => 2,
				'columns4' => 1,
				'speed' => 1000,
				'autoplay' => 'false',
				'interval' => 5000,
				'scroll' => 1,			
				'layout'  => 'default'
			), $atts )
		);
		ob_start();
		if( $layout == 'theme1'){
			include( sw_vendor_override_check( 'themes', 'theme1' ) );
		}
		else{
			include( sw_vendor_override_check( 'themes', 'default' ) );
		}
		$content = ob_get_clean();
		return $content;
	}
	
	function sw_vendor_dk_shortcode_callback( $atts, $content = '' ){
		extract( shortcode_atts(
			array(
				'title1' => '',
				'category' => 0,
				'length' => 0,
				'item_row'=> 1,
				'columns' => 4,
				'columns1' => 4,
				'columns2' => 3,
				'columns3' => 2,
				'columns4' => 1,
				'speed' => 1000,
				'autoplay' => 'false',
				'interval' => 5000,
				'scroll' => 1,			
				'layout'  => 'default'
			), $atts )
		);
		ob_start();
		include( sw_vendor_override_check( 'themes', 'default-dokan' ) );
		$content = ob_get_clean();
		return $content;
	}
	
	function sw_vendor_wcm_shortcode_callback( $atts, $content = '' ){
		extract( shortcode_atts(
			array(
				'title1' => '',
				'category' => 0,
				'length' => 0,
				'item_row'=> 1,
				'columns' => 4,
				'columns1' => 4,
				'columns2' => 3,
				'columns3' => 2,
				'columns4' => 1,
				'speed' => 1000,
				'autoplay' => 'false',
				'interval' => 5000,
				'scroll' => 1,			
				'layout'  => 'default'
			), $atts )
		);
		ob_start();
		include( sw_vendor_override_check( 'themes', 'default-wcm' ) );
		$content = ob_get_clean();
		return $content;
	}
}
new SW_Vendor_Shortcode();