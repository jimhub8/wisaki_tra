jQuery(document).ready(function($){
    var  block_params = {
        message: null,
        overlayCSS: {
            background: '#fff',
            opacity: 0.6
        },
        ignoreIfBlocked: true
    },
    initialize_onoff = function () {
        var $onoff = $('.ywcdd_custom_processing_day_container').find('.yith-plugin-fw-onoff-container span');
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
    };

    $(document).on('click','.yith-add-new-product-day,.yith-add-new-category-day',function(e){

       e.preventDefault();
       if( $(this).hasClass('yith-add-new-product-day')) {
           $('#ywcdd_form_add_product').slideToggle();
       }else{
           $('#ywcdd_form_add_category').slideToggle();
       }
    });

    $(document).on('click','.ywcdd_add_range',function(e){
       e.preventDefault();

        var parent = $(this).parent().parent(),
            range_list = parent.find('.ywcdd_quantity_row'),
            i = range_list.find('.ywcdd_quantity_item').size(),
            data_row  = parent.data('row');

        data_row = $(data_row);
        var html = data_row.html().replace(/index/g,i);

        data_row.html( html );
        data_row.appendTo( range_list);

    });

    $( document ).on( 'click', '.ywcdd_custom_processing_method__toggle', function ( event ) {
        var _toggle  = $( event.target ),
            _section = _toggle.closest( '.ywcdd_list_row' ),
            _content = _section.find( '.ywcdd_list_content' );

        if ( _section.is( '.ywcdd_list_row_close' ) ) {
            _content.slideDown( 400 );
        } else {
            _content.css( { display: 'block' } );
            _content.slideUp( 400 );
        }

        _section.toggleClass( 'ywcdd_list_row_close' );
    } );

    $(document).on('click','.ywcdd_save',function(e){
        e.preventDefault();

        var btn_save = $(this),
            container = btn_save.parent().parent().parent().parent(),
            container_id = container.attr('id');

            //add new product rule
            if( container_id === 'ywcdd_form_add_product'){
                add_new_product_rule();
            }else if( container_id === 'ywcdd_form_add_category'){
                add_new_category_rule();
            }
    });

    $(document).on( 'click','.ywcdd_update_product', function(e){
       e.preventDefault();
        var btn = $(this),
            list_row = btn.parent().parent().parent().parent(),
            product_id = list_row.data('item_key'),
            quantity_field = list_row.find('input').serialize(),
            data = {
                ywcdd_product_id: product_id,
                action:yith_delivery_parmas.actions.update_product_day,
                ywcdd_args: quantity_field,
                ywcdd_action: 'update'
            };
        list_row.block(block_params);
        $.ajax({

            type: 'POST',
            url: yith_delivery_parmas.ajax_url,
            data: data,
            dataType: 'json',
            success: function (response) {
                list_row.unblock();
            }
        });


    });
    $(document).on( 'click','.ywcdd_update_category', function(e){
        e.preventDefault();
        var btn = $(this),
            list_row = btn.parent().parent().parent().parent(),
            category_id = list_row.data('item_key'),
            quantity_field = list_row.find('input').serialize(),
            data = {
                ywcdd_category_id: category_id,
                action:yith_delivery_parmas.actions.update_category_day,
                ywcdd_args: quantity_field,
                ywcdd_action: 'update'
            };
        list_row.block(block_params);
        $.ajax({

            type: 'POST',
            url: yith_delivery_parmas.ajax_url,
            data: data,
            dataType: 'json',
            success: function (response) {
                list_row.unblock();
            }
        });


    });
    $(document).on( 'click', '.ywcdd_delete_category',function(e){
        e.preventDefault();
        var btn = $(this),
            list_row = btn.parent().parent().parent().parent(),
            parent = list_row.parent(),
            category_id = list_row.data('item_key'),
            data = {
                ywcdd_category_id : category_id,
                action:yith_delivery_parmas.actions.delete_category_day,
            };
        parent.block(block_params);
        $.ajax({

            type: 'POST',
            url: yith_delivery_parmas.ajax_url,
            data: data,
            dataType: 'json',
            success: function (response) {

                list_row.remove();
                parent.unblock();
            }
        });
    });
    $(document).on( 'click', '.ywcdd_delete_product',function(e){
        e.preventDefault();
        var btn = $(this),
            list_row = btn.parent().parent().parent().parent(),
            parent = list_row.parent(),
            product_id = list_row.data('item_key'),
            data = {
                ywcdd_product_id : product_id,
                action:yith_delivery_parmas.actions.delete_product_day,
            };
        parent.block(block_params);
        $.ajax({

            type: 'POST',
            url: yith_delivery_parmas.ajax_url,
            data: data,
            dataType: 'json',
            success: function (response) {

                list_row.remove();
                parent.unblock();
            }
        });
    });
    $(document).on( 'click','.ywcdd_delete_range', function(e){

        e.preventDefault();
        var btn = $(this),
            row = btn.parent();

        row.remove();
    });
    $(document).on('change','#ywcdd_processing_type input[type="radio"]',function(e){
        var option = $(this).val(),
            data = {
                action: yith_delivery_parmas.actions.update_processing_type_option,
                ywcdd_processing_type: option
            };

        $('#ywcdd_processing_type').block(block_params);
        $.ajax({

            type: 'POST',
            url: yith_delivery_parmas.ajax_url,
            data: data,
            dataType: 'json',
            success: function (response) {
                $('#ywcdd_processing_type').unblock();
                $(document).trigger( 'ywcdd_processing_type_updated');
            }
        });
    });
    $(document).on('change','input[id^="ywcdd_enable_rule_product"]',function (e) {
            var option = $(this).val(),
                row = $(this).parents('.ywcdd_list_row'),
                product_id =row.data('item_key'),
                data = {
                    action:yith_delivery_parmas.actions.enable_disable_product_day,
                    ywcdd_product_id: product_id,
                    ywcdd_product_enable : option
                };


            row.block( block_params);
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
    $(document).on('change','input[id^="ywcdd_enable_rule_category"]',function (e) {
        var option = $(this).val(),
            row = $(this).parents('.ywcdd_list_row'),
            category_id =row.data('item_key'),
            data = {
                action:yith_delivery_parmas.actions.enable_disable_category_day,
                ywcdd_category_id: category_id,
                ywcdd_category_enable : option
            };


        row.block( block_params);
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
    var add_new_category_rule = function(){

        var category_selected = $('#ywcdd_product_cat_search').val(),
            quantity_row = $('#ywcdd_form_add_category').find('.ywcdd_quantity_row'),
            quantity_field = quantity_row.find( 'input').serialize(),
            data = {
                ywcdd_category_id: category_selected,
                action:yith_delivery_parmas.actions.update_category_day,
                ywcdd_args: quantity_field,
                ywcdd_action: 'add'
            };

        $(document).find('#ywcdd_custom_shipping_category_wrapper').block( block_params );
        $.ajax({

            type: 'POST',
            url: yith_delivery_parmas.ajax_url,
            data: data,
            dataType: 'json',
            success: function (response) {

                if( typeof  response.template !== 'undefined' ){

                    var list_category = $(document).find('#ywcdd_custom_shipping_category_wrapper'),
                        template = $( response.template ).find('form');
                    list_category.html( template);

                    $(document).find('.yith-add-new-category-day').click();
                    initialize_onoff();

                }
                $(document).find('#ywcdd_custom_shipping_category_wrapper').unblock();
            }
        });
    },
        add_new_product_rule = function(){
            var product_selected = $('#ywcdd_product_search').val(),
                quantity_row = $('#ywcdd_form_add_product').find('.ywcdd_quantity_row'),
                quantity_field = quantity_row.find( 'input').serialize(),
                data = {
                    ywcdd_product_id: product_selected,
                    action:yith_delivery_parmas.actions.update_product_day,
                    ywcdd_args: quantity_field,
                    ywcdd_action: 'add'
                };

            $(document).find('#ywcdd_custom_shipping_product_wrapper').block( block_params);
            $.ajax({

                type: 'POST',
                url: yith_delivery_parmas.ajax_url,
                data: data,
                dataType: 'json',
                success: function (response) {

                    if( typeof  response.template !== 'undefined' ){

                        var list_product = $(document).find('#ywcdd_custom_shipping_product_wrapper'),
                            template = $( response.template ).find('form');
                        list_product.html( template);

                        $(document).find('.yith-add-new-product-day').click();
                        initialize_onoff();

                    }
                    $(document).find('#ywcdd_custom_shipping_product_wrapper').unblock();
                }
            });
        };

    $(document).on( 'ywcdd_processing_type_updated', function(e){

        var data = {
            action:yith_delivery_parmas.actions.update_processing_method_table,
            _ajax_processing_method_nonce : $('#_ajax_processing_method_nonce').val()
        };

        $.ajax({

            type: 'POST',
            url: yith_delivery_parmas.ajax_url,
            data: data,
            dataType: 'json',
            success: function (response) {


                if (response.rows.length) {
                    $('#the-list').html(response.rows);
                }
                if (response.column_headers.length) {
                    $('thead tr, tfoot tr').html(response.column_headers);
                }
                if (response.pagination.bottom.length) {
                    $('.tablenav.top .tablenav-pages').html(response.pagination.top);
                }
                if (response.pagination.top.length) {
                    $('.tablenav.bottom .tablenav-pages').html(response.pagination.bottom);
                }

                if( response.views.length ){
                    var new_li = $(response.views).find('li');

                    $('.subsubsub').html( new_li );
                }
            }
        });
    });
});