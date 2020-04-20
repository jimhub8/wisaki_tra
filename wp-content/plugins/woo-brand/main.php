<?php
/*
	*  Plugin Name: Woocomerce Brands Pro
	*  Plugin URI: http://proword.net/Plugins/Woocommerce_Brands_pro/
	*  Description: Woocommerce Brands Plugin. After Install and active this plugin you'll have some shortcode and some widget for display your brands in fornt-end website.
	*  Author: Proword
	*  Version: 4.4.3
	*  Author URI: http://proword.net/
	*  Text Domain: woocommerce-brands
	*  Domain Path: /languages/ 
	*  WC requires at least: 2.4
	*  WC tested up to: 3.5.1

	fixed : add word 'All' to textdomain 
V 4.3.9
	Fixed : permalink brand in init_taxonomy
	Fixed : link for image in product brand shortcode
	Changed : structure code for show brand in single product 
	Added : brand tab in product page 
	Added : compatibility Yoast seo plugin

V 4.3.8
	Fixed : class style in thumbnail shortcode
	Fixed : jQuery a-z view in all styles 
	Fixed : shortoce a-z view in filter style 7 
	Fixed : remove space script in pw_brand_thumbnails shortcode
	Fixed : when Duplicate product also copy brands
	Fixed : keep current settings in new versions plugin.
	Added : Get purchase Code For Auto update .  
V 4.3.5
	Fixed : WC 3.3 compatibility.
	Fixed : WordPress 4.9.4 compatibility
	Fixed : permalink brand in single product 
	Fixed : shortcode pw_brand_product_grid
	Fixed : shortcode in vc
	Fixed : add permalink in image adv2
	Changed : separate with comma if multi brand in archive page 
	Added : add paginations 
	Added : Enable "Sort by Brand" option in shop archive page

V 4.3.2
		Added : "affer price" brand position in shop page
		Fixed : preventDefault in jQuery A-Z shortcode
		Fixed : customize brand label
		Fixed : image size in brand thumbnails
		Fixed : Hide Empty Brands in brand list Advanced Layout 2
		Fixed : check if term is exits in shop loop item
		Fixed : add Sticky defaults position
		Fixed : jQuery error in add product/post/page
		Changed : documentation
		Changed : plugin url
V 4.3.1
		Fixed : fix for woocommerce 3.0.x
V 4.3.0
		Fixed : woocommerce 3.0.x is ready
		Fixed : register taxonomy for brand
		Added : you can change position brand in single and archive
		Added : show sticky button only product/shop pages
V 4.2.0
		added : add link in image advanced layout 2
		Fixed : Fix shortcode thumbnail
		Fixed : carosel slide speed in Visul composer
		Fixed : fix show empty brands in advanced show brands
V 4.1
	12/07/2016
		Fixed: fix Layered Nav widget in WC 2.6
		Fixed: WC 2.6 compatibility issues.
		Added : convert Description brand to editor
V 4.0
	02/04/2016
		Added :	compatible with woocommerce brand coupon add-ons
		Fixed : remove font body in front-style.css file
		fixed : fix for text brand in product page if dont set any brand's
V 3.7
	29/10/2015
		Added : Image Sie for brand logo In settings
		Added : New styles in shortcode
		Fixed : add default speed in carousel shortcode
		Fixed : compatible woocommerce 2.4.X
		Fixed : Fix thumbnail in mobile/tablet
		Fixed : Fix css in bootstrap
		Fixed : fix filter base url in widget
		Fixed : fix Show/Hide Empty Brands in shortcode "pw_brand_thumbnails"
		Fixed : fix with VC 4.8.x
		Fixed : fix a-z shortcode scroll and fillter
V 3.4 
	15/09/2015
		Fixed : fix issue in dorpdown in filter by brand
		Fixed : update sticky get_footer to wp_footer=
		Added : Term counts for hide products from the catalog in settings 
		Fixed : update sticky css in mobile view
V 3.3 
	18/07/2015
		Added : Add Multilanguge A-Z Key fillter Views
		Fixed : Fix Hide empty brand in a-z view
		Fixed : Check Compatible in wc 2.3.x and wordpress 4.2.2 = check
V 3.2.0
	15/04/2015
		Added : Display Brand logo In Product Listing(category page)
V 3.1.0
	7/3/2015
		Fixed : Remove Font Body from front-style.css
		Fixed :  url in wpml

*/

define('plugin_dir_url_pw_woo_brand', plugin_dir_url(__FILE__));
define('plugin_dirname_pw_woo_brand', dirname(__FILE__));
define('plugin_dirpath_pw_woo_brand',untrailingslashit( plugin_dir_path( dirname( __FILE__ ) ) ));
//PERFIX
define ('__PW_BRAND_plugin_slug', basename(dirname(__FILE__)) );
if (!class_exists('pw_woocommerc_brans_active_plugin'))
    require_once 'classes/active-plugins-check.php';

/**
 * WC Detection
 */


/**
 * Check if WooCommerce is active
 **/
if (!function_exists('is_woocommerce_active')) {
    function is_woocommerce_active()
    {
        return pw_woocommerc_brans_active_plugin::woocommerce_active_check();
    }
}

if (is_woocommerce_active()) {

    if (!defined('ABSPATH')) {
        exit; // Exit if accessed directly
    }
    /**
     * Localisation
     **/
    load_plugin_textdomain('woocommerce-brands', false, dirname(plugin_basename(__FILE__)) . '/languages/');

    final class woo_brands
    {

        public function __construct()
        {
            $this->includes();



            add_action('widgets_init', array($this, 'include_widgets'));
            add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'action_links'));
			
			//add_filter('plugin_row_meta', array($this, 'addCheckForpurchasecodeLink'), 100, 2);

            add_action('wp_enqueue_scripts', array($this, 'eb_add_scripts'));
            register_activation_hook(__FILE__, array($this, 'woo_brands_install'));

            //Ui Shortcode
            add_filter('init', array($this, 'brand_shortcodes_add_scripts'));
            add_action('admin_head', array($this, 'brand_shortcodes_addbuttons'));
			
			add_action( 'init', array( $this, 'sort_by_brand' ) );


	        ////ADDED IN VER4.3.6
	        /// AUTO UPDATE
	       // $this->plugin_slug = basename(dirname(__FILE__));


        }



		public function sort_by_brand() {
			$enable_sort = get_option( 'pw_woocommerce_enable_brand_sorting', 'no' );

			if( 'yes' == $enable_sort ){
				add_filter( 'woocommerce_catalog_orderby', array( $this, 'show_sort_by_brand' ) );
				add_filter( 'woocommerce_get_catalog_ordering_args', array( $this, 'set_sort_by_brand' ) );
			}
		}

		public function show_sort_by_brand( $sort ) {
			$sort[ 'brand' ] = __( 'Sort by brand', 'woocommerce-brands' );

			return $sort;
		}
		
		public function set_sort_by_brand( $args ) {
			$orderby_value = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );

			if( $orderby_value == 'brand' ){
				add_filter( 'posts_clauses', array( $this, 'set_sort_by_brand_query_args' ) );
			}

			return $args;
		}

		public function set_sort_by_brand_query_args( $args ) {
			global $wpdb;

			$args['fields'] .= ", bt.name AS brand";
			$args['join'] .= "
			    LEFT JOIN {$wpdb->term_relationships} AS br ON ($wpdb->posts.ID = br.object_id)
			    LEFT JOIN {$wpdb->term_taxonomy} AS btx ON (br.term_taxonomy_id = btx.term_taxonomy_id)
			    LEFT JOIN {$wpdb->terms} AS bt ON (bt.term_id = btx.term_id)
			";
			$args['where'] .= $wpdb->prepare( " AND btx.taxonomy = %s", 'product_brand' );
			$args['orderby'] = 'brand ASC';
			$args['groupby'] = "$wpdb->posts.ID";

			return $args;
		}
		
        function brand_shortcodes_add_scripts()
        {
            if (is_admin()) {
                wp_enqueue_style('fontawesome-style', plugin_dir_url_pw_woo_brand . 'css/fonts/font-awesome.css');
                /////////////////////////CSS CHOSEN///////////////////////
                wp_enqueue_style('pw-brand-chosen-style', plugin_dir_url_pw_woo_brand . 'css/chosen/chosen.css', array(), null);
                wp_enqueue_style('pw-brand-backend-style', plugin_dir_url_pw_woo_brand . 'css/backend-style.css', array(), null);
                wp_enqueue_script('pw-brand-chosen-script', plugin_dir_url_pw_woo_brand . 'js/chosen/chosen.jquery.min.js', array('jquery'));
                //Dependency
                wp_enqueue_script('pw-brand-depds', plugin_dir_url_pw_woo_brand . 'js/dependsOn-1.0.1.min.js', array('jquery'));
                //Colour Picker
                //wp_enqueue_style( 'wp-color-picker' );
                //wp_enqueue_script( 'wp-color-picker' );
            }
        }

        function brand_shortcodes_addbuttons()
        {
            global $typenow;
            // check user permissions
            if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
                return;
            }
            // check if WYSIWYG is enabled
            if (get_user_option('rich_editing') == 'true') {
                add_filter("mce_external_plugins", array($this, "add_woo_brand_shortcodes_tinymce_plugin"));
                add_filter('mce_buttons', array($this, 'register_woo_brand_shortcodes_button'));
            }
        }

        function add_woo_brand_shortcodes_tinymce_plugin($plugin_array)
        {
            $plugin_array['woo_brand_shortcodes_button'] = plugins_url('/includes/tinymce_button.js', __FILE__);
            return $plugin_array;
        }

        function register_woo_brand_shortcodes_button($buttons)
        {
            array_push($buttons, "woo_brand_shortcodes_button");
            return $buttons;
        }

        public function woo_brands_install()
        {

			if(get_option('pw_woocommerce_brands_display_extra')=='no' || get_option('pw_woocommerce_brands_display_extra')=='yes')
			{
				
			}
			else{
				update_option('pw_woocommerce_brands_show_categories', 'no');
				//update_option( 'pw_woocommerce_brands_default_image', plugin_dir_url_pw_woo_brand.'img/default.png' );
				update_option('pw_woocommerce_brands_display_extra', 'yes');
				update_option('pw_woocommerce_brands_position_extra', 'right');
				update_option('pw_woocommerce_brands_text', 'Brands:');
				update_option('pw_woocommerce_brands_text_single', 'yes');
				update_option('pw_woocommerce_brands_image_single', 'yes');
				update_option('pw_woocommerce_brands_image_list', 'yes');
				update_option('pw_woocommerce_brands_desc_single', 'no');
				update_option('pw_woocommerce_brands_desc_list', 'no');
				update_option('pw_wooccommerce_display_brand_in_product_shop', 'yes');
				update_option('pw_woocommerce_image_brand_shop_page', 'no');
				update_option('pw_position_brand_shop', 'above_price');
				update_option('pw_woocommerce_brands_shop_page', 'yes');
				update_option('pw_woocommerce_brands_base', 'brand');
				update_option('pw_woocommerce_brands_style_extra', 'wb-filter-style1');
				update_option('pw_woocommerce_brands_image_list_image_size', '150:150');
				update_option('pw_woocommerce_brands_image_single_image_size', '150:150');
				update_option('pw_woocommerce_image_brand_shop_page_image_size', '150:150');
				update_option('pw_woocommerce_brands_show_pages_extra', 'all');
				update_option('pw_woocommerce_enable_brand_sorting', 'no');
				update_option('pw_woocommerce_enable_brand_tab', 'no');
			}
        }

        private function includes()
        {
            if (get_option('pw_woocommerce_brands_display_extra'))
                include_once('classes/side-button.php');
            include_once('classes/taxonomies.php');
            //include_once('classes/auto-update.php');
            include_once('includes/shortcode.php');
            include_once('includes/all_shortcode.php');
            include_once('classes/setting-tabs.php');
            include_once('classes/class-wc-brands.php');
            include_once('vc_composer/main.php');
            /////ACTION FILE///////
            include_once('includes/actions.php');
            //include_once( 'includes/test.php' );
        }

        public function include_widgets()
        {

            if (version_compare(WC_VERSION, '2.6.0', '>=')) {
                require_once('classes/class-wc-widget-brand-nav.php');
            } else {
                require_once('classes/class-wc-widget-brand-nav-deprecated.php');
            }

            include_once('classes/widget.php');
        }


        public function action_links($links)
        {
            return array_merge(array(
                '<a href="' . admin_url('admin.php?page=wc-settings&tab=pw_woocommerce_brands') . '">' . __('Settings', 'woocommerce-brands') . '</a>',
                '<a href="' . admin_url('admin.php?page=wc-settings&tab=pw_woocommerce_brands') . '">' . __('Purchase Code', 'woocommerce-brands') . '</a>',
                '<a href="' . esc_url(apply_filters('woocommerce_docs_url', 'http://proword.net/Woocommerce_Brands/documentation/', 'woocommerce')) . '">' . __('Docs', 'woocommerce-brands') . '</a>',

            ), $links);
        }
		
/*		public function addCheckForpurchasecodeLink( $links, $file ) {

			if ( strpos( $file, 'main.php' ) !== false ) {
				$new_links = array(
						'donate' => '<a href="donation_url" target="_blank"></a>',
										'doc' => '<a href="doc_url" target="_blank">Documentation</a>'
						);
				
				$links = array_merge( $links, $new_links );
			}
	
			return $links;
		}
	*/			
        public function eb_add_scripts()
        {
            /* Bootstrap  */
            wp_register_style('woob-bootstrap-style', plugin_dir_url_pw_woo_brand . 'css/framework/bootstrap.css');
            wp_enqueue_style('woob-bootstrap-style');

            /*Front-End*/
            wp_register_style('woob-front-end-style', plugin_dir_url_pw_woo_brand . 'css/front-style.css');
            wp_enqueue_style('woob-front-end-style');
            /* Dropdown css */
            wp_register_style('woob-dropdown-style', plugin_dir_url_pw_woo_brand . 'css/msdropdown/dd.css');
            /* scroll Css  */
            wp_register_style('woob-scroller-style', plugin_dir_url_pw_woo_brand . 'css/scroll/tinyscroller.css');
            /* BX Slider  */
            wp_register_style('woob-bxslider-style', plugin_dir_url_pw_woo_brand . 'css/bx-slider/jquery.bxslider.css');
            /* Tooltip  */
            wp_register_style('woob-tooltip-style', plugin_dir_url_pw_woo_brand . 'css/tooltip/tipsy.css');

            //////////PRETTY MULTI SELECT/////////////
            //wp_register_style('woob-multiselect-css', plugin_dir_url_pw_woo_brand.'css/multiselect/bootstrap-multiselect.css', array() , null);
			$page_extra= get_option('pw_woocommerce_brands_show_pages_extra','none');
			$flag=true;
			if(is_array($page_extra))
			{			
				foreach($page_extra as $page ){
					if($page=='none'){
						$flag=false;
						break;
					}
				}		
			}
			else{
				$flag=false;
			}
			
            if ($flag) {
				
                wp_register_style('woob-extra-button-style', plugin_dir_url_pw_woo_brand . 'css/extra-button/extra-style.css');
                wp_enqueue_style('woob-extra-button-style');

                wp_register_script('woob-extra-button-script', plugin_dir_url_pw_woo_brand . 'js/extra-button/extra-button.js', array('jquery'));
                wp_enqueue_script('woob-extra-button-script');
            }
            /* Drop Down Js */
            wp_register_script('woob-dropdown-script', plugin_dir_url_pw_woo_brand . 'js/msdropdown/jquery.dd.min.js', array('jquery'));
            /* carosel Js */
            wp_register_script('woob-carousel-script', plugin_dir_url_pw_woo_brand . 'js/carousel/slick.js', array('jquery'));
            /* Scroll Js */
            wp_register_script('woob-scrollbar-script', plugin_dir_url_pw_woo_brand . 'js/scroll/tinyscroller.js', array('jquery'));
            /* BX Slider */
            wp_register_script('woob-bxslider-script', plugin_dir_url_pw_woo_brand . 'js/bx-slider/jquery.bxslider.js', array('jquery'));
            /* Tooltip */
            wp_register_script('woob-tooltip-script', plugin_dir_url_pw_woo_brand . 'js/tooltip/jquery.tipsy.js', array('jquery'));
            ////////CUSTOM JS FRONT END////////
            wp_register_script('woob-front-end-custom-script', plugin_dir_url_pw_woo_brand . 'js/custom-js.js', array('jquery'));

            wp_localize_script('woob-front-end-custom-script', 'parameters', array(
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'template_url' => ''
                )
            );

        }
    }

    new woo_brands();
    add_filter('widget_text', 'do_shortcode');


    add_action('wp_ajax_pw_recount_brand', 'pw_recount_brand');
    add_action('wp_ajax_nopriv_pw_recount_brand', 'pw_recount_brand');
    function pw_recount_brand()
    {
        $product_brand = get_terms('product_brand', array('hide_empty' => false, 'fields' => 'id=>parent'));
        _wc_term_recount($product_brand, get_taxonomy('product_brand'), true, false);
    }

    add_action('wp_ajax_pw_fetch_woocommerce_brand', 'pw_fetch_woocommerce_brand');
    add_action('wp_ajax_nopriv_pw_fetch_woocommerce_brand', 'pw_fetch_woocommerce_brand');
    function pw_fetch_woocommerce_brand()
    {
        $param_line = '';
        $args = array(
            'taxonomy' => 'product_brand',
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => 0,
            'hierarchical' => 1,
            'exclude' => '',
            'include' => '',
            'child_of' => 0,
            'number' => '',
            'pad_counts' => false

        );
        $categories = get_categories($args);
        if (!isset($_POST['single']))
            echo '<option value="all">'.__('All','woocommerce-brands').'</option>';
        foreach ($categories as $category) {
            $option = '<option value="' . $category->cat_ID . '">';
            $option .= $category->cat_name;
            $option .= ' (' . $category->category_count . ')';
            $option .= '</option>';
            $param_line .= $option;
        }
        echo $param_line;
    }


    add_action('wp_ajax_pw_fetch_woocommerce_brand_category', 'pw_fetch_woocommerce_brand_category');
    add_action('wp_ajax_nopriv_pw_fetch_woocommerce_brand_category', 'pw_fetch_woocommerce_brand_category');
    function pw_fetch_woocommerce_brand_category()
    {
        $param_line = '';
        $args = array(
            'taxonomy' => 'product_cat',
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => 0,
            'hierarchical' => 1,
            'exclude' => '',
            'include' => '',
            'child_of' => 0,
            'number' => '',
            'pad_counts' => false

        );
        $categories = get_categories($args);
        foreach ($categories as $category) {
            $option = '<option value="' . $category->cat_ID . '">';
            $option .= $category->cat_name;
            $option .= ' (' . $category->category_count . ')';
            $option .= '</option>';
            $param_line .= $option;
        }
        echo $param_line;
    }
}
?>
