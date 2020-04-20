<?php 
/**
	* Layout Countdown Muti Category
	* @version     1.0.0
**/

	if( $category == '' ){
		return '<div class="alert alert-warning alert-dismissible" role="alert">
			<a class="close" data-dismiss="alert">&times;</a>
			<p>'. esc_html__( 'Please select a category for SW Woocommerce Category Slider. Layout ', 'sw_woocommerce' ) . $layout .'</p>
		</div>';
	}
	if( !is_array( $category ) ){
		$category = explode( ',', $category );
	}
	$widget_id = isset( $widget_id ) ? $widget_id : 'muti_countdown_'. $this->generateID();
?>
<div id="<?php echo $widget_id; ?>" class="multi-category-countdown">
	<!-- Title -->
	<?php if (  $title1 != '' ) : ?>
	<div class="box-slider-title">
		<h2><?php echo esc_html( $title1 ); ?></h2>
	</div>
	<?php endif; ?>
	<!-- content listing -->
	<div class="multi-countdow-wrapper row">
	<?php foreach( $category as $cat ) { ?>
		<div class="multi-category-wrapper col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<div class="multi-category-item">
			<!-- Banner Thumb -->
			<?php 
				$term = get_term_by('slug', $cat, 'product_cat');
				if( $term ) :
					$thumbnail_id 	= absint( get_term_meta( $term->term_id, 'thumbnail_id', true ) );
					$thumb = wp_get_attachment_image( $thumbnail_id, array(570, 150) );
					if( $thumb ) :
			?>	
				<div class="banner-product">
					<a href="<?php echo get_term_link( $term->term_id, 'product_cat' ); ?>"><?php echo $thumb; ?></a>
				</div>
				<?php endif; ?>
			<?php endif; ?>
			<!-- End Banner Thumb -->
			<div id="<?php echo $cat.'_'.$widget_id; ?>" class="sw-woo-container-slider responsive-slider countdown-muti-slider loading" data-lg="<?php echo esc_attr( $columns ); ?>" data-md="<?php echo esc_attr( $columns1 ); ?>" data-sm="<?php echo esc_attr( $columns2 ); ?>" data-xs="<?php echo esc_attr( $columns3 ); ?>" data-mobile="<?php echo esc_attr( $columns4 ); ?>" data-speed="<?php echo esc_attr( $speed ); ?>" data-scroll="<?php echo esc_attr( $scroll ); ?>" data-interval="<?php echo esc_attr( $interval ); ?>"  data-autoplay="<?php echo esc_attr( $autoplay ); ?>" data-circle="false">
			<?php 
				$default = array(
					'post_type' => 'product',	
					'meta_query' => array(						
						array(
							'key' => '_sale_price',
							'value' => 0,
							'compare' => '>',
							'type' => 'NUMERIC'
						),
						array(
							'key' => '_sale_price_dates_from',
							'value' => time(),
							'compare' => '<',
							'type' => 'NUMERIC'
						),
						array(
							'key' => '_sale_price_dates_to',
							'value' => 0,
							'compare' => '>',
							'type' => 'NUMERIC'
						)
					),
					'orderby' => $orderby,
					'order' => 'desc',
					'post_status' => 'publish',
					'showposts' => $numberposts	
				);
				if( $cat != '' ){			
					$default['tax_query'] = array(
						array(
							'taxonomy'  => 'product_cat',
							'field'     => 'slug',
							'terms'     => $cat					
						)
					);
				}
				$list = new WP_Query( $default );
				if ( $list -> have_posts() ){ 
			?>
				<div class="resp-slider-container">			
					<div class="slider responsive">	
						<?php 
							$count_items = 0;
							$count_items = ( $numberposts >= $list->found_posts ) ? $list->found_posts : $numberposts;
							while($list->have_posts()): $list->the_post();					
							global $product, $post;
							$class = ( $product->get_price_html() ) ? '' : 'item-nonprice';
							$start_time = get_post_meta( $post->ID, '_sale_price_dates_from', true );
							$countdown_time = get_post_meta( $post->ID, '_sale_price_dates_to', true );	
							$forginal_price = get_post_meta( $post->ID, '_regular_price', true );	
							$fsale_price = get_post_meta( $post->ID, '_sale_price', true );	
							$symboy = get_woocommerce_currency_symbol( get_woocommerce_currency() );
							$date = sw_timezone_offset( $countdown_time );
						?>
							<div class="item item-countdown product <?php echo esc_attr( $class )?>">
								<div class="item-wrap5">
									<div class="item-detail clearfix">
										<div class="item-img products-thumb">			
											<?php do_action( 'woocommerce_before_shop_loop_item_title' ); ?>
											<!-- add to cart, wishlist, compare -->
											<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
										</div>	
										<div class="item-content">
											<?php if( $term ) : ?>
											<div class="item-category">
												<h3><a href="<?php echo get_term_link( $term->term_id, 'product_cat' ); ?>"><?php echo $term->name; ?></a></h3>
											</div>
											<?php endif; ?>
											<!-- rating  -->
											<?php 
												$rating_count = $product->get_rating_count();
												$review_count = $product->get_review_count();
												$average      = $product->get_average_rating();
											?>
											<div class="reviews-content">
												<div class="star"><?php echo ( $average > 0 ) ?'<span style="width:'. ( $average*13 ).'px"></span>' : ''; ?></div>
												<div class="item-number-rating">
													<?php echo $review_count; ?><?php echo ( ( $review_count == 1) ? _e(' Review', 'sw_woocommerce') : _e(' Review(s)', 'sw_woocommerce') );?>
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
											<div class="product-countdown" data-date="<?php echo esc_attr( $date ); ?>"  data-price="<?php echo esc_attr( $symboy.$forginal_price ); ?>" data-starttime="<?php echo esc_attr( $start_time ); ?>" data-cdtime="<?php echo esc_attr( $countdown_time ); ?>"></div>
										</div>																						
									</div>
								</div>
							</div>
						<?php endwhile; wp_reset_postdata();?>
					</div>
				</div>
				<?php }else {
					echo '<div class="alert alert-warning alert-dismissible" role="alert">
						<a class="close" data-dismiss="alert">&times;</a>
						<p>'. esc_html__( 'There is not sale product on this category', 'sw_woocommerce' ) .'</p>
					</div>';
				} ?>
			</div>
		</div>
		</div>
	<?php } ?>
	</div>
</div>