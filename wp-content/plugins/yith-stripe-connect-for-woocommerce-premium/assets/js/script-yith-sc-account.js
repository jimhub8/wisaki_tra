jQuery(document).ready(function ($) {

    init_connect_button();

    function init_connect_button() {
        $('#yith-sc-connect-button').on('click', function (e) {
            if ($(this).hasClass('yith-sc-disconnect')) {
                e.preventDefault();
                $('.stripe-connect').block({message: null, overlayCSS: {background: "#fff", opacity: .6}});
                console.log(yith_wcsc_account_page_script.ajaxurl);

                var options = {
                    action: yith_wcsc_account_page_script.disconnect_stripe_connect_action
                };
                $.post(yith_wcsc_account_page_script.ajaxurl, options)
                    .success(function (data) {
                            $('.stripe-connect').unblock();
                            if (data['disconnected']) {
                                $('.stripe-connect').removeClass('yith-sc-disconnect');
                                $('.stripe-connect').attr('href', yith_wcsc_account_page_script.OAuth_link);
                                $('.message').text('');
                                $('.stripe-connect>span').text(yith_wcsc_account_page_script.messages.connect_to);
                            } else {
                                $('.message').text(data['message']);
                                $('.stripe-connect>span').text(yith_wcsc_account_page_script.messages.disconnect_to);
                            }
                        }
                    ).error(function (jqXHR, textStatus, errorThrown) {
                    }
                )
            }
        });


    }

});