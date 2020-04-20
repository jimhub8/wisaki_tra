<?php 
/**
* Layout Countdown Default
* @version     1.0.0
**/

$term_name = esc_html__( 'All Categories', 'sw_woocommerce' );
$default = array(
	'post_type' 			=> 'product',		
	'post_status' 			=> 'publish',
	'ignore_sticky_posts'   => 1,
	'showposts'				=> $numberposts,
	'meta_key' 		 		=> 'total_sales',
	'orderby' 		 		=> 'meta_value_num '. $orderby,
	'order' => $order,
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
			'terms'     => $category )
		);	
}
$default = sw_check_product_visiblity( $default );

$countdown_id = 'sw_tab_bestsales_'.rand().time();

$list = new WP_Query( $default );
if ( $list -> have_posts() ){ ?>
<div id="<?php echo $countdown_id; ?>" class="sw_tab_bestsales">
	<?php if( $title1 != '' ){?>
	<div class="block-title">
		<h3><span><?php echo ( $title1 != '' ) ? $title1 : ''; ?></span></h3>
	</div>
	<?php } ?>
	<div  class="tab-sw-slide clearfix">
		<div class="tab-content clearfix">
			<?php
			$count_items 	= 0;
			$numb 			= ( $list->found_posts > 0 ) ? $list->found_posts : count( $list->posts );
			$count_items 	= ( $numberposts >= $numb ) ? $numb : $numberposts;
			$i 				= 0;
			while($list->have_posts()): $list->the_post();global $product, $post;
			$class = ( $product->get_price_html() ) ? '' : 'item-nonprice';
				?>
				<div class="tab-pane <?php echo ( $i == 0 ) ? 'active' : ''; ?>" id="<?php echo 'product_tab_bestsales'.$post->ID; ?>" >
					<div class="item">
						<div class="item-wrap6">
							<div class="item-detail">	
								<div class="item-img products-thumb">
									<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
										<?php 
										$id = get_the_ID();
										if ( has_post_thumbnail() ){
											echo get_the_post_thumbnail( $post->ID, 'large' ) ? get_the_post_thumbnail( $post->ID, 'large' ): '<img src="'.get_template_directory_uri().'/assets/img/placeholder/'.'large'.'.png" alt="No thumb">';		
										}else{
											echo '<img src="'.get_template_directory_uri().'/assets/img/placeholder/'.'large'.'.png" alt="No thumb">';
										}
										?>
									</a>
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
										<div class="star"><?php echo ( $average > 0 ) ?'<span style="width:'. ( $average*15 ).'px"></span>' : ''; ?></div>
									</div>									
									<!-- end rating  -->
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
								</div>
							</div>
						</div>
					</div>
					<?php
					$i++; endwhile; wp_reset_postdata();
					?>
				</div>

				<div class="top-tab-slider clearfix">
					<div id="<?php echo 'tab_' . $countdown_id; ?>" class="sw-tab-slider responsive-slider loading hidden-xs" data-lg="4" data-md="4" data-sm="3" data-xs="3" data-mobile="2" data-speed="<?php echo esc_attr( $speed ); ?>" data-scroll="<?php echo esc_attr( $scroll ); ?>" data-interval="<?php echo esc_attr( $interval ); ?>" data-autoplay="false" data-vertical="true">
						<ul class="nav nav-tabs slider responsive">
							<?php
							$i = 0;
							while($list->have_posts()): $list->the_post();	
								global $product, $post;
								?>
								<li <?php echo ( $i == 0 )? 'class="item active"' : 'class="item"'; ?>>
									<a href="#<?php echo 'product_tab_bestsales'.$post->ID; ?>" data-toggle="tab">
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