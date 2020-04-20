<?php
if( !defined('ABSPATH' ) ){
    exit;
}

$button_text = get_option( 'ywsfl_button_text_single_product' );
$remove_text = get_option( 'ywsfl_button_text_remove_in_list' );


global $product,  $YIT_Save_For_Later;
$product_id = yit_get_base_product_id( $product );

$hide_class = $YIT_Save_For_Later->is_product_in_savelist( $product_id ) ;

?>
<p class="ywsfl_button_container">
    <button type="submit" class="ywsfl_single_add button alt <?php echo $hide_class ? 'ywsfl_hide' :''?>"><?php echo esc_html( $button_text ); ?></button>
    <button type="submit" class="ywsfl_single_remove button alt <?php echo $hide_class ? '' :'ywsfl_hide'?>"><?php echo esc_html( $remove_text ); ?></button>
    <span class="ywsfl_single_message"></span>
    <input type="hidden"  class="ywslf_product_id" value="<?php echo $product_id;?>">
    <input type="hidden"  class="ywslf_variation_id" value="-1">
</p>    
<?php
