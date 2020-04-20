<?php
if( !defined('ABSPATH')){
    exit;
}

if( !class_exists( 'YITH_Delivery_Date_Integrations' ) ){
    
    class YITH_Delivery_Date_Integrations{
        
        protected static  $instance;
        
        public function __construct()
        {
            if( $this->is_multivendor_active() ){
                
                require_once( 'integrations/class.yith-multivendor-integration.php' );
            }

            if( defined('TABLE_RATE_SHIPPING_VERSION')  ){

            	require_once( 'integrations/class.yith-wc-shipping-table-rate-integration.php' );
            }

            if( defined( 'WC_SHIPPING_FEDEX_VERSION' ) ){
	            require_once( 'integrations/class.yith-wc-fedex-integration.php' );
            }

            if( class_exists( 'WPDesk_Flexible_Shipping' ) ){

                require_once( 'integrations/class.yith-wc-flexible-shipping-integration.php' );
            }

            if( class_exists( 'Woocommerce_Distance_Rate_Shipping' ) ){
            	require_once( 'integrations/class.yith-wc-distance-rate-integration.php');
            }

            if( class_exists( 'tree_table_rate' ) ){
            	require_once( 'integrations/class.yith-wc-tree-table-rate-shipping.php' );
            }


        }

        /**
         * @return YITH_Delivery_Date_Integrations
         */
        public static function get_instance()
        {
            if( is_null( self::$instance )){
                self::$instance = new self();
            }
            return self::$instance;
        }
        
        
        public function is_multivendor_active(){
            
            return defined( 'YITH_WPV_PREMIUM' ) && YITH_WPV_PREMIUM;
        }
    }
}


function YITH_Delivery_Date_Integrations(){
    
    return YITH_Delivery_Date_Integrations::get_instance();
}

YITH_Delivery_Date_Integrations();