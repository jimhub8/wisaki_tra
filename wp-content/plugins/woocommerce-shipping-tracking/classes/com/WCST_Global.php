<?php 
function wcst_get_file_version( $file ) 
{

	// Avoid notices if file does not exist
	if ( ! file_exists( $file ) ) {
		return '';
	}

	// We don't need to write to the file, so just open for reading.
	$fp = fopen( $file, 'r' );

	// Pull only the first 8kiB of the file in.
	$file_data = fread( $fp, 8192 );

	// PHP will close file handle, but we are good citizens.
	fclose( $fp );

	// Make sure we catch CR-only line endings.
	$file_data = str_replace( "\r", "\n", $file_data );
	$version   = '';

	if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( '@version', '/' ) . '(.*)$/mi', $file_data, $match ) && $match[1] )
		$version = _cleanup_header_comment( $match[1] );

	return $version ;
	}
function wcst_get_woo_version_number() 
{
        // If get_plugins() isn't available, require it
	if ( ! function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	
        // Create the plugins folder and file variables
	$plugin_folder = get_plugins( '/' . 'woocommerce' );
	$plugin_file = 'woocommerce.php';
	
	// If the plugin version number is set, return it 
	if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
		return $plugin_folder[$plugin_file]['Version'];

	} else {
	// Otherwise return null
		return NULL;
	}
}
$wcst_result = get_option("_".$wcst_id);
//$wcst_notice = !$wcst_result || $wcst_result != md5($_SERVER['SERVER_NAME']);
$wcst_notice = false;
if(!$wcst_notice)
	wcst_setup();
if( !function_exists('apache_request_headers') ) 
{
    function wcst_apache_request_headers() {
        $arh = array();
        $rx_http = '/\AHTTP_/';

        foreach($_SERVER as $key => $val) {
            if( preg_match($rx_http, $key) ) {
                $arh_key = preg_replace($rx_http, '', $key);
                $rx_matches = array();
           // do some nasty string manipulations to restore the original letter case
           // this should work in most cases
                $rx_matches = explode('_', $arh_key);

                if( count($rx_matches) > 0 and strlen($arh_key) > 2 ) {
                    foreach($rx_matches as $ak_key => $ak_val) {
                        $rx_matches[$ak_key] = ucfirst($ak_val);
                    }

                    $arh_key = implode('-', $rx_matches);
                }

                $arh[$arh_key] = $val;
            }
        }

        return( $arh );
    }
}
function wcst_get_order_tracking_data($order_id)
{
	if(!isset($order_id))
		return;
	
	global $wcst_order_model;
	$result = $first_company = array();
	$tracking_meta = $wcst_order_model->get_order_meta($order_id);

	//First company
	foreach($wcst_order_model->tracking_key_array as $meta_name)
	{
		if(isset($tracking_meta[$meta_name]))
			switch($meta_name)
			{
				case '_wcst_order_trackno': $first_company["tracking_number"] = $tracking_meta[$meta_name][0]; break;
				case '_wcst_order_dispatch_date': $first_company["dispatch_date"] = $tracking_meta[$meta_name][0]; break;
				case '_wcst_custom_text': $first_company["custom_text"] = $tracking_meta[$meta_name][0]; break;
				case '_wcst_order_trackname': $first_company["company_name"] = $tracking_meta[$meta_name][0]; break;
				case '_wcst_order_trackurl': $first_company["company_id"] = $tracking_meta[$meta_name][0]; break;
				case '_wcst_order_track_http_url': $first_company["tracking_url"] = $tracking_meta[$meta_name][0]; break;
			}
	}
	if(!empty($first_company))
		$result[] = $first_company;
	
	//Additional companies
	if(isset($tracking_meta[$wcst_order_model->tracking_additional_company_key]))
	{
		$additional_company = array();			
		foreach($tracking_meta[$wcst_order_model->tracking_additional_company_key] as $current_additional_company)
		{
			foreach($wcst_order_model->tracking_key_array as $meta_name)
			{
				if(isset($current_additional_company[$meta_name]))
					switch($meta_name)
					{
						case '_wcst_order_trackno': $additional_company["tracking_number"] = $current_additional_company[$meta_name]; break;
						case '_wcst_order_dispatch_date': $additional_company["dispatch_date"] = $current_additional_company[$meta_name]; break;
						case '_wcst_custom_text': $additional_company["custom_text"] = $current_additional_company[$meta_name]; break;
						case '_wcst_order_trackname': $additional_company["company_name"] = $current_additional_company[$meta_name]; break;
						case '_wcst_order_trackurl': $additional_company["company_id"] = $current_additional_company[$meta_name]; break;
						case '_wcst_order_track_http_url': $additional_company["tracking_url"] = $current_additional_company[$meta_name]; break;
					}
			}
		}
		if(!empty($additional_company))
			$result[] = $additional_company;
	}
	//wcst_var_dump($result);
	return $result;
}
?>