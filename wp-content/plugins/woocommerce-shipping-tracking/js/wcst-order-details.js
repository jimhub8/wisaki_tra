jQuery(document).ready(function()
{
	//console.log(wcst_options.autofocus == 'yes');
	if(wcst_options.autofocus == 'yes')
		jQuery('#_wcst_order_trackno').focus();
	wcst_set_date_pickers();
	wcst_init_no_tracking_code_checkboxes();
	jQuery(document).on('click', '.wcst_no_tracking_code_checkbox', wcst_no_tracking_code_check_box_click)
});

function wcst_set_date_pickers()
{
	try {
			jQuery( ".wcst_dispatch_date" ).pickadate({formatSubmit: 'yyyy-mm-dd',// wcst_date_format, 
													   format: wcst_date_format, 
													   hiddenSuffix: '',
													   selectYears:true, 
													   selectMonths:true,
													   // Strings and translations
														monthsFull: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
														monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
														weekdaysFull: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
														weekdaysShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
														

														// Buttons
														today: 'Today',
														clear: 'Clear',
														close: 'Close',

														// Accessibility labels
														labelMonthNext: 'Next month',
														labelMonthPrev: 'Previous month',
														labelMonthSelect: 'Select a month',
														labelYearSelect: 'Select a year'});
		}
	catch(err) {}
}

function wcst_init_no_tracking_code_checkboxes()
{
	jQuery('.wcst_no_tracking_code_checkbox').each(function(index, elem)
	{
		wcst_set_disabled(jQuery(elem).data('target'), jQuery(elem).attr('checked'));
	});
}
function wcst_no_tracking_code_check_box_click(event)
{
	var target = jQuery(event.currentTarget).data('target');
	wcst_set_disabled(target, jQuery(event.currentTarget).attr('checked'));
}
function wcst_set_disabled(elem, value)
{
	//jQuery(elem).prop('disabled', function(i, v) { return !v; });
	if(value)
		jQuery(elem).prop('disabled', true);
	else 
		jQuery(elem).removeAttr('disabled');
}