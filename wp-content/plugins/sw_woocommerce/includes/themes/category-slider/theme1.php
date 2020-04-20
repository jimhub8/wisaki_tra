<?php 	
$widget_id = isset( $widget_id ) ? $widget_id : 'category_slide_'.$this->generateID();
$viewall = get_permalink( wc_get_page_id( 'shop' ) );
if( $category == '' ){
	return '<div class="alert alert-warning alert-dismissible" role="alert">
	<a class="close" data-dismiss="alert">&times;</a>
	<p>'. esc_html__( 'Please select a category for SW Woocommerce Category Slider. Layout ', 'sw_woocommerce' ) . $layout .'</p>
</div>';
}
?>
<div id="<?php echo 'slider_' . $widget_id; ?>" class="responsive-slider sw-category-slider2 loading"  data-append=".resp-slider-container" data-lg="<?php echo esc_attr( $columns ); ?>" data-md="<?php echo esc_attr( $columns1 ); ?>" data-sm="<?php echo esc_attr( $columns2 ); ?>" data-xs="<?php echo esc_attr( $columns3 ); ?>" data-mobile="<?php echo esc_attr( $columns4 ); ?>" data-speed="<?php echo esc_attr( $speed ); ?>" data-scroll="<?php echo esc_attr( $scroll ); ?>" data-interval="<?php echo esc_attr( $interval ); ?>"  data-autoplay="<?php echo esc_attr( $autoplay ); ?>">
	<?php	if( $title1 != '' ){ ?>
	<div class="box-title clearfix">
		<h3 class="pull-left"><?php echo $title1; ?></h3>
		<a class="view-all pull-right" href="<?php echo esc_url( $viewall ); ?>"><?php echo esc_html__('see all categories','sw_woocommerce'); ?> <i class="fa fa-caret-right"></i></a>
	</div>
	<?php } ?>
	<?php	if( $desciption != '' ){ ?>
	<div class="description1"><?php echo $desciption; ?>
	</div>
	<?php } ?>
	<div class="resp-slider-container">
		<div class="slider responsive">
			<?php
			if( !is_array( $category ) ){
				$category = explode( ',', $category );
			}
			$i = 0;
			foreach( $category as $cat ){
				$term = get_term_by('slug', $cat, 'product_cat');	
				if( $term ) :
					$thumbnail_id1 	= get_term_meta( $term->term_id, 'thumbnail_id1', true );
				$thumb = wp_get_attachment_image( $thumbnail_id1,'full' );
				
				if( $i % $item_row == 0 ){	
					?>
					<div class="item item-product-cat">
						<?php } ?>
						<div class="item-wrap">
							<div class="item-image">
								<a href="<?php echo get_term_link( $term->term_id, 'product_cat' ); ?>" title="<?php echo esc_attr( $term->name ); ?>"><?php echo $thumb; ?></a>
								<div class="item-content">
									<h3>
										<a href="<?php echo get_term_link( $term->term_id, 'product_cat' ); ?>"><?php sw_trim_words( $term->name, $title_length ); ?></a>
									</h3>
									<!-- Get child category -->
									<?php
									$termchildren = get_terms('product_cat',array('child_of' => $term->term_id));
									if( count( $termchildren ) > 0 ){
										?>
										<div class="childcat-product-cat">
											<?php 
										$termchildren = get_terms('product_cat',array('child_of' => $term->term_id));
										echo '<ul>';
										$i = 1;
										foreach ( $termchildren as $child ) {
											echo '<li><a href="' . get_term_link( $child, 'product_cat' ) . '">' . $child->name . '</a></li>';
											$i ++;
											if( $i == 4 ){
												break;
											}
										}
										echo '<li class="view-all"><a href="' . $viewall . '">' .esc_html__('More categories', 'sw_woocommerce') . '</a></li>';
										echo '</ul>';
										?>
									</div>
									<?php } ?>
									<!-- End get child category -->	
								</div>
							</div>	
						</div>
						<?php if( ( $i+1 ) % $item_row == 0 || ( $i+1 ) == count($category) ){?> </div><?php } ?>
						<?php $i++;?>
					<?php endif; ?>
					<?php } ?>
				</div>
			</div>
		</div>		