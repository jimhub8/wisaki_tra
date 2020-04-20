<?php 
/*
Template Name: Page Home
*/
?>
<?php get_header(); ?>
<?php 
	$emarket_sidebar_template	= get_post_meta( get_the_ID(), 'page_sidebar_layout', true );
	$emarket_sidebar 			= get_post_meta( get_the_ID(), 'page_sidebar_template', true );
?>	
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
				if(have_posts()):
					while (have_posts()) : the_post(); ?>
						<div <?php post_class(); ?>>
							<div class="entry-content">
								<div class="entry-summary">
									<?php emarket_pagecontent_check(); ?>
								</div>
							</div>
							<div class="clearfix"></div>
						</div>
						<?php
					endwhile;
				else:
					get_template_part('templates/no-results');
				endif;
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

