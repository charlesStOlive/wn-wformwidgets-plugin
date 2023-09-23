import PluginBase from '../../../../../../../modules/system/assets/js/snowboard/abstracts/PluginBase';

/**
 * Data configuration provider.
 *
 * Provides a mechanism for passing configuration data through an element's data attributes. This
 * is generally used for widgets or UI interactions to configure them.
 *
 * @copyright 2022 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class MyPlugin extends PluginBase {
    listens() {
        return {
            ready: 'ready',
            eventName: 'myHandler',
        };
    }

    ready() {

    }

    myHandler(context) {
        // This method is run when the `eventName` global event is fired.
    }
}
