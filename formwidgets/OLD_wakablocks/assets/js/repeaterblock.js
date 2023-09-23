/*
 * Blocks FormWidget plugin
 *
 * @TODO:
 * - Rename to prevent conflicts with the Repeaterblock codebase
 * - Remove functionality not used by the Blocks FormWidget
 * - Potentially switch to Editor.js?
 *
 * Data attributes:
 * - data-control="fieldrepeaterblock" - enables the plugin on an element
 * - data-option="value" - an option with a value
 *
 * JavaScript API:
 * $('a#someElement').fieldRepeaterblock({...})
 */

+function ($) { "use strict";

    var Base = $.wn.foundation.base,
        BaseProto = Base.prototype

    // FIELD REPEATER CLASS DEFINITION
    // ============================

    var Repeaterblock = function(element, options) {
        this.options   = options
        this.$el       = $(element)
        if (this.options.sortable) {
            this.$sortable = $(options.sortableContainer, this.$el)
        }

        $.wn.foundation.controlUtils.markDisposable(element)
        Base.call(this)
        this.init()
    }

    Repeaterblock.prototype = Object.create(BaseProto)
    Repeaterblock.prototype.constructor = Repeaterblock

    Repeaterblock.DEFAULTS = {
        sortableHandle: '.repeaterblock-item-handle',
        sortableContainer: 'ul.field-repeaterblock-items',
        titleFrom: null,
        minItems: null,
        maxItems: null,
        sortable: false,
        style: 'default',
    }

    Repeaterblock.prototype.init = function() {
        if (this.options.sortable) {
            this.bindSorting()
        }

        this.$el.on('ajaxDone', '> .field-repeaterblock-items > .field-repeaterblock-item > .repeaterblock-item-remove > [data-repeaterblock-remove]', this.proxy(this.onRemoveItemSuccess))
        this.$el.on('ajaxDone', '> .field-repeaterblock-add-item > [data-repeaterblock-add]', this.proxy(this.onAddItemSuccess))
        this.$el.on('click', '> ul > li > .repeaterblock-item-collapse .repeaterblock-item-collapse-one, > ul > li > .repeaterblock-item-collapsed-title', this.proxy(this.toggleCollapse))
        this.$el.on('click', '> .field-repeaterblock-add-item > [data-repeaterblock-add-group]', this.proxy(this.clickAddGroupButton))

        this.$el.one('dispose-control', this.proxy(this.dispose))

        this.togglePrompt()
        this.applyStyle()
    }

    Repeaterblock.prototype.dispose = function() {
        if (this.options.sortable) {
            this.$sortable.sortable('destroy')
        }

        this.$el.off('ajaxDone', '> .field-repeaterblock-items > .field-repeaterblock-item > .repeaterblock-item-remove > [data-repeaterblock-remove]', this.proxy(this.onRemoveItemSuccess))
        this.$el.off('ajaxDone', '> .field-repeaterblock-add-item > [data-repeaterblock-add]', this.proxy(this.onAddItemSuccess))
        this.$el.off('click', '> ul > li > .repeaterblock-item-collapse .repeaterblock-item-collapse-one, > ul > li > .repeaterblock-item-collapse .repeaterblock-item-collapsed-title', this.proxy(this.toggleCollapse))
        this.$el.off('click', '> .field-repeaterblock-add-item > [data-repeaterblock-add-group]', this.proxy(this.clickAddGroupButton))

        this.$el.off('dispose-control', this.proxy(this.dispose))
        this.$el.removeData('oc.repeaterblock')

        this.$el = null
        this.$sortable = null
        this.options = null

        BaseProto.dispose.call(this)
    }

    // Deprecated
    Repeaterblock.prototype.unbind = function() {
        this.dispose()
    }

    Repeaterblock.prototype.bindSorting = function() {
        var sortableOptions = {
            handle: this.options.sortableHandle,
            nested: false
        }

        this.$sortable.sortable(sortableOptions)
    }

    Repeaterblock.prototype.clickAddGroupButton = function(ev) {
        var $self = this;
        var templateHtml = $('> [data-group-palette-template]', this.$el).html(),
            $target = $(ev.target),
            $form = this.$el.closest('form'),
            $loadContainer = $target.closest('.loading-indicator-container')

        $target.ocPopover({
            content: templateHtml
        })

        var $container = $target.data('oc.popover').$container

        // Initialize the scrollpad control in the popup
        $container.trigger('render')

        $container
            .on('click', 'a', function (ev) {
                setTimeout(function() { $(ev.target).trigger('close.oc.popover') }, 1)
            })
            .on('ajaxPromise', '[data-repeaterblock-add]', function(ev, context) {
                $loadContainer.loadIndicator()

                $form.one('ajaxComplete', function() {
                    $loadContainer.loadIndicator('hide')
                    $self.togglePrompt()
                })
            })

        $('[data-repeaterblock-add]', $container).data('request-form', $form)
    }

    Repeaterblock.prototype.onRemoveItemSuccess = function(ev) {
        var $target = $(ev.target)

        // Allow any widgets inside a deleted item to be disposed
        $target.closest('.field-repeaterblock-item').find('[data-disposable]').each(function () {
            var $elem = $(this),
                control = $elem.data('control'),
                widget = $elem.data('oc.' + control)

            if (widget && typeof widget['dispose'] === 'function') {
                widget.dispose()
            }
        })

        $target.closest('[data-field-name]').trigger('change.oc.formwidget')
        $target.closest('.field-repeaterblock-item').remove()
        this.togglePrompt()
    }

    Repeaterblock.prototype.onAddItemSuccess = function(ev) {
        this.togglePrompt()
        $(ev.target).closest('[data-field-name]').trigger('change.oc.formwidget')
    }

    Repeaterblock.prototype.togglePrompt = function () {
        if (this.options.minItems && this.options.minItems > 0) {
            var repeatedItems = this.$el.find('> .field-repeaterblock-items > .field-repeaterblock-item').length,
                $removeItemBtn = this.$el.find('> .field-repeaterblock-items > .field-repeaterblock-item > .repeaterblock-item-remove');

            $removeItemBtn.toggleClass('disabled', !(repeatedItems > this.options.minItems))
        }

        if (this.options.maxItems && this.options.maxItems > 0) {
            var repeatedItems = this.$el.find('> .field-repeaterblock-items > .field-repeaterblock-item').length,
                $addItemBtn = this.$el.find('> .field-repeaterblock-add-item')

            $addItemBtn.toggle(repeatedItems < this.options.maxItems)
        }
    }

    Repeaterblock.prototype.toggleCollapse = function(ev) {
        var $item = $(ev.target).closest('.field-repeaterblock-item'),
            isCollapsed = $item.hasClass('collapsed')


        ev.preventDefault()

        if (this.getStyle() === 'accordion') {
            if (isCollapsed) {
                this.expand($item)
            }
            return
        }

        if (ev.ctrlKey || ev.metaKey) {
            isCollapsed ? this.expandAll() : this.collapseAll()
        }
        else {
            isCollapsed ? this.expand($item) : this.collapse($item)
        }
    }

    Repeaterblock.prototype.collapseAll = function() {
        var self = this,
            items = $(this.$el).children('.field-repeaterblock-items').children('.field-repeaterblock-item')

        $.each(items, function(key, item){
            self.collapse($(item))
        })
    }

    Repeaterblock.prototype.expandAll = function() {
        var self = this,
            items = $(this.$el).children('.field-repeaterblock-items').children('.field-repeaterblock-item')

        $.each(items, function(key, item){
            self.expand($(item))
        })
    }

    Repeaterblock.prototype.collapse = function($item) {
        $item.addClass('collapsed')
        $('.repeaterblock-item-copy', $item).show();
        // $('.repeaterblock-item-collapsed-title', $item).text(this.getCollapseTitle($item));
    }

    Repeaterblock.prototype.expand = function($item) {
        if (this.getStyle() === 'accordion') {
            this.collapseAll()
        }
        $item.removeClass('collapsed')
        $('.repeaterblock-item-copy', $item).hide();
    }

    Repeaterblock.prototype.getCollapseTitle = function($item) {
        var $target,
            defaultText = '',
            explicitText = $item.data('collapse-title')

        if (explicitText) {
            return explicitText
        }

        if (this.options.titleFrom) {
            $target = $('[data-field-name="'+this.options.titleFrom+'"]', $item)
            if (!$target.length) {
                $target = $item
            }
        }
        else {
            $target = $item
        }

        var $textInput = $('input[type=text]:first, select:first', $target).first();
        if ($textInput.length) {
            switch($textInput.prop("tagName")) {
                case 'SELECT':
                    return $textInput.find('option:selected').text();
                default:
                    return $textInput.val();
            }
        } else {
            var $disabledTextInput = $('.text-field:first > .form-control', $target)
            if ($disabledTextInput.length) {
                return $disabledTextInput.text()
            }
        }

        return defaultText
    }

    Repeaterblock.prototype.getStyle = function() {
        var style = 'default';

        // Validate style
        if (this.options.style && ['collapsed', 'accordion'].indexOf(this.options.style) !== -1) {
            style = this.options.style
        }

        return style;
    }

    Repeaterblock.prototype.applyStyle = function() {
        var style = this.getStyle(),
            self = this,
            items = $(this.$el).children('.field-repeaterblock-items').children('.field-repeaterblock-item')

        $.each(items, function(key, item) {
            switch (style) {
                case 'collapsed':
                    self.collapse($(item))
                    break
                case 'accordion':
                    if (key !== 0) {
                        self.collapse($(item))
                    }
                    break
            }
        })
    }

    // FIELD REPEATER PLUGIN DEFINITION
    // ============================

    var old = $.fn.fieldRepeaterblock

    $.fn.fieldRepeaterblock = function (option) {
        var args = Array.prototype.slice.call(arguments, 1), result
        this.each(function () {
            var $this   = $(this)
            var data    = $this.data('oc.repeaterblock')
            var options = $.extend({}, Repeaterblock.DEFAULTS, $this.data(), typeof option == 'object' && option)
            if (!data) $this.data('oc.repeaterblock', (data = new Repeaterblock(this, options)))
            if (typeof option == 'string') result = data[option].apply(data, args)
            if (typeof result != 'undefined') return false
        })

        return result ? result : this
    }

    $.fn.fieldRepeaterblock.Constructor = Repeaterblock

    // FIELD REPEATER NO CONFLICT
    // =================

    $.fn.fieldRepeaterblock.noConflict = function () {
        $.fn.fieldRepeaterblock = old
        return this
    }

    // FIELD REPEATER DATA-API
    // ===============

    $(document).render(function() {
        $('[data-control="fieldrepeaterblock"]').fieldRepeaterblock()
    });

}(window.jQuery);
