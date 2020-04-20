<?php 

/**
	* ayout Tab Category Countdown 1
	* @version     1.0.0
**/

	
	$widget_id = isset( $widget_id ) ? $widget_id : $this->generateID();
	if( $category == '' ){
		return '<div class="alert alert-warning alert-dismissible" role="alert">
			<a class="close" data-dismiss="alert">&times;</a>
			<p>'. esc_html__( 'Please select a category for SW Woocommerce Tab Category Slider. Layout ', 'sw_woocommerce' ) . $layout .'</p>
		</div>';
	}
	if( !is_array( $category ) ){
		$category = explode( ',', $category );
	}
?>
<div class="sw-tab-countdown sw-woo-tab-cat-ajax sw-ajax" id="<?php echo esc_attr( 'category_' . $widget_id ); ?>" >
	<div class="resp-tab" style="position:relative;">
		<div class="top-tab-slider clearfix">
			<div class="box-title pull-left"><?php echo ( $title1 != '' ) ? '<h3>'. esc_html( $title1 ) .'</h3>' : ''; ?></div>
			<ul class="nav nav-tabs pull-right" id="<?php echo 'nav_'.$widget_id; ?>">
			<?php 
				$i = 1;
				foreach($category as $cat){
					$terms = get_term_by('slug', $cat, 'product_cat');
					if( $terms != NULL ){			
			?>
				<li class="<?php if( $i == $tab_active ){echo 'active loaded'; }?>">
					<a href="#<?php echo esc_attr( $cat. '_' .$widget_id ) ?>" data-type="tab_ajax_countdown" data-layout="<?php echo esc_attr( $layout );?>" data-length="<?php echo esc_attr( $title_length ) ?>" data-ajaxurl="<?php echo esc_url( sw_ajax_url() ) ?>" data-category="<?php echo esc_attr( $cat ) ?>" data-toggle="tab" data-catload="ajax" data-number="<?php echo esc_attr( $numberposts ); ?>" data-lg="<?php echo esc_attr( $columns ); ?>" data-md="<?php echo esc_attr( $columns1 ); ?>" data-sm="<?php echo esc_attr( $columns2 ); ?>" data-xs="<?php echo esc_attr( $columns3 ); ?>" data-mobile="<?php echo esc_attr( $columns4 ); ?>" data-speed="<?php echo esc_attr( $speed ); ?>" data-scroll="<?php echo esc_attr( $scroll ); ?>" data-interval="<?php echo esc_attr( $interval ); ?>"  data-autoplay="<?php echo esc_attr( $autoplay ); ?>">
						<?php echo $terms->name; ?>
					</a>
				</li>	
				<?php $i ++; ?>
			<?php } } ?>
			</ul>
			<button class="button-collapse collapsed pull-right" type="button" data-toggle="collapse" data-target="#<?php echo 'nav_'.$widget_id; ?>"  aria-expanded="false"></button>
		</div>
		<div class="tab-content">
		<?php 
			$active = ( $tab_active - 1 >= 0 ) ? $tab_active - 1 : 0;
			$default = array(
				'post_type'	=> 'product',
				'tax_query'	=> array(
				array(
					'taxonomy'	=> 'product_cat',
					'field'		=> 'slug',
					'terms'		=> $category[$active])),
					'orderby' => $orderby,
					'order' => $order,
					'post_status' => 'publish',
					'showposts' => $numberposts,
					'meta_query' => array(		
					array(
						'key' => '_sale_price',
						'value' => 0,
						'compare' => '>',
						'type' => 'DECIMAL(10,5)'
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
				)
			);
			$list = new WP_Query( $default );
		?>
			<div class="tab-pane active" id="<?php echo esc_attr( $category[$active]. '_' .$widget_id ) ?>">
				<?php if( $list->have_posts() ) : ?>
				<div id="<?php echo esc_attr( 'tab_cat_'. $category[$active]. '_' .$widget_id ); ?>" class="woo-tab-container-slider responsive-slider loading clearfix" data-lg="<?php echo esc_attr( $columns ); ?>" data-md="<?php echo esc_attr( $columns1 ); ?>" data-sm="<?php echo esc_attr( $columns2 ); ?>" data-xs="<?php echo esc_attr( $columns3 ); ?>" data-mobile="<?php echo esc_attr( $columns4 ); ?>" data-speed="<?php echo esc_attr( $speed ); ?>" data-scroll="<?php echo esc_attr( $scroll ); ?>" data-interval="<?php echo esc_attr( $interval ); ?>"  data-autoplay="<?php echo esc_attr( $autoplay ); ?>">
					<div class="resp-slider-container">
							<div class="slider responsive">
						<?php 
							$count_items 	= 0;
							$numb 			= ( $list->found_posts > 0 ) ? $list->found_posts : count( $list->posts );
							$count_items 	= ( $numberposts >= $numb ) ? $numb : $numberposts;
							$i 				= 0;
							$j				= 0;
							while($list->have_posts()): $list->the_post();
							global $product, $post;
							$class = ( $product->get_price_html() ) ? '' : 'item-nonprice';
							$start_time = get_post_meta( $post->ID, '_sale_price_dates_from', true );
							$countdown_time = get_post_meta( $post->ID, '_sale_price_dates_to', true );	
							$forginal_price = get_post_meta( $post->ID, '_regular_price', true );	
							$fsale_price = get_post_meta( $post->ID, '_sale_price', true );	
							$symboy = get_woocommerce_currency_symbol( get_woocommerce_currency() );
							$date = sw_timezone_offset( $countdown_time );
							if( $i % $item_row == 0 ){
						?>
							<div class="item <?php echo esc_attr( $class ) ?> product clearfix">
						<?php } ?>
								<div class="item-wrap3">
									<div class="item-detail">
										<div class="item-img products-thumb">
											<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
												<?php 
												$id = get_the_ID();
												if ( has_post_thumbnail() ){
													echo get_the_post_thumbnail( $post->ID, 'shop_catalog' ) ? get_the_post_thumbnail( $post->ID, 'shop_catalog' ): '<img src="'.get_template_directory_uri().'/assets/img/placeholder/'.'large'.'.png" alt="No thumb">';		
												}else{
													echo '<img src="'.get_template_directory_uri().'/assets/img/placeholder/'.'large'.'.png" alt="No thumb">';
												}
												?>
											</a>
											<?php sw_label_sales();?>
											<?php echo emarket_quickview(); ?>
										</div>											
										<div class="item-content">
											<!-- rating  -->
											<?php 
											$rating_count = $product->get_rating_count();
											$review_count = $product->get_review_count();
											$average      = $product->get_average_rating();
											?>
											<div class="reviews-content">
												<div class="star"><?php echo ( $average > 0 ) ?'<span style="width:'. ( $average*13 ).'px"></span>' : ''; ?></div>
											</div>									
											<!-- end rating  -->
											<h4><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute();?>"><?php sw_trim_words( get_the_title(), $title_length ); ?></a></h4>								
											<!-- price -->
											<?php if ( $price_html = $product->get_price_html() ){?>
											<div class="item-price">
												<span>
													<?php echo $price_html; ?>
												</span>
											</div>
											<?php } ?>	
											<!-- add to cart, wishlist, compare -->
											<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
										</div>
										<?php 
											if( $product->get_stock_quantity() != 0 ){ 
											$available 	 = $product->get_stock_quantity();
											$total_sales = get_post_meta( $post->ID, 'total_sales', true );
											$bar_width 	 = intval( $available ) / intval( $available + $total_sales ) * 100;
										?>
										<div class="sales-bar clearfix">
											<div class="sales-bar-total">
												<span style="width: <?php echo esc_attr( $bar_width . '%' ); ?>"></span>
											</div>
										</div>
										<div class="stock-sold"><?php esc_html_e( 'Sold: ', 'sw_woocommerce' ) ?><span><?php echo $total_sales; ?></span></div>
										<?php } ?>
										<div class="product-countdown" data-date="<?php echo esc_attr( $date ); ?>" data-price="<?php echo esc_attr( $symboy.$forginal_price ); ?>" data-starttime="<?php echo esc_attr( $start_time ); ?>" data-cdtime="<?php echo esc_attr( $countdown_time ); ?>" data-id="<?php echo 'product_'.$id.$post->ID; ?>"></div>	
									</div>
								</div>
							<?php if( ( $i+1 ) % $item_row == 0 || ( $i+1 ) == $count_items ){?> </div><?php } ?>
						<?php $i++; $j++; endwhile; wp_reset_postdata();?>
						</div>
					</div>
				</div>			
				<?php 
					else :
						echo '<div class="alert alert-warning alert-dismissible" role="alert">
						<a class="close" data-dismiss="alert">&times;</a>
						<p>'. esc_html__( 'There is not product on this tab', 'sw_woocommerce' ) .'</p>
						</div>';
					endif;
				?>
			</div>
		</div>
	</div>
</div>