<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/*$args = array(
	'id' => '',
	'btn_class' => '',
	'name' =>'',
	'values' => array()
);
*/
extract( $args );

$label       = _x( 'For quantity from', 'Part of]: For quantity from 50 to 100 set 5 days', 'yith-woocommerce-delivery-date' );
$to          = _x( 'to', '[Part of]: For quantity from 50 to 100 set 5 days', 'yith-woocommerce-delivery-date' );
$set         = _x( 'set', '[Part of]: For quantity from 50 to 100 set 5 days', 'yith-woocommerce-delivery-date' );
$days        = _x( 'days', '[Part of]: For quantity from 50 to 100 set 5 days', 'yith-woocommerce-delivery-date' );
$remove      = __( 'Remove range', 'yith-woocommerce-delivery-date' );
$json_encode = '<div class="ywcdd_quantity_item">';
$json_encode .= " <input type='number' class='ywcdd_from' name='" . $name . "[need_process_day][index][from]'> ";
$json_encode .= $to;
$json_encode .= " <input type='number' class='ywcdd_to' name='" . $name . "[need_process_day][index][to]'> ";
$json_encode .= $set;
$json_encode .= " <input type='number' class='ywcdd_day' name='" . $name . "[need_process_day][index][day]'> ";
$json_encode .= $days;
$json_encode .= ' <a href ="" class="ywcdd_delete_range">' . $remove . '</a>';
$json_encode .= '</div>';

$json_encode = esc_attr( $json_encode );


if ( count( $values ) == 0 ) {

	$values = array(
		array(
			'from' => 1,
			'to'   => '',
			'day'  => 1
		)
	);
}


if( !isset( $btn_class ) ){
    $btn_class = 'ywcdd_save';
}

?>

<div class="ywcdd_quantity_day_container">
    <div class="ywcdd_quantity_list" data-row="<?php echo $json_encode; ?>">
        <label><?php echo $label; ?></label>
        <div class="ywcdd_quantity_row">
			<?php foreach ( $values as $i => $value ): ?>
                <div class="ywcdd_quantity_item">
                    <input type="number" class="ywcdd_from"
                           name="<?php echo $name; ?>[need_process_day][<?php echo $i; ?>][from]" min="1" step="1"
                           value="<?php echo $value['from']; ?>">
					<?php echo $to; ?>
                    <input type="number" class="ywcdd_to"
                           name="<?php echo $name; ?>[need_process_day][<?php echo $i; ?>][to]" min="1" step="1"
                           value="<?php echo $value['to']; ?>">
					<?php echo $set; ?>
                    <input type="number" class="ywcdd_day"
                           name="<?php echo $name; ?>[need_process_day][<?php echo $i; ?>][day]" step="1"
                           value="<?php echo $value['day']; ?>">
					<?php echo $days; ?>
                    <?php if( $i > 0 ):?>
                    <a href="" class="ywcdd_delete_range"><?php echo $remove; ?></a>
                    <?php endif;?>
                </div>
			<?php endforeach; ?>
        </div>

        <span class="description">
                                <?php _e( 'Set custom processing days depending on the number of products ordered by the user. <br/>Leave "to" value empty if you want to set a single processing time without changes when increasing the quantity.', 'yith-woocommerce-delivery-date' ); ?>
                            </span>
        <div class="ywcdd_add_new_range_container">

            <input type="submit" class="button button-secondary ywcdd_add_range"
                   value="<?php _e( '+ Add quantity range', 'yith-woocommerce-delivery-date' ); ?>">

        </div>
    </div>
    <div class="ywcdd_action_container">
        <input type="submit" class="button button-primary <?php echo $btn_class;?> <?php echo $btn_class;?>_<?php echo $type; ?>"
               value="<?php _e( 'Save', 'yith-woocommerce-delivery-date' ); ?>">
        <input type="submit" class="button button-secondary ywcdd_delete ywcdd_delete_<?php echo $type; ?>"
               value="<?php _e( 'Delete', 'yith-woocommerce-delivery-date' ); ?>">
    </div>
</div>

