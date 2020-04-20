<?php 
/**
* Layout Countdown Default
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
			'value' => 0,
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
	$term_name = $term->name;
	$default['tax_query'] = array(
		array(
			'taxonomy'  => 'product_cat',
			'field'     => 'slug',
			'terms'     => $category ));
}
$countdown_id = 'sw_tab_countdown_'.rand().time();
$countdown_id2 = 'sw_tab_countdown2_'.rand().time();
$list = new WP_Query( $default );
if ( $list -> have_posts() ){ ?>
<div id="<?php echo $countdown_id; ?>" class="sw_tab_countdown2">
	<?php if( $title1 != '' ){?>
	<div class="block-title">
		<h3><span><?php echo ( $title1 != '' ) ? $title1 : ''; ?></span></h3>
	</div>
	<?php } ?>
	<div  class="tab-countdown-slide clearfix">
		<div class="tab-content clearfix">
			<?php
			$count_items 	= 0;
			$numb 			= ( $list->found_posts > 0 ) ? $list->found_posts : count( $list->posts );
			$count_items 	= ( $numberposts >= $numb ) ? $numb : $numberposts;
			$i 				= 0;
			while($list->have_posts()): $list->the_post();
				global $product, $post;
				$start_time = get_post_meta( $post->ID, '_sale_price_dates_from', true );
				$countdown_time = get_post_meta( $post->ID, '_sale_price_dates_to', true );	
				$orginal_price = get_post_meta( $post->ID, '_regular_price', true );	
				$sale_price = get_post_meta( $post->ID, '_sale_price', true );	
				$symboy = get_woocommerce_currency_symbol( get_woocommerce_currency() );
				$class = ( $product->get_price_html() ) ? '' : 'item-nonprice';
				$id = get_the_ID();
				?>
				<div class="tab-pane <?php echo ( $i == 0 ) ? 'active' : ''; ?>" id="<?php echo 'product_tab_'.$post->ID; ?>" >
					<div class="item-wrap">
						<div class="item-detail">	
							<div class="item-info">
								<div class="item-img products-thumb">
									<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
										<?php 
										if ( has_post_thumbnail() ){
											echo get_the_post_thumbnail( $post->ID, 'emarket_shop-image' ) ? get_the_post_thumbnail( $post->ID, 'emarket_shop-image' ): '<img src="'.get_template_directory_uri().'/assets/img/placeholder/'.'large'.'.png" alt="No thumb">';		
										}else{
											echo '<img src="'.get_template_directory_uri().'/assets/img/placeholder/'.'large'.'.png" alt="No thumb">';
										}
										?>
									</a>
									<?php sw_label_sales() ?>
								</div>
							</div>
							<div class="item-content">
								<h4><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute();?>"><?php the_title(); ?></a></h4>
								<!-- rating -->
								<?php 
								$rating_count = $product->get_rating_count();
								$review_count = $product->get_review_count();
								$average      = $product->get_average_rating();
								?>
								<div class="reviews-content">
									<div class="star"><?php echo ( $average > 0 ) ?'<span style="width:'. ( $average*14 ).'px"></span>' : ''; ?></div>
								</div>	
								<!-- end rating -->
								<!-- price -->
								<?php if ( $price_html = $product->get_price_html() ){?>
								<div class="item-price">
									<span>
										<?php echo $price_html; ?>
									</span>
								</div>
								<?php } ?>
								<div class="description"><?php echo $post->post_excerpt; ?></div>
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
								<div class="count-left pull-left">
									<h2><?php esc_html_e( 'Hurry up!', 'sw_woocommerce' ) ?></h2>
									<p><?php esc_html_e( 'Offers end in:', 'sw_woocommerce' ) ?></p>
								</div>
								<div class="product-countdown pull-left" data-price="<?php echo esc_attr($orginal_price ); ?>" data-starttime="<?php echo esc_attr( $start_time ); ?>" data-cdtime="<?php echo esc_attr( $countdown_time ); ?>" data-id="<?php echo 'product_'.$id.$post->ID; ?>"></div>				
								</div>	
							</div>
						</div>
					</div>
					<?php
					$i++; endwhile; wp_reset_postdata();
					?>
				</div>

				<div class="top-tab-slider clearfix">
					<div id="<?php echo 'tab_' . $countdown_id; ?>" class="sw-tab-slider responsive-slider loading hidden-xs" data-lg="8" data-md="5" data-sm="4" data-xs="4" data-mobile="3" data-speed="<?php echo esc_attr( $speed ); ?>" data-scroll="<?php echo esc_attr( $scroll ); ?>" data-interval="<?php echo esc_attr( $interval ); ?>" data-autoplay="false" data-vertical="false">
						<ul class="nav nav-tabs slider responsive">
							<?php
							$i = 0;
							while($list->have_posts()): $list->the_post();	
								global $product, $post;
								?>
								<li <?php echo ( $i == 0 )? 'class="item active"' : 'class="item"'; ?>>
									<a href="#<?php echo 'product_tab_'.$post->ID; ?>" data-toggle="tab">
										<?php echo get_the_post_thumbnail( $post->ID, 'shop_catalog' ); ?>
									</a>
								</li>
								<?php
								$i++; endwhile; wp_reset_postdata();
								?>
							</ul>
						</div>
					</div>

				</div>
			</div>
			<?php
		} 
		?>
		<script type="text/javascript">
/* (function ($) {
	"use strict";
	$('#<?php echo 'mytab_' . $countdown_id; ?> a').click(function (e) {
		console.log( $(this) );
		e.preventDefault();
		$(this).tab('show');
	});
})(jQuery);*/
</script>