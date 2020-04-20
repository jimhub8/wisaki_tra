jQuery(document).ready(function ($) {
    $('#woocommerce_yith-stripe-connect_credit-cards-logo').select2({
        allowClear             : true,
        minimumResultsForSearch: Infinity
    });
    if ($('#woocommerce_yith-stripe-connect_test-live').is(':checked')) {
        $('.yith_wcsc_test_live_item').closest('tr').show();
    } else {
        $('.yith_wcsc_test_live_item').closest('tr').hide();
    }

    $('#woocommerce_yith-stripe-connect_test-live').on('click', function (e) {
        if ($(this).is(':checked')) {
            $('.yith_wcsc_test_live_item').closest('tr').show();
        } else {
            $('.yith_wcsc_test_live_item').closest('tr').hide();
        }
    });
    $('.yith_wcsc_message').on('click', '.button-primary', function (e) {
        var $message_panel = $(this).closest('.yith_wcsc_message');
        var action = '';

        if ($message_panel.hasClass('yith_wcsc_message_redirect_uri')) {
            action = 'redirect_uri_done'
        }

        else if ($message_panel.hasClass('yith_wcsc_message_webhook')) {
            action = 'webhook_done'
        }

        else {
            action =  $message_panel.data( 'action' );
        }

        if ('' != action) {
            $message_panel.block({
                message   : null,
                overlayCSS: {
                    background: "#fff",
                    opacity   : .6
                }
            });

            var post_data =
                {
                    action: action,
                }

            $.post(yith_wcsc_admin.ajaxurl, post_data).success(function (result) {
                $message_panel.unblock();
                $message_panel.hide();
            });
        }


    });

});