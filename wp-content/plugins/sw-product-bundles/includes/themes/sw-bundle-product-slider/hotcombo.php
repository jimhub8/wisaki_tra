<?php 
/**
	* Layout Bunled product Default
	* @version     1.0.0
**/
$term_name = esc_html__( 'All Categories', 'sw_product_bundles' );
$default = array(
	'post_type' => 'product',	
	'p' => $product_id,     
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
	<div id="<?php echo $category.'_'.$id; ?>" class="sw-woo-container-slider  bundle-slider style-hotcombo" >
		<div class="resp-slider-container">	
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
				<div class="item-countdown item product product-images <?php echo esc_attr( $class )?>" id="<?php echo 'product_'.$id.$post->ID; ?>" >
				<?php } ?>
					<div class="item-wrap">
						<div class="item-detail">							
							<div class="item-image-bundle products-thumb-big products-thumb">
							    <?php do_action( 'woocommerce_before_shop_loop_item_title' ); ?>
							    <?php sw_label_sales(); ?>
							</div>
							<!--- pack thumail -->
						    <div class="item-pack">
						    	<!-- title -->
					    		<?php if( $title1 != '' ){?>
									<div class="top-tab-slider clearfix">
										<div class="box-title">
											<h3><span><?php echo ( $title1 != '' ) ? $title1 : $term_name; ?></span></h3>
										</div>
									</div>
								<?php } ?>
								<!-- end title -->
								<div class="product-thumbnail responsive-slider" id="product_thumbnail_<?php echo esc_attr( $post->ID ); ?>" data-lg="<?php echo esc_attr( $columns ); ?>" data-md="<?php echo esc_attr( $columns1 ); ?>" data-sm="<?php echo esc_attr( $columns2 ); ?>" data-xs="<?php echo esc_attr( $columns3 ); ?>" data-mobile="<?php echo esc_attr( $columns4 ); ?>" data-speed="<?php echo esc_attr( $speed ); ?>" data-scroll="<?php echo esc_attr( $scroll ); ?>" data-interval="<?php echo esc_attr( $interval ); ?>"  data-autoplay="<?php echo esc_attr( $autoplay ); ?>" data-circle="false" data-dots="false" data-vertical="false">	
									<?php  $bundles = apply_filters( 'swpb/load/bundle', $product->get_id() );
										    echo '<div class="included">'.esc_html__("INCLUDED: ","sw_product_bundles").count($bundles ).esc_html__(" ITEMS","sw_product_bundles").'</div>'; ?>
                                    <div class="slider responsive">	
										<?php	
										    $bundles = apply_filters( 'swpb/load/bundle', $product->get_id() );
											foreach ( $bundles as $key => $value )  { 
	                                        $bundle = wc_get_product( $key ); 
	                                        $product_url = "";
											if ( get_post_type( $key ) == 'product_variation' ) {
												$product_url = get_the_permalink( wp_get_post_parent_id( $key ) );
											} else {
												$product_url = get_the_permalink( $key );
											}

										?>
										<div class="item-thumbnail-product">
											<div class="thumbnail-wrapper">
												<?php echo $bundle->get_image( 'medium' );	?>
											</div>
											<div class="boxinfo-wrapper">
												<div class="title-wrapper">
	                                             <a href="<?php echo esc_url( $product_url ); ?>""><?php echo esc_html( $value['title'] );?></a>
	                                            </div>
	                                            <div class="price-wrapper"><?php echo $bundle->get_price_html();?></div>
                                            </div>
										</div>
										<?php } ?>
									</div>		
								</div>
								<div class="item-content">
									<div class="item-content-left pull-left">
										<!-- rating  -->
										 <?php if ($average = $product->get_average_rating()) : ?>
										    <?php echo '<div class="star-rating" title="'.sprintf(__( 'Rated %s out of 5', 'woocommerce' ), $average).'"><span style="width:'.( ( $average / 5 ) * 100 ) . '%"><strong itemprop="ratingValue" class="rating">'.$average.'</strong> '.__( 'out of 5', 'woocommerce' ).'</span></div>'; ?>
										<?php endif; ?>
																	
										<!-- end rating  -->
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
									<div class="item-content-right pull-right">
										<h4><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute();?>"><?php swpb_bunlde_trim_words( get_the_title(), $title_length ); ?></a></h4>
										<?php the_excerpt(); ?>
									</div>						
								</div>	
							</div>	
							<!-- end pack thumnail -->	
						</div>	
					</div>
				<?php if( ( $i+1 ) % $item_row == 0 || ( $i+1 ) == $count_items ){?> </div><?php }?>
			<?php $i ++; endwhile; wp_reset_postdata();?>
			
		</div>            
	</div>
<?php
	} 
?>