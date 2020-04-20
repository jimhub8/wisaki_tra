<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

return  array(

	'privacy-settings' => array(

		'privacy_options_start'          => array(
			'type'  => 'sectionstart',
		),

		'privacy_options_title'          => array(
			'title' => __( 'Personal Data Exporter', 'yith-paypal-payouts-for-woocommerce' ),
			'type'  => 'title',
		),

		'privacy_options_payout' => array(
			'title'             => __( "Export user's payouts", 'yith-paypal-payouts-for-woocommerce' ),
			'type'              => 'checkbox',
			'default'           => 'yes',
			'desc'              => __( 'Export Payouts', 'yith-paypal-payouts-for-woocommerce' ),
			'desc_tip'          => sprintf( __( 'When handling an <a href="%s">account personal data export request</a>, should it include payouts ?', 'yith-paypal-payouts-for-woocommerce' ), esc_url( admin_url( 'tools.php?page=export_personal_data' ) ) ),
			'id'                => 'yith_payouts_export_payout'
		),

		'privacy_options_end'          => array(
			'type'  => 'sectionend',
		),

		'accounts-privacy_options_start'          => array(
			'type'  => 'sectionstart',
		),

		'privacy_eraser_options_title'          => array(
			'title' => __( 'Personal Data Eraser', 'yith-paypal-payouts-for-woocommerce' ),
			'type'  => 'title',
		),

		'privacy_options_eraser_user_data' => array(
			'title'             => __( "User data", 'yith-paypal-payouts-for-woocommerce' ),
			'type'              => 'checkbox',
			'default'           => 'no',
			'desc'              => __( "Remove user data info", 'yith-paypal-payouts-for-woocommerce' ),
			'desc_tip'          => sprintf( __( 'When handling an <a href="%s">account erasure request</a>, should user\'s PayPal email be retained or removed?', 'yith-paypal-payouts-for-woocommerce' ), esc_url( admin_url( 'tools.php?page=remove_personal_data' ) ) ),
			'id'                => 'yith_payouts_eraser_user_data'
		),

		'privacy_options_eraser_payout' => array(
			'title'             => __( "Payouts", 'yith-paypal-payouts-for-woocommerce' ),
			'type'              => 'checkbox',
			'default'           => 'no',
			'desc'              => __( "Remove customer's PayPal email for payouts", 'yith-paypal-payouts-for-woocommerce' ),
			'desc_tip'          => sprintf( __( 'When handling an <a href="%s">account erasure request</a>, should PayPal email within payouts be retained or removed?', 'yith-paypal-payouts-for-woocommerce' ), esc_url( admin_url( 'tools.php?page=remove_personal_data' ) ) ),
			'id'                => 'yith_payouts_eraser_payout'
		),

		'accounts-privacy_options_end'          => array(
			'type'  => 'sectionend',
		),
	)
);