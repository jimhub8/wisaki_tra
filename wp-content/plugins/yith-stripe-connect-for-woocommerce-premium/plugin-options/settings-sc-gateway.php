<?php
/**
 * Settings for Stripe Connect Gateway.
 */

return apply_filters( 'yith_wcsc_general_settings', array(
		'enabled'              => array(
			'title'   => _x( 'Enable/Disable', 'Settings, activate or deactivate Stripe Connect', 'yith-stripe-connect-for-woocommerce' ),
			'type'    => 'checkbox',
			'label'   => _x( 'Enable Stripe Connect Gateway', 'Settings, Label for checkbox that enables/disables Stripe Connect',
				'yith-stripe-connect-for-woocommerce' ),
			'default' => 'yes',
			'id'      => 'yith_wcsc_on_off'
		),
		'label'                => array(
			'title'       => __( 'Label Settings', 'yith-stripe-connect-for-woocommerce' ),
			'type'        => 'title',
			'description' => __( 'Change the title and description that Stripe Connect displays on Checkout', 'yith-stripe-connect-for-woocommerce' ),
			'id'          => 'yith_wcsc_label_settings'
		),
		'label-title'          => array(
			'title'       => __( 'Title', 'yith-stripe-connect-for-woocommerce' ),
			'type'        => 'text',
			'description' => __( 'This controls the title that users see during checkout.', 'yith-stripe-connect-for-woocommerce' ),
			'default'     => __( 'Credit Card (Stripe Connect)', 'yith-stripe-connect-for-woocommerce' ),
			'desc_tip'    => true,
		),
		'label-description'    => array(
			'title'       => __( 'Description', 'yith-stripe-connect-for-woocommerce' ),
			'type'        => 'text',
			'desc_tip'    => true,
			'description' => __( 'This controls the description that users see during checkout.', 'yith-stripe-connect-for-woocommerce' ),
			'default'     => __( "Pay via Stripe Connect; you can pay with your credit card if you don't have a Stripe account.", 'yith-stripe-connect-for-woocommerce' ),
		),
		'credit-cards-logo'    => array(
			'id'       => 'yith_wcsc_logo_card',
			'title'    => __( 'Display logo card', 'yith-stripe-connect-for-woocommerce' ),
			'type'     => 'multiselect',
			'desc'     => __( 'Choose the card logo that you want shows', 'yith-stripe-connect-for-woocommerce' ),
			'desc_tip' => true,
			'options'  => array(
				'american-express' => 'A.Express',
				'discover'         => 'Discover',
				'mastercard'       => 'Mastercard',
				'visa'             => 'Visa',
				'diners'           => 'Diners Club',
				'jcb'              => 'JCB'
			),
		),
		'show-name-on-card'    => array(
			'id'          => 'yith_wcsc_show_name_on_card',
			'title'       => __( 'Display "Name on Card" field during checkout', 'yith-stripe-connect-for-woocommerce' ),
			'type'        => 'checkbox',
			'description' => __( 'Choose whether to show "Name on Card" field of Credit Card form', 'yith-stripe-connect-for-woocommerce' ),
			'default'     => 'no'
		),
		'api'                  => array(
			'title'       => __( 'API Settings', 'yith-stripe-connect-for-woocommerce' ),
			'type'        => 'title',
			'description' => __( 'Type here your API Keys from Stripe Connect Account. This step is mandatory for the plugin to work',
				'yith-stripe-connect-for-woocommerce' ),
			'id'          => 'yith_wcsc_label_settings'
		),
		'api-prod-client-id'   => array(
			'title'       => __( 'Live mode client ID', 'yith-stripe-connect-for-woocommerce' ),
			'type'        => 'text',
			'description' => '<a href="https://dashboard.stripe.com/account/applications/settings" target="_blank">Stripe Dashboard > Connect > Settings ></a>' . ' <b>' . __( 'Client ID', 'yith-stripe-connect-for-woocommerce' ) . '</b> ' . __( 'section (Check before "View test data" is <b>disabled</b> )', 'yith-stripe-connect-for-woocommerce' ),
		),
		'api-public-live-key'  => array(
			'title'       => __( 'Publishable live key', 'yith-stripe-connect-for-woocommerce' ),
			'type'        => 'text',
			'description' => '<a href="https://dashboard.stripe.com/account/apikeys" target="_blank">Stripe Dashboard > API ></a>' . ' <b>' . __( 'Standard API keys', 'yith-stripe-connect-for-woocommerce' ) . '</b> ' . __( 'section (Check before "View test data" is <b>disabled</b> )', 'yith-stripe-connect-for-woocommerce' ),
		),
		'api-secret-live-key'  => array(
			'title'       => __( 'Secret live key', 'yith-stripe-connect-for-woocommerce' ),
			'type'        => 'text',
			'description' => '<a href="https://dashboard.stripe.com/account/apikeys" target="_blank">Stripe Dashboard > API ></a>' . ' <b>' . __( 'Standard API keys', 'yith-stripe-connect-for-woocommerce' ) . '</b> ' . __( 'section, <b>Reveal live key token</b> (Check before "View test data" is <b>disabled</b> )', 'yith-stripe-connect-for-woocommerce' ),
		),
		'test-live'            => array(
			'title' => __( 'Test live', 'yith-stripe-connect-for-woocommerce' ),
			'type'  => 'checkbox',
			'label' => __( 'Enable Test live mode', 'yith-stripe-connect-for-woocommerce' ),
			'id'    => 'yith_wcsc_test_live_mode',
		),
		'api-dev-client-id'    => array(
			'title'       => __( 'Test mode client ID', 'yith-stripe-connect-for-woocommerce' ),
			'type'        => 'text',
			'description' => '<a href="https://dashboard.stripe.com/account/applications/settings" target="_blank">Stripe Dashboard > Connect > Settings ></a>' . ' <b>' . __( 'Client ID', 'yith-stripe-connect-for-woocommerce' ) . '</b> ' . __( 'section (Check before "View test data" is <b>enabled</b> )', 'yith-stripe-connect-for-woocommerce' ),
			'class'       => 'yith_wcsc_test_live_item'
		),
		'api-public-test-key'  => array(
			'title'       => __( 'Publishable test key', 'yith-stripe-connect-for-woocommerce' ),
			'type'        => 'text',
			'description' => '<a href="https://dashboard.stripe.com/account/apikeys" target="_blank">Stripe Dashboard > API ></a>' . ' <b>' . __( 'Standard API keys', 'yith-stripe-connect-for-woocommerce' ) . '</b> ' . __( 'section (Check before "View test data" is <b>enabled</b> )', 'yith-stripe-connect-for-woocommerce' ),
			'class'       => 'yith_wcsc_test_live_item'
		),
		'api-secret-test-key'  => array(
			'title'       => __( 'Secret test key', 'yith-stripe-connect-for-woocommerce' ),
			'type'        => 'text',
			'description' => '<a href="https://dashboard.stripe.com/account/apikeys" target="_blank">Stripe Dashboard > API ></a>' . ' <b>' . __( 'Standard API keys', 'yith-stripe-connect-for-woocommerce' ) . '</b> ' . __( 'section, <b>Reveal live key token</b> (Check before "View test data" is <b>enabled</b> )', 'yith-stripe-connect-for-woocommerce' ),
			'class'       => 'yith_wcsc_test_live_item'
		),
		'credit-cards-logo'    => array(
			'title'   => __( 'Display card logo', 'yith-stripe-connect-for-woocommerce' ),
			'type'    => 'multiselect',
			'desc'    => __( 'Choose the card logo that you want to show', 'yith-stripe-connect-for-woocommerce' ),
			'id'      => 'yith_wcsc_logo_card',
			'options' => array(
				'american-express' => 'A.Express',
				'discover'         => 'Discover',
				'mastercard'       => 'Mastercard',
				'visa'             => 'Visa',
				'diners'           => 'Diners Club',
				'jcb'              => 'JCB'
			),
			'css'     => 'min-width:300px;',
			'class'   => 'list-select'
		),
		'payment'              => array(
			'title'       => 'Payment settings',
			'type'        => 'title',
			'description' => __( 'Indicate after how many days the payment will be sent to the repeaters', 'yith-stripe-connect-for-woocommerce' )
		),
		'payment-delay'        => array(
			'title'       => __( 'Delay time', 'yith-stripe-connect-for-woocommerce' ),
			'type'        => 'number',
			'id'          => 'yith_wcsc_payment_delay',
			'description' => __( 'Instant payment if empty or with "0" value', 'yith-stripe-connect-for-woocommerce' ),
			'desc_tip'    => true
		),
		'others'               => array(
			'title' => 'Other',
			'type'  => 'title',
		),
		'test-live'            => array(
			'title' => __( 'Test live', 'yith-stripe-connect-for-woocommerce' ),
			'type'  => 'checkbox',
			'label' => __( 'Enable test live mode', 'yith-stripe-connect-for-woocommerce' ),
			'id'    => 'yith_wcsc_test_live_mode'
		),
		'log'                  => array(
			'title'       => __( 'Log', 'yith-stripe-connect-for-woocommerce' ),
			'type'        => 'checkbox',
			'label'       => __( 'Enable log', 'yith-stripe-connect-for-woocommerce' ),
			'id'          => 'yith_wcsc_log',
			'description' => sprintf( __( 'Log Stripe Connect events inside <code>%s</code>', 'yith-stripe-connect-for-woocommerce' ), wc_get_log_file_path( 'stripe-connect' ) ) . '<br />' . sprintf( __( 'You can also consult the logs in your <a href="%s">Log Dashboard</a>, without checking this option.', 'yith-woocommerce-stripe' ), 'https://dashboard.stripe.com/logs' )

		),
		'commissions-exceeded' => array(
			'title' => __( 'Exceeding commissions', 'yith-stripe-connect-for-woocommerce' ),
			'type'  => 'checkbox',
			'label' => __( 'Enable this option to allow commission creation when the commissions exceed the order total' ),
			'id'    => 'yith_wcsc_commissions_exceeded'
		),
		'webhooks'      => array(
			'title'       => __( 'Config Webhooks', 'yith-woocommerce-stripe' ),
			'type'        => 'title',
			'description' => sprintf( __( 'You can configure the webhook url %s in your <a href="%s" target="_blank">Stripe Dashboard > API > Webhooks</a> (Endpoints receiving events from Connect applications) section. All the webhooks for all your connected users will be sent to this endpoint.', 'yith-stripe-connect-for-woocommerce' ), '<code>' . esc_url( site_url( '/wc-api/sc_webhook_event' ) ) . '</code>', 'https://dashboard.stripe.com/account/webhooks' ) . '<br /><br />'
			                 . __( "It's important to note that only test webhooks will be sent to your development webhook url*. Yet, if you are working on a live website, <b>both live and test</b> webhooks will be sent to your production webhook URL. This is due to the fact that you can create both live and test objects under a production application.", 'yith-stripe-connect-for-woocommerce' ) . ' — ' . __( "we'd recommend that you check the livemode when receiving an event webhook.", 'yith-stripe-connect-for-woocommerce' ) . '<br /><br />'
			                 . sprintf( __( 'For more information about webhooks, see the <a href="%s" target="_blank">webhook documentation</a>', 'yith-stripe-connect-for-woocommerce' ), 'https://stripe.com/docs/webhooks' ),
		),
		'redirect-uris' => array(
			'title'       => __( 'Config Redirect URIs', 'yith-woocommerce-stripe' ),
			'type'        => 'title',
			'description' => sprintf( __( 'A <b>Redirection URI is required</b> when users connect their account to your site. Go to <a href="%s" target="_blank">Stripe Dashboard > Connect > Settings ></a> <b>Redirect URIs</b> section and add the following URl to redirect: %s. Redirects URI can be defined on test and live mode, we would recommend to test both scenarios.', 'yith-stripe-connect-for-woocommerce' ), 'https://dashboard.stripe.com/account/applications/settings', '<code>' . esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) . 'stripe-connect' ) . '</code>' )
		)
	)
);