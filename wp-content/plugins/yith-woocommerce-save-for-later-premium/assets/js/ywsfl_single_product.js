jQuery(document).ready(function ($) {


    if ($('form.variations_form.cart').length) {


        $('.variations_form').on('show_variation', function (e, variation, purchasable) {

            var variation_id = variation.variation_id,
                product_id = $('.ywslf_product_id').val(),
                data = {
                    'product_id': product_id,
                    'variation_id': variation_id,
                    'action': 'check_if_variation_is_in_list'
                },
                block_params = {
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    },
                    ignoreIfBlocked: true
                },
                container = $(this).parent();


            $('.ywslf_variation_id').val(variation_id);
            $('.ywsfl_single_message').html('');
            container.block(block_params);
            $.ajax({
                type: 'POST',
                url: ywsfl_single_product_args.ajax_url,
                data: data,
                dataType: 'json',
                success: function (response) {

                    if (response.in_save_list) {
                        $('.ywsfl_single_remove').removeClass('ywsfl_hide');
                        $('.ywsfl_single_add').addClass('ywsfl_hide');


                    } else {
                        $('.ywsfl_single_add').removeClass('ywsfl_hide');
                        $('.ywsfl_single_remove').addClass('ywsfl_hide');
                    }
                    container.unblock();
                }
            });

        }).on('reset_data', function (e) {

            $('.ywsfl_single_remove').addClass('ywsfl_hide');
            $('.ywsfl_single_add').addClass('ywsfl_hide');
        });

    }

    $(document).on('click', '.ywsfl_single_add', function (e) {

        e.preventDefault();

        var t = $(this),
            product_id = $('.ywslf_product_id').val(),
            variation_id = $('.ywslf_variation_id').val(),
            data = {
                'product_id': product_id,
                'variation_id': variation_id,
                'action': ywsfl_single_product_args.actions.add_single_product_save_list
            };

        $.ajax({
            type: 'POST',
            url: ywsfl_single_product_args.ajax_url,
            data: data,
            dataType: 'json',
            success: function (response) {

                var response_result = response.result,
                    response_message = response.message,
                    response_template = response.template;

                if (response_result == "true") {

                    var url = "<a href='" + ywsfl_single_product_args.view_list.url + "'>" + ywsfl_single_product_args.view_list.label + "</a>";
                    $('.ywsfl_single_add').addClass('ywsfl_hide');

                    $('body').trigger('added_to_save_for_later_list', [product_id, variation_id]);
                    $('.ywsfl_single_message').html(response_message + " " + url).show();
                }

                $('body').trigger('refresh_save_for_later_list', [response_message, response_template]);

            }

        });

    })
        .on('click', '.ywsfl_single_remove', function (e) {
            e.preventDefault();
            var t = $(this),
                product_id = $('.ywslf_product_id').val(),
                variation_id = $('.ywslf_variation_id').val(),
                data = {
                    'remove_from_savelist': product_id,
                    'variation_id': variation_id,
                    'action': ywsfl_single_product_args.actions.remove_from_savelist
                };

            $.ajax({
                type: 'POST',
                url: ywsfl_single_product_args.ajax_url,
                data: data,
                dataType: 'json',
                success: function (response) {

                    var response_result = response.result,
                        response_message = response.message,
                        response_template = response.template;


                    if (response_result == "true") {
                        $('.ywsfl_single_remove').addClass('ywsfl_hide');
                        $('body').trigger('removed_to_save_for_later_list', [product_id, variation_id]);
                        $('.ywsfl_single_message').html(response_message).show();

                    }


                    $('body').trigger('refresh_save_for_later_list', [response_message, response_template]);
                }

            });

        });


    $('body').on('added_to_save_for_later_list', function (e, product_id, variation_id) {

        e.preventDefault();
        var data = {
            'product_id': product_id,
            'variation_id': variation_id,
            'action': ywsfl_single_product_args.actions.remove_after_add_list
        };

        $.ajax({
            type: 'POST',
            url: ywsfl_single_product_args.ajax_url,
            data: data,
            dataType: 'json',
            success: function (response) {

                var response_result = response.result,
                    cart_item_key = response.cart_item_key;


                if (response_result) {


                    refresh_mini_cart(cart_item_key);

                }
            }

        });


    }).on('refresh_save_for_later_list', function (e, message, template) {

        refresh_save_list(message, template);
    });


    function refresh_mini_cart($cart_item_key) {

        var mini_cart = $(document).find('.cart_list'),
            mini_cart_item = mini_cart.find('.mini_cart_item a:first-child');


        mini_cart_item.each(function () {
            var t = $(this);
            href = t.attr('href');


            if (href.search($cart_item_key) != -1) {
                window.location.href = href;

            }
        });


    }

    function refresh_save_list(message, template) {

        var save_container_list = $(document).find('#ywsfl_general_content');

        if (save_container_list.length) {

            save_container_list.replaceWith(template);
        }
    }

    $(document).on('added_to_cart', 'body', function (ev, fragments, cart_hash, button) {
        var content = button.closest('#ywsfl_general_content'),
            row = button.closest('div.ywsfl-row');


        if (content.length != 0) {
            $('.ywsfl_single_add').removeClass('ywsfl_hide');
            $('.ywsfl_single_remove').addClass('ywsfl_hide');
            $(document).find('.ywsfl_single_remove').trigger('click');

        }

    });

});
