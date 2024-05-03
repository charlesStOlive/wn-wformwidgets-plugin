(function ($) {
    // Create a new class that inherits from the Popup class
    var PopupInline = function (element, options) {
        $.fn.popup.Constructor.call(this, element, options);
    };

    PopupInline.prototype = Object.create($.fn.popup.Constructor.prototype);
    PopupInline.prototype.constructor = PopupInline;

    // Override the init method


    PopupInline.prototype.init = function () {
        $.fn.popup.Constructor.prototype.init.call(this);
        const leftColumn = document.querySelector('.layout-cell.w-xxx.form-sidebar.control-scrollpanel');
        let startWidth = leftColumn.offsetWidth - 10;
        // $('.popup-backdrop').remove();
        $('.popup-backdrop').css({
            'top': '0',
            'right': '0',
            'left': 'auto',
            'width': startWidth + 'px',
        })
        let popupContainer = $(this.$container[0])
        popupContainer.css({
            'top': '0',
            'right': '0',
            'left': 'auto',
            'width': startWidth + 'px',
            'overflow-y': 'auto'
        });
        popupContainer.children('.modal-dialog').first().css({
            'width': 'auto',
            // 'margin': '5px'
        });

        popupContainer.find('.modal-content').first().css({
            'width': 'auto'
        });

        document.addEventListener('manualResize', function (event) {
            if(!$(this.$container)) {
                return;
            }
            $(this.$container[0]).css({
                'width': event.detail
            });
            $('.popup-backdrop').css({
                'width': event.detail,
            })
        }.bind(this));
        // $('.control-popup modal').css({
        //     'left': '0',
        //     'right': 'auto',
        //     'width': '600px'
        // });
    };



    // Register the new class as a jQuery plugin
    $.fn.popupInline = function (option) {
        var args = Array.prototype.slice.call(arguments, 1),
            returnValue = undefined;

        this.each(function () {
            var $this = $(this),
                obj = $this.data('oc.popupInline'),
                options = $.extend({}, PopupInline.DEFAULTS, $this.data(), typeof option == 'object' && option);

            if (!obj) {
                $this.data('oc.popupInline', (obj = new PopupInline(this, options)));
            }

            if (typeof option == 'string') {
                returnValue = obj[option].apply(obj, args);
            }

            if (typeof returnValue != 'undefined') {
                return false;
            }
        });

        return returnValue ? returnValue : this;
    };

    // Default options
    $.fn.popupInline.defaults = {
        // Add your default options here
    };
})(jQuery);