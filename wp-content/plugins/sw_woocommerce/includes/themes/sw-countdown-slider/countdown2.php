<?php 
/**
* Layout Countdown 2
* @version     1.0.0
**/


$term_name = esc_html__( 'All Categories', 'sw_woocommerce' );
$default = array(
	'post_type' => 'product',	
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
			'value' => time(),
			'compare' => '>',
			'type' => 'NUMERIC'
			)
		),
	'orderby' => $orderby,
	'order' => $order,
	'post_status' => 'publish',
	'showposts' => $numberposts	
	);
if( $category != '' ){
	$term = get_term_by( 'slug', $category, 'product_cat' );
	if( $term ) :
		$term_name = $term->name;
	endif; 

	$default['tax_query'] = array(
		array(
			'taxonomy'  => 'product_cat',
			'field'     => 'slug',
			'terms'     => $category ));
}
$default = sw_check_product_visiblity( $default );

$id = 'sw_countdown_'.$this->generateID();
$list = new WP_Query( $default );
if ( $list -> have_posts() ){ ?>
<div id="<?php echo $category.'_'.$id; ?>" class="sw-woo-container-slider responsive-slider countdown-slider style3 loading" data-lg="<?php echo esc_attr( $columns ); ?>" data-md="<?php echo esc_attr( $columns1 ); ?>" data-sm="<?php echo esc_attr( $columns2 ); ?>" data-xs="<?php echo esc_attr( $columns3 ); ?>" data-mobile="<?php echo esc_attr( $columns4 ); ?>" data-speed="<?php echo esc_attr( $speed ); ?>" data-scroll="<?php echo esc_attr( $scroll ); ?>" data-interval="<?php echo esc_attr( $interval ); ?>"  data-autoplay="<?php echo esc_attr( $autoplay ); ?>" data-circle="false">       
	<div class="resp-slider-container">
		<?php if( $title1 != '' ){?>
		<div class="box-title">
			<h3><span><?php echo ( $title1 != '' ) ? $title1 : $term_name; ?></span></h3>
		</div>
		<?php } ?>
		<div class="slider responsive">	
			<?php 
			$count_items = 0;
			$count_items = ( $numberposts >= $list->found_posts ) ? $list->found_posts : $numberposts;
			$i = 0;
			while($list->have_posts()): $list->the_post();					
			global $product, $post;
			$class = ( $product->get_price_html() ) ? '' : 'item-nonprice';
			$start_time 	= get_post_meta( $post->ID, '_sale_price_dates_from', true );
			$countdown_date = get_post_meta( $post->ID, '_sale_price_dates_to', true );
			
			if( $i % $item_row2 == 0 ){
				?>
				<div class="item-countdown product <?php echo 'item-'.esc_attr( $item_row2 ).'columns' ?> <?php echo esc_attr( $class )?>" id="<?php echo 'product_'.$id.$post->ID; ?>">
					<?php } ?>
					<div class="item-wrap item-wrap4 <?php echo ( $i % $item_row2 == 0 ) ? 'first-item' : ''; ?>">
						<div class="item-detail clearfix">
							<div class="item-image-countdown">
								<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail( 'shop_single' ); ?></a>
								<?php sw_label_sales() ?>
								<?php echo emarket_quickview() ;?>
							</div>
							<div class="item-content">
								<h4><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute();?>"><?php sw_trim_words( get_the_title(), $title_length ); ?></a></h4>
								<!-- rating  -->
								<?php 
								$rating_count = $product->get_rating_count();
								$review_count = $product->get_review_count();
								$average      = $product->get_average_rating();
								?>
								<div class="reviews-content">
									<div class="star"><?php echo ( $average > 0 ) ?'<span style="width:'. ( $average*12 ).'px"></span>' : ''; ?></div>
								</div>									
								<!-- end rating  -->
								<!-- Price -->
								<?php if ( $price_html = $product->get_price_html() ){?>
								<div class="item-price">
									<span>
										<?php echo $price_html; ?>
									</span>
								</div>
								<?php } ?>
								<?php 
									if( $product->get_stock_quantity() != 0 ){ 
									$available 	 = $product->get_stock_quantity();
									$total_sales = get_post_meta( $post->ID, 'total_sales', true );
									$bar_width 	 = intval( $available ) / intval( $available + $total_sales ) * 100;
								?>
								<div class="emarket-stock clearfix">
									<div class="stock-avail pull-left"><?php esc_html_e( 'Available: ', 'sw_woocommerce' ) ?><span><?php echo $available; ?></span></div>
									<div class="stock-sold pull-right"><?php esc_html_e( 'Sold: ', 'sw_woocommerce' ) ?><span><?php echo $total_sales; ?></span></div>
								</div>
								<div class="sales-bar clearfix">
									<div class="sales-bar-total">
										<span style="width: <?php echo esc_attr( $bar_width . '%' ); ?>"></span>
									</div>
								</div>
								<?php } ?>
								<div class="countdown-info clearfix">
									<div class="count-left pull-left">
										<h2><?php esc_html_e( 'Hurry up!', 'sw_woocommerce' ) ?></h2>
										<p><?php esc_html_e( 'Offers end in:', 'sw_woocommerce' ) ?></p>
									</div>
									<div class="product-countdown pull-right" data-date="<?php echo esc_attr( $countdown_date ); ?>"  data-starttime="<?php echo esc_attr( $start_time ); ?>"></div>
								</div>
								<div class="item-button clearfix">
									<?php woocommerce_template_loop_add_to_cart(); ?>
									<?php
									if ( class_exists( 'YITH_WCWL' ) ){
										echo do_shortcode( "[yith_wcwl_add_to_wishlist]" );
									} ?>
									<?php if ( class_exists( 'YITH_WOOCOMPARE' ) ){ 
										?>
										<a href="javascript:void(0)" class="compare button"  title="<?php esc_html_e( 'Add to Compare', 'sw_woocommerce' ) ?>" data-product_id="<?php echo esc_attr($post->ID); ?>" rel="nofollow"> <?php esc_html('compare','sw-woocomerce'); ?></a>
										<?php } ?>
									</div>
								</div>															
							</div>
						</div>
						<?php if( ( $i+1 ) % $item_row2 == 0 || ( $i+1 ) == $count_items ){?> </div><?php } ?>
						<?php $i ++; endwhile; wp_reset_postdata();?>
					</div>
				</div>            
			</div>
			<?php
		} 
		?>