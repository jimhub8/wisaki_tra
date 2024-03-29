<?php
if( !defined( 'ABSPATH' ) ){
    exit;
}

if( !class_exists( 'YITH_WC_Save_For_Later_Premium' ) ){

    class YITH_WC_Save_For_Later_Premium extends YITH_WC_Save_For_Later{

        /**static instance of the class
         * @var YITH_WC_Save_For_Later_Premium
         */
        protected static $instance;

        public function __construct(){

            parent::__construct();

            // register plugin to licence/update system
            add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
            add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

            add_filter( 'ywsfl_add_plugin_tab', array( $this, 'add_premium_tab' ), 20, 1 );
            add_action( 'wp_enqueue_scripts', array( $this, 'include_premium_script' ) );

            add_action( 'wp_ajax_add_to_cart_variable_product', array( $this, 'add_to_cart_variable_product' ) );
            add_action( 'wp_ajax_nopriv_add_to_cart_variable_product', array( $this, 'add_to_cart_variable_product' ) );

            add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'redirect_to_cart' ), 10, 2 );
            add_filter( 'ywsfl_saveforlater_link', array( $this, 'saveforlater_premium_link' ), 25 ,4 );

            $show_button_in_product_page = get_option( 'ywsfl_show_button_single_product' );

            if( 'yes' == $show_button_in_product_page ){

                add_action('wp_enqueue_scripts', array( $this, 'include_single_product_script' ),20 );
                add_action( 'woocommerce_single_product_summary', array( $this, 'show_single_product_page' ), 35 );
                add_action( 'wp_ajax_check_if_variation_is_in_list', array( $this, 'variation_in_save_for_later_list' ) );
                add_action( 'wp_ajax_nopriv_check_if_variation_is_in_list', array( $this, 'variation_in_save_for_later_list' ) );
                add_action( 'wp_ajax_add_single_product_save_list',  array( $this, 'ajax_add_single_product_save_list' ) );
                add_action( 'wp_ajax_nopriv_add_single_product_save_list',  array( $this, 'ajax_add_single_product_save_list' ) );
            }


        }

        /**
         * Register plugins for activation tab
         *
         * @return void
         * @since    1.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register_plugin_for_activation() {
            if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
                require_once YWSFL_DIR.'plugin-fw/licence/lib/yit-licence.php';
                require_once YWSFL_DIR.'plugin-fw/licence/lib/yit-plugin-licence.php';
            }
            YIT_Plugin_Licence()->register( YWSFL_INIT, YWSFL_SECRET_KEY, YWSFL_SLUG );
        }

        /**
         * Register plugins for update tab
         *
         * @return void
         * @since    1.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register_plugin_for_updates() {
            if ( ! class_exists( 'YIT_Upgrade' ) ) {
                require_once(YWSFL_DIR.'plugin-fw/lib/yit-upgrade.php');
            }
            YIT_Upgrade()->register( YWSFL_SLUG, YWSFL_INIT );
        }

        /**Returns single instance of the class
         * @author YITHEMES
         * @since 1.0.0
         * @return YITH_WooCommerce_Save_for_Later_Premium
         */
        public static function get_instance()
        {
            if( is_null( self::$instance ) ){
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function add_premium_tab( $tabs ){

            unset( $tabs['premium-landing'] );

            $tabs['single-product'] = __( 'Product Settings', 'yith-woocommerce-save-for-later' );
            return $tabs;
        }


        public function include_premium_script(){

            wp_register_script( 'yith_wsfl_premium', YWSFL_ASSETS_URL . 'js/yith_premium_wsfl'.$this->_suffix.'.js', array( 'jquery' ), '1.0', false );

            $yith_wsfl_premium_l10n = array(
                'ajax_url' => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
                'actions' => array(
                    'add_to_cart_variable'          =>  'add_to_cart_variable_product',
                )
            );



           wp_localize_script( 'yith_wsfl_premium', 'yith_wsfl_premium_l10n', $yith_wsfl_premium_l10n );


        }

        public function add_to_cart_variable_product() {

            ob_start();


            $product_id = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
            $quantity = empty( $_POST['quantity'] ) ? 1 : apply_filters( 'woocommerce_stock_amount', $_POST['quantity'] );
            $variation_id = 0;

	        if ( 'product_variation' === get_post_type( $product_id ) ) {
		        $variation_id = $product_id;
		        $product_id   = wp_get_post_parent_id( $variation_id );
	        }
            $variation  = $_POST['variation'];
            $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

            if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation  ) ) {
                do_action( 'woocommerce_ajax_added_to_cart', $product_id );
                if ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) {
                    wc_add_to_cart_message( $product_id );
                }

	            $this->savelists['product_id']  =   $product_id;
	            $this->savelists['variation_id']  =  $variation_id ;

               // $this->remove();

                // Return fragments
                WC_AJAX::get_refreshed_fragments();
            } else {

                // If there was an error adding to the cart, redirect to the product page to show any errors
                $data = array(
                    'error' => true,
                    'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id )
                );
                echo json_encode( $data );
            }
            die();
        }

        /**
         * redirect to cart after "Add to cart" button pressed on savelist table
         * @author YITHEMES
         * @since 1.0.0
         * @use woocommerce_product_add_to_cart_url
         * @param $url string Original redirect url
         * @return string Redirect url
         */
        public function redirect_to_cart( $url, $product ) {

            global $yith_wsfl_is_savelist;


            if( $yith_wsfl_is_savelist ){

                if (!( defined('AJAX_DOING') && DOING_AJAX  ) )
                {
                    $product_id = yit_get_product_id( $product );
                    $url = add_query_arg(
                        array(
                            'remove_from_savelist' => $product_id,
                        ),
                        $url
                    );
                }
            }


            return apply_filters( 'yit_wsfl_add_to_cart_redirect_url', esc_url( $url ) );
        }

     

        /**print a "Save for later" premium link in cart table
         * @author YITHEMES
         * @since 1.0.2
         * @use ywsfl_saveforlater_link
         * @param $product_name
         * @param $cart_item
         * @param $cart_item_key
         */
        public function saveforlater_premium_link( $html, $product_name, $cart_item, $cart_item_key ){

            if( !(defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {

                $cart = WC()->cart->get_cart();
                $product_id = $cart[$cart_item_key]['product_id'];
                $variation_id = $cart[$cart_item_key]['variation_id'];

                $args = array(
                    'save_for_later' => $product_id,
                    'variation_id' => $variation_id
                );

                $url = esc_url( remove_query_arg('remove_from_savelist'), get_permalink( wc_get_page_id( 'cart' ) ) );
                $save_for_later_url = esc_url(add_query_arg($args), $url);
                $text_link = get_option( 'ywsfl_text_add_button' );
                $href = '<div class="saveforlater_button">
                                <a href="' . $save_for_later_url . '" rel="nofollow" class="add_saveforlater" title="Save for Later">' . $text_link . '</a>
                            </div>';

                return $product_name . $href;
            }
            else return $product_name;
        }

        /**add a new product in savelist
         * @author YITHEMES
         * @since 1.0.0
         * overridden
         */
        public function add_to_saveforlater(){
            if( isset( $_GET['save_for_later'] ) ){
                $this->savelists['product_id']      =   $_GET['save_for_later'];
                $this->savelists['variation_id']    =   isset( $_GET['variation_id'] ) && !empty( $_GET['variation_id'] )  ? $_GET['variation_id']    :   -1;
                $res = $this->add();
            }
        }

        /**call ajax for add a new product in savelist
         * @author YITHEMES
         * @since 1.0.0
         * overridden
         */
        public function add_to_saveforlater_ajax(){

            $this->savelists['product_id']      =   isset( $_POST['save_for_later'] ) ? $_POST['save_for_later']    :   -1;
            $this->savelists['variation_id']    =   isset( $_POST['variation_id'] ) && !empty( $_POST['variation_id'] )   ? $_POST['variation_id']    :   -1;
            $page_url = !empty( $_POST['page_url'] ) ? $_POST['page_url'] : wc_get_cart_url() ;

            $return = $this->add();

            $message = '';
            if( $return == 'true' ){
                $message = __('Product added', 'yith-woocommerce-save-for-later');
            }
            elseif( $return == 'exists' ){
                $message = __('Product already in Save for later', 'yith-woocommerce-save-for-later') ;
            }

            wp_send_json(
                array(
                    'result' => $return,
                    'message' => $message,
                    'template'  => YITH_WSFL_Shortcode::saveforlater( array( 'current_page' => $page_url ) )
                )
            );
        }

        /**remove a product from savelist
         * @author YITHEMES
         * @since 1.0.0
         * overridden
         */
        public function remove_from_savelist() {

            if( isset( $_GET['remove_from_savelist'] ) ) {



                $this->savelists['product_id']  =   $_GET['remove_from_savelist'];
                $this->savelists['variation_id']  =  isset( $_GET['variation_id'] ) ?  $_GET['variation_id'] : -1  ;
                $this->remove();
            }
        }


        /**
         * remove no longer available product from save list
         * @author YIThemes
         * @since 1.0.3
         * @param $product_id
         * @param $variation_id
         */
        public function remove_no_available_product_form_save_list( $product_id, $variation_id ){
            $this->savelists['product_id']  =  $product_id;
            $this->savelists['variation_id']  =  $variation_id  ;
            $this->remove();
        }

        /** call ajax for remove a product from savelist
         * @author YITHEMES
         * @since 1.0.0
         * overridden
         */
    /*    public function remove_from_savelist_ajax(){

            $this->savelists['product_id']  =   isset( $_POST['remove_from_savelist'] ) ? $_POST['remove_from_savelist']    :   -1;
            $this->savelists['variation_id']  =  isset(  $_POST['variation_id'] ) ? $_POST['variation_id'] : -1;
            $result =   $this->remove();
            $message    =   '';

            if( $result=="true" )
                $message    =   __('Product deleted from Save for later', 'yith-woocommerce-save-for-later');
            else
                $message    =   __('No product', 'yith-woocommerce-save-for-later');

            wp_send_json(
                array(
                    'result'    =>  $result,
                    'message'   =>  $message,
                    'template'  =>  YITH_WSFL_Shortcode::saveforlater( array() )
                )
            );

        }
*/
        /**check if a product is in savelist
         * @author YITHEMES
         * @since 1.0.0
         * @param $product_id
         * @return bool
         * overridden
         */
        public function is_product_in_savelist( $product_id, $variation_id=-1 ){
            $exist =   false;

            if ( is_user_logged_in() ){
                global $wpdb;

                $user_id    =   get_current_user_id();

                $query  =   "SELECT COUNT(*) as cnt
                             FROM {$wpdb->yith_wsfl_table}
                             WHERE {$wpdb->yith_wsfl_table}.product_id=%d AND {$wpdb->yith_wsfl_table}.user_id=%d";

                $parms  =   array(
                    $product_id,
                    $user_id
                );

                if ($variation_id > 0)
                {
                    $query.=    " AND {$wpdb->yith_wsfl_table}.variation_id=%d";
                    $parms[]=$variation_id;
                }

                $results = $wpdb->get_var( $wpdb->prepare( $query, $parms ) );

                return (bool) ( $results > 0 );
            }
            else
            {
                $cookie =   yith_getcookie('yith_wsfl_savefor_list');

                foreach( $cookie as $key=>$item ){
                    if( $item['product_id']==$product_id && $item['variation_id']==$variation_id )
                        $exist  =   true;
                }
                return $exist;
            }

        }

        /** remove product to savelist
         * @author YITHEMES
         * @since 1.0.0
         * @return string
         * overridden
         */
        public function remove(){

            global $wpdb;
            $user_id        =   isset( $this->savelists['user_id'] )        ?   $this->savelists['user_id']     :   -1;
            $product_id     =   isset( $this->savelists['product_id'] )     ?   $this->savelists['product_id']  :   -1;
            $variation_id   =   isset(  $this->savelists['variation_id'] )  ?    $this->savelists['variation_id']   :   -1;

            if( $product_id==-1 )
                return "errors";

            if( is_user_logged_in() ){

                $sql        =   "DELETE FROM {$wpdb->yith_wsfl_table} WHERE {$wpdb->yith_wsfl_table}.user_id=%d AND {$wpdb->yith_wsfl_table}.product_id=%d";

                $sql_parms  =   array(
                    $user_id,
                    $product_id
                );

                if( $variation_id > 0 ) {

                    $sql            .= " AND {$wpdb->yith_wsfl_table}.variation_id=%d";
                    $sql_parms[]    =   $variation_id;
                }


                $result     =   $wpdb->query( $wpdb->prepare($sql,$sql_parms));

                if( $result )
                    return "true";
                else
                    return "false";
            }
            else
            {
                $savelist_cookie    =   yith_getcookie('yith_wsfl_savefor_list');

                foreach( $savelist_cookie as $key=> $item ) {
                    if ( $item['product_id'] == $product_id ) {
                        if( $item['variation_id'] == $variation_id )
                            unset( $savelist_cookie[$key] );
                    }
                }

                yith_setcookie('yith_wsfl_savefor_list', $savelist_cookie );

                return "true";
            }
        }

        public function include_single_product_script(){
            if( is_product() ){

                wp_enqueue_script( 'ywsfl_single_product', YWSFL_ASSETS_URL.'/js/'.yit_load_js_file('ywsfl_single_product.js'), array('jquery'), YWSFL_VERSION, true );
                $ywsfl_single_param = array(
                    'actions' => array( 
                        'add_single_product_save_list' => 'add_single_product_save_list',
                        'remove_after_add_list' => 'remove_to_cart_after_save_list',
                        'remove_from_savelist' => 'remove_from_savelist'
                        ),
                    
                    'ajax_url' => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
	                'view_list' => array(
	                	'label' => __( 'View List', 'yith-woocommerce-save-for-later'),
		                'url' =>  get_post_permalink( get_option( 'yith-wsfl-page-id' ) )
	                )
                );
                wp_localize_script( 'ywsfl_single_product', 'ywsfl_single_product_args', $ywsfl_single_param );
                wp_enqueue_style( 'ywsfl_single_product_style', YWSFL_ASSETS_URL.'css/ywsfl_singleproduct.css', array(), YWSFL_VERSION );

            }
        }

        public function show_single_product_page(){

            global $product;

            if( ( $product instanceof  WC_Product ) && $product->is_type( array('simple','variable' ) ) ) {
                wc_get_template( 'single-product/saveforlater-button.php', array(), '', YWSFL_TEMPLATE_PATH );
            }
        }


        public function ajax_add_single_product_save_list() {

            $product_id = isset( $_REQUEST['product_id'] ) ? $_REQUEST['product_id'] : -1 ;
            $variation_id = isset( $_REQUEST['variation_id'] ) ? $_REQUEST['variation_id'] : -1;

            $this->savelists['product_id']      =  $product_id;
            $this->savelists['variation_id']    =  $variation_id;
            $result =   $this->add();

            if( $result == 'true' ){
                $message = __('Product added', 'yith-woocommerce-save-for-later');
            }
            else{
                $message = __('Product already in Save for later', 'yith-woocommerce-save-for-later') ;
            }

            wp_send_json( 
                    array(
                        'result' => $result,
                        'message' => $message,
                        'template'  =>  YITH_WSFL_Shortcode::saveforlater( array() )
                    )
            );
        }

        public function variation_in_save_for_later_list( ){

            $product_id =   isset( $_REQUEST['product_id'] ) ? $_REQUEST['product_id'] : false;

            $variation_id   =    isset( $_REQUEST['variation_id'] ) ? $_REQUEST['variation_id'] : -1;

            $is_in_save_list = false;

            if( $product_id ) {
                $is_in_save_list = $this->is_product_in_savelist($product_id, $variation_id);
            }



            wp_send_json( array( 'in_save_list' => $is_in_save_list ) );
        }

	    /**
	     * plugin_row_meta
	     *
	     * add the action links to plugin admin page
	     *
	     * @param $new_row_meta_args
	     * @param $plugin_meta
	     * @param $plugin_file
	     * @param $plugin_data
	     * @param $status
	     *
	     * @return   array
	     * @since    1.0
	     * @author   Andrea Grillo <andrea.grillo@yithemes.com>
	     * @use plugin_row_meta
	     */
	    public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YWSFL_INIT' ) {

	    	$new_row_meta_args = parent::plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file );
		    if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
			    $new_row_meta_args['is_premium'] = true;
		    }

		    return $new_row_meta_args;

	    }
}
}