<?php
class pw_brand_class_auto_update
{
	public $plugin_slug=__PW_BRAND_plugin_slug;
	public $username = '';
	public $email = '';
	public $api_key = '';
	public $item_valid_id='';
	public $domain='';
	public $license_key='';
	public $api_url = '';
	
	public function __construct()
	{
			add_action('init', array($this, 'on_wp_init'), 1);	

	        ////ADDED IN VER4.3.6
	        //SET DEAFULT VALUES
	        $this->username = 'proword';
	        $this->api_key = 't0kbg3ez6pl5yo1ojhhoja9d64swh6wi';
	        $this->item_valid_id='8039481'; //8218941
			$url= '';
			if(isset( $_SERVER['HTTP_HOST'])
			{
				$url= $_SERVER['SERVER_NAME'];
			}
			
	        $this->domain=$this->getHost($url);
	        $this->license_key=get_option('pw_woo_brand_activate_purchase_code');
	        $this->email=get_option('pw_woo_brand_activate_email','');
	        $this->api_url = 'http://proword.net/Update_Plugins/';
			
	        add_filter('pre_set_site_transient_update_plugins', array($this,'pw_report_check_for_plugin_update'));
	        // Take over the Plugin info screen
	        add_filter('plugins_api', array($this,'pw_report_plugin_api_call'), 10, 3);		
			
			add_action('admin_notices', array($this, 'display_update'));							
	}

    public function on_wp_init()
    {
        // Intercept nag dismiss request
        if (!empty($_REQUEST['pw_brand_purchase_code_dismiss'])) {
			update_option('pw_brand_purchase_code_dismiss', 'yes');
			$redirect_url = remove_query_arg(array('pw_brand_purchase_code_dismiss'));
			wp_redirect($redirect_url);
			exit;
        }	
    }
	
    public function display_update()
    {
        $show = get_option('pw_brand_purchase_code_dismiss','no');
        $code = get_option('pw_brand_purchase_code_verify','no');
        if ($show=='yes' || $code!='no') {
            return;
        }
		$title = __('Automatic Update Setup', 'woocommerce-brands');
		$url='<a href="' . admin_url('admin.php?page=wc-settings&tab=pw_woocommerce_brands') . '">' . __('Settings', 'woocommerce-brands') . '</a><br/>';
		$content = __('Automatic update is unavailable for <strong>Woocomerce Brands Pro</strong>. To enable automatic updates, enter your CodeCanyon Purchase Code in '.'', 'rightpress-updates').$url;
		//$content = __('There is a new version of <strong>Woocomerce Brands Pro</strong> available. To enable automatic updates, enter your CodeCanyon Purchase Code in '.'', 'rightpress-updates').$url;		
		$content .= '<div><small><a target="_blank" href="https://help.market.envato.com/hc/en-us/articles/202822600">' . __('Where do I find my Purchase Code?', 'woocommerce-brands') . '</a>&nbsp;&nbsp;&nbsp;<a href="' . add_query_arg(array('pw_brand_purchase_code_dismiss' => 'yes')) . '">' . __('Hide This Notice', 'woocommerce-brands') . '</a></small></div>';		
		echo '<div class="update-nag" style="display: block;"><h3 style="margin-top: 0.3em; margin-bottom: 0.6em;">' . $title . '</h3>' . $content . '</div>';		
    }
	
	function getHost($url) {
		$parseUrl = parse_url(trim($url));
		if(isset($parseUrl['host']))
		{
			$host = $parseUrl['host'];
		}
		else
		{
			$path = explode('/', $parseUrl['path']);
			$host = $path[0];
		}
		$host=str_ireplace('www.', '', $host);

		return trim($host);
	}

	function pw_report_check_for_plugin_update($checked_data) {
		global $api_url, $wp_version;
		$plugin_slug=__PW_BRAND_plugin_slug;
		$domain=$this->domain;
		$license_key=$this->license_key;
		$email=$this->email;
		$item_valid_id=$this->item_valid_id; //8218941
		$api_url = $this->api_url;

		update_option("UPDATE",$license_key.$domain.'AW'.$plugin_slug);
		//Comment out these two lines during testing.
		if (empty($checked_data->checked))
			return $checked_data;

		$args = array(
			'slug' => $plugin_slug,
			'version' => $checked_data->checked[$plugin_slug .'/main.php'],
		);

		/////////////
		/// /// CHECK LICENSE PLUGIN
		/////////////
		$request_string = array(
			"body" => array(
				"action" => "insert_licensekey",
				"license-key" => $this->license_key,
				"email" => $this->email,
				"domain" => $this->domain,
				"item-id" => $this->item_valid_id,
			)
		);
		$response = wp_remote_post($this->api_url, $request_string);
		if ( is_wp_error( $response ) or ( wp_remote_retrieve_response_code( $response ) != 200 ) ) {
			update_option('pw_brand_purchase_code_verify', 'no');
			return false;
		}
		$response = json_decode($response['body']);

		if(is_array( $response ))
		{
            if(@$response[0]=='not_valid')
    		{
    		  update_option('pw_brand_purchase_code_verify', 'no');  
    		}
		}
		else
		{
		    update_option('pw_brand_purchase_code_verify', 'yes');
		}
	/*	if(@$response[0]=='not_valid')
		{
		  update_option('pw_brand_purchase_code_verify', 'no');  
		}
		else
		{
		    update_option('pw_brand_purchase_code_verify', 'yes');  
		}		
*/
		$request_string = array(
			'body' => array(
				'action' => 'basic_check',
				'request' => serialize($args),
				'api-key' => md5(get_bloginfo('url')),
				'license-key' => $license_key,
				'email' => $email,
				'domain' => $domain,
				'item-id' => $item_valid_id,

			),
			'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
		);

		// Start checking for an update
		$raw_response = wp_remote_post($api_url, $request_string);
		$response='';
		if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200) && isset($raw_response['body']))
		{
			$response = unserialize($raw_response['body']);
			//add_action('admin_notices', array($this, 'display_update'));
		}

		if (is_object($response) && !empty($response)) // Feed the update data into WP updater
			$checked_data->response[$plugin_slug .'/main.php'] = $response;

		return $checked_data;
	}
	function pw_report_plugin_api_call($def, $action, $args) {
		global  $api_url, $wp_version;

		$plugin_slug=__PW_BRAND_plugin_slug;
		$domain=$this->domain;
		$license_key=$this->license_key;
		$email=$this->email;
		$item_valid_id=$this->item_valid_id; //8218941
		$api_url = $this->api_url;

		if (!isset($args->slug) || ($args->slug != $plugin_slug))
			return false;

		// Get the current version
		$plugin_info = get_site_transient('update_plugins');
		$current_version = $plugin_info->checked[$plugin_slug .'/main.php'];
		$args->version = $current_version;

		$request_string = array(
			'body' => array(
				'action' => $action,
				'request' => serialize($args),
				'api-key' => md5(get_bloginfo('url')),
				'license-key' => $license_key,
				'email' => $email,
				'domain' => $domain,
				'item-id' => $item_valid_id,
			),
			'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
		);

		$request = wp_remote_post($api_url, $request_string);
		if (is_wp_error($request)) {
			$res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'), $request->get_error_message());
		} else {
			$res = unserialize($request['body']);

			if ($res === false)
				$res = new WP_Error('plugins_api_failed', __('An unknown error occurred'), $request['body']);
		}

		return $res;
	}
		
}
new pw_brand_class_auto_update();



?>