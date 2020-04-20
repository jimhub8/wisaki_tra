<?php
$text_button  = '';
$button_class = '';
$message_text = '';
if ( 'connect' == $current_status ) {
	$text_button  = __( 'Disconect from Stripe', 'yith-stripe-connect-for-woocommerce' );
	$button_class = 'yith-sc-disconnect';
} else if ( 'disconnect' == $current_status ) {
	$text_button = __( 'Connect with Stripe', 'yith-stripe-connect-for-woocommerce' );
}
?>
<span class="message"> </span>
<a id="yith-sc-connect-button" href="<?php echo $OAuth_link ?>" class="stripe-connect <?php echo $button_class ?>"><span><?php echo $text_button; ?></span></a>
<br />

<?php
if ( 0 < $count_commissions ) {
	$args        = array(
	    'current_status'    => $current_status,
		'current_page'      => isset( $_GET['current_page'] ) ? abs( (int) $_GET['current_page'] ) : 1,
		'items_per_page'    => $items_per_page,
		'count_commissions' => $count_commissions,
		'commissions'       => $commissions,
	);
	$commissions = new YITH_Stripe_Connect_Commissions();
	$commissions->enqueue_scripts();
	yith_wcsc_get_template( 'commissions-panel', $args, 'common' );
}
?>


