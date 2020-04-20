<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ( isset( $_GET['page'] ) && 'yith_delivery_date_panel' == $_GET['page'] ) && ( isset( $_GET['tab'] ) && 'processing-method' == $_GET['tab'] ) ) {
	$admin_url        = admin_url( 'post-new.php' );
	$params           = array(
		'post_type' => 'yith_proc_method'
	);
	$add_new_url      = esc_url( add_query_arg( $params, $admin_url ) );
	$processing_table = '';

	if ( ! class_exists( 'YITH_Processing_Method_Table' ) ) {
		include_once( YITH_DELIVERY_DATE_INC . 'admin-tables/class.yith-delivery-date-processing-method-table.php' );
	}

	$processing_table = new YITH_Processing_Method_Table();

	$processing_type = get_option( 'ywcdd_processing_type', 'checkout' );
	?>
    <div id="ywcdd_processing_method_tab" class="wrap">
        <!--<div id="ywcdd_processing_type" class="yith-plugin-fw yit-admin-panel-container">
            <table class="form-table">
                <tbody>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label><?php _e( 'Processing type', 'yith-woocommerce-delivery-date' ); ?></label></th>
                    <td class="frominp forminp-radio">
                        <fieldset>
                            <ul>
                                <li><label><input type="radio" name="ywcdd_processing_type"
                                                  value="checkout" <?php checked( 'checkout', $processing_type ); ?>><?php _e( 'Checkout', 'yith-woocommerce-delivery-date' ); ?>
                                    </label></li>
                                <li><label><input type="radio" name="ywcdd_processing_type"
                                                  value="product" <?php checked( 'product', $processing_type ); ?>><?php _e( 'Product', 'yith-woocommerce-delivery-date' ); ?>
                                    </label></li>
                            </ul>
                            <span class="description"><?php _e( 'Checkout: allow you to set delivery dates for the whole orders in processing methods. Product: allows you to set custom delivery dates for different products and will enable a table in product page', '' ); ?></span>
                        </fieldset>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        !-->
        <div class="ywcdd_processing_method_table_container yith-plugin-fw yit-admin-panel-container">
            <h2>
		        <?php _e( 'Processing Methods', 'yith-woocommerce-delivery-date' ); ?> <a href="<?php echo $add_new_url; ?>"
                                                                                          class="add-new-h2"><?php echo YITH_Delivery_Date_Processing_Method()->get_taxonomy_label( 'add_new' ); ?></a>
            </h2>
			<?php
			$processing_table->prepare_items();
			$processing_table->views();
			?>
            <form method="post">
				<?php
				$processing_table->display();
				?>
            </form>
        </div>
        <div class="ywcdd_custom_processing_day_container yith-plugin-fw yit-admin-panel-container">
            <h2><?php _e('Custom Processing Day', 'yith-woocommerce-delivery-date' );?></h2>
            <div class="ywcdd_custom_processing_day_for_product">
                <h4><?php _e('Processing days for products', 'yith-woocommerce-delivery-date' );?></h4>
                <?php
                    $type = 'product';

                    include( YITH_DELIVERY_DATE_TEMPLATE_PATH.'/admin/custom-processing-day-view.php');
                    include( YITH_DELIVERY_DATE_TEMPLATE_PATH.'/admin/add-new-custom-processing-day.php');
                ?>
            </div>
            <div class="ywcdd_custom_processing_day_for_categories">
                <h4><?php _e('Processing days for categories', 'yith-woocommerce-delivery-date' );?></h4>
		        <?php
		        $type = 'category';
		        include( YITH_DELIVERY_DATE_TEMPLATE_PATH.'/admin/custom-processing-day-view.php');
		        include( YITH_DELIVERY_DATE_TEMPLATE_PATH.'/admin/add-new-custom-processing-day.php');
		        ?>
            </div>
        </div>
    </div>
	<?php
}