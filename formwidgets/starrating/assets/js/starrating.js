+function ($) { "use strict";
    var Base = $.wn.foundation.base,
        BaseProto = Base.prototype

    var StarRating = function (element, options) {
        this.$el = $(element)
        this.options = options || {}

        $.wn.foundation.controlUtils.markDisposable(element)
        Base.call(this)
        this.init()
    }

    StarRating.prototype = Object.create(BaseProto)
    StarRating.prototype.constructor = StarRating

    StarRating.prototype.init = function() {
        this.$el.on('mouseover', '.star-rating-icon', this.proxy(this.onMouseOver))
        this.$el.on('click touchstart', '.star-rating-icon', this.proxy(this.onClick))
        this.$el.one('dispose-control', this.proxy(this.dispose))
    }

    StarRating.prototype.dispose = function() {
        this.$el.off('mouseover', '.star-rating-icon', this.proxy(this.onMouseOver))
        this.$el.off('click', '.star-rating-icon', this.proxy(this.onClick))
        this.$el.off('dispose-control', this.proxy(this.dispose))
        this.$el.removeData('oc.starRating')

        this.$el = null
        this.options = null

        BaseProto.dispose.call(this)
    }

    StarRating.DEFAULTS = {}

    StarRating.prototype.onMouseOver = function (event) {
        const index = $(event.target).index() + 1;
        this.$el.children('.star-rating-icon').each(function (i) {
            $(this).toggleClass('active', i < index);
        });
    }

    StarRating.prototype.onClick = function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $star = $(e.target),
            index = $star.index() + 1;

        $star.siblings().addBack().each(function(i) {
            $(this).toggleClass('icon-star', i < index).toggleClass('icon-star-o', i >= index);
        });

        this.$el.find('input').val(index).trigger('change');
    }

    var old = $.fn.starRating

    $.fn.starRating = function (option) {
        var args = Array.prototype.slice.call(arguments, 1), items, result

        items = this.each(function () {
            var $this   = $(this)
            var data    = $this.data('oc.starRating')
            var options = $.extend({}, StarRating.DEFAULTS, $this.data(), typeof option == 'object' && option)
            if (!data) $this.data('oc.starRating', (data = new StarRating(this, options)))
            if (typeof option == 'string') result = data[option].apply(data, args)
            if (typeof result != 'undefined') return false
        })

        return result ? result : items
    }

    $.fn.starRating.Constructor = StarRating

    $.fn.starRating.noConflict = function () {
        $.fn.starRating = old
        return this
    }

    $(document).render(function (){
        $('[data-control="starRating"]').starRating()
    })

}(window.jQuery);
