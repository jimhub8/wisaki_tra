jQuery(document).ready(function()
{
	wcst_load_in_site_tracking_info();
});
function wcst_load_in_site_tracking_info()
{
	jQuery('.wcst-in-site-shipping-tracking-container').each(function(index, elem)
	{
		var formData = new FormData();
		formData.append('action', 'wcst_get_tracking_info'); //WCST_ShippingCompany
		formData.append('tracking_code', jQuery(this).data('tracking-code')); 
		formData.append('tracking_company_id', 'track_in_site'); 
		//Request
		jQuery.ajax( { url: wcsts_in_site_tracking.ajax_url, 
					   type: 'POST',
					   data: formData,
					   async: true,
					   processData: false,
					   contentType: false,
					   success: function(data)
					   {
							jQuery(elem).html(data);
						  }
					}
			  );
		});
}