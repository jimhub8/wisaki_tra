// Add functionality to Bolder Table Rates Products settings form
jQuery(document).ready(function($) {

    // Hide user permission settings from view unless selected
    if( jQuery('#user_permissions.betrs_settings_section') != undefined ) {
        var settings_table = jQuery('#user_permissions.betrs_settings_section table tbody tr');

        if( jQuery('#woocommerce_betrs_shipping_user_limitation').val() == 'everyone' )
            settings_table.eq(1).css( 'display', 'none' );

        switch( jQuery('#woocommerce_betrs_shipping_user_modification').val() ) {
            case 'specific-users':
                settings_table.eq(3).css( 'display', 'none' );
                break;
            case 'specific-roles':
                settings_table.eq(4).css( 'display', 'none' );
                break;
            case 'admins':
            default:
                settings_table.eq(3).css( 'display', 'none' );
                settings_table.eq(4).css( 'display', 'none' );
                break;
        }
    }
    jQuery('#user_permissions.betrs_settings_section').on('change', '#woocommerce_betrs_shipping_user_limitation', function(e){
        //prevent default action (hyperlink)
        e.preventDefault();

        var chosen = $( this ).val();
        var settings_table = jQuery('#user_permissions.betrs_settings_section table tbody tr');

        if( chosen == 'everyone' )
            settings_table.eq(1).css( 'display', 'none' );
        else
            settings_table.eq(1).css( 'display', '' );
    });
    jQuery('#user_permissions.betrs_settings_section').on('change', '#woocommerce_betrs_shipping_user_modification', function(e){
        //prevent default action (hyperlink)
        e.preventDefault();

        var chosen = $( this ).val();
        var settings_table = jQuery('#user_permissions.betrs_settings_section table tbody tr');

        settings_table.eq(3).css( 'display', 'none' );
        settings_table.eq(4).css( 'display', 'none' );

        if( chosen == 'specific-roles' ) {
            settings_table.eq(3).css( 'display', '' );
        } else if( chosen == 'specific-users' ) {
            settings_table.eq(4).css( 'display', '' );
        }
    });


    // Hide Per Class settings if not the selected option
    if( jQuery('#other.betrs_settings_section') != undefined ) {
        var settings_table = jQuery('#other.betrs_settings_section table tbody');
        var single_fields = settings_table.find('.per-class-only');

        if( jQuery('#woocommerce_betrs_shipping_condition').val() != 'per-class' ) {

            single_fields.each(function(i, el){
                jQuery(el).closest('tr').css( 'display', 'none' );
            });
        }
    }
    jQuery('#general.betrs_settings_section').on('change', '#woocommerce_betrs_shipping_condition', function(e){
        //prevent default action (hyperlink)
        e.preventDefault();

        var chosen = $( this ).val();

        if( chosen == 'per-class' ) {
            single_fields.each(function(i, el){
                jQuery(el).closest('tr').css( 'display', '' );
            });
        } else {
            single_fields.each(function(i, el){
                jQuery(el).closest('tr').css( 'display', 'none' );
            });
        }
    });


    // Toggle plus/minus icon
    jQuery('.betrs_settings_section').on('click', 'h3', function(e){
        //prevent default action (hyperlink)
        e.preventDefault();

        betrs_toggle_section_icon( jQuery(this) );

        var inner = $( this ).next( '.betrs_settings_inner' );
        inner.slideToggle('fast');
    });
    function betrs_toggle_section_icon( section_div ) {
        if( section_div.hasClass('open') )
            section_div.removeClass('open');
        else
            section_div.addClass('open');
    }

    // Create new category button
    jQuery('.betrs_settings_section').on('click', '.next-link a', function(e){
        //prevent default action (hyperlink)
        e.preventDefault();

        var inner = jQuery( this ).closest( '.betrs_settings_inner' );
        var inner_next = jQuery( this ).closest( '.betrs_settings_section' ).next( '.betrs_settings_section' ).find( '.betrs_settings_inner' );

        betrs_toggle_section_icon( inner.parent().find('h3') );
        inner.slideToggle('fast');
        betrs_toggle_section_icon( inner_next.parent().find('h3') );
        inner_next.slideToggle('fast');
    });

    // Swap voluemtric operand in printed equation
    jQuery('.betrs_settings_section').on('change', '.operand_selector', function(e){
        //prevent default action (hyperlink)
        e.preventDefault();

        var volumetric_operand = $( this ).val();
        var volumetric_divisor = jQuery( '.volumetric_number' ).next( '.description' ).find( 'span' );
        var volumetric_divisor_desc = volumetric_divisor.html();

        if( volumetric_operand == 'multiply' )
            var new_desc = volumetric_divisor_desc.replace( '/', 'x' );
        else
            var new_desc = volumetric_divisor_desc.replace( 'x', '/' );

        volumetric_divisor.html( new_desc );
    });

    // Handle sorting the options
    jQuery('.betrs_settings_section').on('click', '.betrs-move-option-up', function(e){
        //prevent default action (hyperlink)
        e.preventDefault();

        var current = jQuery(this).closest('.single-row');
        var previous = current.prev('.single-row');

        if( previous.length !== 0 ) {
            current.insertBefore( previous );
            betrs_update_input_names();
        }

        return false;
    });
    jQuery('.betrs_settings_section').on('click', '.betrs-move-option-down', function(e){
        //prevent default action (hyperlink)
        e.preventDefault();

        var current = jQuery(this).closest('.single-row');
        var next = current.next('.single-row');

        if( next.length !== 0 ) {
            current.insertAfter( next );
            betrs_update_input_names();
        }

        return false;
    });

    // Rename input names to match newly sorted options
    function betrs_update_input_names() {
        // gather all options on page and cycle through one by one
        var page_options = jQuery('');
        jQuery( ".betrs_settings_section .single-row" ).each(function( index ) {
            op_number = index + 1;

            // update row ID, option number, and title input
            jQuery(this).attr('data-row_id', op_number);
            jQuery(this).find('.shipping-headline').each(function() {
                jQuery(this).html( jQuery(this).html().replace( /(\d)+/, op_number) );
            });
            jQuery(this).find('.titlewrap input').each(function() {
                this.name = this.name.replace(/\[\d+\]/, '[' + op_number + ']');
                if(this.name.search('option_id') == 0){
                    this.value = op_number;
                }
            });

            // update additional options section
            additional_box = jQuery(this).find('.additional-settings');
            additional_box.html( additional_box.html().replace(/\[\d+\]/g, '[' + op_number + ']'));

            // update table inputs
            jQuery(this).find('table.table_rates tbody').children("tr").each(function (idx) {
                var $inp = jQuery(this).find('td:not(:first-child, :last-child)').find('input,textarea,select');

                $inp.each(function () {
                    this.name = this.name.replace(/\[\d+\]/, '[' + op_number + ']');
                })
            });
        });
    }

    // Handle Import Table link click
    jQuery('.betrs_settings_section').on('click', 'a.betrs_table_import', function(e) {
        e.preventDefault();

        var cost_table = jQuery(this).closest('.single-row').find('.wp-list-table.table_rates');
        var option_ID = jQuery(this).closest('.single-row').attr('data-row_id');
        var row_ID = cost_table.find("tbody > tr").length;

        //alter row_ID if there is only one row and it has the 'no-rows' class
        if( row_ID == 1 && cost_table.find("tbody").children('tr:first').hasClass('no-items') ) {
            row_ID--;
        }

        var create_form = 
        '<div id="betrs-import-table-popup" class="betrs-popup">' +
            '<div class="be-popup-container add_form" id="betrs-import-table-form">' +
                '<form method="post" enctype="multipart/form-data">' +
                '<h3>' + betrs_data.text_importing_table + '</h3>' +
                '<p><span>' + betrs_data.text_importing_csv + '</span></p>' +
                '<input type="hidden" name="option_ID" id="option_ID" value="' + option_ID + '" />' +
                '<input type="hidden" name="row_ID" id="row_ID" value="' + row_ID + '" />' +
                '<p><input type="file" name="betrs_import_csv" id="betrs_import_csv" accept=".csv, text/csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" /></p>' +
                '<p><label><input type="checkbox" name="betrs_table_delete" id="betrs_table_delete" />' + betrs_data.text_importing_del + '</label></p>' +
                '<p><input type="submit" name="betrs_table_import" value="' + betrs_data.text_upload + '" class="form_submit" /> <a href="#" class="cancel">' + betrs_data.text_cancel + '</a></p>' +
                '</form>' +
            '</div>' +  
        '</div>';

        //insert lightbox HTML into page
        jQuery('body').append(create_form);
        betrswc_doBoxSize();

    });

    // Handle Import Table form submission
    jQuery(document).on('submit', '#betrs-import-table-form', function(e){
        e.preventDefault();

        var option_ID = jQuery(this).find('input#option_ID').val();
        var row_ID = jQuery(this).find('input#row_ID').val();
        var cost_table = jQuery('#BETRS-table-rates-parent').find('.single-row[data-row_id="' + option_ID + '"]').find('.wp-list-table.table_rates tbody');
        var delete_rows = jQuery(this).find('input#betrs_table_delete');
        var fileInput = jQuery(this).find('input#betrs_import_csv');
        var acceptable_types = [".csv", "text/csv", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", "application/vnd.ms-excel"];
        var valid_extensions = /(\.csv)$/i;

        if ( fileInput.val() ) {
            var file = fileInput[0].files[0];
            var form_div = jQuery(this);

            // if a valid CSV file is entered
            if( acceptable_types.indexOf( file.type ) > -1 || valid_extensions.test( file.name ) ) {
                form_div.block({ message: null, overlayCSS: { background: '#fff', opacity: 0.6 } });
                var reader  = new FileReader();
                reader.readAsText(file);
                reader.onload = function(event) {
                    // Remove existing rows if option checked
                    if( delete_rows.is(':checked') ) {
                        cost_table.find('tr').remove();
                        row_ID = 0;
                    }

                    var csv = event.target.result;
                    var data = {'action': 'betrs_import_table', csvFile: csv, optionID: option_ID, rowID: row_ID };
                    $.post( ajaxurl, data, function( response ) {
                        if( response.search('<tr') != -1 ) {

                            // append results
                            cost_table.append( response );

                            // reinitialize enhanced select boxes for new fields
                            $( document.body ).trigger( 'wc-enhanced-select-init' );

                            // Remove 'No Items' row if exists
                            if( cost_table.find( 'tr.no-items' ) != undefined )
                                cost_table.find( 'tr.no-items' ).remove();
                            
                            jQuery('#betrs-import-table-popup').remove();
                        } else {
                            var form_desc = form_div.find('p span');
                            form_desc.text(betrs_data.text_error);
                            form_desc.css('color', '#f44336');
                            form_div.find('.blockUI').remove();
                        }
                    });
                }
            } else {
                jQuery(this).find('input[type="file"]').parent().css('border','1px solid #f44336');
            }
        } else {
            jQuery(this).find('input[type="file"]').parent().css('border','1px solid #f44336');
        }

        return false;
    });

    // Handle Export link click
    jQuery('.betrs_settings_section').on('click', 'a.betrs_table_export', betrs_export_table);

    function betrs_export_table( e ) {
        e.preventDefault();

        var rows_selected = [];
        var link_clicked = jQuery(this);
        var cost_table = jQuery(this).closest('.single-row').find('.wp-list-table.table_rates tbody tr th input:checked');

        cost_table.each(function(i, el){
            var table_row = jQuery(el).closest('tr').find("input, select, textarea");
            rows_selected.push( table_row.serialize() )
        });

        if( rows_selected.length == 0 ) {
            alert( betrs_data.text_no_selection );
            return;
        }

        var create_form = 
        '<div id="betrs-export-table-popup" class="betrs-popup">' +
            '<div class="be-popup-container add_form">' +
                '<h2>' + betrs_data.text_exporting + '</h2>' +
                '<p><img src="' + betrs_data.ajax_loader_url + '" alt="loading..." />' +
            '</div>' +  
        '</div>';
            
        //insert lightbox HTML into page
        jQuery('body').append(create_form);
        betrswc_doBoxSize();

        var data = { action: 'betrs_export_table', rowsSelected: rows_selected };
        $.post( ajaxurl, data, function( response ) {

            if( response == 0 ) {
                jQuery('#betrs-export-table-popup').find('h2').text( betrs_data.text_error ).addClass('betrs_error');
                jQuery('#betrs-export-table-popup').find('h2').after( '<p>' + betrs_data.text_error_server + '</p>' );
                jQuery('#betrs-export-table-popup').find('img').parent().remove();
                setTimeout(function() {
                    jQuery('#betrs-export-table-popup').remove();
                }, 3000);
            } else {
                link_clicked.attr('href', 'data:text/csv;base64,' + btoa(response) );
                jQuery('.betrs_settings_section').off('click', 'a.betrs_table_export');
                link_clicked[0].click();
                jQuery('#betrs-export-table-popup').remove();
                jQuery('.betrs_settings_section').on('click', 'a.betrs_table_export', betrs_export_table);
            }
        });

        return false;
    }

    // handle resizing of popup box
    function betrswc_doBoxSize() {
        // set max height for popup box
        var window_height = jQuery( window ).height();
        var box_height = window_height - 180;
        jQuery( '.be-popup-container' ).css( 'max-height', box_height +'px' );
    } jQuery( window ).on( 'resize', function() { betrswc_doBoxSize(); });
       
    // Hide popup window on cancel
    jQuery(document).on('click', '.be-popup-container .cancel', function() {
        jQuery( '.betrs-popup' ).remove();
        return false;
    });
             
});
