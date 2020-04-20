(function($) {
	$(document).ready(function(){
		/*
		 *
		 * Emarket_Options_upload function
		 * Adds media upload functionality to the page
		 *
		 */
		 
		var header_clicked = false;
		 
		$("img[src='']").attr("src", emarket_upload.url);
		
		$('.emarket-menu-upload').on('click', function() {
			header_clicked = true;
			formfield = $(this).attr('rel-id');
			preview = $(this).prev('img');
			tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
			return false;
		});
		
		$('.emarket-opts-upload').click(function() {
			header_clicked = true;
			formfield = $(this).attr('rel-id');
			preview = $(this).prev('img');
			tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
			return false;
		});
		
		
		// Store original function
		window.original_send_to_editor = window.send_to_editor;
		
		
		window.send_to_editor = function(html) {
			if (header_clicked) {
				if ($('img', html).attr('src')) {
					imgurl = $('img', html).attr('src');
				} else imgurl = $(html).attr('src');
				
				$('#' + formfield).val(imgurl);
				$('#' + formfield).next().fadeIn('slow');
				$('#' + formfield).next().next().fadeOut('slow');
				$('#' + formfield).next().next().next().fadeIn('slow');
				$(preview).attr('src' , imgurl);
				tb_remove();
				header_clicked = false;
			} else {
				window.original_send_to_editor(html);
			}
		}
		$('.emarket-opts-upload-remove').click(function(){
			$relid = $(this).attr('rel-id');
			$('#'+$relid).val('');
			$(this).prev().fadeIn('fast');
			$(this).prev().prev().fadeOut('fast', function(){jQuery(this).attr("src", emarket_upload.url);});
			$(this).fadeOut('slow');
		});
		
		$('.emarket-menu-upload-remove').on('click', function(){
			$relid = $(this).attr('rel-id');
			$('#'+$relid).val('');
			$(this).prev().fadeIn('fast');
			$(this).prev().prev().fadeOut('fast', function(){$(this).attr("src", emarket_menu_upload.url);});
			$(this).fadeOut('slow');
		});
		
		$( '.field-which_user' ).each( function(){
			var $this  = $(this);
			var target = $(this).find( '.edit-menu-item-which_user' );
			var select_current = target.val();
			if( select_current == 'logined' ){
				$this.parent().find( '.field-user_role' ).show();
			}else{
				$this.parent().find( '.field-user_role' ).hide();
			}
			target.change(function(){
				var select_val = $(this).val();
				if( select_val == 'logined' ){
					$this.parent().find( '.field-user_role' ).show();
				}else{
					$this.parent().find( '.field-user_role' ).hide();
				}
			});
		});
		
		$( '.field-advanced' ).each( function(){
			var $this  = $(this);
			var target = $(this).find( '.edit-menu-item-advanced' );
			var select_current = target.val();
			if( select_current == 'apc' ){
				$this.parent().find( '.field-advanced_content' ).show();
				$this.parent().find( '.field-page_select' ).hide();
			}else if( select_current == 'apcs' ){
				$this.parent().find( '.field-page_select' ).show();
				$this.parent().find( '.field-advanced_content' ).hide();
			}else{
				$this.parent().find( '.field-advanced_content' ).hide();
				$this.parent().find( '.field-page_select' ).hide();
			}
			target.change(function(){
				var select_val = $(this).val();
				if( select_val == 'apc' ){
					$this.parent().find( '.field-advanced_content' ).show();
					$this.parent().find( '.field-page_select' ).hide();
				}else if( select_val == 'apcs' ){
					$this.parent().find( '.field-page_select' ).show();
					$this.parent().find( '.field-advanced_content' ).hide();
				}else{
					$this.parent().find( '.field-advanced_content' ).hide();
					$this.parent().find( '.field-page_select' ).hide();
				}
			});
		});
	});
	function MenuClick(){
		$('.menu-advance-href').on('click', function(){
			$(this).parent().find('.menu-config-content').slideToggle();
		});
	}
	$(document).ready(function(){
		MenuClick();
	});
}(jQuery));