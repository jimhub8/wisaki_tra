<?php 	
	$widget_id = isset( $widget_id ) ? $widget_id : 'category_slide_'.$this->generateID();
	if( $category == '' ){
		return '<div class="alert alert-warning alert-dismissible" role="alert">
			<a class="close" data-dismiss="alert">&times;</a>
			<p>'. esc_html__( 'Please select a category for SW Woocommerce Category Slider. Layout ', 'sw_woocommerce' ) . $layout .'</p>
		</div>';
	}
	$category_color = '';
?>
<div id="<?php echo 'slider_' . $widget_id; ?>" class="responsive-slider sw-category-slider7 loading"  data-append=".resp-slider-container" data-lg="<?php echo esc_attr( $columns ); ?>" data-md="<?php echo esc_attr( $columns1 ); ?>" data-sm="<?php echo esc_attr( $columns2 ); ?>" data-xs="<?php echo esc_attr( $columns3 ); ?>" data-mobile="<?php echo esc_attr( $columns4 ); ?>" data-speed="<?php echo esc_attr( $speed ); ?>" data-scroll="<?php echo esc_attr( $scroll ); ?>" data-interval="<?php echo esc_attr( $interval ); ?>"  data-autoplay="<?php echo esc_attr( $autoplay ); ?>">
	<?php if( $title1 != '' ){ ?>
	<div class="box-title">
		<h3><span><?php echo $title1; ?></span></h3>
	</div>
	<?php } ?>
	<div class="resp-slider-container">
		<div class="slider responsive">
		<?php
			if( !is_array( $category ) ){
				$category = explode( ',', $category );
			}
			foreach( $category as $cat ){
				$term = get_term_by('slug', $cat, 'product_cat');	
				if( $term ) :
				$thumbnail_id 	= get_term_meta( $term->term_id, 'thumbnail_id', true );
				$category_color =  get_term_meta( $term->term_id, 'ets_cat_color', true );
				$thumb = wp_get_attachment_image( $thumbnail_id,'full' );
		?>
			<div class="item item-product-cat">					
				<div class="item-content">
					<h3><a data-color="<?php echo esc_attr( $category_color );?>" href="<?php echo get_term_link( $term->term_id, 'product_cat' ); ?>">#<?php sw_trim_words( $term->name, $title_length ); ?></a></h3>
				</div>
			</div>
			<?php endif; ?>
		<?php } ?>
		</div>
	</div>
</div>
<script type="text/javascript">
		(function($){
			$(window).on( 'load',function(){
				$( '.item .item-content h3 a' ).each(function(){
					var data = $(this).data( 'color' );
					if( data != '' ){
						$(this).css( 'background', data );
					}
				});
			});
		})(jQuery);
	</script>		