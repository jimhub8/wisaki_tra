<?php get_header(); ?>

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
	<div class="listing-title">			
			<h1><span><?php emarket_title(); ?></span></h1>				
		</div>
	<?php
		$emarket_post_type = isset( $_GET['search_posttype'] ) ? $_GET['search_posttype'] : '';
		if( ( $emarket_post_type != '' ) &&  locate_template( 'templates/search-' . $emarket_post_type . '.php' ) ){
			get_template_part( 'templates/search', $emarket_post_type );
		}else{ 
			if( have_posts() ){
		?>
			<div class="blog-content content-search">
		<?php 
			while (have_posts()) : the_post(); 
			global $post;
			$post_format = get_post_format();
		?>
			<div id="post-<?php the_ID();?>" <?php post_class( 'theme-clearfix' ); ?>>
				<div class="entry clearfix">
					<?php if (get_the_post_thumbnail()){?>
					<div class="entry-thumb pull-left">
						<a class="entry-hover" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">			
							<?php the_post_thumbnail("thumbnail")?>
						</a>
					</div>
					<?php }?>
					<div class="entry-content">
						<div class="title-blog">
							<h3>
								<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?> </a>
							</h3>
						</div>
						<div class="entry-meta">
							<span class="entry-author"><i class="fa fa-user"></i><?php the_author_posts_link(); ?></span>
							<span class="entry-date">
								<i class="fa fa-clock-o"></i><?php echo ( get_the_title() ) ? date_i18n( 'F j, Y',strtotime($post->post_date)) : '<a href="'.get_the_permalink().'">'.date_i18n( 'F j, Y',strtotime($post->post_date)).'</a>'; ?>
							</span>
							<?php if( has_category()) :?>
							<span class="category-blog">
								<i class="fa fa fa-folder-open"></i><?php the_category(', '); ?>
							</span>
							<?php endif; ?>
						</div>
						<div class="entry-description">
							<?php the_excerpt(); ?>
						</div>
						<div class="bl_read_more"><a href="<?php the_permalink(); ?>"><?php esc_html_e('Read more','emarket')?><i class="fa fa-angle-double-right"></i></a></div>
						 <?php wp_link_pages( array( 'before' => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'emarket' ).'</span>', 'after' => '</div>' , 'link_before' => '<span>', 'link_after'  => '</span>' ) ); ?>
					</div>
				</div>
			</div>			
		<?php endwhile; ?>
		<?php get_template_part('templates/pagination'); ?>
		</div>
	<?php
		}else{
				get_template_part('templates/no-results');
			}
		}
	?>
</div>
<?php get_footer(); ?>