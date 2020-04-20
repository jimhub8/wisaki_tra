<?php 
$wcst_active_plugins = get_option('active_plugins');
$wcst_acf_pro = 'advanced-custom-fields-pro/acf.php';
$wcst_acf_pro_is_aleady_active = in_array($wcst_acf_pro, $wcst_active_plugins) || class_exists('acf') ? true : false;
if(!$wcst_acf_pro_is_aleady_active)
	include_once( WCST_PLUGIN_ABS_PATH . '/classes/acf/acf.php' );

$wcst_hide_menu = true;
if ( ! function_exists( 'is_plugin_active' ) ) 
{
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); 
}
/* Checks to see if the acf pro plugin is activated  */
if ( is_plugin_active('advanced-custom-fields-pro/acf.php') )  {
	$wcst_hide_menu = false;
}

/* Checks to see if the acf plugin is activated  */
if ( is_plugin_active('advanced-custom-fields/acf.php') ) 
{
	add_action('plugins_loaded', 'wcst_load_acf_standard_last', 10, 2 ); //activated_plugin
	add_action('deactivated_plugin', 'wcst_detect_plugin_deactivation', 10, 2 ); //activated_plugin
	$wcst_hide_menu = false;
}
function wcst_detect_plugin_deactivation(  $plugin, $network_activation ) { //after
   // $plugin == 'advanced-custom-fields/acf.php'
	//wcst_var_dump("wcst_detect_plugin_deactivation");
	$acf_standard = 'advanced-custom-fields/acf.php';
	if($plugin == $acf_standard)
	{
		$active_plugins = get_option('active_plugins');
		$this_plugin_key = array_keys($active_plugins, $acf_standard);
		if (!empty($this_plugin_key)) 
		{
			foreach($this_plugin_key as $index)
				unset($active_plugins[$index]);
			update_option('active_plugins', $active_plugins);
			//forcing
			deactivate_plugins( plugin_basename( WP_PLUGIN_DIR.'/advanced-custom-fields/acf.php') );
		}
	}
} 
function wcst_load_acf_standard_last($plugin, $network_activation = null) { //before
	$acf_standard = 'advanced-custom-fields/acf.php';
	$active_plugins = get_option('active_plugins');
	$this_plugin_key = array_keys($active_plugins, $acf_standard);
	if (!empty($this_plugin_key)) 
	{ 
		foreach($this_plugin_key as $index)
			//array_splice($active_plugins, $index, 1);
			unset($active_plugins[$index]);
		//array_unshift($active_plugins, $acf_standard); //first
		array_push($active_plugins, $acf_standard); //last
		update_option('active_plugins', $active_plugins);
	} 
}

if(!$wcst_acf_pro_is_aleady_active)
	add_filter('acf/settings/path', 'wcst_acf_settings_path');
function wcst_acf_settings_path( $path ) 
{
 
    // update path
    $path = WCST_PLUGIN_ABS_PATH. '/classes/acf/';
    
    // return
    return $path;
    
}
if(!$wcst_acf_pro_is_aleady_active)
	add_filter('acf/settings/dir', 'wcst_acf_settings_dir');
function wcst_acf_settings_dir( $dir ) {
 
    // update path
    $dir = WCST_PLUGIN_PATH . '/classes/acf/';
    
    // return
    return $dir;
    
}

function wcst_acf_init() {
    
    include WCST_PLUGIN_ABS_PATH . "/assets/fields.php";
    
}
add_action('acf/init', 'wcst_acf_init');

//hide acf menu
if($wcst_hide_menu)	
	add_filter('acf/settings/show_admin', '__return_false');

//Avoid custom fields metabox removed by pages
add_filter('acf/settings/remove_wp_meta_box', '__return_false');


//Custom filtering functions 
function wcst_get_variation_complete_name($variation_id)
{
	$error = false;
	$variation = null;
	$variation = wc_get_product($variation_id);
	if($variation == null)
		return "";
	if($variation->is_type('simple'))
		return $variation->get_title();
	
	$product_name = $variation->get_title()." - ";	
	if($product_name == " - ")
		return false;
	$attributes_counter = 0;
	
	foreach($variation->get_variation_attributes( ) as $attribute_name => $value)
	{
		
		if($attributes_counter > 0)
			$product_name .= ", ";
		$meta_key = urldecode( str_replace( 'attribute_', '', $attribute_name ) ); 
		
		$product_name .= " ".wc_attribute_label($meta_key).": ".$value;
		$attributes_counter++;
	}
	return $product_name;
}

function wcst_change_product_name( $title, $post, $field, $post_id ) {

    if($post->post_type == "product_variation" )
	{
		$title_temp = "#{$post->ID} - ".wcst_get_variation_complete_name($post->ID);
		$title = $title_temp != false && $title_temp != "" ? $title_temp : $title;
	}
	 return $title;
}
add_filter('acf/fields/post_object/result', 'wcst_change_product_name', 10, 4);
?>