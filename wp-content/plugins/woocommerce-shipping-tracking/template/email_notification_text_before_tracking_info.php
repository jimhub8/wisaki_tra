<?php 
$active_notification_text_before_tracking_info = (!isset($messages['wcst_active_notification_text_before_tracking_info']) || $messages['wcst_active_notification_text_before_tracking_info'] == "") ? "":nl2br($messages['wcst_active_notification_text_before_tracking_info']);


$active_notification_text_before_tracking_info = str_replace("[order_id]", $order->get_id(), $active_notification_text_before_tracking_info);
$active_notification_text_before_tracking_info = str_replace("[billing_first_name]", $order->get_billing_first_name(), $active_notification_text_before_tracking_info);
$active_notification_text_before_tracking_info = str_replace("[billing_last_name]", $order->get_billing_last_name(), $active_notification_text_before_tracking_info);
$active_notification_text_before_tracking_info = str_replace("[shipping_first_name]", $order->get_shipping_first_name(), $active_notification_text_before_tracking_info);
$active_notification_text_before_tracking_info = str_replace("[shipping_last_name]", $order->get_shipping_last_name(), $active_notification_text_before_tracking_info);
$active_notification_text_before_tracking_info = str_replace("[formatted_billing_address]", $order->get_formatted_billing_address(), $active_notification_text_before_tracking_info);
$active_notification_text_before_tracking_info = str_replace("[formatted_shipping_address]", $order->get_formatted_shipping_address(), $active_notification_text_before_tracking_info);

echo $active_notification_text_before_tracking_info;
?>