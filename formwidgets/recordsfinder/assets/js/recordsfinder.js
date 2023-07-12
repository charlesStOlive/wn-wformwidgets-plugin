/*
 * RecordsFinder plugin
 *
 * Data attributes:
 * - data-control="recordsfinder" - enables the plugin on an element
 * - data-option="value" - an option with a value
 *
 * JavaScript API:
 * $('a#someElement').recordFinder({ option: 'value' })
 *
 * Dependences:
 * - Some other plugin (filename.js)
 */

+function ($) {
    "use strict";

    var Base = $.wn.foundation.base,
        BaseProto = Base.prototype

    // RECORDFINDER CLASS DEFINITION
    // ============================

    var RecordsFinder = function (element, options) {
        this.$el = $(element)
        this.options = options || {}
        this.loading = false; // Ajouter cet état
        $.wn.foundation.controlUtils.markDisposable(element)
        Base.call(this)
        this.init()
    }

    RecordsFinder.prototype = Object.create(BaseProto)
    RecordsFinder.prototype.constructor = RecordsFinder

    RecordsFinder.prototype.init = function () {
        // this.$el.on('dblclick', this.proxy(this.onDoubleClick))
        this.$el.one('dispose-control', this.proxy(this.dispose))
        this.$el.on('submit', 'form', this.proxy(this.handleSelection));
        const $selectedValueButtons = this.$el.find('.selected-value');

        // Activez l'indicateur de chargement lors du déclenchement de l'événement ajaxPromise
        $selectedValueButtons.on('ajaxPromise', () => {
            this.$el.loadIndicator({ opaque: true });
        });

        // Désactivez l'indicateur de chargement lors du déclenchement de l'événement ajaxFail
        $selectedValueButtons.on('ajaxFail', () => {
            this.$el.loadIndicator('hide');
        });

        const $validateButton = this.$el.find('#validateSelection')

        $validateButton.on('ajaxPromise', () => {
            this.$el.loadIndicator({ opaque: true });
        });

        // Désactivez l'indicateur de chargement lors du déclenchement de l'événement ajaxFail
        $validateButton.on('ajaxFail', () => {
            this.$el.loadIndicator('hide');
        });
    }

    RecordsFinder.prototype.dispose = function () {
        // this.$el.off('dblclick', this.proxy(this.onDoubleClick))
        this.$el.off('dispose-control', this.proxy(this.dispose))
        this.$el.off('submit', 'form', this.proxy(this.handleSelection));
        this.$el.removeData('oc.recordsfinder')

        this.$el = null

        // In some cases options could contain callbacks,
        // so it's better to clean them up too.
        this.options = null

        BaseProto.dispose.call(this)
    }

    RecordsFinder.DEFAULTS = {
        refreshHandler: null,
        dataLocker: null
    }

    RecordsFinder.prototype.addRecord = function (recordId) {
        var $locker = $(this.options.dataLocker);
        var selectedRecords = $locker.val() ? JSON.parse($locker.val()) : [];
        selectedRecords.push(recordId);
        $locker.val(JSON.stringify(selectedRecords));
    };

    // Remplacez la méthode updateRecord par cette méthode
    RecordsFinder.prototype.handleSelection = function (e) {
        console.log('handleSelection')
    };


    RecordsFinder.prototype.updateRecord = function (linkEl, recordId) {
    if (!this.options.dataLocker || this.loading) return; // Vérifiez si le chargement est en cours

    var locker = this.options.dataLocker;
    this.addRecord(recordId);
    this.loading = true; // Mettre à jour l'état de chargement

    this.$el.loadIndicator({ opaque: true })
    this.$el.request(this.options.refreshHandler, {
        success: function (data) {
            this.success(data)
            $(locker).trigger('change')
            this.loading = false; // Réinitialiser l'état de chargement
        },
        complete: () => {
            this.$el.loadIndicator('hide');
            this.loading = false; // Réinitialiser l'état de chargement en cas d'échec de la requête
        }
    })

    $(linkEl).closest('.recordsfinder-popup').popup('hide')
}


    // RECORDFINDER PLUGIN DEFINITION
    // ============================

    var old = $.fn.recordsFinder

    $.fn.recordsFinder = function (option) {
        var args = Array.prototype.slice.call(arguments, 1), result
        this.each(function () {
            var $this = $(this)
            var data = $this.data('oc.recordsfinder')
            console.log(data)
            var options = $.extend({}, RecordsFinder.DEFAULTS, $this.data(), typeof option == 'object' && option)
            if (!data) $this.data('oc.recordsfinder', (data = new RecordsFinder(this, options)))
            if (typeof option == 'string') result = data[option].apply(data, args)
            if (typeof result != 'undefined') return false
        })

        return result ? result : this
    }

    $.fn.recordsFinder.Constructor = RecordsFinder

    // RECORDFINDER NO CONFLICT
    // =================

    $.fn.recordsFinder.noConflict = function () {
        $.fn.recordsFinder = old
        return this
    }

    // RECORDFINDER DATA-API
    // ===============
    $(document).render(function () {
        $('[data-control="recordsfinder"]').recordsFinder();
    })

}(window.jQuery);
