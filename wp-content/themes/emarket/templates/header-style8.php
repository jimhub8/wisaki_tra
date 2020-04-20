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
			<?php if (is_active_sidebar('top')) {?>
			<div class="top-header">
				<?php dynamic_sidebar('top'); ?>
			</div>
			<?php }?>
		</div>
	</div>
	<div class="header-bottom">
		<div class="container rows">
			<div class="row">
			
				<!-- Logo -->
				<div class="top-header col-lg-2 col-md-3 pull-left">
					<div class="emarket-logo">
						<?php emarket_logo(); ?>
					</div>
				</div>
				
					<!-- Primary navbar -->
				<?php if ( has_nav_menu('primary_menu') ) { ?>
				<div id="main-menu" class="main-menu clearfix col-lg-6 col-md-7 pull-left">
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
				<!-- Sidebar Top Menu -->
				<?php if (is_active_sidebar('bottom-right-home5')) {?>
				<div class="header-cart pull-right">
					<?php dynamic_sidebar('bottom-right-home5'); ?>
				</div>
				<?php }?>
				
				<?php if( !emarket_options()->getCpanelValue( 'disable_search' ) ) : ?>
					<div class="search-cate pull-right">
						<span class="search-tog"><i class="fa fa-search" aria-hidden="true"></i></span>
						<?php if( is_active_sidebar( 'search' ) && class_exists( 'sw_woo_search_widget' ) ): ?>
							<?php dynamic_sidebar( 'search' ); ?>
						<?php else : ?>
							<div class="widget emarket_top clearfix">
								<div class="widget-inner">
									<?php get_template_part( 'widgets/sw_top/searchcate' ); ?>
								</div>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				
			</div>
		</div>
	</div>

</header>