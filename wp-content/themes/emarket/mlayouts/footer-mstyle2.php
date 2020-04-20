<?php 
	/* 
	** Content Footer Mobile
	*/	
?>
<footer id="footer" class="footer-mstyle2 theme-clearfix">
	<div class="footer-container">
		<div class="footer-menu clearfix">
		<?php if ( has_nav_menu('mobile_menu') ) {?>
				<div class="wrapper_menu_footer">
					<?php wp_nav_menu(array('theme_location' => 'mobile_menu', 'menu_class' => 'menu-footer')); ?>
				</div>
		<?php } ?>
		</div>
	</div>
</footer>