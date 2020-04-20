<?php get_header() ?>
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
	<div id="main" class="main">
	<?php 
		while (have_posts()) : the_post(); 
		global $post;
		$skill 		= get_post_meta( $post->ID, 'skill', true );	
		$p_url 		= get_post_meta( $post->ID, 'p_url', true );
		$copyright 	= get_post_meta( $post->ID, 'copyright', true );
		$terms 		= get_the_terms( $post->ID, 'portfolio_cat' );
		$term_str 	= '';
		foreach( $terms as $key => $term ){
			$str = ( $key == 0 ) ? '' : ', ';
			$term_str .= $str . '<a href="'. get_term_link( $term->term_id, 'portfolio_cat' ) .'">'. $term->name .'</a>';
		}
	?>
		<div <?php post_class(); ?>>
		<!-- Content Portfolio -->
			<div class="portfolio-top">
				<h1 class="portfolio-title"><?php the_title(); ?></h1>
				<div class="portfolio-content clearfix">
				<?php if( has_post_thumbnail() ){ ?>
					<div class="single-thumbnail pull-left">
						<?php the_post_thumbnail( 'large' ); ?>
					</div>
					<?php } ?>
					<div class="single-portfolio-content">
						<h3><?php esc_html_e( 'Project Description', 'sw_core' ); ?></h2>
						<div class="single-description">
							<?php the_content(); ?>
						</div>
						<h3><?php esc_html_e( 'Project Detail', 'sw_core' ); ?></h2>
						<div class="portfolio-meta">
							<?php if( $skill != '' ){ ?>
								<div class="pmeta-item">
									<?php echo '<span>'.__( 'Skill Needed', 'sw_core' ).':</span> <label>'. esc_html( $skill ).'</label>'; ?>
								</div>
							<?php } ?>
							<?php if( $skill != '' ){ ?>
								<div class="pmeta-item">
									<?php echo '<span>'.__( 'Category', 'sw_core' ).':</span> <label>'. $term_str.'</label>'; ?>
								</div>
							<?php } ?>
							<?php if( $p_url != '' ){ ?>
								<div class="pmeta-item">
									<?php echo '<span>'.__( 'URL', 'sw_core' ).':</span> <label><a class="meta-link" href="'. esc_html( $p_url ) .'">'. esc_html( $p_url ) .'</a></label>' ; ?>
								</div>
							<?php } ?>
							<?php if( $copyright != '' ){ ?>
								<div class="pmeta-item">
									<?php echo '<span>'.__( 'Copyright', 'sw_core' ).':</span> <label>'. esc_html( $copyright ).'</label>'; ?>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
			<!-- End Content Portfolio -->			
		</div>
	<?php endwhile; ?>
	</div>
</div>
<?php get_footer() ?>