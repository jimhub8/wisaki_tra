<?php 
	/* 
	** Content Header
	*/
	$sticky_mobile	= emarket_options()->getCpanelValue( 'sticky_menu' );
?>
<?php if( is_front_page() || get_post_meta( get_the_ID(), 'page_mobile_enable', true ) || is_search() || emarket_options()->getCpanelValue( 'mobile_header_inside' ) ): ?>
<header id="header" class="header header-mobile-style1">
	<div class="header-wrrapper clearfix">
		<div class="header-top-mobile clearfix">
			<?php if ( has_nav_menu('vertical_menu') && !has_nav_menu('mobile_header_menu') ) {?>
			<div class="header-menu-categories pull-left">				
				<div class="vertical_megamenu">
					<?php wp_nav_menu(array('theme_location' => 'vertical_menu', 'menu_class' => 'nav vertical-megamenu')); ?>
				</div>			
			</div>
			<?php }else{ ?>
			<div class="header-menu-categories pull-left">				
				<div class="vertical_megamenu">
					<?php wp_nav_menu(array('theme_location' => 'mobile_header_menu', 'menu_class' => 'nav vertical-megamenu')); ?>
				</div>			
			</div>
			<?php } ?>
			<div class="emarket-logo pull-left">
				<?php emarket_logo(); ?>
			</div>
			<div class="header-cart pull-right">
				<a href="<?php echo get_permalink(get_option('woocommerce_cart_page_id') ); ?>">
					<?php get_template_part( 'woocommerce/minicart-ajax-mobile' ); ?>
				</a>
			</div>
			<div class="header-wishlist pull-right">
				<a href="<?php echo get_permalink( get_option('yith_wcwl_wishlist_page_id') ); ?>" title="<?php esc_attr_e('Wishlist','emarket'); ?>"></a>
			</div>
		</div>
	</div>
</header>
<?php else : ?>
<!--  header page -->
<header id="header" class="header-page">
	<div class="header-shop clearfix">
		<div class="container">
			<div class="back-history"></div>
			<h1 class="page-title"><?php emarket_title(); ?></h1>
			<?php if ( has_nav_menu('vertical_menu') && !has_nav_menu('mobile_header_menu') ) {?>
				<div class="vertical_megamenu vertical_megamenu_shop pull-right">
					<?php wp_nav_menu(array('theme_location' => 'vertical_menu', 'menu_class' => 'nav vertical-megamenu')); ?>
				</div>
			<?php }else{ ?>
				<div class="vertical_megamenu vertical_megamenu_shop pull-right">
					<?php wp_nav_menu(array('theme_location' => 'mobile_header_menu', 'menu_class' => 'nav vertical-megamenu')); ?>
				</div>
			<?php } ?>
		</div>
	</div>
</header>
	<!-- End header -->
<?php endif; ?>