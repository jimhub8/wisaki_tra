<script>
var orders_id_to_url = new Array(); 
<?php foreach($track_urls as $oder_id => $url_array) 
{
	if($url_array === 'false')
		echo "orders_id_to_url['{$oder_id}'] = \"{$url_array}\"; ";
	else
	{
		?>
		if(typeof orders_id_to_url['<?php echo $oder_id?>'] === 'undefined')
			orders_id_to_url['<?php echo $oder_id?>'] = new Array();
		<?php foreach($url_array as $url): ?>
			orders_id_to_url['<?php echo $oder_id?>'].push('<?php echo $url?>');
	<?php   endforeach;
	}
}
?>
jQuery(document).ready(function()
{
	var tracking_shipping_button_text = '<?php echo addslashes ($tracking_shipment_button); ?>';
	jQuery('table.shop_table.my_account_orders tbody tr.order').each(function(index)
	{
		var wcst_var = "<?php echo WC_VERSION; ?>";
		//var order_num = /* wcst_versionCompare(wcst_var, "3.0.0") < 0 */ jQuery(this).find('td.order-number a').length ? jQuery(this).find('td.order-number a').html() : jQuery(this).find('td.woocommerce-orders-table__cell-order-number a').html();
		var order_num = jQuery(this).find('td.woocommerce-orders-table__cell-order-number a').data("wcst-id");
		var main_element = /* wcst_versionCompare(wcst_var, "3.0.0")  < 0 */ jQuery(this).find('td.order-actions').length ? jQuery(this).find('td.order-actions') : jQuery(this).find('td.woocommerce-orders-table__cell.woocommerce-orders-table__cell-order-actions');
		
		/*order_num = order_num.replace('#',' ');
		order_num = order_num.replace(/\s/g, '');
		order_num = order_num.replace(/\D/g,'');*/
		if(orders_id_to_url[order_num] !== 'false')
		{
			//jQuery(this).find('td.order-actions').prepend('<a href="'+orders_id_to_url[order_num]+'" class="button wcst-myaccount-tracking-button" target="_blank"><?php _e('Track shipment', 'woocommerce-shipping-tracking'); ;?></a>');
			var last_element = null;
			if(typeof orders_id_to_url[order_num] !== 'undefined')
				for(var i=0; i< orders_id_to_url[order_num].length; i++)
				{
					var value = orders_id_to_url[order_num][i];
					var button_text = tracking_shipping_button_text.replace('%s', i+1); 
					var button_element = jQuery('<a href="'+value+'" class="button wcst-myaccount-tracking-button" target="_blank">'+button_text+'</a>');
					//if(last_element == null)
						main_element.prepend(button_element);
					/* else
						button_element.after(last_element);
					
					last_element = button_element; */
				}
			//);
		}
	});
});
function wcst_versionCompare(a, b) 
{
    var i, cmp, len, re = /(\.0)+[^\.]*$/;
    a = (a + '').replace(re, '').split('.');
    b = (b + '').replace(re, '').split('.');
    len = Math.min(a.length, b.length);
    for( i = 0; i < len; i++ ) {
        cmp = parseInt(a[i], 10) - parseInt(b[i], 10);
        if( cmp !== 0 ) {
            return cmp;
        }
    }
    return a.length - b.length;
}
</script>