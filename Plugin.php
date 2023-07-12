<?php namespace Waka\Wformwidgets;

use Backend;
use System\Classes\PluginBase;
use System\Classes\CombineAssets;

/**
 * wformwidget Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'wformwidget',
            'description' => 'AJout de formWidget perso...',
            'author'      => 'waka',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {
        CombineAssets::registerCallback(function ($combiner) {
            $combiner->registerBundle('~/plugins/waka/wformwidgets/formwidgets/wakaupload/assets/less/wakaupload.less');
        });

        \Event::listen('backend.page.beforeDisplay', function ($controller, $action, $params) {
            $controller->addJs('/plugins/waka/wformwidgets/assets/js/froala.js');
            $controller->addJs('/plugins/waka/wformwidgets/assets/js/clipboard.min.js');
            /**NODS-C*/$controller->addCss('/plugins/wcli/wconfig/assets/css/waka.css');
        });
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
    }

    public function registerFormWidgets(): array
    {
        return [
            'Waka\Wformwidgets\FormWidgets\Treejs' => 'treejs',
            'Waka\Wformwidgets\FormWidgets\RecordsFinder' => 'wrecordsfinder',
            'Waka\Wformwidgets\FormWidgets\StarRating' => 'wstarrating',
            'Waka\Wformwidgets\FormWidgets\WakaUpload' => 'wakaupload',
            'Waka\WformWidgets\FormWidgets\ColorPickerAnalyser' => 'colorpickeranalyser',
            'Waka\WformWidgets\FormWidgets\CommentField' => 'commentfield',
            'Waka\WformWidgets\FormWidgets\WakaRichEditor' => 'wakaeditor',
        ];
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return []; // Remove this line to activate
    }
}
