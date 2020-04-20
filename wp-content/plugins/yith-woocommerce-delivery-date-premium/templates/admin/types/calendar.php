<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$calendar_event = esc_attr( YITH_Delivery_Date_Calendar()->get_calendar_events( true ) );
?>
<tbody>
    <tr>
        <td>
            <div id="ywcdd_general_calendar" data-ywcdd_events_json="<?php echo $calendar_event; ?>"></div>
        </td>
    </tr>
</tbody>