/**
 * 
 * Overflow Menu Plugin
 *
 * @version 1.0.0
 * 
 */
(function($) {    
    $.fn.swMenu = function() {
        
        return this.each(function() {

            var $this = $(this),
                limit,
                position,
                area,
                list;
            setup($this);

            function setup(obj) {
                obj.wrap("<div class='sw-container'></div>").css("visibility", "hidden");
                if (!obj.find("li.sw-dropdown").length) {
                    obj.append('<li style="display:none" class="dropdown sw-dropdown"></li>')
                        .find("li.sw-dropdown")                        
                        .append('<a href="#" class="item-link"><span class="have-title"><span class="menu-title">'+ menu_text.more_text +'</span></span></a>')
                        .append('<ul class="dropdown-menu dropdown-menu-center sw-menu"></ul>');
                }
                obj.find(" > li").each(function(index) {
                    $(this).attr("data-index", index);
                });
                limit = obj.find("> li").length;
                index = limit - 1;
                create(obj);
            }

            function reset(obj) {
                position = limit;
                $.when(obj.find(".sw-menu > li").appendTo(obj)).done(function() {
                    $.when(sort(obj)).done(function() {
                        create(obj);
                    });
                });
            }

            function sort(obj) {
                obj.each(function() {
                    $(this).html(
                        $(this).find(" > li").sort(function(a, b) {
                            return $(b).data("index") < $(a).data("index") ? 1 : -1;
                        })
                    );
                });
            }

            function create(obj) {
                area = obj.outerWidth(true);
                list = obj.find("li.sw-dropdown").outerWidth(true);
				obj.find(".sw-dropdown > a").removeAttr( 'href' );
                obj.find('> li:not(".sw-dropdown")').each(function(index) {
                    list += $(this).outerWidth(true);
                    if (list > area) {
                        position = index;
                        move(obj, position, index);
                        obj.find(".sw-dropdown").show();
                        return false;
                    } 
                    else {
                        obj.find(".sw-dropdown").hide();
                    }
                });
                obj.css("visibility", "visible");
            }

            function move(obj, position, index) {
                for (x = position; x <= limit; x++) {
                    obj.find(' > li:not(".sw-dropdown")[data-index="' + x + '"]').appendTo(obj.find(".sw-menu"));
                }
            }

            $(window).resize(function() {
                reset($this);
            });
        });
    };
	if( menu_text.more_menu ){
		$( '.primary-menu ul.nav' ).swMenu();
	}
	$( '.footer-mstyle2 ul.menu-footer' ).swMenu();
})(jQuery);
