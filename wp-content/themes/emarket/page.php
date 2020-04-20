<?php get_header(); ?>
<?php 
	$emarket_sidebar_template	= get_post_meta( get_the_ID(), 'page_sidebar_layout', true );
	$emarket_sidebar 			= get_post_meta( get_the_ID(), 'page_sidebar_template', true );
?>
	<?php if (!is_front_page() ) { ?>
	<div class="emarket_breadcrumbs">
		<div class="container">			
			<div class="listing-title">			
				<h1><span><?php emarket_title(); ?></span></h1>				
			</div>
			<?php				
				if (function_exists('emarket_breadcrumb')){
					emarket_breadcrumb('<div class="breadcrumbs custom-font theme-clearfix">', '</div>');
				} 			
			?>
		</div>
	</div>	
	<?php } ?>
	<div class="container">
		<div class="row sidebar-row">
		<?php 
			if ( is_active_sidebar( $emarket_sidebar ) && $emarket_sidebar_template != 'right' && $emarket_sidebar_template !='full' ):
			$emarket_left_span_class = 'col-lg-'.emarket_options()->getCpanelValue('sidebar_left_expand');
			$emarket_left_span_class .= ' col-md-'.emarket_options()->getCpanelValue('sidebar_left_expand_md');
			$emarket_left_span_class .= ' col-sm-'.emarket_options()->getCpanelValue('sidebar_left_expand_sm');
		?>
			<aside id="left" class="sidebar <?php echo esc_attr( $emarket_left_span_class ); ?>">
				<?php dynamic_sidebar( $emarket_sidebar ); ?>
			</aside>
		<?php endif; ?>
		
			<div id="contents" role="main" class="main-page <?php emarket_content_page(); ?>">
				<?php
				get_template_part('templates/content', 'page')
				?>
			</div>
			<?php 
			if ( is_active_sidebar( $emarket_sidebar ) && $emarket_sidebar_template != 'left' && $emarket_sidebar_template !='full' ):
				$emarket_left_span_class = 'col-lg-'.emarket_options()->getCpanelValue('sidebar_left_expand');
				$emarket_left_span_class .= ' col-md-'.emarket_options()->getCpanelValue('sidebar_left_expand_md');
				$emarket_left_span_class .= ' col-sm-'.emarket_options()->getCpanelValue('sidebar_left_expand_sm');
			?>
				<aside id="right" class="sidebar <?php echo esc_attr($emarket_left_span_class); ?>">
					<?php dynamic_sidebar( $emarket_sidebar ); ?>
				</aside>
			<?php endif; ?>
		</div>		
	</div>
<?php get_footer(); ?>

