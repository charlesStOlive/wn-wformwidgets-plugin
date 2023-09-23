/*
 * AdvancedMediaFinder plugin
 *
 * Data attributes:
 * - data-control="mediafinder" - enables the plugin on an element
 * - data-option="value" - an option with a value
 *
 * JavaScript API:
 * $('a#someElement').advancedMediaFinder({ option: 'value' })
 *
 * Dependences:
 * - Some other plugin (filename.js)
 */

+function ($) { "use strict";
    var Base = $.wn.foundation.base,
        BaseProto = Base.prototype

    var AdvancedMediaFinder = function (element, options) {
        this.$el = $(element)
        this.options = options || {}

        $.wn.foundation.controlUtils.markDisposable(element)
        Base.call(this)
        this.init()
    }

    AdvancedMediaFinder.prototype = Object.create(BaseProto)
    AdvancedMediaFinder.prototype.constructor = AdvancedMediaFinder

    AdvancedMediaFinder.prototype.init = function() {
        if (this.options.isMulti === null) {
            this.options.isMulti = this.$el.hasClass('is-multi')
        }

        if (this.options.isPreview === null) {
            this.options.isPreview = this.$el.hasClass('is-preview')
        }

        if (this.options.isImage === null) {
            this.options.isImage = this.$el.hasClass('is-image')
        }

        if (this.options.mode === null) {
            this.options.mode = this.$el.data('mediafinder-mode') || 'all';
        }

        this.$el.one('dispose-control', this.proxy(this.dispose))

        if (this.options.thumbnailWidth > 0) {
            this.$el.find('[data-find-image]').css('maxWidth', this.options.thumbnailWidth + 'px')
        }

        else if (this.options.thumbnailHeight > 0) {
            this.$el.find('[data-find-image]').css('maxHeight', this.options.thumbnailHeight + 'px')
        }

        // Stop here for preview mode
        if (this.options.isPreview) {
            return
        }

        this.$el.on('click', '.find-button', this.proxy(this.onClickFindButton))
        this.$el.on('click', '.find-remove-button', this.proxy(this.onClickRemoveButton))

        this.$findValue = $('[data-find-value]', this.$el)
    }

    AdvancedMediaFinder.prototype.dispose = function() {
        this.$el.off('click', '.find-button', this.proxy(this.onClickFindButton))
        this.$el.off('click', '.find-remove-button', this.proxy(this.onClickRemoveButton))
        this.$el.off('dispose-control', this.proxy(this.dispose))
        this.$el.removeData('oc.advancedMediaFinder')

        this.$findValue = null
        this.$el = null

        // In some cases options could contain callbacks,
        // so it's better to clean them up too.
        this.options = null

        BaseProto.dispose.call(this)
    }

    AdvancedMediaFinder.prototype.setValue = function(value) {
        // set value and trigger change event, so that wrapping implementations
        // like mlmediafinder can listen for changes.
        this.$findValue.val(value).trigger('change')
    }

    AdvancedMediaFinder.prototype.onClickRemoveButton = function() {
        this.setValue('')

        this.evalIsPopulated()
    }

    AdvancedMediaFinder.prototype.onClickFindButton = function() {
        var self = this
        
        new $.wn.mediaManager.popup({
            alias: 'ocmediamanager',
            cropAndInsertButton: ['image', 'all'].includes(self.options.mode),
            mode: self.options.mode,
            onInsert: function(items) {

                if (!items.length) {
                    alert('Please select image(s) to insert.')
                    return
                }

                if (items.length > 1) {
                    alert('Please select a single item.')
                    return
                }

                var path, publicUrl
                for (var i=0, len=items.length; i<len; i++) {
                    path = items[i].path
                    publicUrl = items[i].publicUrl
                }

                self.setValue(path)

                if (self.options.isImage) {
                    $('[data-find-image]', self.$el).attr('src', publicUrl)
                    $('[data-find-error]', self.$el).hide()
                }

                self.evalIsPopulated()

                this.hide()
            }
        })
    }

    AdvancedMediaFinder.prototype.evalIsPopulated = function() {
        var isPopulated = !!this.$findValue.val()
        this.$el.toggleClass('is-populated', isPopulated)
        $('[data-find-file-name]', this.$el).text(this.$findValue.val().substring(1))
    }

    AdvancedMediaFinder.DEFAULTS = {
        isMulti: null,
        isPreview: null,
        isImage: null,
        mode: null
    }

    // PLUGIN DEFINITION
    // ============================

    var old = $.fn.advancedMediaFinder

    $.fn.advancedMediaFinder = function(option) {
        var args = arguments;

        return this.each(function() {
            var $this   = $(this)
            var data    = $this.data('oc.advancedMediaFinder')
            var options = $.extend({}, AdvancedMediaFinder.DEFAULTS, $this.data(), typeof option == 'object' && option)
            if (!data) $this.data('oc.advancedMediaFinder', (data = new AdvancedMediaFinder(this, options)))
            if (typeof option == 'string') data[option].apply(data, args)
        })
      }

    $.fn.advancedMediaFinder.Constructor = AdvancedMediaFinder

    $.fn.advancedMediaFinder.noConflict = function() {
        $.fn.advancedMediaFinder = old
        return this
    }

    $(document).render(function() {
        $('[data-control="advancedMediaFinder"]').advancedMediaFinder()
    })

}(window.jQuery);
