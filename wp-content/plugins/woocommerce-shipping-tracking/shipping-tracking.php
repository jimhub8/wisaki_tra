<?php
/*
Plugin Name: WooCommerce Shipping Tracking
Description: WCST plugin adds shipping tacking code to woocommerce mails and view order page.
Author: Lagudi Domenico
Version: 22.4
*/


/*
Copyright: WooCommerce Shipping Tracking uses the ACF PRO plugin. ACF PRO files are not to be used or distributed outside of the WooCommerce Shipping Tracking plugin.
*/

//define('WCST_PLUGIN_PATH', WP_PLUGIN_URL."/".dirname( plugin_basename( __FILE__ ) ) );
define('WCST_PLUGIN_PATH', rtrim(plugin_dir_url(__FILE__), "/") );
define('WCST_PLUGIN_ABS_PATH', plugin_dir_path( __FILE__ ) );

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ||
     (is_multisite() && array_key_exists( 'woocommerce/woocommerce.php', get_site_option('active_sitewide_plugins') ))
	)
{
	$wcst_id = 11363158;
	$wcst_name = "WooCommerce Shipping Tracking";
	$wcst_activator_slug = "wcst-activator";
	
	//com
	include_once( "classes/com/WCST_Acf.php");
	include_once( "classes/com/WCST_Global.php");
	require_once('classes/admin/WCST_ActivationPage.php');
	
	add_action( 'init', 'wcst_12892_init');
	add_action('admin_notices', 'wcst_admin_notices' );
	add_action('admin_menu', 'wcst_init_act');
	if(defined('DOING_AJAX') && DOING_AJAX)
			wcst_init_act();
}

function wcst_unregister_css_and_js($enqueue_styles)
{
	//WCST_QuickAssignPage::force_dequeue_scripts($enqueue_styles);
}

function register_settings()
{
	register_setting('wcst_shipping_companies_group','wcst_options');
	register_setting('wcst_general_options_group','wcst_general_options');
	register_setting('wcst_template_messages_group','wcst_template_messages');
}
function wcst_admin_notices()
{
	global $wcst_notice, $wcst_name, $wcst_activator_slug;
	if($wcst_notice && (!isset($_GET['page']) || $_GET['page'] != $wcst_activator_slug))
	{
		 ?>
		<div class="notice notice-success">
			<p><?php echo sprintf(__( 'To complete the <span style="color:#96588a; font-weight:bold;">%s</span> plugin activation, you must verify your purchase license. Click <a href="%s">here</a> to verify it.', 'woocommerce-shipping-tracking' ), $wcst_name, get_admin_url()."admin.php?page=".$wcst_activator_slug); ?></p>
		</div>
		<?php
	}
}
function wcst_12892_init()
{
	load_plugin_textdomain('woocommerce-shipping-tracking', false, basename( dirname( __FILE__ ) ) . '/languages' );

	/* if(is_admin())
		wcst_init_act(); */
}
function wcst_init_act()
{
	global $wcst_activator_slug, $wcst_name, $wcst_id;
	new WCST_ActivationPage($wcst_activator_slug, $wcst_name, 'woocommerce-shipping-tracking', $wcst_id, WCST_PLUGIN_PATH);
}
function wcst_setup()
{
	global $wcst_checkout_page, $wcst_order_model, $wcst_email_model, $wcst_shipping_company_model, $wcst_html_helper,
		   $wcst_shortcodes, $wcst_time_model, $wcst_product_model, $wcst_tracking_info_displayer, $wcst_cron_helper, $wcst_order_page,
		   $wcst_order_table_page, $wcst_wdashboard, $wcst_extra_delivery, $wcst_product_page, $wcst_my_account_page, $wcst_order_table_fragment,
		   $wcst_product_table, $wcst_checkout_page;
	
	if(!class_exists('WCST_Option'))
		require_once('classes/com/WCST_Option.php');
	if(!class_exists('WCST_AfterShip'))
		require_once('classes/com/WCST_AfterShip.php');
	if(!class_exists('WCST_Wpml'))
		require_once('classes/com/WCST_Wpml.php');
	if(!class_exists('WCST_shipping_companies_url'))
		require_once('included_companies/WCST_shipping_companies_url.php');
	if(!class_exists('WCST_Order'))
		require_once('classes/com/WCST_Order.php');
	$wcst_order_model = new WCST_Order();

	if(!class_exists('WCST_Email'))
		require_once('classes/com/WCST_Email.php');
	$wcst_email_model = new WCST_Email();

	if(!class_exists('WCST_ShippingCompany'))
	{	require_once('classes/com/WCST_ShippingCompany.php');
		$wcst_shipping_company_model = new WCST_ShippingCompany();
	}
	if(!class_exists('WCST_HtmlHelper'))
	{	require_once('classes/com/WCST_HtmlHelper.php');
		$wcst_html_helper = new WCST_HtmlHelper();
	}
	if(!class_exists('WCST_Shortcodes'))
	{	require_once('classes/com/WCST_Shortcodes.php');
		$wcst_shortcodes = new WCST_Shortcodes();
	}
	if(!class_exists('WCST_Time'))
	{	require_once('classes/com/WCST_Time.php');
		$wcst_time_model = new WCST_Time();
	}
	if(!class_exists('WCST_Product'))
	{	require_once('classes/com/WCST_Product.php');
		$wcst_product_model = new WCST_Product();
	}
	if(!class_exists('WCST_Tracking_info_displayer'))
	{
		require_once('classes/com/WCST_Tracking_info_displayer.php');
		$wcst_tracking_info_displayer = new WCST_Tracking_info_displayer();
	}
	if(!class_exists('WCST_Cron'))
	{
		require_once('classes/com/WCST_Cron.php');
		$wcst_cron_helper = new WCST_Cron();
	}

	//admin
	if(!class_exists('WCST_AdminMenu'))
		require_once('classes/admin/WCST_AdminMenu.php');

	if(!class_exists('WCST_OrderPage'))
	{
		require_once('classes/admin/WCST_OrderPage.php');
		$wcst_order_page = new WCST_OrderPage();
	}
	if(!class_exists('WCST_OrderTable'))
	{
		require_once('classes/admin/WCST_OrderTable.php');
		$wcst_order_table_page = new WCST_OrderTable();
	}
	if(!class_exists('WCST_Dashboard'))
	{
		require_once('classes/admin/WCST_Dashboard.php');
		$wcst_wdashboard = new WCST_Dashboard();
	}
	if(!class_exists('WCST_ExtraDelivery'))
	{
		require_once('classes/com/WCST_ExtraDelivery.php');
		$wcst_extra_delivery = new WCST_ExtraDelivery();
	}
	if(!class_exists('WCST_EstimatorConfigurator'))
	{
		require_once('classes/admin/WCST_EstimatorConfigurator.php');
		/* $wcst_estimator_configurator = new WCST_EstimatorConfigurator(); */
	}
	if(!class_exists('WCST_QuickAssignPage'))
	{
		require_once('classes/admin/WCST_QuickAssignPage.php');
	}
	if(!class_exists('WCST_BulkImport'))
	{
		require_once('classes/admin/WCST_BulkImport.php');
	}
	if(!class_exists('WCST_DeliveryEstimatorConfigurator'))
	{
		require_once('classes/admin/WCST_DeliveryEstimatorConfigurator.php');
	}

	//frontend
	if(!class_exists('WCST_ProductPage'))
	{
		require_once('classes/frontend/WCST_ProductPage.php');
		$wcst_product_page = new WCST_ProductPage();
	}
	if(!class_exists('WCST_MyAccountPage'))
	{
		require_once('classes/frontend/WCST_MyAccountPage.php');
		$wcst_my_account_page = new WCST_MyAccountPage();
	}
	if(!class_exists('WCST_OrderTableFragment')) //Cart and Checkout page
	{
		require_once('classes/frontend/WCST_OrderTableFragment.php');
		$wcst_order_table_fragment = new WCST_OrderTableFragment();
	}
	if(!class_exists('WCST_ProductTable')) //Cart and Checkout page
	{
		require_once('classes/frontend/WCST_ProductTable.php');
		$wcst_product_table = new WCST_ProductTable();
	}
	if(!class_exists('WCST_CheckoutPage'))
	{
		require_once('classes/frontend/WCST_CheckoutPage.php');
		$wcst_checkout_page = new WCST_CheckoutPage();
	}

	add_action('admin_menu', 'init_wcst_admin_panel');
	add_action( 'admin_init', 'register_settings');
	add_action( 'wp_print_scripts', 'wcst_unregister_css_and_js' );
}
function wcst_get_free_menu_position($start, $increment = 0.1)
{
	foreach ($GLOBALS['menu'] as $key => $menu) {
		$menus_positions[] = $key;
	}

	if (!in_array($start, $menus_positions)) return $start;

	/* the position is already reserved find the closet one */
	while (in_array($start, $menus_positions))
	{
		$start += $increment;
	}
	return $start;
}
function init_wcst_admin_panel()
{
	if(!current_user_can('manage_woocommerce'))
		return;

	$place = wcst_get_free_menu_position(54, 0.5);

	add_menu_page( __( 'Shipping tracking', 'woocommerce' ), __( 'Shipping tracking', 'woocommerce-shipping-tracking' ), 'manage_woocommerce', 'wcst-shipping-tracking', null, WCST_PLUGIN_PATH."/img/menu-icon.png", (string)$place );
	add_submenu_page('wcst-shipping-tracking', __('Shipping companies','woocommerce-shipping-tracking'), __('Shipping companies','woocommerce-shipping-tracking'), 'manage_woocommerce', 'wcst-shipping-companies', 'wcst_render_option_page');
	//add_submenu_page('woocommerce', __('Shipping tracking options','woocommerce-shipping-tracking'), __('Shipping tracking options','woocommerce-shipping-tracking'), 'edit_shop_orders', 'woocommerce-shipping-tracking', 'wcst_render_option_page');
	add_submenu_page('wcst-shipping-tracking', __('Add custom company','woocommerce-shipping-tracking'), __('Add custom company','woocommerce-shipping-tracking'), 'manage_woocommerce', 'wcst-add-custom-shipping-company', 'wcst_render_option_page');
	add_submenu_page('wcst-shipping-tracking', __('Edit emails/order page messages','woocommerce-shipping-tracking'), __('Edit emails/order page messages','woocommerce-shipping-tracking'), 'manage_woocommerce', 'wcst-edit-messages', 'wcst_render_option_page');
	add_submenu_page('wcst-shipping-tracking', __('Delivery date and time input fields','woocommerce-shipping-tracking'), __('Delivery date and time input fields','woocommerce-shipping-tracking'), 'manage_woocommerce', 'wcst-delivery-extra-fields', 'wcst_render_option_page');
	add_submenu_page('wcst-shipping-tracking', __('General options & Texts','woocommerce-shipping-tracking'), __('General options & Texts','woocommerce-shipping-tracking'), 'manage_woocommerce', 'wcst-general-options', 'wcst_render_option_page');
	add_submenu_page('wcst-shipping-tracking', __('Quick assign','woocommerce-shipping-tracking'), __('Quick assign','woocommerce-shipping-tracking'), 'manage_woocommerce', 'wcst-quick-assign', 'wcst_render_wcst_quick_assign_page');
	add_submenu_page('wcst-shipping-tracking', __('Bulk import','woocommerce-shipping-tracking'), __('Bulk import','woocommerce-shipping-tracking'), 'manage_woocommerce', 'wcst-bulk-import', 'wcst_render_wcst_bulk_import_page');
	add_submenu_page('wcst-shipping-tracking', __('Estimated delivery for shipping rate','woocommerce-shipping-tracking'), __('Estimated delivery for shipping rate','woocommerce-shipping-tracking'), 'manage_woocommerce', 'wcst-delivery-estimator-configrator', 'wcst_render_delivery_estimator_configurator');

	remove_submenu_page( 'wcst-shipping-tracking', 'wcst-shipping-tracking');

	$wcst_estimator_configurator = new WCST_EstimatorConfigurator();
}
function wcst_render_delivery_estimator_configurator()
{
	$page = new WCST_DeliveryEstimatorConfigurator();
	$page->render_page();
}
function wcst_render_wcst_bulk_import_page()
{
	$page = new WCST_BulkImport();
	$page->render_page();
}
function wcst_render_wcst_quick_assign_page()
{
	$page = new WCST_QuickAssignPage();
	$page->render_page();
}
function wcst_render_option_page()
{

	$page = new WCST_AdminMenu();
	$page->render_page();
}
function wcst_var_dump($var)
{
	echo "<pre>";
	var_dump($var);
	echo "</pre>";
}
?>