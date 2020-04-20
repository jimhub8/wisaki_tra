jQuery(document).ready(function ($) {

    $(document).on('click', '.payout-item-preview:not(.disabled)', function (e) {

        e.preventDefault();
        var $previewButton = $(this),
            $payout_item_id = $previewButton.data('id');


        if ($previewButton.data('payout-data')) {
            $(this).WCBackboneModal({
                template: 'wc-modal-view-payout-item',
                variable: $previewButton.data('payout-data')
            });
        } else {
            $previewButton.addClass('disabled');

            $.ajax({
                url: payouts_modal.admin_url,
                data: {
                    payout_item_id: $payout_item_id,
                    action: payouts_modal.actions.payouts_get_payout_item_details,

                },
                type: 'GET',
                success: function (response) {
                    $('.payout-item-preview').removeClass('disabled');

                    if (response.success) {
                        $previewButton.data('payout-data', response.data);

                        $(this).WCBackboneModal({
                            template: 'wc-modal-view-payout-item',
                            variable: response.data
                        });
                    }
                }
            });
        }
    });
});