<?php 
/**
	* Layout Bunled product Default
	* @version     1.0.0
**/
//echo "da vao";
$viewall = get_permalink( wc_get_page_id( 'shop' ) );
$term_name = esc_html__( 'All Categories', 'sw_woocommerce' );
$default = array(
	'post_type' => 'product',	
	'orderby' => $orderby,
	'order' => $order,
	'post_status' => 'publish',
	'showposts' => $numberposts	
);
$default['tax_query'][] = array(						
		'taxonomy' => 'product_type',
		'field'    => 'name',
		'terms'    => 'bundle',
		'operator' => 'IN',	
	);
//var_dump($category);
if( $category != '' ){
	$term = get_term_by( 'slug', $category, 'product_cat' );
	if( $term ) :
		$term_name = $term->name;
	endif; 
	
	$default['tax_query'][] = array(
		array(
			'taxonomy'  => 'product_cat',
			'field'     => 'slug',
			'terms'     => $category ));
}
$id = 'sw_countdown_'.$this->generateID();
$list = new WP_Query( $default );
$countdown_time = strtotime( $date );
if ( $list -> have_posts() ){ ?>
	<div id="<?php echo $category.'_'.$id; ?>" class="sw-woo-container-slider responsive-slider bundle-slider style2 loading" data-lg="<?php echo esc_attr( $columns ); ?>" data-md="<?php echo esc_attr( $columns1 ); ?>" data-sm="<?php echo esc_attr( $columns2 ); ?>" data-xs="<?php echo esc_attr( $columns3 ); ?>" data-mobile="<?php echo esc_attr( $columns4 ); ?>" data-speed="<?php echo esc_attr( $speed ); ?>" data-scroll="<?php echo esc_attr( $scroll ); ?>" data-interval="<?php echo esc_attr( $interval ); ?>"  data-autoplay="<?php echo esc_attr( $autoplay ); ?>" data-circle="false" data-dots="false">
		<div class="resp-slider-container">
			<?php if( $title1 != '' ){?>
				<div class="block-title clearfix">
					<h3 class="pull-left"><?php echo ( $title1 != '' ) ? $title1 : $term_name; ?></h3>
					<a class="view-all pull-right" href="<?php echo esc_url( $viewall ); ?>"><?php echo esc_html__('see all','sw_woocommerce'); ?> <i class="fa fa-caret-right"></i></a>
				</div>
			<?php } ?>
			<div class="slider responsive">	
			<?php 
				$count_items = 0;
				$count_items = ( $numberposts >= $list->found_posts ) ? $list->found_posts : $numberposts;
				$i = 0;
				while($list->have_posts()): $list->the_post();					
				global $product, $post;
				//var_dump($list);
				$class = ( $product->get_price_html() ) ? '' : 'item-nonprice';
				if( $i % $item_row == 0 ){
			?>
				<div class="item-countdown item product product-images-bundle<?php echo esc_attr( $class )?>" id="<?php echo 'product_'.$id.$post->ID; ?>" data-vertical="false">
				<?php } ?>
					<div class="item-wrap3">
						<div class="item-detail">
							<div class="item-top-product">							
								<div class="item-image-bundle products-thumb-big products-thumb">
									<div class="item-thumb-top">
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
										<?php echo function_exists( 'emarket_quickview' ) ? emarket_quickview() : ''; ?>
										<?php swpb_bunlde_label_sales();?>
									</div>
								</div>
								<!--- pack thumail -->
							    <div class="item-pack">
									<div class="slider product-responsive-thumbnail" id="product_thumbnail_<?php echo esc_attr( $post->ID ); ?>">											 
										<?php	
										    $bundles = apply_filters( 'swpb/load/bundle', $product->get_id() );
											foreach ( $bundles as $key => $value )  { 
	                                        $bundle = wc_get_product( $key ); 
										?>
										<div class="item-thumbnail-product">
											<div class="thumbnail-wrapper">
												<?php echo $bundle->get_image( 'thumbnail' );	?>
											</div>
										</div>
										<?php } ?> 
									</div>	
								</div>	
							</div>
							<!-- end pack thumnail -->	
							<div class="item-content">
							   <!-- rating  -->
								<?php 
									$rating_count = $product->get_rating_count();
									$average      = $product->get_average_rating();
								?>
								<div class="reviews-content">
									<div class="star"><?php echo ( $average > 0 ) ?'<span style="width:'. ( $average*12 ).'px"></span>' : ''; ?></div>
								</div>									
								<!-- end rating  -->
								<h4><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute();?>"><?php swpb_bunlde_trim_words( get_the_title(), $title_length ); ?></a></h4>
								<!-- Price -->
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
				<?php if( ( $i+1 ) % $item_row == 0 || ( $i+1 ) == $count_items ){?> </div><?php }?>
			<?php $i ++; endwhile; wp_reset_postdata();?>
			</div>
		</div>            
	</div>
<?php
	} 
?>