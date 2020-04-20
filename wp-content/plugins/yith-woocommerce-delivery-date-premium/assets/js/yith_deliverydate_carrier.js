jQuery(document).ready(function ($) {

        var block_params = {
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            },
            ignoreIfBlocked: true
        };

        var toggle_time_slot = function (event) {
                var _toggle = $(event.target),
                    _section = _toggle.closest('.ywcdd_list_row'),
                    _content = _section.find('.ywcdd_list_content');

                if (_section.is('.ywcdd_list_row_close')) {
                    _content.slideDown(400);
                } else {
                    _content.css({display: 'block'});
                    _content.slideUp(400);
                }

                _section.toggleClass('ywcdd_list_row_close');
            },
            initialize_time_picker = function () {

                $(document).find('.yith_timepicker').timepicker({
                    'timeFormat': yith_delivery_parmas.timeformat,
                    'step': yith_delivery_parmas.timestep,

                });
            },
            toggle_add_new_time_slot = function () {
                $(document).on('click', '#yith_add_time_slot', function (e) {
                    e.preventDefault();
                    $('.yith-new-time-slot').slideToggle();
                });
            },
            toggle_override_workday = function () {
                $(document).on('change', '.override_working_days input[id ^="yith_override_day"]', function (e) {

                    var btn = $(this),
                            row = btn.parents('.override_working_days'),
                            container = row.find('.working_day_container');

                        if( 'yes' === btn.val() ){

                            container.slideDown();
                        }else{
                            container.slideUp();
                        }
                });
            },
            add_new_time_slot = function () {
                $(document).on('click', '#yith_save_time_slot', function (e) {
                    var container = $(this).parent(),
                        slot_name = container.find('#yith_time_slot_name').val(),
                        timefrom = container.find('#yith_timepicker_from').val(),
                        timeto = container.find('#yith_timepicker_to').val(),
                        max_order = container.find('#yith_max_tot_order').val(),
                        fee_name = container.find('#yith_fee_name').val(),
                        fee = container.find('#yith_fee').val(),
                        post_id = container.find('#yith_carrier_id').val(),
                        metakey = container.find('#yith_metakey').val();
                    e.preventDefault();
                    if ('' !== timefrom && '' !== timeto) {

                        var data = {
                            ywcdd_slot_name: slot_name,
                            ywcdd_time_from: timefrom,
                            ywcdd_time_to: timeto,
                            ywcdd_max_order: max_order,
                            ywcdd_fee_name: fee_name,
                            ywcdd_fee: fee,
                            ywcdd_carrier_id: post_id,
                            ywcdd_metakey: metakey,
                            action: yith_delivery_parmas.actions.add_carrier_time_slot
                        };

                        container.block(block_params);
                        $.ajax({
                            type: 'POST',
                            url: yith_delivery_parmas.ajax_url,
                            data: data,
                            dataType: 'json',
                            success: function (response) {
                                container.unblock();
                                $('#yith_add_time_slot').click();

                                if (typeof response.template !== 'undefined') {
                                    var time_slot_list = $(document).find('.ywcdd_carrier_table');

                                    time_slot_list.html(response.template);
                                    initialize_time_picker();
                                    $(document.body).trigger('wc-enhanced-select-init');
                                    initialize_onoff();
                                    delete_time_slot();
                                    update_time_slot();
                                    toggle_override_workday();
                                }
                            }
                        });
                    }else{
                        container.find('.yith_timepicker').trigger('change');
                    }

                });
            },
            initialize_onoff = function () {
                var $onoff = $('.ywcdd_list_row').find('.yith-plugin-fw-onoff-container span');
                $onoff.on('click', function () {
                    var input = $(this).prev('input'),
                        checked = input.prop('checked');

                    if (checked) {
                        input.prop('checked', false).attr('value', 'no').removeClass('onoffchecked');
                    } else {
                        input.prop('checked', true).attr('value', 'yes').addClass('onoffchecked');
                    }

                    input.change();
                });
            },
            enable_disable_time_slot = function () {
                $(document).on('change', '#_ywcdd_addtimeslot .ywcdd_time_slot__enabled input[type="checkbox"]', function (e) {
                    var option = $(this).val(),
                        time_slot_id = $(this).attr('id'),
                        row = $(this).parents('.ywcdd_list_row'),
                        carrier_id = row.data('item_key'),
                        data = {
                            ywcdd_carrier_id: carrier_id,
                            ywcdd_slot_id: time_slot_id,
                            ywcdd_enable: option,
                            action: yith_delivery_parmas.actions.enable_disable_time_slot
                        };

                    row.block(block_params);
                    $.ajax({

                        type: 'POST',
                        url: yith_delivery_parmas.ajax_url,
                        data: data,
                        dataType: 'json',
                        success: function (response) {
                            row.unblock();
                        }
                    });

                });
            },
            delete_time_slot = function () {
                $(document).on('click', '#_ywcdd_addtimeslot .ywcdd_list_content .ywcdd_delete_time_slot', function (e) {
                    e.preventDefault();
                    var btn = $(this),
                        row = btn.parents('.ywcdd_list_row'),
                        onoff = row.find('.ywcdd_time_slot__enabled input[type="checkbox"]'),
                    parent = row.parent().parent(),
                        time_slot_id = onoff.attr('id'),
                        carrier_id = row.data('item_key'),
                        data = {
                            ywcdd_carrier_id: carrier_id,
                            ywcdd_slot_id: time_slot_id,
                            action: yith_delivery_parmas.actions.delete_carrier_time_slot
                        };

                    parent.block(block_params);
                    $.ajax({

                        type: 'POST',
                        url: yith_delivery_parmas.ajax_url,
                        data: data,
                        dataType: 'json',
                        success: function (response) {
                            row.remove();
                            parent.unblock();
                        }
                    });
                });
            },
            update_time_slot = function () {
                $(document).on('click', '#_ywcdd_addtimeslot .ywcdd_list_content .yith_update_time_slot', function (e) {

                    var btn = $(this),
                        row = btn.parents('.ywcdd_list_row'),
                        index = row.data('index'),
                        parent = row.parent(),
                        onoff = row.find('.ywcdd_time_slot__enabled input[type="checkbox"]'),
                        onoff_override = row.find('#yith_override_day_'+index),
                        enabled = onoff.val(),
                        slot_name = row.find('.yith_time_slot_name').val(),
                        time_from = row.find('.yith_timepicker_from').val(),
                        time_to = row.find('.yith_timepicker_to').val(),
                        max_order = row.find('.yith_max_tot_order').val(),
                        fee_name = row.find('.yith_fee_name').val(),
                        fee = row.find('.yith_fee').val(),
                        override_day = onoff_override.val(),
                        days = row.find('.yith_dayworkselect').select2('val'),
                        item_id = onoff.attr('id'),
                        carrier_id = row.data('item_key');

                    if ('' !== time_from && '' !== time_to) {
                        e.preventDefault();
                        parent.block(block_params);
                        var data = {
                            ywcdd_slot_name: slot_name,
                            ywcdd_time_from: time_from,
                            ywcdd_time_to: time_to,
                            ywcdd_max_order: max_order,
                            ywcdd_fee_name: fee_name,
                            ywcdd_fee: fee,
                            ywcdd_day: days,
                            ywcdd_carrier_id: carrier_id,
                            ywcdd_override_days: override_day,
                            ywcdd_item_id: item_id,
                            ywcdd_enabled: enabled,
                            action: yith_delivery_parmas.actions.update_carrier_time_slot
                        };

                        $.ajax({
                            type: 'POST',
                            url: yith_delivery_parmas.ajax_url,
                            data: data,
                            dataType: 'json',
                            success: function (response) {
                                parent.unblock();
                            }

                        });
                    }

                });
            };

        $(document).on('ywcdd_init_carrier_metabox', function (e) {

            $(document.body).trigger('wc-enhanced-select-init');
            $(document).on('click', '.ywcdd_time_slot__toggle', toggle_time_slot);
            initialize_time_picker();
            toggle_add_new_time_slot();
            add_new_time_slot();
            enable_disable_time_slot();
            delete_time_slot();
            update_time_slot();
            toggle_override_workday();
        }).trigger('ywcdd_init_carrier_metabox');

        $(document).on('change','.yith_timepicker',function(e){

            if( $(this).val()=== ''){
                $(this).addClass('ywcdd_required_field');
            }else{
                $(this).removeClass('ywcdd_required_field');
            }
        });

    }
);