(function($) {
	var swpb = function() {
		/* used to holds next request's data (most likely to be transported to server) */
		this.request = null;
		/* used to holds last operation's response from server */
		this.response = null;
		/* to prevetn Ajax conflict. */
		this.ajaxFlaQ = true;
		/*Holds currently selected fields */
		this.activeField = null;
		
		this.initialize = function() {
			this.registerEvents();
		};
		
		this.registerEvents = function() {
			$(document).on( "keyup", "#swpb-product-search-txt", this, function(e) {
				e.data.prepareRequest( "GET", "search", $(this).val() );
				e.data.dock();
			});			
			$(document).on( "click", "ul.swpb-auto-complete-ul li a", this, function(e) {
				$(this).toggleClass("selected");
				e.preventDefault();
				e.stopPropagation();
			});
			$(document).on( "click", "#swpb-add-product", this, function(e) {
				var sel_product = [];
				$("ul.swpb-auto-complete-ul li").each(function(){
					if( $(this).find('a').hasClass('selected') ) {
						sel_product.push( $(this).find('a').attr('product_id') );
					}
				});
				if( sel_product.length > 0 ) {
					e.data.prepareRequest( "GET", "add-to-bundle", sel_product );
					e.data.dock();
				} else {
					$("#swpb-product-search-txt").attr( 'placeholder', 'Please search for a product.!' );
					$("#swpb-product-search-txt").focus();
				}
				e.preventDefault();
			});
			// $(document).on( "click", "#swpb-add-product", this, function(e) {
			// 	var sel_product = [];
				
			// 	$("#bundled_product").each(function(){
			// 		sel_product =  $(this).val() ;		
			// 	});
			// 	$("#bundled_product").html("");
			// 	if( sel_product != null ) {
			// 		e.data.prepareRequest( "GET", "add-to-bundle", sel_product );
			// 		e.data.dock();
			// 	} else {
			// 		alert("add product to search");
			// 	}
			// 	e.preventDefault();
              
			// });
			$(document).on( "click", "a.swpb-remove-bundle-btn", this, function(e) {
				$( '.swpb-products-container' ).css( 'opacity', '0.5' );
				e.data.prepareRequest( "DELETE", "remove-from-bundle", $(this).attr('product_id') );
				e.data.dock();
				e.preventDefault();
				e.stopPropagation();
			});
			$(document).on( "click", "a.swpb-update-bundle-btn", this, function(e) {			
				return e.data.onPostSubmit( $(this));
		   });
			$(document).on( "click", ".swpb_close_all", function(e) {
				$(".swpb-wc-metabox-content").hide();
				e.preventDefault();
			});
			$(document).on( "click", ".swpb_expand_all", function(e) {
				$(".swpb-wc-metabox-content").show();
				e.preventDefault();
			});
			$(document).on( "click", "h3.swpb-product-bundle-row-header", function() {
				$(this).next().toggle();
			});
			$(document).on( "submit", "form#post", this, function(e) {			
				return e.data.onPostSubmit( $(this));
			});
			
		};	
		
		this.onPostSubmit = function() {
			var key = "";
			var bundles = [];
			$("#swpb-products-container > div").each(function(){	
				key = $(this).attr('product_id');
				bundles.push( 
					{ 
						"product_id" : key,
						"bundle" : {						
							quantity : $("input[name=swpb_bundle_product_"+ $(this).attr('product_id') +"_quantity]").val(),
							price : $("input[name=swpb_bundle_product_"+ $(this).attr('product_id') +"_price]").val(),	
							tax_included : ( $("input[name=swpb_bundle_product_"+ $(this).attr('product_id') +"_tax_included]").is(':checked') ) ? "yes" : "no",
							thumbnail : ( $("input[name=swpb_bundle_product_"+ $(this).attr('product_id') +"_thumbnail]").is(':checked') ) ? "yes" : "no",
							category : ( $("input[name=swpb_bundle_product_"+ $(this).attr('product_id') +"_category]").is(':checked') ) ? "yes" : "no",
							title : $("input[name=swpb_bundle_product_"+ $(this).attr('product_id') +"_title]").val(),
							desc : $("textarea[name=swpb_bundle_product_"+ $(this).attr('product_id') +"_desc]").val()
						} 
					} 
				);
			});		
			
			$("#swpb-bundles-array").val( JSON.stringify( bundles ) );			
		};
		
		this.prepareRequest = function( _request, _context, _payload ) {
			this.request = {
				request : _request,
				context : _context,
				post 	: swpb_var.post_id,
				payload : _payload
			};
		};
		
		this.prepareResponse = function( _status, _msg, _data ) {
			this.response = {
				status : _status,
				message : _msg,
				payload : _data
			};
		};
		
		this.dock = function( _action, _target ) {		
			var me = this;
			/* see the ajax handler is free */
			if( !this.ajaxFlaQ ) {
				return;
			}				
			$.ajax({  
				type       : "POST",  
				data       : { action : "swpb_ajax", SWPB_AJAX_PARAM : JSON.stringify(this.request)},  
				dataType   : "json",  
				url        : swpb_var.ajaxurl,
				beforeSend : function(){  				
					/* enable the ajax lock - actually it disable the dock */	

					me.ajaxFlaQ = false;				
					$("#swpb-ajax-spinner").show();
				},  
				success    : function(data) {	
				
					/* disable the ajax lock */
					me.ajaxFlaQ = true;			

					$("#swpb-ajax-spinner").hide();
					me.prepareResponse( data.status, data.message, data.data );		               

					/* handle the response and route to appropriate target */
					if( me.response.status ) {
						me.responseHandler();
					} else {
						/* alert the user that some thing went wrong */						
					}
					$( '.swpb-products-container' ).removeAttr( 'style' );
				},  
				error      : function(jqXHR, textStatus, errorThrown) {                    
					/* disable the ajax lock */
					me.ajaxFlaQ = true;
					$("#swpb-ajax-spinner").hide();
					$( '.swpb-products-container' ).removeAttr( 'style' );
				}  
			});		
		};
		
		this.responseHandler = function(){	
			var key = "";
			var item_price = "";
			var item_quantity = "";
			var Sum = 0;
			var Total = [];
			if( this.request.context == "search" ) {
				$("#swpb-product-search-result-holder").html( this.response.payload );
				$("#swpb-product-search-result-holder").show();
			} else if( this.request.context == "add-to-bundle" ) {
				if( this.response.status ) {
					if( $("#swpb-products-container > div").hasClass('swpb-empty-msg') ) {
						$("#swpb-products-container").html( this.response.payload );
					} else {
						$("#swpb-products-container").append( this.response.payload );
					}					
				}
				$("#swpb-products-container > div").each(function(){	
					key = $(this).attr('product_id');
					item_quantity = $("input[name=swpb_bundle_product_"+ key +"_quantity]").val(); 
					item_price = item_quantity * $("input[name=swpb_bundle_product_"+ key +"_price]").val();
					Sum += +item_price;	
					Total.push(Sum);
				});
				Total_fil = Total.pop();
				$("#_swpb_product_regular_price").val(Sum);
				$("#_regular_price").val(Sum);
				$("#_regular_price").keyup(function(){
					if ($(this).val() > Total_fil) {
						alert('Please enter in a value less than the total bundle price');
						$( this ).val( '' );
					}
			
				});
				
			} else if( this.request.context == "remove-from-bundle" ) {
				if( this.response.status ) {
					$("#swpb-products-container > div[product_id="+ this.request.payload +"]").remove();					
					if( !$("#swpb-products-container > div").hasClass('swpb-product-bundle-row') ) {
						$("#swpb-products-container").html('<div class="swpb-empty-msg"><p>Search for products, select as many as product you want and add those to bundle using "Add Products". Only "Simple" or "variable" product are allowed to add. You can also drag drop to re arrange the order of bundled products in product page.!</p></div>');
					}
				}	
				$("#swpb-products-container > div").each(function(){	
					key = $(this).attr('product_id');
					item_quantity = $("input[name=swpb_bundle_product_"+ key +"_quantity]").val(); 
					item_price = item_quantity * $("input[name=swpb_bundle_product_"+ key +"_price]").val();
					Sum += +item_price;
					Total.push(Sum);
				});
				Total_fil = Total.pop();
				$("#_swpb_product_regular_price").val(Sum);	
				$("#_regular_price").keyup(function(){
					if ($(this).val() > Total_fil) {
						alert('Please enter in a value less than the total bundle price');
						$( this ).val( '' );
					}
			
				});
				if($("#_regular_price").val() > Total_fil) {
					alert('Please enter in a value less than the total bundle price');
					$("#_regular_price").val( '' );
				}
			}
		};
	};
	
	$(document).ready(function(){
		var swpbObj = new swpb();
		swpbObj.initialize();		
		var regular_price = "";
		$('#swpb-products-container').sortable();
		$("#_swpb_product_regular_price").attr("readonly", true);
		$(".swpb-bundle-product-admin-price").attr("readonly", true);
		var key = "";
		var item_price = "";
		var item_quantity = "";
		var Sum = 0;
		$("#swpb-products-container > div").each(function(){	
			key = $(this).attr('product_id');
			item_quantity = $("input[name=swpb_bundle_product_"+ key +"_quantity]").val(); 
			item_price = item_quantity * $("input[name=swpb_bundle_product_"+ key +"_price]").val();
			Sum += +item_price;
		});
		$("#_swpb_product_regular_price").val(Sum);	
	});	
	$(document).click(function(){
		$("#swpb-product-search-result-holder").hide();
	});	
})( jQuery );