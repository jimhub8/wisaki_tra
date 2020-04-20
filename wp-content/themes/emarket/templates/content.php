<?php get_header(); ?>
<?php 
	$emarket_sidebar_template = emarket_options()->getCpanelValue('sidebar_blog') ;
	$emarket_blog_styles = emarket_options()->getCpanelValue('blog_layout');
?>

<div class="emarket_breadcrumbs">
	<div class="container">
		<?php
			if (!is_front_page() ) {
				if (function_exists('emarket_breadcrumb')){
					emarket_breadcrumb('<div class="breadcrumbs custom-font theme-clearfix">', '</div>');
				} 
			} 
		?>
	</div>
</div>

<div class="container">
	<div class="row sidebar-row">
		<?php if ( is_active_sidebar('left-blog') && $emarket_sidebar_template == 'left' ):
			$emarket_left_span_class = 'col-lg-'.emarket_options()->getCpanelValue('sidebar_left_expand');
			$emarket_left_span_class .= ' col-md-'.emarket_options()->getCpanelValue('sidebar_left_expand_md');
			$emarket_left_span_class .= ' col-sm-'.emarket_options()->getCpanelValue('sidebar_left_expand_sm');
		?>
		<aside id="left" class="sidebar <?php echo esc_attr($emarket_left_span_class); ?>">
			<?php dynamic_sidebar('left-blog'); ?>
		</aside>
		<?php endif; ?>
		
		<div class="category-contents <?php emarket_content_blog(); ?>">
			<div class="listing-title">			
				<h1><span><?php emarket_title(); ?></span></h1>				
			</div>
			<!-- No Result -->
			<?php if (!have_posts()) : ?>
			<?php get_template_part('templates/no-results'); ?>
			<?php endif; ?>			
			
			<?php 
				$emarket_blogclass = 'blog-content blog-content-'. $emarket_blog_styles;
				if( $emarket_blog_styles == 'grid' ){
					$emarket_blogclass .= ' row';
				}
			?>
			<div class="<?php echo esc_attr( $emarket_blogclass ); ?>">
			<?php 			
				while( have_posts() ) : the_post();
					get_template_part( 'templates/content', $emarket_blog_styles );
				endwhile;
			?>
			<?php get_template_part('templates/pagination'); ?>
			</div>
			<div class="clearfix"></div>
		</div>		
		
		<?php if ( is_active_sidebar('right-blog') && $emarket_sidebar_template =='right' ):
			$emarket_right_span_class = 'col-lg-'.emarket_options()->getCpanelValue('sidebar_right_expand');
			$emarket_right_span_class .= ' col-md-'.emarket_options()->getCpanelValue('sidebar_right_expand_md');
			$emarket_right_span_class .= ' col-sm-'.emarket_options()->getCpanelValue('sidebar_right_expand_sm');
		?>
		<aside id="right" class="sidebar <?php echo esc_attr($emarket_right_span_class); ?>">
			<?php dynamic_sidebar('right-blog'); ?>
		</aside>
		<?php endif; ?>
	</div>
</div>
<?php get_footer(); ?>
