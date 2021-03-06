<?php 
class WCST_Email
{
	public function __construct()
	{
	}
	public function send_active_notification_email_with_tracking_codes($recipients, $content, $subject = "", $email_heading = "", $order = null)
	{
		$mail = WC()->mailer();
		//$email_heading = get_bloginfo('name');
		//wcsts_var_dump($mail);
		ob_start();
		$mail->email_header($email_heading);
		echo stripcslashes($content);
		$mail->email_footer();
		$message =  ob_get_contents();
		ob_end_clean(); 
		
		//$subject = __('Your products have been shipped','woocommerce-shipping-tracking');
		
		do_action('wcst_before_active_notification_email', $recipients, $order);
		
		add_filter('wp_mail_from_name',array(&$this, 'wp_mail_from_name'), 99, 1);
		add_filter('wp_mail_from', array(&$this, 'wp_mail_from')/* , 99, 1 */);
		$attachments = /* isset($attachment[$recipients]) ? $attachment[$recipients] : */ array();
		if(!$mail->send( $recipients, $subject, $message, "Content-Type: text/html\r\n", $attachments)) //$mail->send || wp_mail
			wp_mail( $recipients, $subject, $message, "Content-Type: text/html\r\n", $attachments);
		remove_filter('wp_mail_from_name',array(&$this, 'wp_mail_from_name'));
		remove_filter('wp_mail_from',array(&$this, 'wp_mail_from'));
		
		do_action('wcst_after_active_notification_email', $recipients, $order);
	}
	public function wp_mail_from_name($name) 
	{
		/* global $wcsts_text_helper;
		$text = $wcsts_text_helper->get_email_sender_name(); */
		return get_bloginfo('name');
	}
	public function wp_mail_from($content_type) 
	{
		$server_headers = function_exists('apache_request_headers') ? apache_request_headers() : wcst_apache_request_headers();
		$domain = isset($server_headers['Host']) ? $server_headers['Host'] : null ;
		if(!isset($domain) && isset($_SERVER['HTTP_HOST']))
			$domain = str_replace("www.", "", $_SERVER['HTTP_HOST'] );
		
		return isset($domain) ? 'noprely@'.$domain : $content_type;
	}
	public function force_status_email_sending($action, $order)
	{
		// Ensure gateways are loaded in case they need to insert data into the emails
		WC()->payment_gateways();
		WC()->shipping();

		// Load mailer
		$mailer = WC()->mailer();

		$email_to_send = str_replace( 'send_email_', '', $action );

		$mails = $mailer->get_emails();

		if ( ! empty( $mails ) ) {
			foreach ( $mails as $mail ) {
				if ( $mail->id == $email_to_send ) {
					$mail->trigger( WCST_Order::get_id($order));
					//$order->add_order_note( sprintf( __( '%s email notification manually sent.', 'woocommerce' ), $mail->title ), false, true );
				}
			}
		}
	}
	function send_error_email_to_admin($text)
	{
		$mail = WC()->mailer();
		$email_heading = get_bloginfo('name');
		$subject = __('Something needs your attention...', 'woocommerce-shipping-tracking');
		
		ob_start();
		$mail->email_header($email_heading );
		_e('<h2>The following error has been generated by your site:</h2>', 'woocommerce-shipping-tracking');
		echo "<p>".$text."</p>";
		$mail->email_footer();
		$message =  ob_get_contents();
		ob_end_clean(); 
		
		
		$mail->send( get_bloginfo('admin_email'), $subject, $message, "Content-Type: text/html\r\n");
	}
}