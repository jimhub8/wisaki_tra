jQuery(document).ready(function ($) {

    var general_calendar = $('#ywcdd_general_calendar'),
        block_params = {
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            },
            ignoreIfBlocked: true
        };


    var render_calendar = function () {
            $events_json = general_calendar.data('ywcdd_events_json');
            general_calendar.fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                defaultDate: ywcdd_calendar_params.starday,
                locale: ywcdd_calendar_params.calendar_language,
                aspectRatio:1.8,
                editable: false,
                eventLimit: true, // allow "more" link when too many events
                events: $events_json,
                timeFormat: 'H:mm',
                buttonIcons: false,
                navLinks: true,
                eventRender: function (event, element, view) {
                    // we can remove only holiday

                    if (event.event_type == 'holiday') {

                        element.append("<span class='ywcdd_delete_calendar'></span>");
                        element.on('click', '.ywcdd_delete_calendar', function (e) {

                            var data = {
                                ywcdd_event_id: event.id,
                                action: ywcdd_calendar_params.actions.delete_holidays
                            };

                            $.ajax({
                                type: 'POST',
                                url: ywcdd_calendar_params.ajax_url,
                                data: data,
                                dataType: 'json',
                                success: function (response) {

                                    if (response.result === 'deleted') {
                                        $('#ywcdd_general_calendar').fullCalendar('removeEvents', event.id);
                                    }
                                }

                            });


                        });
                    }
                    element.find('.fc-title').html(element.find('.fc-title').text());

                }
            });
        },
        getDate = function (element) {
            var date;
            try {
                date = $.datepicker.parseDate(ywcdd_calendar_params.dateformat, element.value);
            } catch (error) {

                date = null;
            }

            return date;
        },
        delete_calendar_event = function () {
            $(document).on('change', '#ywcdd_holiday_list input[id^="ywcdd_holiday"]', function (e) {
                var opt = $(this).val(),
                    parent = $(this).parents('tr'),
                    item_id = parent.data('holiday_id'),
                    data = {
                        ywcdd_holiday_id: item_id,
                        ywcdd_holiday_enabled: opt,
                        action: ywcdd_calendar_params.actions.enable_disable_holidays
                    };

                $('#ywcdd_holiday_list').block(block_params);
                $.ajax({
                    type: 'POST',
                    url: ywcdd_calendar_params.ajax_url,
                    data: data,
                    dataType: 'json',
                    success: function (response) {

                        var events_json = response.result;
                        general_calendar.data('ywcdd_events_json', events_json);
                        general_calendar.fullCalendar("destroy");
                        render_calendar(general_calendar);

                        $('#ywcdd_holiday_list').unblock();
                    }
                });

            }).on('click', '#ywcdd_holiday_list a.ywcdd_delete_holiday', function (e) {
                e.preventDefault();
                var opt = $(this).val(),
                    parent = $(this).parents('tr'),
                    item_id = parent.data('holiday_id'),
                    data = {
                        ywcdd_holiday_id: item_id,
                        ywcdd_holiday_enabled: 'no',
                        ywcdd_delete_holiday: 'yes',
                        action: ywcdd_calendar_params.actions.enable_disable_holidays
                    };

                $('#ywcdd_holiday_list').block(block_params);
                $.ajax({
                    type: 'POST',
                    url: ywcdd_calendar_params.ajax_url,
                    data: data,
                    dataType: 'json',
                    success: function (response) {

                        var events_json = response.result;
                        general_calendar.data('ywcdd_events_json', events_json);
                        general_calendar.fullCalendar("destroy");
                        render_calendar(general_calendar);

                        $('#ywcdd_holiday_list').unblock();
                        parent.remove();
                    }
                });
            });
        },
        update_calendar_event = function () {
            $(document).on('click', '#ywcdd_holiday_list a.ywcdd_update_holiday', function (e) {
                e.preventDefault();
                var btn = $(this),
                    row = $(this).parents('tr'),
                    item_id = row.data('holiday_id'),
                    onoff = row.find('#' + item_id).val(),
                    from = row.find('.holiday_from').val(),
                    to = row.find('.holiday_to').val(),
                    event_name = row.find('.ywcdd_holiday_name').val(),
                    holiday_for = row.find('.ywcdd_how_holiday').select2('val');


                if ('' !== from && '' !== to && '' !== event_name && null !== holiday_for) {

                    var data = {
                        ywcdd_holiday_id: item_id,
                        ywcdd_holiday_enabled: onoff,
                        ywcdd_holiday_for : holiday_for,
                        ywcdd_event_name : event_name,
                        ywcdd_from: from,
                        ywcdd_to: to,
                        action: ywcdd_calendar_params.actions.update_holidays
                    };
                    $('#ywcdd_holiday_list').block(block_params);
                    $.ajax({
                        type: 'POST',
                        url: ywcdd_calendar_params.ajax_url,
                        data: data,
                        dataType: 'json',
                        success: function (response) {

                            var events_json = response.result;
                            general_calendar.data('ywcdd_events_json', events_json);
                            general_calendar.fullCalendar("destroy");
                            render_calendar(general_calendar);

                            go_to_view_mode(row);
                            $('#ywcdd_holiday_list').unblock();
                        }
                    });
                }
            });
        },
        initialize_onoff = function () {
            var $onoff = $('#ywcdd_holiday_list').find('.yith-plugin-fw-onoff-container span');
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
        clear_form = function () {

            var form_content = $('#ywcdd_add_holiday_container');

            form_content.find('input[type="text"], select').each(function () {

                if (!$(this).is('select')) {
                    $(this).val('');
                    if ($(this).hasClass('hasDatepicker')) {
                        $(this).datepicker("option", "minDate", 0);
                    }
                } else {

                    $(this).val(null).trigger('change');
                }
            });

        },
        init_datepicker = function () {
            var datepickers = $(document).find('.ywcdd_datepicker');
            datepickers.datepicker({
                dateFormat: ywcdd_calendar_params.dateformat,
            });

            $(document).find('.holiday_from').datepicker("option", "minDate", 0).on('change', function () {
                var from_field = $(this),
                    parent = from_field.parent().parent(),
                    to_field = parent.find('.holiday_to');

                if (from_field.val() !== '') {
                    to_field.datepicker("option", 'minDate', getDate(this));
                } else {
                    to_field.datepicker("option", 'minDate', 0);
                }
            });
        },
        go_to_view_mode =function( row ){
           var info_row =row.find('.ywcdd_row_holiday'),
               edit_row = row.find('.ywcdd_edit_row_holiday'),
               from = row.find('.holiday_from').val(),
               to = row.find('.holiday_to').val(),
               event_name = row.find('.ywcdd_holiday_name').val(),
               name = row.find('select.ywcdd_how_holiday option:selected'),
               name_html ='';

             name.each(function(){
                 name_html +='<p>'+$(this).html()+'</p>';
             });

            row.find('.ywcdd_row_holiday .ywcdd_holiday_name').html( event_name );
            row.find('.ywcdd_row_holiday.how_holiday_for').html( name_html );
            row.find('.ywcdd_row_holiday.start_event').html( '<p>'+from+'</p>' );
            row.find('.ywcdd_row_holiday.end_event').html( '<p>'+to+'</p>' );
            info_row.show();
            edit_row.hide();
        };


    $(document).on('click', '.ywcdd_add_holiday_btn', function (e) {
        e.preventDefault();

        $(document).find('#ywcdd_add_new_holiday').slideToggle();
    });

    $(document).on('click', 'tr td div.ywcdd_row_holiday a.ywcdd_edit_holiday',function(e){
       e.preventDefault();
       var row = $(this).parents('tr'),
           info_row =row.find('.ywcdd_row_holiday'),
           edit_row = row.find('.ywcdd_edit_row_holiday');

        info_row.hide();
        edit_row.show();
    });

    // Add new holiday
    $(document).on('click', '#ywcdd_add_new_holiday .yith-add-new-holiday', function (e) {
        e.preventDefault();

        var field_container = $(this).parent().parent(),
            table = field_container.parents('table'),
            event_name_field = field_container.find('#ywcdd_add_holiday_name'),
            event_start = field_container.find('#ywcdd_add_holiday_from'),
            event_end = field_container.find('#ywcdd_add_holiday_to'),
            event_subject = field_container.find('#ywcdd_add_holiday_how');

        var event_name_value = event_name_field.val(),
            event_start_value = event_start.val(),
            event_end_value = event_end.val(),
            event_subject_value = event_subject.select2('val');


        if ('' !== event_name_value && '' !== event_start_value && '' !== event_end_value && '' !== event_subject_value) {
            table.block(block_params);
            $(document).find('.ywcdd_add_holiday_btn').hide();

            var data = {
                ywcdd_add_holidays: 'add_new_holidays',
                ywcdd_how_add: event_subject_value,
                ywcdd_start_event: event_start_value,
                ywcdd_end_event: event_end_value,
                ywcdd_event_name: event_name_value,
                action: ywcdd_calendar_params.actions.add_holidays
            };

            $.ajax({
                type: 'POST',
                url: ywcdd_calendar_params.ajax_url,
                data: data,
                dataType: 'json',
                success: function (response) {

                    var events_json = response.result,
                        list = $(response.list);

                    general_calendar.data('ywcdd_events_json', events_json);
                    general_calendar.fullCalendar("destroy");
                    render_calendar(general_calendar);

                    $(document).find('#ywcdd_holiday_list').html(list);
                    $(document.body).trigger('wc-enhanced-select-init');
                    initialize_onoff();
                    init_datepicker();
                    delete_calendar_event();
                    clear_form();
                    table.unblock();

                }

            });
        } else {
            field_container.find("input[type='text'], select").change();

        }
    });

    $(document).on('change', '.ywcdd_holiday_name, .ywcdd_how_holiday, .holiday_from, .holiday_to', function (e) {
        var $this = $(this),
            value = '',
            element = '';

        if ($this.hasClass('ywcdd_how_holiday')) {
            value = $this.select2('val');
            element = $(this).parent().find('.select2-selection');
        } else {
            value = $this.val();
            element = $this;
        }
        if (value === '' || value === null) {

            element.addClass('ywcdd_required_field');
        } else {
            element.removeClass('ywcdd_required_field');
        }
    });


    $(document).on('ywcdd_init_calendar_tab', function () {

        $(document).find('tbody#ywcdd_holiday_body').parent('table').attr('id','ywcdd_holiday_list').addClass('widefat');

        if (general_calendar.length) {
            render_calendar();
        }
        init_datepicker();
        delete_calendar_event();
        update_calendar_event();

    }).trigger('ywcdd_init_calendar_tab');

});