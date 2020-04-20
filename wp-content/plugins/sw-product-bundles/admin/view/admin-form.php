<?php 
if ( ! defined( 'ABSPATH' ) ) { exit; }

class SWPB_Product_Interface {
	
	function __construct() {
		add_filter( 'product_type_selector', array( $this, 'swpb_add_product_bundle_type' ), 1, 2 );
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'swpb_add_product_bundle_tab' ) );
		add_action( 'admin_footer', array( $this,'simple_rental_custom_js' ));
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'swpb_add_bundle_pricing_fields' ), 10, 1 );
		add_action( 'woocommerce_product_data_panels', array( $this, 'swpb_add_product_bundle_tab_panel' ), 10, 1 );		
	}
	
	function swpb_add_product_bundle_type( $ptypes ) {
		$ptypes['bundle'] = __( 'Bundled Product', 'sw_product_bundles' );
		return $ptypes;
	}
	
	function swpb_add_product_bundle_tab( $tabs ) {
		$tabs['bundle'] = array (
			'label'  => __( 'Bundled', 'sw_product_bundles' ),
			'target' => 'bundle_data',
			'class'  => array( 'hide_if_virtual', 'hide_if_grouped', 'hide_if_external', 'hide_if_simple', 'hide_if_variable', 'show_if_bundle' ),
		);
		return $tabs;
	}
	
	function swpb_add_bundle_pricing_fields() {
		global $post;
		
		echo '<div class="options_group pricing hide_if_virtual hide_if_grouped hide_if_external hide_if_simple hide_if_variable show_if_bundle">';
	
		$sprice = swpb_utils::get_swpb_meta( $post->ID, '_swpb_product_regular_price' );	
		//$sale_price = swpb_utils::get_swpb_meta( $post->ID, '_swpb_product_sale_price' );
		woocommerce_wp_text_input( array( 'id' => '_swpb_product_regular_price', 'value' => esc_html( $sprice ), 'data_type' => 'price', 'label' => __( 'Total Price', 'sw_product_bundles' ) . ' ('.get_woocommerce_currency_symbol().')') );

		do_action( 'woocommerce_product_options_pricing' );
		
		echo '</div>';
		
		echo '<div class="options_group pricing hide_if_virtual hide_if_grouped hide_if_external hide_if_simple hide_if_variable show_if_bundle">';
				
		$on_product = swpb_utils::get_swpb_meta( $post->ID, '_swpb_show_bundle_on_product', 'yes' );
		$on_cart = swpb_utils::get_swpb_meta( $post->ID, '_swpb_show_bundle_on_cart', 'yes' );
		$on_order = swpb_utils::get_swpb_meta( $post->ID, '_swpb_show_bundle_on_order', 'yes' );
		
		woocommerce_wp_checkbox( array( 'id' => '_swpb_show_bundle_on_product', 'label' => __( 'Show on Product Page', 'sw_product_bundles' ), 'value' => 'yes', 'cbvalue' => $on_product, 'desc_tip' => 'true', 'description' => __( 'Un check if you want to hide the bundle on product page.', 'sw_product_bundles' ) ) );
		woocommerce_wp_checkbox( array( 'id' => '_swpb_show_bundle_on_cart', 'label' => __( 'Show on Cart Page', 'sw_product_bundles' ), 'value' => 'yes', 'cbvalue' => $on_cart, 'desc_tip' => 'true', 'description' => __( 'Un check if you want to hide the bundle on cart.', 'sw_product_bundles' ) ) );
		woocommerce_wp_checkbox( array( 'id' => '_swpb_show_bundle_on_order', 'label' => __( 'Show on Order', 'sw_product_bundles' ), 'value' => 'yes', 'cbvalue' => $on_order, 'desc_tip' => 'true', 'description' => __( 'Un check if you want to hide the bundle on order.', 'sw_product_bundles' ) ) );
		
		echo '</div>';
	}
	function simple_rental_custom_js() {

	    if ('product' != get_post_type()) :
	        return;
	    endif;
	    ?>
	    <script type='text/javascript'>
	        jQuery(document).ready(function () {
	            //for Price tab
	            jQuery( 'body' ).on( 'woocommerce-product-type-change', function ( event, select_val, select ) {

		        if ( select_val == 'bundle' ) {

		            jQuery( 'input#_downloadable' ).prop( 'checked', false );
		            jQuery( 'input#_virtual' ).removeAttr( 'checked' );

		            jQuery( '.show_if_external' ).hide();
		            jQuery( '.show_if_simple' ).show();

		            jQuery( 'input#_downloadable' ).closest( '.show_if_simple' ).hide();
		            jQuery( 'input#_virtual' ).closest( '.show_if_simple' ).hide();

		            jQuery( 'input#_manage_stock' ).change();
		            jQuery( 'input#_per_product_pricing_active' ).change();
		            jQuery( 'input#_per_product_shipping_active' ).change();

		            jQuery( '#_nyp' ).change();

		            jQuery( '.pricing' ).show();
		            jQuery( '.product_data_tabs' ).find( 'li.general_options' ).show();
		        } else {
		            jQuery( '.show_if_bundle' ).hide();
		        }

		    } );

		    jQuery( 'select#product-type' ).change();

		    jQuery( '#_regular_price' ).closest( 'div.options_group' ).addClass( 'show_if_yith_bundle' );
	    });
	    </script>
	    <?php

	}
	function swpb_add_product_bundle_tab_panel() { ?>	

		<div id="bundle_data" class="panel woocommerce_options_panel hide_if_virtual hide_if_grouped hide_if_external hide_if_simple hide_if_variable show_if_bundle">	
		    <ul class="swpb-product-search-container-ul wc-metaboxes-wrapper">  
				<li>
					<div class="swpb-product-search-txt-wrapper">
						<input type="text" id="swpb-product-search-txt" placeholder="<?php _e( 'Search Products', 'sw_product_bundles' ); ?>" />
						<img alt="spinner" src="<?php echo PBDIR ?>/assets/images/spinner.gif" id="swpb-ajax-spinner" />
						<div id="swpb-product-search-result-holder">
						
						</div>
					</div>												      
				</li>
				<li>						
					<a href="#" class="swpb_close_all"><?php _e( 'Close all', 'sw_product_bundles' ); ?></a>
					<a href="#" class="swpb_expand_all"><?php _e( 'Expand all', 'sw_product_bundles' ); ?></a>
					<a href="#" class="button button-primary button-large" id="swpb-add-product"><?php _e( 'Add Products', 'sw_product_bundles' ); ?></a>
				</li>
			</ul>			
			
			<div class="swpb-products-container wc-metaboxes ui-sortable" id="swpb-products-container">
				<?php global $post; echo apply_filters( 'swpb_included_products', $post->ID ); ?>
			</div>
			<input type="hidden" id="swpb-bundles-array" name="swpb-bundles-array" value="" />							
		</div>
	<?php 
		$this->swpb_admin_head();
	}
	function swpb_admin_head() {
		global $post; ?>
<script type="text/javascript">
var swpb_var = {
	post_id : <?php echo $post->ID; ?>,
	nonce  : "<?php echo wp_create_nonce( 'swpb_nonce' ); ?>",
	admin_url : "<?php echo admin_url(); ?>",
	ajaxurl : "<?php echo admin_url( 'admin-ajax.php' ); ?>"	 
};		
</script>
<?php
	}
}

new SWPB_Product_Interface();

?>