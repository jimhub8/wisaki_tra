/*
 *
 * Emarket_Options_radio_img function
 * Changes the radio select option, and changes class on images
 *
 */
function emarket_radio_img_select(relid, labelclass){
	jQuery(this).prev('input[type="radio"]').prop('checked');

	jQuery('.emarket-radio-img-'+labelclass).removeClass('emarket-radio-img-selected');	
	
	jQuery('label[for="'+relid+'"]').addClass('emarket-radio-img-selected');
}//function