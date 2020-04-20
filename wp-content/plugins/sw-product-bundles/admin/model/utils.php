<?php 

class SWPB_Utils {
	
	public static function get_swpb_meta( $pid, $key, $default = "" ) {
		$val = get_post_meta( $pid, $key, true );
		return ( $val == "" ) ? $default : $val;
	}
	
	public static function get_variable_attributes( $variant ) {
		$attributes = array();
		foreach ( $variant->get_variation_attributes() as $attribute_name => $attribute ) {
			// taxonomy-based attributes are prefixed with `pa_`, otherwise simply `attribute_`
			$attributes[] = array (					
				'name'   => esc_attr( sanitize_title( ucwords( str_replace( 'attribute_', '', str_replace( 'pa_', '', $attribute_name ) ) ) ) ),
				'option' => esc_attr( sanitize_title( $attribute ) )
			);
		}
		return $attributes;
	}
	
}
?>