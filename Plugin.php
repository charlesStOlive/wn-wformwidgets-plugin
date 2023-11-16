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
            $combiner->registerBundle('~/plugins/waka/wformwidgets/formwidgets/advancedmediafinder/assets/less/advancedmediafinder.less');
            // $combiner->registerBundle('$/waka/wformwidgets/formwidgets/wakablocks/assets/less/repeaterblock.less');
        });

        \Event::listen('backend.page.beforeDisplay', function ($controller, $action, $params) {
            $controller->addJs('/plugins/waka/wformwidgets/assets/js/froala.js');
            $controller->addJs('/plugins/waka/wformwidgets/assets/js/clipboard.min.js');
            /**NODS-C*/$controller->addCss('/plugins/wcli/wconfig/assets/css/waka.css');
        });
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
            // 'Waka\WformWidgets\FormWidgets\AdvancedMediaFinder' => 'advancedmediafinder',
        ];
    }
}
