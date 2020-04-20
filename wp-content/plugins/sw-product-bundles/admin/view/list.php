<?php 

if ( ! defined( 'ABSPATH' ) ) { exit; }

class SWPB_List_Bundles {
	function __construct() {	
		add_filter( 'swpb/build/products_search', array( $this, 'build_products_search_list' ) );		
		add_filter( 'swpb_included_products', array( $this, 'build_swpb_included_products' ), 10, 2 );
	}
	function build_products_search_list( $search ) {
		$bundles = array();
		$bmeta = get_post_meta( swpb()->request['post'], "sw_product_bundles", true );
		if( $bmeta ) {
			$bmeta = json_decode( $bmeta, true );
			if( is_array( $bundles ) ) {
				foreach ( $bmeta as $key => $value ) {
					$bundles[] = $key;
				}				
			} 
		}				
		$products = apply_filters( "swpb/search/products", $search );
		//var_dump($products);
		$html = '<div class="swpb-auto-complete-results-box">';
		$html .= '<ul class="swpb-auto-complete-ul">';		
		foreach ( $products as $product ) {
			if( !in_array( $product->ID, $bundles ) ) {
				$p = wc_setup_product_data( $product );
				if( $p->product_type == "simple" ) {
					$html .= '<li class="swpb-product"><a href="#" product_id="'. $product->ID .'" product_type="simple" title="'. esc_attr( $product->post_title ) .'">#'. $product->ID .' - '. esc_html( $product->post_title ) .'</a></li>';
				} else if( $p->product_type == "variable" ) {
					$variations = array();						
					$args = array(
							'post_parent'  => $product->ID,
							'post_type'   => 'product_variation',
							'orderby'     => 'menu_order',
							'order'       => 'ASC',
							'fields'      => 'ids, title',
							'post_status' => 'publish',
							'numberposts' => -1
					);
					$childposts = get_posts( $args );						
					foreach ( $childposts as $child ) {
						if( !in_array( $child->ID, $bundles ) ) {
							$variation = wc_get_product( $child );
							if ( ! $variation->exists() ) {
								continue;
							}
							$index = 0;
							$title = "";
							$attributes = swpb_utils::get_variable_attributes( $variation );
							foreach ( $attributes as $attr ) {
								if( $index == 0 ) {
									$title .= 'with '. $attr["option"];
								} else {
									$title .= ', '. $attr["option"];
								}
								$index++;
							}
							$html .= '<li class="swpb-product"><a href="#" product_id="'. $variation->get_variation_id() .'" product_type="simple" title="'. esc_attr( trim( $title ) ) .'">#'. $variation->get_variation_id() .' - '. esc_html( $product->post_title ) .' '. esc_html( trim( $title ) ) .'</a></li>';
						}						
					}
				}	
			}				
		}
		$html .= '</ul>';
		$html .= '</div>';
		return $html;		
	}
	function build_swpb_included_products( $product, $bundles = null ) {
		$html = '';
		$newly_added = ( is_array( $bundles ) ) ? $bundles : array();
	    $bundles_product = apply_filters( 'swpb/load/bundle', $product );	
	  
		ob_start();
		if( $bundles_product ) {
			foreach ( $bundles_product as $key => $value ) { 
				
				if( ( $bundles == null ) || in_array( $key, $newly_added ) ) {		
				
					$p = wc_setup_product_data( $key );
					?>			
					<div class="swpb-product-bundle-row wc-metabox" product_id="<?php echo $key; ?>">
						<h3 class="swpb-product-bundle-row-header">
							<strong>#<?php echo esc_html( $key ); ?> <?php echo esc_html( $value['title'] );?> - <?php echo esc_html( $p->get_type() ); ?></strong>
							<a href="#" title="<?php _e( 'Remove', 'sw_product_bundles' ); ?> <?php echo esc_attr( $value['title'] ); ?> <?php _e( 'from bundle', 'sw_product_bundles' ); ?>" product_id="<?php echo esc_attr( $key ); ?>" class="button swpb-remove-bundle-btn"><?php _e( 'Remove', 'sw_product_bundles' ); ?></a>
						</h3>
						<div class="swpb-wc-metabox-content">
							<?php woocommerce_wp_checkbox( array( 'id' => 'swpb_bundle_product_'. $key .'_thumbnail', 'label' => __( 'Show Thumbnail', 'sw_product_bundles' ), 'value' => 'yes', 'cbvalue' => $value['thumbnail'], 'desc_tip' => 'true', 'description' => __( 'Check if you want to show the thumbnail of this product.', 'sw_product_bundles' ) ) ); ?>
							<?php woocommerce_wp_checkbox( array( 'id' => 'swpb_bundle_product_'. $key .'_category', 'label' => __( 'Show Category', 'sw_product_bundles' ), 'value' => 'yes', 'cbvalue' => $value['category'], 'desc_tip' => 'true', 'description' => __( 'Check if you want to show the category of this product.', 'sw_product_bundles' ) ) ); ?>
							<?php woocommerce_wp_checkbox( array( 'id' => 'swpb_bundle_product_'. $key .'_tax_included', 'label' => __( 'Include Tax', 'sw_product_bundles' ), 'value' => 'yes', 'cbvalue' => $value['tax_included'], 'desc_tip' => 'true', 'description' => __( 'Check if you want to include the tax with price.', 'sw_product_bundles' ) ) ); ?>							
							<?php woocommerce_wp_text_input( array( 'id' => 'swpb_bundle_product_'. $key .'_price', 'class' => 'swpb-bundle-product-admin-price', 'label' => __( 'Price', 'sw_product_bundles' ), 'value' => $value['price'], 'desc_tip' => 'true', 'description' => __( 'Give a special price. If you leave it blank, product\'s regular price will be used instead', 'woocommerce' ) ) ); ?>	
							<?php woocommerce_wp_text_input( array( 'id' => 'swpb_bundle_product_'. $key .'_quantity', 'label' => __( 'Quantity', 'sw_product_bundles' ), 'value' => $value['quantity'], 'desc_tip' => 'true', 'description' => __( 'How many quantity should be added to the bundle. If you leave it blank, by default it will be 1', 'sw_product_bundles' ) ) ); ?>	
							<?php woocommerce_wp_text_input( array( 'id' => 'swpb_bundle_product_'. $key .'_title', 'label' => __( 'Title', 'sw_product_bundles' ), 'value' => $value['title'], 'desc_tip' => 'true', 'description' => __( 'Give a special title, otherwise original title will be used instead.', 'sw_product_bundles' ) ) ); ?>	
						</div>
					</div>
					<?php	
				}	
			}	
		} else { ?>
			<div class="swpb-empty-msg">
				<p><?php _e( 'Search for products, select as many as product you want and add those to bundle using "Add Products". Only "Simple" or "variable" product are allowed to add. You can also drag drop to re arrange the order of bundled products in product page.!', 'sw_product_bundles' ); ?></p>
			</div>
		<?php
		}
		return ob_get_clean();		
	}
}

new SWPB_List_Bundles();

?>