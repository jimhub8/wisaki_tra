<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $_GET['page'] ) && 'yith_delivery_date_panel' == $_GET['page'] ) {
	$admin_url        = admin_url( 'post-new.php' );
	$params['post_type'] = 'yith_carrier';
	$add_new_url         = esc_url( add_query_arg( $params, $admin_url ) );
	if ( ! class_exists( 'YITH_Carrier_Table' ) ) {
		include_once( YITH_DELIVERY_DATE_INC . 'admin-tables/class.yith-delivery-date-carrier-table.php' );
	}

	$carrier_table = new YITH_Carrier_Table();
	?>
    <div id="ywcdd_carrier_tab" class="wrap">
        <div class="ywcdd_carrier_table_container yith-plugin-fw yit-admin-panel-container">
            <h2>
				<?php _e( 'Carriers', 'yith-woocommerce-delivery-date' ); ?> <a href="<?php echo $add_new_url; ?>"
                                                                               class="add-new-h2"><?php echo YITH_Delivery_Date_Carrier()->get_taxonomy_label( 'add_new' ); ?></a>
            </h2>
			<?php
			$carrier_table->prepare_items();
			$carrier_table->views();
			?>
            <form method="post">
				<?php
				$carrier_table->display();
				?>
            </form>
        </div>
    </div>
	<?php
}