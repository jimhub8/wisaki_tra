<?php 

/**
	* Layout Child Category
	* @version     1.0.0
**/
if( $category == '' ){
	return '<div class="alert alert-warning alert-dismissible" role="alert">
		<a class="close" data-dismiss="alert">&times;</a>
		<p>'. esc_html__( 'Please select a category for SW Woo Slider. Layout ', 'sw_woocommerce' ) . $layout .'</p>
	</div>';
}

$widget_id = isset( $widget_id ) ? $widget_id : $this->generateID();
$viewall = get_permalink( wc_get_page_id( 'shop' ) );
$default = array();
if( $category != '' ){
	$default = array(
		'post_type' => 'product',
		'tax_query' => array(
		array(
			'taxonomy'  => 'product_cat',
			'field'     => 'slug',
			'terms'     => $category ) ),
		'orderby' => $orderby,
		'order' => $order,
		'post_status' => 'publish',
		'showposts' => $numberposts
	);
}

$term_name = '';
$term = get_term_by( 'slug', $category, 'product_cat' );
if( $term ) :
	$term_name = $term->name;
	$viewall = get_term_link( $term->term_id, 'product_cat' );
endif;

$list = new WP_Query( $default );
if ( $list -> have_posts() ){ ?>
	<div id="<?php echo 'slider_' . $widget_id; ?>" class="responsive sw-child-cat2 clearfix">
		<div class="child-top clearfix">
			<div class="box-title pull-left">
				<h3><?php echo ( $title1 != '' ) ? $title1 : $term_name; ?></h3>
			</div>
			<?php 
			if( $term ) :
				$termchild 		= get_terms( 'product_cat', array( 'parent' => $term->term_id, 'hide_empty' => 0, 'number' => $number_child ) );
			if( count( $termchild ) > 0 ){
			?>		
			<div class="childcat-content pull-right"  id="<?php echo 'child_' . $widget_id; ?>">
				<?php 					
					echo '<ul>';
					echo '<li>' .esc_html__('Top 10 Items', 'sw_woocommerce') . '</li>';
					foreach ( $termchild as $key => $child ) {
						echo '<li><a href="' . get_term_link( $child->term_id, 'product_cat' ) . '">' . $child->name . '</a></li>';
					}
					echo '</ul>';
				?>
			</div>
			<?php } ?>
			<?php endif; ?>
			<button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#<?php echo 'child_' . $widget_id; ?>"  aria-expanded="false">				
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>	
			</button>
		</div>
		<div class="content-slider">
			<div class="childcat-slider-content">
				<!-- Product tab listing -->
				<?php 
				$banner_links = explode( ',', $banner_links );
				if( $image != '' ) :
					$image = explode( ',', $image );	
				endif;
				if( count( $image ) > 0 && count( $banner_links ) == count( $image ) ) :
				?>
				<div class="banner-category">
					<div id="<?php echo esc_attr( 'banner_' . $widget_id ); ?>" class="responsive-slider banner-slider loading" data-lg="1" data-md="1" data-sm="1" data-xs="1" data-arrow="false" data-fade="false">
						<div class="slider responsive">
							<?php foreach( $image as $key => $img ) : ?>
								<div class="item">
									<a href="<?php echo esc_url( $banner_links[$key] ); ?>"><?php echo wp_get_attachment_image( $img, 'full' ); ?></a>
								</div>
							<?php endforeach;?>						
						</div>
					</div>
				</div>
				<?php endif; ?>	
				<div class="resp-slider-container <?php echo esc_attr( 'item-columns'.$columns ); ?> clearfix">
					<div class="slider">	
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
							<?php include( WCTHEME . '/default-item3.php' ); ?>
						<?php if( ( $i+1 ) % $item_row == 0 || ( $i+1 ) == $count_items ){?> </div><?php } ?>
						<?php $i++; endwhile; wp_reset_postdata();?>
					</div>
				</div> 
			</div>
			<div class="best-seller-product">
				<div class="box-slider-title">
					<h2 class="page-title-slider"><?php echo esc_html__('best sellers', 'sw_woocommerce'); ?></h2>
				</div>
				<div class="wrap-content">
				<?php 
					$seller = array(
						'post_type' 			=> 'product',		
						'post_status' 			=> 'publish',
						'ignore_sticky_posts'   => 1,
						'showposts'				=> $bnumber,
						'meta_key' 		 		=> 'total_sales',
						'orderby' 		 		=> 'meta_value_num',						
					);
					if( $category != '' ){
						$term = get_term_by( 'slug', $category, 'product_cat' );	
						if( $term ) :
							$term_name = $term->name;
						endif;
						
						$seller['tax_query'] = array(
							array(
								'taxonomy'	=> 'product_cat',
								'field'	=> 'slug',
								'terms'	=> $category,
								'operator' => 'IN'
							)
						);
					}
					$r = new WP_Query( $seller );
					while ( $r -> have_posts() ) : $r -> the_post();
					global $product, $post;
				?>				
					<div class="item clearfix">
						<div class="item-inner">
							<div class="item-img">
								<a href="<?php echo get_permalink($post->ID) ?>" title="<?php echo esc_attr( $post->post_title ) ?>">
								<?php if( has_post_thumbnail() ){  ?>
								<?php echo ( get_the_post_thumbnail( $r->post->ID, 'thumbnail' ) ) ? get_the_post_thumbnail( $r->post->ID, 'thumbnail' ):'<img src="'.get_template_directory_uri().'/assets/img/placeholder/shop_thumbnail.png" alt="No thumb"/>';
									}else{ ?>
									<img src="'.get_template_directory_uri().'/assets/img/placeholder/shop_thumbnail.png" alt="No thumb"/>
								<?php	} ?>
								</a>
							</div>
							<div class="item-content">
								<!-- rating  -->
								<?php 
									$rating_count = $product->get_rating_count();
									$review_count = $product->get_review_count();
									$average      = $product->get_average_rating();
								?>
								<div class="reviews-content">
									<div class="star"><?php echo ( $average > 0 ) ?'<span style="width:'. ( $average*14 ).'px"></span>' : ''; ?></div>
								</div>	
								<!-- end rating  -->
								<h4><a href="<?php echo get_permalink($post->ID) ?>" title="<?php echo esc_attr( $post->post_title ) ?>"><?php echo  esc_html( $post->post_title ) ?></a></h4>
								<div class="item-price"><?php echo $product->get_price_html() ?></div>			 
							</div>
						</div>
					</div>
				<?php 
					endwhile;
					wp_reset_postdata();
				?>
				</div>
			</div>
		</div>
	</div>
	<?php
	}else{
		echo '<div class="alert alert-warning alert-dismissible" role="alert">
		<a class="close" data-dismiss="alert">&times;</a>
		<p>'. esc_html__( 'There is not product in this category', 'sw_woocommerce' ) .'</p>
	</div>';
	}
?>