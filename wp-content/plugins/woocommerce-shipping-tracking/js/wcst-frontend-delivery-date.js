var wcst_start_date_range;
var wcst_end_date_range;
jQuery(document).ready(function()
{
	if(wcst.just_one_date_field == 'true')
	{
		jQuery( "#wcst_start_date_range" ).css('width', '100%');
		jQuery( "#wcst_end_date_range" ).remove();
	}
	wcst_start_date_range = jQuery( "#wcst_start_date_range" ).pickadate({formatSubmit: wcst_date_format, format: wcst_date_format,
																				 // Strings and translations
																				monthsFull: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
																				monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
																				weekdaysFull: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
																				weekdaysShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
																				

																				// Buttons
																				today: 'Today',
																				clear: 'Clear',
																				close: 'Close',
																				
																				//Min date 
																				min: [wcst_delivery_min_year,wcst_delivery_min_month,wcst_delivery_min_day],

																				// Accessibility labels
																				labelMonthNext: 'Next month',
																				labelMonthPrev: 'Previous month',
																				labelMonthSelect: 'Select a month',
																				labelYearSelect: 'Select a year'});
	wcst_end_date_range = jQuery( "#wcst_end_date_range" ).pickadate({formatSubmit: wcst_date_format, format: wcst_date_format,
																		   // Strings and translations
																			monthsFull: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
																			monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
																			weekdaysFull: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
																			weekdaysShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
																		

																			// Buttons
																			today: 'Today',
																			clear: 'Clear',
																			close: 'Close',
																			
																			//Min date 
																			min: [wcst_delivery_min_year,wcst_delivery_min_month,wcst_delivery_min_day],

																			// Accessibility labels
																			labelMonthNext: 'Next month',
																			labelMonthPrev: 'Previous month',
																			labelMonthSelect: 'Select a month',
																			labelYearSelect: 'Select a year'});
	
	
	var wcst_start_time_range = jQuery( "#wcst_start_time_range" ).pickatime({formatSubmit: 'HH:i', format: 'HH:i', min:[wcst_time_range_start_hour,wcst_time_range_start_minute], max:[wcst_time_range_end_hour,wcst_time_range_end_minute]});
	var wcst_end_time_range = jQuery( "#wcst_end_time_range" ).pickatime({formatSubmit: 'HH:i', format: 'HH:i',min:[wcst_time_range_start_hour,wcst_time_range_start_minute], max:[wcst_time_range_end_hour,wcst_time_range_end_minute]});
	var wcst_start_time_secondary_range = jQuery( "#wcst_start_time_secondary_range" ).pickatime({formatSubmit: 'HH:i', format: 'HH:i', min:[wcst_time_secondary_range_start_hour,wcst_time_secondary_range_start_minute], max:[wcst_time_secondary_range_end_hour,wcst_time_secondary_range_end_minute]});
	var wcst_end_time_secondary_range = jQuery( "#wcst_end_time_secondary_range" ).pickatime({formatSubmit: 'HH:i', format: 'HH:i',min:[wcst_time_secondary_range_start_hour,wcst_time_secondary_range_start_minute], max:[wcst_time_secondary_range_end_hour,wcst_time_secondary_range_end_minute]});
	
	wcst_add_shipping_delivery_times_to_desidered_delivery_date(null);
	jQuery(document).on('click','.shipping_method',wcst_add_shipping_delivery_times_to_desidered_delivery_date);
	
	jQuery(document).on('click', '#place_order', function(event) 
	{
		var start_date_range, end_date_range, start_time_range, end_time_range, start_time_secondary_range, end_time_secondary_range;
			
		start_date_range = wcst_start_date_range.pickadate('picker');
		if(typeof start_date_range != 'undefined')
			start_date_range = start_date_range.get('select', "yyyymmdd"); 
		
		end_date_range = wcst_end_date_range.pickadate('picker');
		if(typeof end_date_range != 'undefined')
			end_date_range = end_date_range.get('select', "yyyymmdd"); 
		
		start_time_range = wcst_start_time_range.pickatime('picker');
		if(typeof start_time_range != 'undefined')
			start_time_range = start_time_range.get('select','HH:i'); 
		
		end_time_range = wcst_end_time_range.pickatime('picker');
		if(typeof end_time_range != 'undefined')
			end_time_range = end_time_range.get('select','HH:i'); 
		
		start_time_secondary_range = wcst_start_time_secondary_range.pickatime('picker');
		if(typeof start_time_secondary_range != 'undefined')
			start_time_secondary_range = start_time_secondary_range.get('select','HH:i'); 
		
		end_time_secondary_range = wcst_end_time_secondary_range.pickatime('picker');
		if(typeof end_time_secondary_range != 'undefined')
			end_time_secondary_range = end_time_secondary_range.get('select','HH:i');  
		
		if((typeof start_date_range != 'undefined' && typeof end_date_range != 'undefined') && ( (start_date_range != null && end_date_range == null) || (start_date_range == null && end_date_range != null) || start_date_range > end_date_range) )
		{
			alert(wcst_date_error_message);
			event.preventDefault();
			event.stopImmediatePropagation();
			return false;
		}
		
		if((typeof start_time_range != 'undefined' && typeof end_time_range != 'undefined') && ( (start_time_range != null && end_time_range == null) || (start_time_range == null && end_time_range != null) || start_time_range > end_time_range) )
		{
			alert(wcst_time_error_message);
			event.preventDefault();
			event.stopImmediatePropagation();
			return false;
		}
		if((typeof start_time_secondary_range != 'undefined' && typeof end_time_secondary_range != 'undefined') && ((start_time_secondary_range != null && end_time_secondary_range == null) || (start_time_secondary_range != null && end_time_secondary_range == null) || start_time_secondary_range > end_time_secondary_range) )
		{
			alert(wcst_secondary_error_message);
			event.preventDefault();
			event.stopImmediatePropagation();
			return false;
		}
	
	});
});

function wcst_add_shipping_delivery_times_to_desidered_delivery_date(event)
{
	jQuery('.shipping_method').each(function(index, elem)
	{
		var estimated_delivery_elem = jQuery(elem).parent().find('.wcst_estimated_shipping_delivery');
		if(jQuery(elem).is(':checked'))
		{
			var max = 0;
			if(estimated_delivery_elem.length != 0)
			{
				max = estimated_delivery_elem.data('min') != "" ? estimated_delivery_elem.data('min') : 0;
				max = estimated_delivery_elem.data('max') != ""   ? estimated_delivery_elem.data('max') : max;
			}
			wcst_set_min_date(max);
		}
	});
}
function wcst_set_min_date(day_offset)
{
	var date = new Date(wcst_delivery_min_year,wcst_delivery_min_month,wcst_delivery_min_day);
	date.setDate(date.getDate() + day_offset);
	
	start_date_range = wcst_start_date_range.pickadate('picker');
	if(typeof start_date_range != 'undefined')
	{
		start_date_range.set('min', date);
		start_date_range.clear();
	}
		
	end_date_range = wcst_end_date_range.pickadate('picker');
	if(typeof end_date_range != 'undefined')
	{
		end_date_range.set('min', date);
		end_date_range.clear();
	}
}