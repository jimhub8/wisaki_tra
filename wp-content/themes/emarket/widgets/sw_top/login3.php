<?php 
$emarket_header_style = emarket_options()->getCpanelValue('header_style');
?>
<?php do_action( 'before' ); ?>
<?php if ( class_exists( 'WooCommerce' ) ) { ?>
<?php global $woocommerce; ?>
<div class="top-login3">
	<?php if ( ! is_user_logged_in() ) {  ?>
		<ul>
			<li>
			<span class="wg"><?php esc_html_e( 'Hello Guest!', 'emarket' ) ?></span>
			<?php echo ' <a href="javascript:void(0);" data-toggle="modal" data-target="#login_form"><span>'.esc_html__('Login', 'emarket').'</span></a> '; ?>
			</li>
		</ul>
	<?php } else{?>
		<div class="div-logined">
			<ul>
				<li>
					<span class="wg"><?php esc_html_e( 'Hello!', 'emarket' ) ?></span>
					<?php 
						$user_id = get_current_user_id();
						$user_info = get_userdata( $user_id );	
					?>
					<a href="<?php echo wp_logout_url( home_url('/') ); ?>" title="<?php esc_attr_e( 'Logout', 'emarket' ) ?>"><span><?php esc_html_e('Logout', 'emarket'); ?></span></a>
				</li>
			</ul>
		</div>
	<?php } ?>
</div>
<?php }  ?>