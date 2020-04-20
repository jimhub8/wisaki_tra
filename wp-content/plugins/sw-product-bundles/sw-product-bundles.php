<?php
/**
 * Plugin Name: Sw Product Bundles
 * Plugin URI: http://wpthemego.com/premium-plugins/sw-product-bundles.html
 * Description: A plugin help to display woocommerce beauty.
 * Version: 2.0.17
 * Author: wpthemego
 * Author URI: http://wpthemego.com
 * Requires at least: 4.1
 * Tested up to: WorPress 5.3.x and WooCommerce 3.8.x
 
 * Text Domain: sw_product_bundles
 * Domain Path: /languages/
 * WC tested up to: 3.9.0
 */
 
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! defined( 'PBPATH' ) ) {
	define( 'PBPATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'PBDIR' ) ) {
	define( 'PBDIR', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'PBTHEME' ) ) {
	define( 'PBTHEME', plugin_dir_path( __FILE__ ) . 'includes/themes/sw-bundle-product-slider' );
}

if( !class_exists('SWPB_Product_Bundles')  ){
	class SWPB_Product_Bundles {
		
		var $request,$response;
		
		public function __construct() {
		
			add_action( 'admin_enqueue_scripts', array( $this, 'sw_bundle_admin_script' ), 100 );
			add_action( 'init', array( $this, 'init' ), 1 );
			add_action( 'plugins_loaded', array( $this, 'setup_front_end_env' ), 1 );
			add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
		
		}
		
		function sw_bundle_admin_script(){
			$action = ( array_key_exists( 'action', $_REQUEST) ? $_REQUEST['action'] : "" );
			$post_type = ( array_key_exists( 'post_type', $_REQUEST) ? $_REQUEST['post_type'] : "" );
			if( $action == 'edit' || $post_type == "product" ) {
				wp_register_script( 'swpb-script', PBDIR . "assets/js/swpb.js", 'jquery', 1.0 );
				wp_register_style( 'swpb-style', PBDIR . 'assets/css/swpb-admin.css' );
				wp_enqueue_style( 'swpb-style' );
				wp_enqueue_script( 'swpb-script' );
			}
		}
		
		function init() {	
			if( !is_admin() ){
				if (!wp_script_is('slick_slider') && !function_exists( 'sw_woocommerce_construct' ) ) {
					wp_register_script( 'slick_slider', PBDIR .'assets/js/slick.min.js', __FILE__ ,array(), null, true );
					wp_enqueue_script('slick_slider');
				}
				
				if ( !wp_style_is('swpb_slick_slider_css') ) {
					wp_register_style( 'swpb_slick_slider_css', PBDIR .'assets/css/slider.css', __FILE__ );
					wp_enqueue_style('swpb_slick_slider_css'); 
				}	
				wp_register_style( 'swpb-style', PBDIR . 'assets/css/swpb-front-end.css' );
				wp_enqueue_style( 'swpb-style' );
			}
			load_plugin_textdomain( 'sw_product_bundles', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
			$this->includes();		
		}
		
		function includes() {	
			/* Include Widget File and hook */
			include_once('admin/model/dao.php');
			include_once('admin/model/utils.php');
			include_once('admin/controller/controller.php');		
			include_once('admin/view/admin-form.php');
			include_once('admin/view/list.php');
			
		}
		
		function setup_front_end_env() {
		global $woocommerce ;
		if ( ! isset( $woocommerce ) || ! function_exists( 'WC' ) ) {
				add_action( 'admin_notices', 'swpb_bundle_admin_notice' );
				return;
		}	
			include_once('includes/sw-bundle-product-slider-widget.php' );		
			include_once('front-end/sw_bundled_product.php');
			include_once('front-end/product-form.php');		
		}
		
		function load_scripts() {		
			
		}
		
	}
}

function swpb() {
	global $swpb;
	if( !isset( $swpb ) ) {
		$swpb = new SWPB_Product_Bundles();
	}
	return $swpb;
}

swpb();


/*
** Load admin notice when WooCommerce not active
*/
function swpb_bundle_admin_notice(){
	?>
	<div class="error">
		<p><?php _e( 'Sw Bundles Product is enabled but not effective. It requires WooCommerce in order to work.', 'sw_product_bundles' ); ?></p>
	</div>
<?php
}

/*
** Trim Words
*/

function swpb_bunlde_trim_words( $title, $title_length = 0 ){
	$html = '';
	if( $title_length > 0 ){
		$html .= wp_trim_words( $title, $title_length, '...' );
	}else{
		$html .= $title;
	}
	echo esc_html( $html );
}
/*
** WooCommerce Compare Version
*/

function swpb_woocommerce_version_check( $version = '3.0' ) {
	global $woocommerce;
	if( version_compare( $woocommerce->version, $version, ">=" ) ) {
		return true;
	}else{
		return false;
	}
}

/*
** Sales label
*/

function swpb_bunlde_label_sales(){
	global $product, $post;
	$product_type = ( swpb_woocommerce_version_check( '3.0' ) ) ? $product->get_type() : $product->product_type;
	if( $product_type != 'variable' ) {
		$sale_off = 0;
		$forginal_price 	= get_post_meta( $post->ID, '_swpb_product_regular_price', true );	
		$fsale_price 		= get_post_meta( $post->ID, '_regular_price', true );
		$fssale_price 		= get_post_meta( $post->ID, '_sale_price', true );
		if( $forginal_price > 0 && $fsale_price > 0 && $fssale_price == '' ){ 
		$sale_off = 100 - ( ( $fsale_price/$forginal_price ) * 100 ); 
	?>
			<div class="sale-off">
				<?php echo round( $sale_off ).'%';?>
			</div>

			<?php }elseif( $forginal_price > 0 && $fsale_price > 0 && $fssale_price != '' ){
				$sale_off = 100 - ( ( $fssale_price/$forginal_price ) * 100 ); 
				?>
				<div class="sale-off">
						<?php echo round( $sale_off ).'%';?>
					</div>

			<?php } 
	}else {

		wc_get_template( 'single-product/sale-flash.php' );
	}
}	
?>
