<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Product_bundle extends WC_Product {
	
	public function __construct( $product ) {
		$this->product_type= 'bundle';
		parent::__construct( $product );
	}
	
	/**
	 * Calculate and return the price for the bundle
	 *
	 * @return string [ price ]
	 *
	 */
	public function get_category() {
		 $bundles_product = apply_filters( 'swpb/load/bundle', $product );	
		   	if( count( $bundles_product ) > 0 ) { 
               	foreach ( $bundles_product as $key => $value ) { 
                    $this->category = $key;
               	}
		   	}
		   	return $this->category;
	}
	public function get_price_html($context = 'view') {
		$price = 0;
		
			/* Sale price has not been set,
			 * So here we are going to sum
			 **/
			global $product;
			$bundles = apply_filters( 'swpb/load/bundle', $this->id );
		    $tax_enable = get_option( 'woocommerce_tax_display_shop' );
		    $suffix = $this->get_price_suffix();
			
			$producttotal = wc_get_product( $this->id );
			if( is_array( $bundles ) ) {
				foreach ( $bundles as $key => $value ) {
					if( get_post_type( $key ) == 'product_variation' ){
						$bundle = new WC_Product_Variation( $key );
						if( $tax_enable  == "incl" ) {
							$price += wc_get_price_including_tax( $bundle, array( 'qty'   => $value["quantity"], $value["price"] => $price ) );
						} else {
							$price += wc_get_price_excluding_tax( $bundle, array( 'qty'   => $value["quantity"], $value["price"] => $price ) );
						}	
						
					}else{
						$bundle = new WC_Product( $key );					
						if( $tax_enable  == "incl" ) {
							$price +=  apply_filters( 'swpb_bundle_product_price', wc_get_price_including_tax($bundle,array('qty' =>  $value["quantity"] ) ), $bundle, $value["quantity"] );
						} else {
							$price += apply_filters( 'swpb_bundle_product_price', wc_get_price_excluding_tax( $bundle,array('qty' =>  $value["quantity"] ) ), $bundle, $value["quantity"] );
						}	
					}
				}
			}
			$this->price = apply_filters( 'swpb_bundle_regular_price', $price, $this );
			$from = $this->price;

			$price = $this->get_regular_price();
			if( $tax_enable  == "incl" ) {
		        $to = wc_get_price_including_tax($producttotal);
		      
	        }else {
	        	$to = wc_get_price_excluding_tax($producttotal);
	       
	        	
	        }
	       
	         return '<del>'. ( ( is_numeric( $from ) ) ? wc_price( $from ) : $from ) .'</del> <ins>'.( ( is_numeric( $to ) ) ? wc_price( $to ) : $to ) .'</ins>'.$suffix;
			
	}
	
	/**
	 * Returns the product weight - in this case sum of bundles items weight.
	 *
	 * @return decimal
	 */
	public function get_weight($context = 'view') {
		$weight = 0;		
		if( parent::get_weight() ) {
			$weight = parent::get_weight();
		} else {		
			$bundles =  json_decode( get_post_meta( $this->id, "swpb_bundle_products", true ), true );
			if( is_array( $bundles ) ) {
				foreach ( $bundles as $key => $value ) {
					$bundle = new WC_Product( $key );
					if( $bundle->has_weight() ) {
						$weight += ( floatval( $bundle->get_weight() ) * intval( $value["quantity"] ) );
					}				
				}
			}
		}		
		return $weight;
	}
	
	/**
	 * Returns whether or not the product has weight set.
	 *
	 * @return bool
	 */
	public function has_weight() {
		return $this->get_weight() ? true : false;
	}

}

?>