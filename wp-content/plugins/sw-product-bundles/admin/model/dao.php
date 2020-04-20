<?php 

if ( ! defined( 'ABSPATH' ) ) { exit; }

class SWPB_Dao {
	
	function __construct() {
		add_filter( 'swpb/search/products', array( $this, 'search_products' ) );
		add_filter( 'swpb/add_to_bundle/products', array( $this, 'add_to_bundle' ) );
		add_filter( 'swpb/remove_from_bundle/products', array( $this, 'remove_from_bundle' ) );
		add_filter( 'swpb/load/bundle', array( $this, 'load_products_bundle' ), 10, 2 );
		add_action( 'save_post', array( $this, 'update_bundle_products' ), 1, 3 );
	}
	function search_products() {
		$args = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'swpb_product_search' => swpb()->request['payload'],
			'fields'         => 'ids, title'
		);	
		$args['tax_query'][] = array(						
			'taxonomy' => 'product_type',
			'field'    => 'name',
			'terms'    => 'bundle',
			'operator' => 'NOT IN',
		);	
		add_filter( 'posts_where', array( $this, 'swpb_product_search' ), 10, 2 );
		$wp_query = new WP_Query($args);
		remove_filter( 'posts_where', array( $this, 'swpb_product_search' ), 10, 2 );		
		return $wp_query->posts;		
	}
	function add_to_bundle() {	
		$is_new = true;	
		$bundles =  json_decode( get_post_meta( swpb()->request['post'], "sw_product_bundles", true ), true );		
		if( is_array( $bundles ) ) {
			$is_new = false;				
		} else {
			$bundles = array();
		}										
		foreach ( swpb()->request['payload'] as $bundle ) {				
			$product = get_post( $bundle );	
			
			if( get_post_type( $bundle) === 'product_variation' ) {
				$index = 0;
				$variation = wc_get_product( $bundle );		
				$parent_post_id = wp_get_post_parent_id( $bundle );		
				$parent_product = wc_get_product( $parent_post_id );
				$title = $parent_product->get_title() ." - ";
				$attributes = swpb_utils::get_variable_attributes( $variation );
				
				$args = array(
					'post_type'     => 'product_variation',
					'post_status'   => array( 'private', 'publish' ),
					'numberposts'   => -1,
					'orderby'       => 'menu_order',
					'order'         => 'asc',
					'post_parent'   => $bundle // get parent post-ID
				);
				$variations = get_posts( $args );

				foreach ( $attributes as $attr ) {					
					if( $index == 0 ) {
						$title .= 'with '. $attr["option"];
					} else {
						$title .= ", ". $attr["option"];
					}
					$index++;					
				}
			} else {
				$title = $product->post_title;
			}	
			
			$bundles[ $bundle ] = array(
				"quantity" => 1,
				"price" => get_post_meta( $bundle, '_price', true ),
				"thumbnail" => "yes",
				"tax_included" => "yes",
				"category"   => "yes",
				"title" => trim( $title ),
			);
		}			
		if( $is_new ) {
			add_post_meta( swpb()->request['post'], 'sw_product_bundles', wp_slash( json_encode( $bundles ) ) );
		} else {
			update_post_meta( swpb()->request['post'], 'sw_product_bundles', wp_slash( json_encode( $bundles ) ) );
		}		
		return true;
	}
	
	function update_bundle_products( $post_id, $post, $update ) {		
		if( $post->post_type != "product" ) {
			return;
		}	
		$product = wc_setup_product_data( $post );

		$terms        = get_the_terms( $post_id, 'product_type' );
		$product_type = ! empty( $terms ) ? sanitize_title( current( $terms )->name ) : 'simple';
				
		if( $product_type == "bundle" ) {
			/* Update sale price meta */
			delete_post_meta( $post_id, '_swpb_product_regular_price' );
			add_post_meta( $post_id, '_swpb_product_regular_price', $_REQUEST['_swpb_product_regular_price'] );
		
			/* Update bundle visibility meta */
			delete_post_meta( $post_id, '_swpb_show_bundle_on_product' );
			delete_post_meta( $post_id, '_swpb_show_bundle_on_cart' );
			delete_post_meta( $post_id, '_swpb_show_bundle_on_order' );
			
			$on_product = isset( $_REQUEST['_swpb_show_bundle_on_product'] ) ? 'yes' : 'no';
			$on_cart = isset( $_REQUEST['_swpb_show_bundle_on_cart'] ) ? 'yes' : 'no';
			$on_order = isset( $_REQUEST['_swpb_show_bundle_on_order'] ) ? 'yes' : 'no';
			
			add_post_meta( $post_id, '_swpb_show_bundle_on_product', $on_product );
			add_post_meta( $post_id, '_swpb_show_bundle_on_cart', $on_cart );
			add_post_meta( $post_id, '_swpb_show_bundle_on_order', $on_order );			
			/* Update the bundles */
			$bundles =  json_decode( get_post_meta( $post_id, "sw_product_bundles", true ), true );			
			if( is_array( $bundles ) ) {
				$bundles = array();				
				$temp_bundles = json_decode( stripslashes( $_REQUEST['swpb-bundles-array'] ), true );				
				foreach ( $temp_bundles as $bundle ) {
					$bundles[ $bundle["product_id"] ] = $bundle["bundle"];
				}				
				update_post_meta( $post_id, 'sw_product_bundles', wp_slash( json_encode( $bundles ) ) );
			}				
		}		
	}
	
	
	function remove_from_bundle() {
		$bundle = swpb()->request['payload'];		
		$bundles = json_decode( get_post_meta( swpb()->request['post'], "sw_product_bundles", true ), true );		
		if( is_array( $bundles ) ) {
			if( isset( $bundles[ $bundle ] ) ) {
				unset( $bundles[ $bundle ] );
				return update_post_meta( swpb()->request['post'], 'sw_product_bundles', wp_slash( json_encode( $bundles ) ) );
			}				
		}		
		return false;
	}
	
	function load_products_bundle( $pid ) {		
		$products = array();
		$bundles =  json_decode( get_post_meta( $pid, "sw_product_bundles", true ), true );		
		if( is_array( $bundles ) ) {
			$products = $bundles;
		}		
		return $products;
	}
	function swpb_product_search( $where, &$wp_query ) {
		global $wpdb;
		if ( $search_term = $wp_query->get( 'swpb_product_search' ) ) {
			$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( like_escape( $search_term ) ) . '%\'';
		}
		return $where;
	}
}

new SWPB_Dao();

?>