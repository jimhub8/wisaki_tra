<?php
/* 
** Content Header
*/
$emarket_page_header = get_post_meta( get_the_ID(), 'page_header_style', true );
$emarket_colorset = emarket_options()->getCpanelValue('scheme');
$emarket_logo = emarket_options()->getCpanelValue('sitelogo');
$sticky_menu 		= emarket_options()->getCpanelValue( 'sticky_menu' );
$emarket_page_header  = ( get_post_meta( get_the_ID(), 'page_header_style', true ) != '' ) ? get_post_meta( get_the_ID(), 'page_header_style', true ) : emarket_options()->getCpanelValue('header_style');
$emarket_menu_item 	= ( emarket_options()->getCpanelValue( 'menu_number_item' ) ) ? emarket_options()->getCpanelValue( 'menu_number_item' ) : 9;
$emarket_title_text = emarket_options()->getCpanelValue('menu_title_text');
$emarket_more_text 	= ( emarket_options()->getCpanelValue( 'menu_more_text' ) ) ? emarket_options()->getCpanelValue( 'menu_more_text' ) : esc_html__( 'See More', 'emarket' );
$emarket_less_text 	= emarket_options()->getCpanelValue( 'menu_less_text' )	 ? emarket_options()->getCpanelValue( 'menu_less_text' ) : esc_html__( 'See Less', 'emarket' );
?>
<header id="header" class="header header-<?php echo esc_attr( $emarket_page_header ); ?>">
	<div class="header-top">
		<div class="container rows">
			<!-- Sidebar Top Menu -->
			<?php if (is_active_sidebar('top5')) {?>
			<div class="top-header">
				<?php dynamic_sidebar('top5'); ?>
			</div>
			<?php }?>
		</div>
	</div>
	
	<div class="header-mid">
		<div class="container rows">
			<div class="row">
				<!-- Logo -->
				<div class="top-header col-lg-2 col-md-2 col-xs-12 pull-left">
					<div class="emarket-logo">
						<?php emarket_logo(); ?>
					</div>
				</div>
				
				<?php if( !emarket_options	()->getCpanelValue( 'disable_search' ) ) : ?>
					<div class="search-cate col-lg-7 col-md-7 col-xs-6">
						<?php if( is_active_sidebar( 'search' ) && class_exists( 'sw_woo_search_widget' ) ): ?>
							<?php dynamic_sidebar( 'search' ); ?>
						<?php else : ?>
							<div class="widget emarket_top-3 emarket_top non-margin pull-left">
								<div class="widget-inner">
									<?php get_template_part( 'widgets/sw_top/searchcate' ); ?>
								</div>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<!-- Sidebar Top Menu -->
				<?php if (is_active_sidebar('bottom-right-home5')) {?>
				<div class="header-cart pull-right">
					<?php dynamic_sidebar('bottom-right-home5'); ?>
				</div>
				<?php }?>
			</div>
		</div>
	</div>
	
	<div class="header-bottom">
		<div class="container rows">
			
				<div class="top-header col-lg-2 col-md-2 col-xs-12 pull-left">
					<div class="emarket-logo">
						<?php emarket_logo(); ?>
					</div>
				</div>
				
				<?php if ( has_nav_menu('vertical_menu') ) {?>
				<div class="col-lg-2 col-md-3 col-sm-2 col-xs-2 vertical_megamenu vertical_megamenu-header pull-left">
					<div class="mega-left-title">
						<?php if ($emarket_title_text == "") { ?>
							<span><?php esc_html_e('All Categories','emarket')?></span>
						<?php } else { ?>
							<span><?php echo $emarket_title_text; ?></span>
						<?php } ?>
					</div>
					<div class="vc_wp_custommenu wpb_content_element">
						<div class="wrapper_vertical_menu vertical_megamenu" data-number="<?php echo esc_attr( $emarket_menu_item ); ?>" data-moretext="<?php echo esc_attr( $emarket_more_text ); ?>" data-lesstext="<?php echo esc_attr( $emarket_less_text ); ?>">
							<?php wp_nav_menu(array('theme_location' => 'vertical_menu', 'menu_class' => 'nav vertical-megamenu')); ?>
						</div>
					</div>
				</div>
				<?php } ?>
				
				<!-- Primary navbar -->
				<?php if ( has_nav_menu('primary_menu') ) { ?>
				<div id="main-menu" class="main-menu clearfix col-lg-7 col-md-9 pull-left">
					<nav id="primary-menu" class="primary-menu">
						<div class="mid-header clearfix">
							<div class="navbar-inner navbar-inverse">
								<?php
								$emarket_menu_class = 'nav nav-pills';
								if ( 'mega' == emarket_options()->getCpanelValue('menu_type') ){
									$emarket_menu_class .= ' nav-mega';
								} else $emarket_menu_class .= ' nav-css';
								?>
								<?php wp_nav_menu(array('theme_location' => 'primary_menu', 'menu_class' => $emarket_menu_class)); ?>
							</div>
						</div>
					</nav>
				</div>			
				<?php } ?>
				<!-- /Primary navbar -->
				
				<?php if (is_active_sidebar('mid-header2')) {?>
				<div class="header-right pull-right">
					<?php dynamic_sidebar('mid-header2'); ?>
				</div>
				<?php }?>
				
				<?php if( !emarket_options()->getCpanelValue( 'disable_cart' ) ) : ?>
					<div class="sw-emarket-cart pull-right">
						<?php get_template_part( 'woocommerce/minicart-ajax-style3' ); ?>
					</div>
				<?php endif; ?>
				
				<?php if( !emarket_options	()->getCpanelValue( 'disable_search' ) ) : ?>
					<div class="search-cate">
						<span class="search-tog pull-right"><i class="fa fa-search" aria-hidden="true"></i></span>
						<?php if( is_active_sidebar( 'search' ) && class_exists( 'sw_woo_search_widget' ) ): ?>
							<?php dynamic_sidebar( 'search' ); ?>
						<?php else : ?>
							<div class="widget emarket_top-3 emarket_top non-margin pull-left">
								<div class="widget-inner">
									<?php get_template_part( 'widgets/sw_top/searchcate' ); ?>
								</div>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				
		</div>
	</div>

</header>