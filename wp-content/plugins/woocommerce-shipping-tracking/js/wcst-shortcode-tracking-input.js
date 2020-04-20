jQuery(document).ready(function()
{
	jQuery(document).on('click', '.wcst_tracking_code_button', wcst_redirect_to_company_url);
});
function wcst_start_loading_ui_effects(id)
{
	jQuery('#wcst_tracking_code_button_'+id).attr('disabled', true);
	jQuery('#wcst_tracking_code_input_'+id).attr('disabled', true);
	jQuery('#wcst_loading_'+id).fadeIn();
}
function wcst_end_loading_ui_effects(id)
{
	jQuery('#wcst_tracking_code_button_'+id).attr('disabled', false);
	jQuery('#wcst_tracking_code_input_'+id).attr('disabled', false);
	jQuery('#wcst_loading_'+id).fadeOut();
}
function wcst_redirect_to_company_url(event)
{
	var id = jQuery(event.currentTarget).data('id');
	var tracking_code = jQuery('#wcst_tracking_code_input_'+id).val();
	var tracking_company_id = jQuery('#wcst_shipping_company_'+id).val();
	
	if(tracking_code == "")
	{
		alert(wcst_tracking_code_empty_error);
		return false;
	}
	//UI
	wcst_start_loading_ui_effects(id);
	
	var formData = new FormData();
	formData.append('action', 'wcst_get_tracking_info'); //WCST_ShippingCompany
	formData.append('tracking_code', tracking_code); 
	formData.append('tracking_company_id', tracking_company_id); 
	//Request
	jQuery.ajax( { url: wcst_ajaxurl, 
				   type: 'POST',
				   data: formData,
				   async: true,
				   processData: false,
				   contentType: false,
				   success: function(data){
						wcst_end_loading_ui_effects(id);
						if(tracking_company_id != 'track_in_site')
						{
							if(typeof wcst_redirection_method === 'undefined' || wcst_redirection_method == 'same_page')
								window.location.href = data;
							else
								window.open(data, '_blank');
						}
						else 
						{
							jQuery('#wcst_tracking_info_box_'+id).html(data);
						}
					  }
				}
		  )/* .done(function( data ) { 
			wcst_end_loading_ui_effects(id);
			if(typeof wcst_redirection_method === 'undefined' || wcst_redirection_method == 'same_page')
				window.location.href = data;
			else
				window.open(data, '_blank');
			//console.log(data);
		  } )*/;
}