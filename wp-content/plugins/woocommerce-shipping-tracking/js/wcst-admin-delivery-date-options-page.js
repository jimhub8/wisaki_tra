jQuery(document).ready(function()
{
	jQuery(document).on('click', '#wcst_date_range', wcst_show_just_one_delivery_date_option);
	wcst_show_just_one_delivery_date_option(null);
});
function wcst_show_just_one_delivery_date_option(event)
{
	if(document.getElementById('wcst_date_range').checked)
		jQuery('#wcst_just_one_date_field').show();
	else 
		jQuery('#wcst_just_one_date_field').hide();
}