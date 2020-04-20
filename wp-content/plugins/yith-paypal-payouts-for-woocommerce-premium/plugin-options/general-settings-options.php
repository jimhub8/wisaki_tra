<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$general_settings = array(
	'general-settings' => array(

		'payouts_section_start' => array(
			'type' => 'sectionstart'
		),

		'payout_section_title'             => array(
			'type' => 'title',
			'name' => __( 'Payouts API Settings', 'yith-paypal-payouts-for-woocommerce' ),
		),
		'payout_business_email'            => array(
			'id'   => 'yith_payouts_business_email',
			'name' => __( 'PayPal business email', 'yith-paypal-payouts-for-woocommerce' ),
			'desc' => __( 'Set your business PayPal email to use Payouts service', 'yith-paypal-payouts-for-woocommerc' ),
			'type' => 'text',

		),
		'payout_app_id'                    => array(
			'id'   => 'yith_payouts_application_id',
			'name' => __( 'Application Client ID', 'yith-paypal-payouts-for-woocommerce' ),
			'desc' => __( 'Set your application client ID to use Payouts service', 'yith-paypal-payouts-for-woocommerc' ),
			'type' => 'text',

		),
		'payout_app_secret_key'            => array(
			'id'   => 'yith_payouts_application_secret_key',
			'name' => __( 'Application Client KEY', 'yith-paypal-payouts-for-woocommerce' ),
			'desc' => __( 'Set your application client secret Key to use Payouts service', 'yith-paypal-payouts-for-woocommerc' ),
			'type' => 'text',
		),
		'payout_sandbox_mode'              => array(
			'id'      => 'yith_payouts_sandbox_mode',
			'name'    => __( 'Enable Sandbox Mode', 'yith-paypal-payouts-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no'

		),
		'payout_enable_log'                => array(
			'id'      => 'yith_payouts_enable_log',
			'name'    => __( 'Enable Log', 'yith-paypal-payouts-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no'

		),
		'payout_include_vendor_commission' => array(
			'id'      => 'yith_payouts_exclude_vendor_commission',
			'name'    => __( 'Exclude Vendor Product from Payouts calculation', 'yith-paypal-payouts-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'yes',
			'desc'    => __( 'If checked, vendors\' products won\'t be calculated in payouts. This option is available only with YITH WooCommerce Multi Vendor version 3.0.0 or higher', 'yith-paypal-payouts-for-woocommerce' ),
			'class'   => ! defined( 'YITH_WPV_PREMIUM' ) ? 'disable_option' : ''
		),

		'payout_webhook_info' => array(
			'id'        => 'payout_webhook',
			'type'      => 'yith-field',
			'yith-type' => 'webhook-info',
			'name'      => __( 'WebHook Configuration', 'yith-paypal-payouts-for-woocommerce' ),
			'desc'      => sprintf( __( 'You can configure the webhook URL %s in your <a href="%s">application settings</a>. All the webhooks for all your connected users will be sent to this endpoint.', 'yith-paypal-payouts-for-woocommerce' ), '<code>' . esc_url( add_query_arg( 'wc-api', 'yith_payouts_response', site_url( '/' ) ) ) . '</code>', 'https://developer.paypal.com/developer/applications/' ) . '<br /><br />'
			               . __( "It's important to note that only test webhooks will be sent to your development webhook URL. Yet, if you are working on a live website, <b>both live and test</b> webhooks will be sent to your production webhook URL. This is due to the fact that you can create both live and test objects under a production application.", 'yith-paypal-payouts-for-woocommerce' ) . ' — ' . __( "we'd recommend that you check the live mode when receiving an event webhook.", 'yith-paypal-payouts-for-woocommerce' ) . '<br /><br />'
			               . sprintf( __( 'For further information about webhooks, refer to the <a href="%s">webhook documentation</a>', 'yith-paypal-payouts-for-woocommerce' ), 'https://developer.paypal.com/docs/integration/direct/webhooks/' ),
		),

		'payout_section_end' => array(
			'type' => 'sectionend'
		)
	)
);


return $general_settings;