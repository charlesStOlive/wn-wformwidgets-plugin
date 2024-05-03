/*
 * Scripts for the Relation controller behavior.
 */
+function ($) { "use strict";

    var RelationInlineBehavior = function() {

        this.toggleListCheckbox = function(el) {
            $(el).closest('.control-list').listWidget('toggleChecked', [el])
        }

        this.clickViewListRecord = function(recordId, relationId, sessionKey) {
            var newPopup = $('<a />'),
                $container = $('#'+relationId),
                requestData = paramToObj('data-request-data', $container.data('request-data'))

            newPopup.popupInline({
                handler: 'onRelationInlineClickViewList',
                extraData: $.extend({}, requestData, {
                    'manage_id': recordId,
                    '_session_key': sessionKey
                })
            })
        }

        this.clickManageListRecord = function(recordId, relationId, sessionKey) {
            var oldPopup = $('#relationManagePopup'),
                $container = $('#'+relationId),
                requestData = paramToObj('data-request-data', $container.data('request-data'))

            $.request('onRelationInlineClickManageList', {
                data: $.extend({}, requestData, {
                    'record_id': recordId,
                    '_session_key': sessionKey
                })
            })

            oldPopup.popupInline('hide')
        }

        this.clickManagePivotListRecord = function(foreignId, relationId, sessionKey) {
            var oldPopup = $('#relationManagePivotPopup'),
                newPopup = $('<a />'),
                $container = $('#'+relationId),
                requestData = paramToObj('data-request-data', $container.data('request-data'))

            if (oldPopup.length) {
                oldPopup.popupInline('hide')
            }

            newPopup.popupInline({
                handler: 'onRelationInlineClickManageListPivot',
                extraData: $.extend({}, requestData, {
                    'foreign_id': foreignId,
                    '_session_key': sessionKey
                })
            })
        }

        /*
         * This function is called every time a record is created, added, removed
         * or deleted using the relation widget. It triggers the change.oc.formwidget
         * event to notify other elements on the page about the changed form state.
         */
        this.changed = function(relationId, event) {
            $('[data-field-name="' + relationId + '"]').trigger('change.oc.formwidget', {event: event});
        }

        /*
         * This function transfers the supplied variables as hidden form inputs,
         * to any popup that is spawned within the supplied container. The spawned
         * popup must contain a form element.
         */
        this.bindToPopups = function(container, vars) {
            $(container).on('show.oc.popup', function(event, $trigger, $modal){
                var $form = $('form', $modal)
                $.each(vars, function(name, value){
                    $form.prepend($('<input />').attr({ type: 'hidden', name: name, value: value }))
                })
            })
        }

        function paramToObj(name, value) {
            if (value === undefined) value = ''
            if (typeof value == 'object') return value

            try {
                return ocJSON("{" + value + "}")
            }
            catch (e) {
                throw new Error('Error parsing the '+name+' attribute value. '+e)
            }
        }

    }

    $.wn.relationInlineBehavior = new RelationInlineBehavior;
}(window.jQuery);
