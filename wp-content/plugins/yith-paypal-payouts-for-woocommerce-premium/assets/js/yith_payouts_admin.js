jQuery(document).ready(function ($) {


    $('.yith_payouts_receiver_table').on('click', '.insert', function (e) {


        e.preventDefault();
        var button = $(this),
            table = button.closest('.yith_payouts_receiver_table');

        if (table.length) {

            var i = table.find('tbody tr').size(),
                data = {
                    'i': i,
                    'yith_action': 'add_new_receiver',
                    'action': payouts_admin.actions.add_receiver_row
                };


            $.ajax({
                type: 'POST',
                url: payouts_admin.admin_url,
                data: data,
                dataType: 'json',
                success: function (response) {

                    var table_body = $(document).find('.yith_payouts_receiver_table tbody');

                    table_body.append(response.result);
                    $(document.body).trigger('wc-enhanced-select-init');
                }
            });

        }

    });

    $('.yith_payouts_receiver_table').on('click', '.delete', function (e) {

        e.preventDefault();

        $(this).closest('tr').remove();

        var table = $('.yith_payouts_receiver_table');

        if (table.find('tbody tr').size() == 0) {

            var hidden_field = table.find('tbody .yith_payout_hidden_field');

            if (!hidden_field.length) {

                hidden_field = "<input type='hidden' class= 'yith_payout_hidden_field' name='yith_payouts_receiver_list'/>";
                table.find('tbody').append(hidden_field);

            }
        }
    });

    $('#yith_payouts_exclude_vendor_commission.disable_option').closest('tr').addClass('yith-disabled');

    $('.wp-list-table.payouts').on('click', 'a.disabled', function (e) {
        e.preventDefault();
    });
});