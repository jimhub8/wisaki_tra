<?php
global $wpdb, $woocommerce;

if( !is_product() ) {
    wp_enqueue_script( 'yith_wsfl_free' );
    wp_enqueue_script( 'yith_wsfl_premium' );
}
?>
    <div id="yith-wsfl-messages"></div>

<?php wc_get_template( 'saveforlater-' . $template_part . '.php', $atts, YWSFL_TEMPLATE_PATH,YWSFL_TEMPLATE_PATH ) ?>