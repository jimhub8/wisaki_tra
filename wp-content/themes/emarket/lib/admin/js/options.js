jQuery(document).ready(function(){
	
	
	if(jQuery('#last_tab').val() == ''){

		jQuery('.emarket-opts-group-tab:first').slideDown('fast');
		jQuery('#emarket-opts-group-menu li:first').addClass('active');
	
	}else{
		
		tabid = jQuery('#last_tab').val();
		jQuery('#'+tabid+'_section_group').slideDown('fast');
		jQuery('#'+tabid+'_section_group_li').addClass('active');
		
	}
	
	
	jQuery('input[name="'+emarket_opts.opt_name+'[defaults]"]').click(function(){
		if(!confirm(emarket_opts.reset_confirm)){
			return false;
		}
	});
	
	jQuery('.emarket-opts-group-tab-link-a').click(function(){
		relid = jQuery(this).attr('data-rel');
		
		jQuery('#last_tab').val(relid);
		
		jQuery('.emarket-opts-group-tab').each(function(){
			if(jQuery(this).attr('id') == relid+'_section_group'){
				jQuery(this).show();
			}else{
				jQuery(this).hide();
			}
			
		});
		
		jQuery('.emarket-opts-group-tab-link-li').each(function(){
				if(jQuery(this).attr('id') != relid+'_section_group_li' && jQuery(this).hasClass('active')){
					jQuery(this).removeClass('active');
				}
				if(jQuery(this).attr('id') == relid+'_section_group_li'){
					jQuery(this).addClass('active');
				}
		});
	});
	
	
	
	
	if(jQuery('#emarket-opts-save').is(':visible')){
		jQuery('#emarket-opts-save').delay(4000).slideUp('slow');
	}
	
	if(jQuery('#emarket-opts-imported').is(':visible')){
		jQuery('#emarket-opts-imported').delay(4000).slideUp('slow');
	}	
	
	jQuery('input, textarea, select').change(function(){
		jQuery('#emarket-opts-save-warn').slideDown('slow');
	});
	
	
	jQuery('#emarket-opts-import-code-button').click(function(){
		if(jQuery('#emarket-opts-import-link-wrapper').is(':visible')){
			jQuery('#emarket-opts-import-link-wrapper').fadeOut('fast');
			jQuery('#import-link-value').val('');
		}
		jQuery('#emarket-opts-import-code-wrapper').fadeIn('slow');
	});
	
	jQuery('#emarket-opts-import-link-button').click(function(){
		if(jQuery('#emarket-opts-import-code-wrapper').is(':visible')){
			jQuery('#emarket-opts-import-code-wrapper').fadeOut('fast');
			jQuery('#import-code-value').val('');
		}
		jQuery('#emarket-opts-import-link-wrapper').fadeIn('slow');
	});
	
	
	
	
	jQuery('#emarket-opts-export-code-copy').click(function(){
		if(jQuery('#emarket-opts-export-link-value').is(':visible')){jQuery('#emarket-opts-export-link-value').fadeOut('slow');}
		jQuery('#emarket-opts-export-code').toggle('fade');
	});
	
	jQuery('#emarket-opts-export-link').click(function(){
		if(jQuery('#emarket-opts-export-code').is(':visible')){jQuery('#emarket-opts-export-code').fadeOut('slow');}
		jQuery('#emarket-opts-export-link-value').toggle('fade');
	});
	
	

	
	
	
});