/* global Stripe, yith_stripe_connect_info, woocommerce_params */

Stripe.setPublishableKey(yith_stripe_connect_info.public_key);

(function ($) {

    $(document).ready(function () {
        // Checkout handled
        $( 'form.checkout' ).on( 'checkout_place_order_yith-stripe-connect', function (e) {
            return stripeFormHandler(e);
        });

        // Pay page handler
        $( 'form#order_review' ).on( 'submit', function (e) {
            return stripeFormHandler(e);
        });
    });

    // Form handler
    function stripeFormHandler( event ) {
        var $form = $( 'form.checkout, form#order_review, form#add_payment_method' ),
            $type = typeof  yith_stripe_connect_source_info != 'undefined' ? yith_stripe_connect_source_info.payment_type : 'token';

        if ( $form.is('.add-card') || $( 'input#payment_method_yith-stripe-connect' ).is( ':checked' ) && ( ! $( 'input[name="wc-yith-stripe-connect-payment-token"]').length || $( 'input[name="wc-yith-stripe-connect-payment-token"]:checked').val() == 'new' ) ) {

            if ( 0 === $( 'input.stripe-token' ).length ) {

                $form.block({
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });

                var name_input   = $( '.wc-credit-card-form-card-name'),
                    name         = name_input.length ? name_input.val() : $('#billing_first_name' ).val() + ' ' + $('#billing_last_name' ).val(),
                    card_input   = $( '.wc-credit-card-form-card-number' ),
                    card         = card_input.val(),
                    cvc_input    = $( '.wc-credit-card-form-card-cvc' ),
                    cvc          = cvc_input.val(),
                    expiry_input = $( '.wc-credit-card-form-card-expiry'),
                    expiry       = $.payment.cardExpiryVal( expiry_input.val() ),
                    billing_country_input = $('#billing_country'),
                    billing_country = billing_country_input.val(),
                    billing_city_input = $('#billing_city:visible'),
                    billing_city = billing_city_input.val(),
                    billing_address_1_input = $('#billing_address_1:visible'),
                    billing_address_1 = billing_address_1_input.val(),
                    billing_address_2_input = $('#billing_address_2:visible'),
                    billing_address_2 = billing_address_2_input.val(),
                    billing_state_input = $('select#billing_state, input#billing_state:visible'),
                    billing_state = billing_state_input.val(),
                    billing_postcode_input = $('#billing_postcode:visible'),
                    billing_postcode = billing_postcode_input.val();

                card = card.replace( /\s/g, '' );

                var error = false,
                    fields = [];

                // Validate the number:
                if ( ! Stripe.validateCardNumber( card ) ) {
                    error = true;
                    fields.push( 'card.number' );
                    card_input.parents( 'p.form-row' ).addClass( 'error' );
                }

                // Validate the CVC:
                if ( ! Stripe.validateCVC( cvc ) ) {
                    error = true;
                    fields.push( 'card.cvc' );
                    cvc_input.parents( 'p.form-row' ).addClass( 'error' );
                }

                // Validate the expiration:
                if ( ! Stripe.validateExpiry( expiry.month, expiry.year ) ) {
                    error = true;
                    fields.push( 'card.expire' );
                    expiry_input.parents( 'p.form-row' ).addClass( 'error' );
                }

                // validate extra fields
                if (
                    billing_country_input.closest('p.form-row.validate-required' ).length      && billing_country_input.length   && billing_country == ''
                    || billing_city_input.closest('p.form-row.validate-required' ).length      && billing_city_input.length      && billing_city == ''
                    || billing_address_1_input.closest('p.form-row.validate-required' ).length && billing_address_1_input.length && billing_address_1 == ''
                    || billing_state_input.closest('p.form-row.validate-required' ).length     && billing_state_input.length     && billing_state == ''
                    || billing_postcode_input.closest('p.form-row.validate-required' ).length  && billing_postcode_input.length  && billing_postcode == ''
                ) {
                    error = true;
                    fields.push( 'billing.fields' );
                    billing_country == ''   && billing_country_input.parents( 'p.form-row' ).addClass( 'error' );
                    billing_city == ''      && billing_city_input.parents( 'p.form-row' ).addClass( 'error' );
                    billing_address_1 == '' && billing_address_1_input.parents( 'p.form-row' ).addClass( 'error' );
                    billing_state == ''     && billing_state_input.parents( 'p.form-row' ).addClass( 'error' );
                    billing_postcode == ''  && billing_postcode_input.parents( 'p.form-row' ).addClass( 'error' );
                }

                if ( error ) {
                    stripeResponseHandler( 200, {
                        error: {
                            code: 'validation',
                            fieldErrors : fields
                        }
                    });

                    $('fieldset#wc-yith-stripe-connect-cc-form input, fieldset#wc-yith-stripe-connect-cc-form select, fieldset#yith-stripe-connect-cc-form input, fieldset#yith-stripe-connect-cc-form select').one( 'keydown', function() {
                        $(this).closest('p.form-row.error').removeClass('error');
                    });

                    $(document).trigger( 'yith-stripe-connect-card-error' );
                }

                // go to payment
                else {

                    if( 'token' == $type ){
                        // Get the Stripe token:
                        Stripe.createToken({
                            number: card,
                            cvc: cvc,
                            exp_month: expiry.month,
                            exp_year: expiry.year,
                            name: name,
                            address_line1   : billing_address_1,
                            address_line2   : billing_address_2,
                            address_city    : billing_city,
                            address_state   : billing_state,
                            address_zip     : billing_postcode,
                            address_country : billing_country
                        }, stripeResponseHandler );
                    }

                }

                // Prevent the form from submitting
                return false;
            }
        }

        return event;
    }

    // Handle Stripe response
    function stripeResponseHandler( status, response ) {
        var $form  = $( 'form.checkout, form#order_review, form#add_payment_method' ),
            ccForm = $( '#wc-yith-stripe-connect-cc-form, #yith-stripe-connect-cc-form' );
        console.log(response);
        if ( response.error ) {

            // Show the errors on the form
            $( '.woocommerce-error, .stripe-token', ccForm ).remove();
            $form.unblock();

            if ( response.error.message ) {
                ccForm.prepend( '<ul class="woocommerce-error">' + response.error.message + '</ul>' );
            }

            // Show any validation errors
            else if ( 'validation' === response.error.code ) {
                var fieldErrors = response.error.fieldErrors,
                    fieldErrorsLength = fieldErrors.length,
                    errorList = '';

                for ( var i = 0; i < fieldErrorsLength; i++ ) {
                    errorList += '<li>' + yith_stripe_connect_info[ fieldErrors[i] ] + '</li>';
                }

                ccForm.prepend( '<ul class="woocommerce-error">' + errorList + '</ul>' );
            }

        } else {

            // Insert the token into the form so it gets submitted to the server
            ccForm.append( '<input type="hidden" class="stripe-token" name="stripe_connect_token" value="' + response.id + '"/>' );
            $form.submit();
        }
    }

}(jQuery) );
