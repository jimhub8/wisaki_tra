<?php 	
$emarket_page_header  = ( get_post_meta( get_the_ID(), 'page_header_style', true ) != '' ) ? get_post_meta( get_the_ID(), 'page_header_style', true ) : emarket_options()->getCpanelValue('header_style');
$emarket_page_footer   	 = ( get_post_meta( get_the_ID(), 'page_footer_style', true ) != '' ) ? get_post_meta( get_the_ID(), 'page_footer_style', true ) : emarket_options()->getCpanelValue( 'footer_style' );
$emarket_copyright_text  = emarket_options()->getCpanelValue( 'footer_copyright' );
$emarket_copyright_footer = get_post_meta( get_the_ID(), 'copyright_footer_style', true );
$emarket_copyright_footer  = ( get_post_meta( get_the_ID(), 'copyright_footer_style', true ) != '' ) ? get_post_meta( get_the_ID(), 'copyright_footer_style', true ) : emarket_options()->getCpanelValue('copyright_style');
?>

<footer id="footer" class="footer default <?php echo ( ( $emarket_page_header == 'style8' ) ? 'style5' : ''); ?> theme-clearfix">
	<!-- Content footer -->
	<div class="container">
		<?php 
		if( $emarket_page_footer != '' ) :
			echo sw_get_the_content_by_id( $emarket_page_footer ); 
		endif;
		?>
	</div>
	<div class="footer-copyright <?php echo esc_attr( $emarket_copyright_footer ); ?>">
		<div class="container rows">
			<!-- Copyright text -->
			<div class="copyright-text">
				<?php if( $emarket_copyright_text == '' ) : ?>
					<p>&copy;<?php echo date_i18n('Y') .' '. esc_html__('WordPress Theme SW Emarket. All Rights Reserved. Designed by ','emarket'); ?><a class="mysite" href="<?php echo esc_url( 'http://wpthemego.com/' ); ?>"><?php esc_html_e('WPThemeGo.Com','emarket');?></a>.</p>
				<?php else : ?>
					<?php echo wp_kses( $emarket_copyright_text, array( 'a' => array( 'href' => array(), 'title' => array(), 'class' => array() ), 'p' => array()  ) ) ; ?>
				<?php endif; ?>
			</div>
			<?php if (is_active_sidebar('footer-copyright')){ ?>
			<div class="sidebar-copyright">
				<?php dynamic_sidebar('footer-copyright'); ?>
			</div>
			<?php } ?>
		</div>
	</div>
</footer>