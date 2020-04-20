<?php 

/*
** Add Multi Select Param
*/
if( !function_exists( 'sw_mselect_settings_field' ) ) :
	vc_add_shortcode_param( 'multiselect', 'sw_mselect_settings_field' );
	function sw_mselect_settings_field( $settings, $value ) {
		$output = '';
		$values = explode( ',', $value );
		$output .= '<select name="'
							 . $settings['param_name']
							 . '" class="wpb_vc_param_value wpb-input wpb-select '
							 . $settings['param_name']
							 . ' ' . $settings['type']
							 . '" multiple="multiple">';
		if ( is_array( $value ) ) {
			$value = isset( $value['value'] ) ? $value['value'] : array_shift( $value );
		}
		if ( ! empty( $settings['value'] ) ) {
			foreach ( $settings['value'] as $index => $data ) {
				if ( is_numeric( $index ) && ( is_string( $data ) || is_numeric( $data ) ) ) {
					$option_label = $data;
					$option_value = $data;
				} elseif ( is_numeric( $index ) && is_array( $data ) ) {
					$option_label = isset( $data['label'] ) ? $data['label'] : array_pop( $data );
					$option_value = isset( $data['value'] ) ? $data['value'] : array_pop( $data );
				} else {
					$option_value = $data;
					$option_label = $index;
				}
				$selected = '';
				$option_value_string = (string) $option_value;
				$value_string = (string) $value;
				$selected = (is_array($values) && in_array($option_value, $values))?' selected="selected"':'';
				$option_class = str_replace( '#', 'hash-', $option_value );
				$output .= '<option class="' . esc_attr( $option_class ) . '" value="' . esc_attr( $option_value ) . '"' . $selected . '>'
									 . htmlspecialchars( $option_label ) . '</option>';
			}
		}
		$output .= '</select>';

		return $output;
	}
endif;


add_action( 'vc_before_init', 'sw_vendor_integrateVC_WC', 10 );
add_action( 'vc_before_init', 'sw_vendor_integrateVC_DK', 10 );
add_action( 'vc_before_init', 'sw_vendor_integrateVC_WCM', 10 );

/**
* Add Vc Params
**/
function sw_vendor_integrateVC_WC(){
	$terms = get_users( array( 'role' => 'vendor', 'orderby' => 'name', 'order' => 'ASC' ) );
	$term = array( __( 'Select Users', 'sw_vendor_slider' ) => 0 );
	if( count( $terms ) > 0 ){			
		foreach( $terms as $cat ){
			$term[$cat->user_login] = $cat -> ID;
		}
	}
	vc_map( array(
	  "name" => __( "Sw Vendor Slider WC Vendor", 'sw_vendor_slider' ),
	  "base" => "sw_vendor",
	  "icon" => "icon-wpb-ytc",
	  "class" => "",
	  "category" => __( "SW Shortcodes", 'sw_vendor_slider' ),
	  "params" => array(
		 array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Title", 'sw_vendor_slider' ),
			"param_name" => "title1",
			"admin_label" => true,
			"value" => '',
			"description" => __( "Title", 'sw_vendor_slider' )
		 ),
		
		array(
			'type' => 'attach_images',
			'heading' => __( 'Select Image', 'sw_vendor_slider' ),
			'param_name' => 'image',
			'description' => __( 'Select Image', 'sw_vendor_slider' ),
			'dependency' => array(
				'element' => 'layout',
				'value' => array( 'theme1'),
			)
		),
		
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Banner Links", 'sw_woocommerce' ),
			"param_name" => "banner_links",
			"value" => '',
			"description" => __( "Each banner link seperate by commas.", 'sw_woocommerce' ),
			"dependency" => array( 
				'element' => 'layout',
				'value' => array( 'theme1'),
				)
			),		
		 array(
			"type" => "multiselect",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Users", 'sw_vendor_slider' ),
			"param_name" => "category",
			"admin_label" => true,
			"value" => $term,
			"description" => __( "Select Categories", 'sw_vendor_slider' )
		 ),
		 
		 array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Number Of Post", 'sw_vendor_slider' ),
			"param_name" => "numberposts",
			"admin_label" => true,
			"value" => 5,
			"description" => __( "Number Of Post", 'sw_vendor_slider' )
		 ),
		 
		 array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Description Length", 'sw_vendor_slider' ),
			"param_name" => "length",
			"admin_label" => true,
			"value" => 0,
			"description" => __( "Number word of description to show", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Number row per column", 'sw_vendor_slider' ),
			"param_name" => "item_row",
			"admin_label" => true,
			"value" =>array(1,2,3),
			"description" => __( "Number row per column", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Number of Columns >1200px: ", 'sw_vendor_slider' ),
			"param_name" => "columns",
			"admin_label" => true,
			"value" => array(1,2,3,4,5,6,7,8,9,10),
			"description" => __( "Number of Columns >1200px:", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Number of Columns on 992px to 1199px:", 'sw_vendor_slider' ),
			"param_name" => "columns1",
			"admin_label" => true,
			"value" => array(1,2,3,4,5,6,7,8),
			"description" => __( "Number of Columns on 992px to 1199px:", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Number of Columns on 768px to 991px:", 'sw_vendor_slider' ),
			"param_name" => "columns2",
			"admin_label" => true,
			"value" => array(1,2,3,4,5,6),
			"description" => __( "Number of Columns on 768px to 991px:", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Number of Columns on 480px to 767px:", 'sw_vendor_slider' ),
			"param_name" => "columns3",
			"admin_label" => true,
			"value" => array(1,2,3,4,5,6),
			"description" => __( "Number of Columns on 480px to 767px:", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Number of Columns in 480px or less than:", 'sw_vendor_slider' ),
			"param_name" => "columns4",
			"admin_label" => true,
			"value" => array(1,2,3,4,5,6),
			"description" => __( "Number of Columns in 480px or less than:", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Speed", 'sw_vendor_slider' ),
			"param_name" => "speed",
			"admin_label" => true,
			"value" => 1000,
			"description" => __( "Speed Of Slide", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Auto Play", 'sw_vendor_slider' ),
			"param_name" => "autoplay",
			"admin_label" => true,
			"value" => array( 'False' => 'false', 'True' => 'true' ),
			"description" => __( "Auto Play", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Interval", 'sw_vendor_slider' ),
			"param_name" => "interval",
			"admin_label" => true,
			"value" => 5000,
			"description" => __( "Interval", 'sw_vendor_slider' )
		 ),
		  array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Layout", 'sw_vendor_slider' ),
			"param_name" => "layout",
			"admin_label" => true,
			"value" => array( 'Layout Default' => 'default', 'Layout Theme1' => 'theme1' ),
			"description" => __( "Layout", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Total Items Slided", 'sw_vendor_slider' ),
			"param_name" => "scroll",
			"admin_label" => true,
			"value" => 1,
			"description" => __( "Total Items Slided", 'sw_vendor_slider' )
		 ),
	  )
   ) );
}

/**
* Add Vc Params
**/
function sw_vendor_integrateVC_DK(){
	$terms = get_users( array( 'role' => 'seller', 'orderby' => 'name', 'order' => 'ASC' ) );
	$term = array( __( 'Select Users', 'sw_vendor_slider' ) => 0 );
	if( count( $terms ) > 0 ){			
		foreach( $terms as $cat ){
			$term[$cat->user_login] = $cat -> ID;
		}
	}
	vc_map( array(
	  "name" => __( "Sw Vendor Slider Dokan Vendor", 'sw_vendor_slider' ),
	  "base" => "sw_vendor_dokan",
	  "icon" => "icon-wpb-ytc",
	  "class" => "",
	  "category" => __( "SW Shortcodes", 'sw_vendor_slider' ),
	  "params" => array(
		 array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Title", 'sw_vendor_slider' ),
			"param_name" => "title1",
			"admin_label" => true,
			"value" => '',
			"description" => __( "Title", 'sw_vendor_slider' )
		 ),						 
		 array(
			"type" => "multiselect",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Users", 'sw_vendor_slider' ),
			"param_name" => "category",
			"admin_label" => true,
			"value" => $term,
			"description" => __( "Select Categories", 'sw_vendor_slider' )
		 ),
		 
		 array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Number Of Post", 'sw_vendor_slider' ),
			"param_name" => "numberposts",
			"admin_label" => true,
			"value" => 5,
			"description" => __( "Number Of Post", 'sw_vendor_slider' )
		 ),
		 
		 array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Description Length", 'sw_vendor_slider' ),
			"param_name" => "length",
			"admin_label" => true,
			"value" => 0,
			"description" => __( "Number word of description to show", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Number row per column", 'sw_vendor_slider' ),
			"param_name" => "item_row",
			"admin_label" => true,
			"value" =>array(1,2,3),
			"description" => __( "Number row per column", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Number of Columns >1200px: ", 'sw_vendor_slider' ),
			"param_name" => "columns",
			"admin_label" => true,
			"value" => array(1,2,3,4,5,6,7,8,9,10),
			"description" => __( "Number of Columns >1200px:", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Number of Columns on 992px to 1199px:", 'sw_vendor_slider' ),
			"param_name" => "columns1",
			"admin_label" => true,
			"value" => array(1,2,3,4,5,6,7,8),
			"description" => __( "Number of Columns on 992px to 1199px:", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Number of Columns on 768px to 991px:", 'sw_vendor_slider' ),
			"param_name" => "columns2",
			"admin_label" => true,
			"value" => array(1,2,3,4,5,6),
			"description" => __( "Number of Columns on 768px to 991px:", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Number of Columns on 480px to 767px:", 'sw_vendor_slider' ),
			"param_name" => "columns3",
			"admin_label" => true,
			"value" => array(1,2,3,4,5,6),
			"description" => __( "Number of Columns on 480px to 767px:", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Number of Columns in 480px or less than:", 'sw_vendor_slider' ),
			"param_name" => "columns4",
			"admin_label" => true,
			"value" => array(1,2,3,4,5,6),
			"description" => __( "Number of Columns in 480px or less than:", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Speed", 'sw_vendor_slider' ),
			"param_name" => "speed",
			"admin_label" => true,
			"value" => 1000,
			"description" => __( "Speed Of Slide", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Auto Play", 'sw_vendor_slider' ),
			"param_name" => "autoplay",
			"admin_label" => true,
			"value" => array( 'False' => 'false', 'True' => 'true' ),
			"description" => __( "Auto Play", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Interval", 'sw_vendor_slider' ),
			"param_name" => "interval",
			"admin_label" => true,
			"value" => 5000,
			"description" => __( "Interval", 'sw_vendor_slider' )
		 ),
		  array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Layout", 'sw_vendor_slider' ),
			"param_name" => "layout",
			"admin_label" => true,
			"value" => array( 'Layout Default' => 'default' ),
			"description" => __( "Layout", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Total Items Slided", 'sw_vendor_slider' ),
			"param_name" => "scroll",
			"admin_label" => true,
			"value" => 1,
			"description" => __( "Total Items Slided", 'sw_vendor_slider' )
		 ),
	  )
   ) );
}

/**
* Add Vc Params WC MarketPlace
**/
function sw_vendor_integrateVC_WCM(){
	$terms = get_users( array( 'role' => 'dc_vendor', 'orderby' => 'name', 'order' => 'ASC' ) );
	$term = array( __( 'Select Users', 'sw_vendor_slider' ) => 0 );
	if( count( $terms ) > 0 ){			
		foreach( $terms as $cat ){
			$term[$cat->user_login] = $cat -> ID;
		}
	}
	vc_map( array(
	  "name" => __( "Sw Vendor Slider WC MarketPlace", 'sw_vendor_slider' ),
	  "base" => "sw_vendor_wcm",
	  "icon" => "icon-wpb-ytc",
	  "class" => "",
	  "category" => __( "SW Shortcodes", 'sw_vendor_slider' ),
	  "params" => array(
		 array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Title", 'sw_vendor_slider' ),
			"param_name" => "title1",
			"admin_label" => true,
			"value" => '',
			"description" => __( "Title", 'sw_vendor_slider' )
		 ),						 
		 array(
			"type" => "multiselect",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Users", 'sw_vendor_slider' ),
			"param_name" => "category",
			"admin_label" => true,
			"value" => $term,
			"description" => __( "Select Categories", 'sw_vendor_slider' )
		 ),
		 
		 array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Number Of Post", 'sw_vendor_slider' ),
			"param_name" => "numberposts",
			"admin_label" => true,
			"value" => 5,
			"description" => __( "Number Of Post", 'sw_vendor_slider' )
		 ),
		 
		 array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Description Length", 'sw_vendor_slider' ),
			"param_name" => "length",
			"admin_label" => true,
			"value" => 0,
			"description" => __( "Number word of description to show", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Number row per column", 'sw_vendor_slider' ),
			"param_name" => "item_row",
			"admin_label" => true,
			"value" =>array(1,2,3),
			"description" => __( "Number row per column", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Number of Columns >1200px: ", 'sw_vendor_slider' ),
			"param_name" => "columns",
			"admin_label" => true,
			"value" => array(1,2,3,4,5,6,7,8,9,10),
			"description" => __( "Number of Columns >1200px:", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Number of Columns on 992px to 1199px:", 'sw_vendor_slider' ),
			"param_name" => "columns1",
			"admin_label" => true,
			"value" => array(1,2,3,4,5,6,7,8),
			"description" => __( "Number of Columns on 992px to 1199px:", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Number of Columns on 768px to 991px:", 'sw_vendor_slider' ),
			"param_name" => "columns2",
			"admin_label" => true,
			"value" => array(1,2,3,4,5,6),
			"description" => __( "Number of Columns on 768px to 991px:", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Number of Columns on 480px to 767px:", 'sw_vendor_slider' ),
			"param_name" => "columns3",
			"admin_label" => true,
			"value" => array(1,2,3,4,5,6),
			"description" => __( "Number of Columns on 480px to 767px:", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Number of Columns in 480px or less than:", 'sw_vendor_slider' ),
			"param_name" => "columns4",
			"admin_label" => true,
			"value" => array(1,2,3,4,5,6),
			"description" => __( "Number of Columns in 480px or less than:", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Speed", 'sw_vendor_slider' ),
			"param_name" => "speed",
			"admin_label" => true,
			"value" => 1000,
			"description" => __( "Speed Of Slide", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Auto Play", 'sw_vendor_slider' ),
			"param_name" => "autoplay",
			"admin_label" => true,
			"value" => array( 'False' => 'false', 'True' => 'true' ),
			"description" => __( "Auto Play", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Interval", 'sw_vendor_slider' ),
			"param_name" => "interval",
			"admin_label" => true,
			"value" => 5000,
			"description" => __( "Interval", 'sw_vendor_slider' )
		 ),
		  array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Layout", 'sw_vendor_slider' ),
			"param_name" => "layout",
			"admin_label" => true,
			"value" => array( 'Layout Default' => 'default' ),
			"description" => __( "Layout", 'sw_vendor_slider' )
		 ),
		 array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __( "Total Items Slided", 'sw_vendor_slider' ),
			"param_name" => "scroll",
			"admin_label" => true,
			"value" => 1,
			"description" => __( "Total Items Slided", 'sw_vendor_slider' )
		 ),
	  )
   ) );
}
?>