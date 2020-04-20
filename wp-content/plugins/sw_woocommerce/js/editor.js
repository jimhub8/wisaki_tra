(function($) {
	$( window ).on( 'elementor/frontend/init', function() {
		//hook name is 'frontend/element_ready/{widget-name}.{skin} - i dont know how skins work yet, so for now presume it will
		//always be 'default', so for example 'frontend/element_ready/slick-slider.default'
		//$scope is a jquery wrapped parent element
		if( $(window).width() > 1024 ){
			elementorFrontend.hooks.addAction( 'frontend/element_ready/global', function($scope, $){
				$scope.find('.responsive-slider').each(function(){
					var $id 	= $(this);
					var $app 	= $id.data('append');
					var $append	= ( typeof( $app ) == 'undefined' ) ? $id : $(this).find($app);
					var $target = $(this).find( '.responsive' );
					var $col_lg = $id.data('lg');
					var $col_md = $id.data('md');
					var $col_sm = $id.data('sm');
					var $col_xs = $id.data('xs');
					var $col_mobile = $id.data('mobile');
					var $speed = $id.data('speed');
					var $interval = $id.data('interval');
					var $scroll = $id.data('scroll');
					var $autoplay = $id.data('autoplay');
					var $rtl = ( $('body').hasClass('rtl') ) ? true : false;
					var $fade 		= ( typeof( $id.data('fade') != "undefined" ) ) ? $id.data('fade') : false;
					var $dots 		= ( typeof( $id.data('dots') != "undefined" ) ) ? $id.data('dots') : false;
					$target.slick({
						appendArrows: $append,
						prevArrow: '<span data-role="none" class="res-button slick-prev" aria-label="previous"></span>',
						nextArrow: '<span data-role="none" class="res-button slick-next" aria-label="next"></span>',
						dots: $dots,
						infinite: true,
						speed: $speed,
						slidesToShow: $col_lg,
						slidesToScroll: $scroll,
						autoplay: $autoplay,
						autoplaySpeed: $interval,
						rtl: $rtl,			  
						responsive: [
						{
							breakpoint: 1199,
							settings: {
								slidesToShow: $col_md
							}
						},
						{
							breakpoint: 991,
							settings: {
								slidesToShow: $col_sm
							}
						},
						{
							breakpoint: 767,
							settings: {
								slidesToShow: $col_xs
							}
						},
						{
							breakpoint: 480,
							settings: {
								slidesToShow: $col_mobile    
							}
						}
						// You can unslick at a given breakpoint now by adding:
						// settings: "unslick"
						// instead of a settings object
						]
					});
					$(this).removeClass('loading');
				});
			});
		}
	 } );	
})(jQuery);