<?php 

/**
	* Layout Child Category 3
	* @version     1.0.0
	**/
	if( $category == '' ){
		return '<div class="alert alert-warning alert-dismissible" role="alert">
		<a class="close" data-dismiss="alert">&times;</a>
		<p>'. esc_html__( 'Please select a category for SW Woo Slider. Layout ', 'sw_woocommerce' ) . $layout .'</p>
	</div>';
}
$widget_id = isset( $widget_id ) ? $widget_id : 'sw_woo_slider_'.rand().time();
$viewall = get_permalink( wc_get_page_id( 'shop' ) );
$default = array(
	'post_type' => 'product',		
	'orderby' => $orderby,
	'order' => $order,
	'post_status' => 'publish',
	'showposts' => $numberposts
	);
$term_name = '';
$category_color = '';
if( $category != '' ){
	$term = get_term_by( 'slug', $category, 'product_cat' );	
	if( $term ) :
		$term_name = $term->name;
		$category_color =  get_term_meta( $term->term_id, 'ets_cat_color', true );
		$viewall = get_term_link( $term->term_id, 'product_cat' );
	endif;
	
	$default['tax_query'] = array(
		array(
			'taxonomy'  => 'product_cat',
			'field'     => 'slug',
			'terms'     => $category )
		);	
}
$list = new WP_Query( $default );
if ( $list -> have_posts() ){
	?>
	<div id="<?php echo 'slider_' . $widget_id; ?>" class="responsive-slider woo-slider-default sw-child-cat4 clearfix <?php echo esc_attr( $style );?> loading" data-lg="<?php echo esc_attr( $columns ); ?>" data-md="<?php echo esc_attr( $columns1 ); ?>" data-sm="<?php echo esc_attr( $columns2 ); ?>" data-xs="<?php echo esc_attr( $columns3 ); ?>" data-mobile="<?php echo esc_attr( $columns4 ); ?>" data-speed="<?php echo esc_attr( $speed ); ?>" data-scroll="<?php echo esc_attr( $scroll ); ?>" data-interval="<?php echo esc_attr( $interval ); ?>"  data-autoplay="<?php echo esc_attr( $autoplay ); ?>">
		<div class="child-top clearfix" data-color="<?php echo esc_attr( $category_color );?>">
			<div class="box-title pull-left">
				<h3><?php echo ( $title1 != '' ) ? $title1 : $term_name; ?></h3>
				<?php echo ( $description != '' ) ? '<div class="slider-description">'. $description .'</div>' : ''; ?>
				<button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#<?php echo 'child_' . $widget_id; ?>"  aria-expanded="false">				
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>	
				</button>
			</div>
			<div class="box-title-right clearfix">
				<?php 
				if( $term ) :
					$termchild 		= get_terms( 'product_cat', array( 'parent' => $term->term_id, 'hide_empty' => 0, 'number' => $number_child ) );
				if( count( $termchild ) > 0 ){
					?>	
					<?php echo '<div class="view-all pull-left"><a href="' . esc_url( $viewall ) . '">' .esc_html__( 'See All', 'sw_woocommerce' ) . '<i class="fa  fa-caret-right"></i></a></div>'; ?>		
					<div class="childcat-content pull-right"  id="<?php echo 'child_' . $widget_id; ?>">
						<?php 					
							echo '<ul>';
							foreach ( $termchild as $key => $child ) {
								echo '<li><a href="' . get_term_link( $child->term_id, 'product_cat' ) . '">' . $child->name . '</a></li>';
							}
							echo '</ul>';
						?>
					</div>
					<?php } ?>
				<?php endif; ?>
			</div>
		</div>
		<div class="tab-content">
			<!-- Product tab listing -->
			<?php 
			$banner_links = explode( ',', $banner_links );
			if( $image != '' ) :
				$image = explode( ',', $image );	
			endif;
			?>
			<div class="banner-category">
				<div id="<?php echo esc_attr( 'banner_' . $widget_id ); ?>" class="banner-slider" data-lg="1" data-md="1" data-sm="1" data-xs="1" data-mobile="1" data-dots="true" data-arrow="false" data-fade="false">
					<div class="banner-responsive">
						<?php foreach( $image as $key => $img ) : ?>
							<div class="item pull-left">
								<a href="<?php echo esc_url( $banner_links[$key] ); ?>"><?php echo wp_get_attachment_image( $img, 'full' ); ?></a>
							</div>
						<?php endforeach;?>
					</div>
				</div>									
			</div>
			<!-- slider content -->
			<div class="resp-slider-container resp-child-container">
				<div class="slider responsive responsive-child">
					<?php 
					$count_items 	= 0;
					$numb 			= ( $list->found_posts > 0 ) ? $list->found_posts : count( $list->posts );
					$count_items 	= ( $numberposts >= $numb ) ? $numb : $numberposts;
					$i 				= 0;
					while($list->have_posts()): $list->the_post();global $product, $post;
					$class = ( $product->get_price_html() ) ? '' : 'item-nonprice';
					if( $i % $item_row == 0 ){
						?>
						<div class="item <?php echo esc_attr( $class )?> product pull-left">
							<?php } ?>
							<div class="item-wrap5">
								<div class="item-detail">
									<div class="item-content">
										<!-- rating  -->
										<?php 
											$rating_count = $product->get_rating_count();
											$review_count = $product->get_review_count();
											$average      = $product->get_average_rating();
										?>
										<div class="reviews-content">
											<div class="star"><?php echo ( $average > 0 ) ?'<span style="width:'. ( $average*13 ).'px"></span>' : ''; ?></div>
											<div class="item-number-rating">
												<?php echo $review_count; ?> <?php echo ( ( $review_count == 1) ? _e(' Review', 'sw_woocommerce') : _e(' Review(s)', 'sw_woocommerce') );?>
											</div>
										</div>									
										<!-- end rating  -->
										<h4><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute();?>"><?php the_title(); ?></a></h4>
										<!-- Price -->
										<?php if ( $price_html = $product->get_price_html() ){?>
										<div class="item-price">
											<span>
												<?php echo $price_html; ?>
											</span>
										</div>
										<?php } ?>	
										<?php sw_label_sales(); ?>				
									</div>
									<div class="item-img products-thumb">		
										<?php 
											do_action( 'woocommerce_before_shop_loop_item_title' );
											do_action( 'woocommerce_after_shop_loop_item' );  
										?>
									</div>																		
								</div>
							</div>
							<?php if( ( $i+1 ) % $item_row == 0 || ( $i+1 ) == $count_items ){?> </div><?php } ?>
							<?php $i++; endwhile; wp_reset_postdata();?>
						</div>
					</div> 
				</div>
			</div>
			<?php
		}else{
			echo '<div class="alert alert-warning alert-dismissible" role="alert">
			<a class="close" data-dismiss="alert">&times;</a>
			<p>'. esc_html__( 'Has no product in this category', 'sw_woocommerce' ) .'</p>
		</div>';
	}
	?>
	<script type="text/javascript">
		(function($){
			$(window).on( 'load',function(){
				$( '.child-top' ).each(function(){
					var data = $(this).data( 'color' );
					if( data != '' ){
						$(this).css( 'background', data );
					}
				});
			});
		})(jQuery);
	</script>