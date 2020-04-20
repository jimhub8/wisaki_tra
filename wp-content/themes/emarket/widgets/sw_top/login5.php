<?php if ( class_exists( 'WooCommerce' ) ) { ?>
<?php global $woocommerce; ?>
<div class="top-login5">
	<?php if ( ! is_user_logged_in() ) {  ?>
		<ul>
			<li>
			<?php echo ' <a href="javascript:void(0);" data-toggle="modal" data-target="#login_form"><span>'.esc_html__('Login', 'emarket').'</span></a> '; ?>
			<span class="wg"><?php esc_html_e('Welcome Guest', 'emarket'); ?></span>
			</li>
		</ul>
	<?php } else{?>
		<div class="div-logined">
			<ul>
				<li>
					<?php 
						$user_id = get_current_user_id();
						$user_info = get_userdata( $user_id );	
					?>
					<a href="<?php echo wp_logout_url( home_url('/') ); ?>" title="<?php esc_attr_e( 'Logout', 'emarket' ) ?>"><span><?php esc_html_e('Logout', 'emarket'); ?></span></a>
					<span class="wg"><?php echo sprintf( esc_html__('Welcome %s', 'emarket'), $user_info->data->display_name ); ?></span>
				</li>
			</ul>
		</div>
	<?php } ?>
</div>
<?php } ?>
