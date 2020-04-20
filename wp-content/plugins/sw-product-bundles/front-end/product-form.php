<?php 

if ( ! defined( 'ABSPATH' ) ) { exit; }

class SWPB_Product_Form {
	
	var $product_type = "bundle";
	
	function __construct() {		
		add_action( 'woocommerce_bundle_add_to_cart', array( $this, 'swpb_product_form' ), 10 );
		add_action( 'woocommerce_add_to_cart_handler_'.$this->product_type, array( $this, 'swpb_add_to_cart' ) );
		add_filter( 'woocommerce_cart_item_name', array( $this, 'swpb_render_bundle_on_cart' ), 1, 3 );
		add_filter( 'woocommerce_checkout_cart_item_quantity', array( $this, 'swpb_render_bundle_on_order_review' ), 1, 3 );	
		add_action( 'woocommerce_add_order_item_meta', array( $this, 'swpb_add_bundle_as_order_meta' ), 1, 3 );
		add_action( 'woocommerce_reduce_order_stock', array( $this, 'swpb_sync_bundle_stocks' ) );
		add_action( 'woocommerce_order_status_cancelled', array( $this, 'swpb_re_sync_bundle_stocks' ) );
		add_filter( 'woocommerce_sale_flash', array( $this, 'swpb_add_combo_pack_label' ), 10, 2 );		
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'swpb_add_to_cart_bundle_validation' ), 10, 3 );
		add_filter ( 'wc_add_to_cart_message_html',  array( $this,'wc_add_to_cart_message_html_filter'), 10, 2 );
	}
	
	function swpb_product_form() { 
		global $product;				
		if( swpb_utils::get_swpb_meta( $product->get_id(), '_swpb_show_bundle_on_product', 'yes' ) == "yes" ) {		
	?>
	
		<div class="swpb-bundled-products-container">
			<?php $bundles = apply_filters( 'swpb/load/bundle', $product->get_id() ); ?>
			
			<?php if( has_action( 'swpb/bundle/rendering' ) ) { 

				do_action( 'swpb/bundle/rendering', $bundles );
				
			} else { ?>
			
			<?php do_action( 'swpb/bundle/before/main/content/rendering' ); ?>		
			
			<?php foreach ( $bundles as $key => $value ) : ?>

				<?php $bundle = wc_get_product( $key ); 
			
					$product_url = "";
					if ( get_post_type( $key ) == 'product_variation' ) {
						$product_url = get_the_permalink( wp_get_post_parent_id( $key ) );
					} else {
						$product_url = get_the_permalink( $key );
					}
				?>
				
				<?php do_action( 'swpb/bundle/before/product/content/rendering', $bundle ); ?>
				
				<div class="swpb-bundled-product">
					<div class="swpb-bundled-content clearfix">
						<!-- bundled product's thumbnail section -->
						<?php if( $value["thumbnail"] == "yes" ) : ?>				
						<div class="swpb-thumbnail pull-left">				
							<a href="<?php echo esc_url( $product_url ); ?>" title="<?php echo esc_attr( $value['title'] ); ?>" class="swpb-featured"><?php echo $bundle->get_image( 'thumbnail' ); ?></a>		
						</div>
						<?php endif; ?>
						<!-- bundled product's summary section -->
						<div class="swpb-info">
							<?php if( $value["category"] == "yes" ) : 
							echo '<div class="category">';
							echo get_the_term_list( $key, 'product_cat','<span>',',', '</span>' ); 
							echo '</div>';
							?>
							<?php endif; ?>			
							<h3><a href="<?php echo esc_url( $product_url ); ?>" class="swpb-bundled-product-title"><?php echo esc_html( $value['quantity'] ) ." x ". esc_html( $value['title'] ); ?></a></h3>
							<div class="price"><?php echo $bundle->get_price_html();?></div>
							<p class="swpb-bundled-product-stock">
								<?php 
								if( $bundle->get_stock_status() == "instock" ) {
									echo '<span class="swpb-in-stock-label">'. apply_filters( 'swpb/bundle/instock/label', __( 'instock', 'sw_product_bundles' ) ) .'</span>';
								} else {
									echo '<span class="swpb-out-of-stock-label">'.  apply_filters( 'swpb/bundle/outofstock/label', __( 'out of stock', 'sw_product_bundles' ) )  .'</span>'; 
								}
								?>
							</p>
						</div>
					</div>
				</div>
				
				<?php do_action( 'swpb/bundle/after/product/content/rendering', $bundle ); ?>
				
			<?php endforeach; ?>
			
			<?php do_action( 'swpb/bundle/after/main/content/rendering' ); ?>
			
			<?php 
			
			}
			
			?>
			
		</div>
		
		<?php 
		
		}
		
		?>
		
		<form class="cart" method="post" enctype='multipart/form-data'>
		 	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
	
		 	<?php
		 		if ( ! $product->is_sold_individually() )
		 			woocommerce_quantity_input( array(
		 				'min_value' => apply_filters( 'woocommerce_quantity_input_min', 1, $product ),
		 				'max_value' => apply_filters( 'woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product )
		 			) );
		 	?>
	
		 	<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" />
		 	<button type="submit" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
			<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
		</form>
	
	<?php 
	}

    function wc_add_to_cart_message_html_filter( $message, $products ) {
    	$titles = array();
		$count  = 0;

		if ( ! is_array( $products ) ) {
			$products = array( $products => 1 );
			$show_qty = false;
		}

		if ( ! $show_qty ) {
			$products = array_fill_keys( array_keys( $products ), 1 );
		}

		foreach ( $products as $product_id => $qty ) {
			$titles[] = apply_filters( 'woocommerce_add_to_cart_qty_html', ( $qty > 1 ? absint( $qty ) . ' &times; ' : '' ), $product_id ) . apply_filters( 'woocommerce_add_to_cart_item_name_in_quotes', sprintf( _x( '&ldquo;%s&rdquo;', 'Item name in quotes', 'woocommerce' ), strip_tags( get_the_title( $product_id ) ) ), $product_id );
			$count   += $qty;
		}

		$titles = array_filter( $titles );
		$added_text = sprintf( _n( '%s has been added to your cart.', '%s have been added to your cart.', sizeof( $titles ), 'woocommerce' ), wc_format_list_of_items( $titles ) );

		$message = sprintf( '%s <a href="%s" class="button">%s</a>',
		               esc_html( $added_text ),
		               esc_url( wc_get_page_permalink( 'cart' ) ),
		               esc_html__( 'View Cart', 'sw_product_bundles' ));

		return $message;

	   
	}

	function swpb_add_to_cart() { 
		$was_added_to_cart   = false;
		$product_id         = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_REQUEST['add-to-cart'] ) );
		$quantity 			= empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( $_REQUEST['quantity'] );		
		$passed_validation 	= apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
		
		if ( $passed_validation ) {
			if ( WC()->cart->add_to_cart( $product_id, $quantity ) ) {
				$was_added_to_cart = true;
				$added_to_cart[] = $product_id;
			}
		}
		
		if ( $was_added_to_cart ) {
			wc_add_to_cart_message( $added_to_cart );
		}
		
		if ( ! $was_added_to_cart ) {			
			return;
		}
		
		// If we added the product to the cart we can now optionally do a redirect.
		if ( $was_added_to_cart && wc_notice_count( 'error' ) == 0 ) {
			$url = '';
		
			$url = apply_filters( 'woocommerce_add_to_cart_redirect', $url );
		
			// If has custom URL redirect there
			if ( $url ) {
				wp_safe_redirect( $url );
				exit;
			}
		
			// Redirect to cart option
			elseif ( get_option('woocommerce_cart_redirect_after_add') == 'yes' ) {
				wp_safe_redirect( WC()->cart->get_cart_url() );
				exit;
			}		
		}
	}
	
	function swpb_render_bundle_on_cart( $title = null, $cart_item = null, $cart_item_key = null ) {				
		if( is_cart() ) {
			return $this->swpb_render_bundle_item( $title, $cart_item );
		}
		return $title;		
	}
	
	function swpb_render_bundle_on_order_review( $quantity = null, $cart_item = null, $cart_item_key = null ) {		
		return $this->swpb_render_bundle_item( $quantity, $cart_item );										
	}
	
	function swpb_render_bundle_item( $html, $cart_item ) {
		if( isset( $cart_item['product_id'] ) ) {
			$terms        = get_the_terms( $cart_item['product_id'], 'product_type' );
			$product_type = ! empty( $terms ) ? sanitize_title( current( $terms )->name ) : 'simple';
			if( $product_type == "bundle" ) {				
				if( swpb_utils::get_swpb_meta( $cart_item['product_id'], '_swpb_show_bundle_on_cart', 'yes' ) == "yes" ) {				
					$bundles =  json_decode( get_post_meta( $cart_item['product_id'], "sw_product_bundles", true ), true );
					if( has_filter( 'swpb/bundle/item/rendering' ) ) {
						$html .= apply_filters( 'swpb/bundle/item/rendering', $bundles );
					} else {
						$html .= '<dl class="swpb-cart-item-container">';
						$html .= '<dt>'. apply_filters( 'swpb/bundle/item/title', __( 'Bundle Includes', 'sw_product_bundles' ) ) .'</dt>';
						$html .= '<dd>';
						foreach ( $bundles as $key => $bundle ) {
							if ( get_post_type( $key ) == 'product_variation' ) {							
								$html .= '<div>'. $bundle['quantity'] .' x <a href="'. get_permalink( wp_get_post_parent_id( $key ) ) .'">'. esc_html( $bundle['title'] ) .'</a></div>';
							} else {
								$html .= '<div>'. $bundle['quantity'] .' x <a href="'. get_permalink( $key ) .'">'. esc_html( $bundle['title'] ) .'</a></div>';
							}												
						}
						$html .= '</dd>';
						$html .= '</dl>';
					}
					return $html;				
				}
			}
		}
		return $html;		
	}

	function swpb_add_bundle_as_order_meta( $item_id, $values, $cart_item_key ) {
		if( isset( $values['product_id'] ) ) {
			$terms        = get_the_terms( $values['product_id'], 'product_type' );
			$product_type = ! empty( $terms ) ? sanitize_title( current( $terms )->name ) : 'simple';
			if( $product_type == "bundle" ) {				
				if( swpb_utils::get_swpb_meta( $values['product_id'], '_swpb_hide_bundle_on_order', 'yes' ) == "yes" ) {
					$index = 0;
					$btitle = '';
					$bundles =  json_decode( get_post_meta( $values['product_id'], "sw_product_bundles", true ), true );
					foreach ( $bundles as $key => $bundle ) {
						if( $index == 0 ) {
							$btitle .= $bundle['quantity'] .'x'. esc_html( $bundle['title'] );
						} else {
							$btitle .= ', '. $bundle['quantity'] .'x'. esc_html( $bundle['title'] );
						}
						$index++;
					}
					wc_add_order_item_meta( $item_id, "Bundle Includes", $btitle );
				}
			}
		}
	}
	
	function swpb_sync_bundle_stocks( $order_id ) {
		$order = new WC_Order( $order_id );
		if ( get_option('woocommerce_manage_stock') == 'yes' ) {
			foreach ( $order->get_items() as $item ) {				
				if ( $item['product_id'] > 0 ) {
					if ( ! empty( $item['variation_id'] ) && 'product_variation' === get_post_type( $item['variation_id'] ) ) {
						$pid = $item['variation_id'];
					} else {
						$pid = $item['product_id'];
					}	
					
					$terms        = get_the_terms( $pid, 'product_type' );
					$product_type = ! empty( $terms ) ? sanitize_title( current( $terms )->name ) : 'simple';
										
					if( $product_type == "bundle" ) {
						$bundles =  json_decode( get_post_meta( $pid, "sw_product_bundles", true ), true );
						foreach ( $bundles as $key => $bundle ) {
							$_product = wc_get_product( $key );						
							if ( $_product && $_product->exists() && $_product->managing_stock() ) {
								$new_stock = $_product->reduce_stock( intval( $item['qty'] ) * intval( $bundle['quantity'] ) );
								$order->send_stock_notifications( $_product, $new_stock, $bundle['quantity'] );
								do_action( 'swpb_reduce_order_bundle_stock', $_product, $new_stock, $bundle['quantity'] );
							}
						}
					}
				}
			}
		}
	}
	
	function swpb_re_sync_bundle_stocks( $order_id ) {
		$order = new WC_Order( $order_id );
		if ( get_option('woocommerce_manage_stock') == 'yes' ) {
			foreach ( $order->get_items() as $item ) {
				if ( $item['product_id'] > 0 ) {
					if ( ! empty( $item['variation_id'] ) && 'product_variation' === get_post_type( $item['variation_id'] ) ) {
						$pid = $item['variation_id'];
					} else {
						$pid = $item['product_id'];
					}
						
					$terms        = get_the_terms( $pid, 'product_type' );
					$product_type = ! empty( $terms ) ? sanitize_title( current( $terms )->name ) : 'simple';
		
					if( $product_type == "bundle" ) {
						$bundles =  json_decode( get_post_meta( $pid, "sw_product_bundles", true ), true );
						foreach ( $bundles as $key => $bundle ) {
							$_product = wc_get_product( $key );
							if ( $_product && $_product->exists() && $_product->managing_stock() ) {
								$new_stock = $_product->increase_stock( intval( $item['qty'] ) * intval( $bundle['quantity'] ) );								
								do_action( 'swpb_increase_order_bundle_stock', $_product, $new_stock, $bundle['quantity'] );
							}
						}
					}
				}
			}
		}
	}	
	
	function swpb_add_combo_pack_label( $label, $post ) {
		$terms        = get_the_terms( $post->ID, 'product_type' );
		$product_type = ! empty( $terms ) ? sanitize_title( current( $terms )->name ) : 'simple';
		
		if( $product_type == "bundle" ) {
			return apply_filters( 'swpb_combo_pack_label', '<span class="onsale">'. __( 'Combo', 'sw_product_bundles' ) .'</span>' );
		} else {
			return $label;
		}
	}
	
	function swpb_add_to_cart_bundle_validation( $unknown, $pid = null, $quantity ) {
		$terms        = get_the_terms( $pid, 'product_type' );
		$product_type = ! empty( $terms ) ? sanitize_title( current( $terms )->name ) : 'simple';
		
		if( $product_type == "bundle" ) {
			
				$bundles = apply_filters( 'swpb/load/bundle', $pid );
				foreach ( $bundles as $key => $value ) {
					$bundle = wc_get_product( $key );
					if($bundle->get_stock_status() == "outofstock") {
						wc_add_notice( __( 'You cannot add that amount of quantity, because there is not enough stock', 'sw_product_bundles' ), 'error' );
						return false;
					}
				}
			
		}
		return true;		
	}
}

new SWPB_Product_Form();

?>