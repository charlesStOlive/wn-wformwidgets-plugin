/*
 * Treejs field widget plugin.
 *
 * Data attributes:
 * - data-control="treejs" - enables the plugin on an element
 *
 * JavaScript API:
 * $('div#someElement').treejs({...})
 */
+function ($) {
    "use strict";
    var Base = $.wn.foundation.base,
        BaseProto = Base.prototype

    var Treejs = function (element, options) {
        this.$el = $(element)
        this.options = options || {}
        //this.$input = this.$el.find('[data-input]').first()
        $.wn.foundation.controlUtils.markDisposable(element)
        Base.call(this)
        this.init()
    }

    Treejs.DEFAULTS = {
        // readOnly: false,
        // disabled: false,
        eventHandler: null,
        // hideOnTabChange: false,
    }

    Treejs.prototype = Object.create(BaseProto)
    Treejs.prototype.constructor = Treejs

    Treejs.prototype.init = function () {
        console.log(this.$el)
        let input1 = this.$el.find('.treeval').first()
        let treeDisplay = this.$el.find('.treeDisplay').first()
        let mydata = this.options.values
        let startValue = mydata.map(item => {
            return item.text
        })
        //Si on enregistre sans toucher le tree il faut au préalable charger les réponses.
        input1.attr('value', startValue.join(','));
        //Lancement de jstree
        treeDisplay.jstree({
            'plugins': ['search', 'checkbox', 'wholerow'],
            'core': {
                'data': mydata,
                'animation': false,
                'expand_selected_onload': false,
                'themes': {
                    'icons': false,
                }
            },
            'search': {
                'show_only_matches': true,
                'show_only_matches_children': true
            }
        })

        // $('#search').on("keyup change", function () {
        //     $('#jstree').jstree(true).search($(this).val())
        // })

        // $('#clear').click(function (e) {
        //     $('#search').val('').change().focus()
        // })

        treeDisplay.on('changed.jstree', function (e, data) {
            var objects = data.instance.get_selected(true)
            let onlyId = objects.map(item => {
                //console.log()
                return item.text
            })
            input1.attr('value', onlyId.join(','));
        })


        // this.$input.on('keydown', this.proxy(this.onInput))
        // this.$toggle.on('click', this.proxy(this.onToggle))
    }

    Treejs.prototype.dispose = function () {
        // this.$input.off('keydown', this.proxy(this.onInput))
        // this.$toggle.off('click', this.proxy(this.onToggle))
        // this.$input = this.$toggle = this.$icon = this.$loader = null
        this.$el = null
        BaseProto.dispose.call(this)
    }

    var old = $.fn.treejs

    $.fn.treejs = function (option) {
        var args = Array.prototype.slice.call(arguments, 1), result
        this.each(function () {
            var $this = $(this)
            var data = $this.data('oc.treejs')
            var options = $.extend({}, Treejs.DEFAULTS, $this.data(), typeof option == 'object' && option)
            if (!data) $this.data('oc.treejs', (data = new Treejs(this, options)))
            if (typeof option == 'string') result = data[option].apply(data, args)
            if (typeof result != 'undefined') return false
        })

        return result ? result : this
    }

    $.fn.treejs.noConflict = function () {
        $.fn.treejs = old
        return this
    }

    $(document).render(function () {
        $('[data-control="treejs"]').treejs()
    });

}(window.jQuery);
