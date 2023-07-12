<?php namespace Waka\WformWidgets\FormWidgets;

use App;
use File;
use Lang;
use Event;
use Request;
use Backend;
use BackendAuth;
use Backend\Models\EditorSetting;
use Backend\Classes\FormWidgetBase;
use Config;

/**
 * Rich Editor
 * Renders a rich content editor field.
 *
 * @package winter\wn-backend-module
 * @author Alexey Bobkov, Samuel Georges
 */
class WakaRichEditor extends FormWidgetBase
{
    use \Waka\WformWidgets\Classes\Traits\UploadableWidget;

    //
    // Configurable properties
    //

    /**
     * @var boolean Determines whether content has HEAD and HTML tags.
     */
    public $fullPage = false;

    /**
     * @var boolean Determines whether content has HEAD and HTML tags.
     */
    public $toolbarButtons;
    
    /**
     * @var string Determines une config de toolbar (minimal, decorate, fullwaka, full)
     */
    public $toolbarDefault = 'default';
    /**
     * @var boolean If true, the editor is set to read-only mode
     */
    public $readOnly = false;

    /**
     * @var string|null Path in the Media Library where uploaded files should be stored. If null it will be pulled from Request::input('path');
     */
    public $uploadPath = 'auto';

    //
    // Object properties
    //

    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'richeditor';

    /**
     * @inheritDoc
     */
    public function init()
    {
        if ($this->formField->disabled) {
            $this->readOnly = true;
        }

        $this->fillFromConfig([
            'fullPage',
            'readOnly',
            'toolbarButtons',
            'toolbarDefault',
            'uploadPath',
        ]);
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('~/modules/backend/formwidgets/richeditor/partials/_richeditor.php');
    }

    /**
     * Prepares the list data
     */
    public function prepareVars()
    {
        $this->vars['field'] = $this->formField;
        $this->vars['editorLang'] = $this->getValidEditorLang();
        $this->vars['fullPage'] = $this->fullPage;
        $this->vars['stretch'] = $this->formField->stretch;
        $this->vars['size'] = $this->formField->size;
        $this->vars['readOnly'] = $this->readOnly;
        $this->vars['name'] = $this->getFieldName();
        $this->vars['value'] = $this->getLoadValue();
        $this->vars['toolbarButtons'] = $this->evalToolbarButtons();
        $this->vars['useMediaManager'] = BackendAuth::getUser()->hasAccess('media.manage_media');
        //trace_log('waka.wformwidgets::froala.html_toolbar_buttons.'.$this->toolbarDefault);
        //trace_log(Config::get('waka.wformwidgets::froala.html_toolbar_buttons.'.$this->toolbarDefault));
        $this->vars['globalToolbarButtons'] = Config::get('waka.wformwidgets::froala.html_toolbar_buttons.'.$this->toolbarDefault);
        $this->vars['allowEmptyTags'] = Config::get('waka.wformwidgets::froala.html_allow_empty_tags');
        $this->vars['allowTags'] = Config::get('waka.wformwidgets::froala.html_allow_tags');
        $this->vars['allowAttributes'] = Config::get('waka.wformwidgets::froala.html_allow_attributes');
        $this->vars['noWrapTags'] = Config::get('waka.wformwidgets::froala.html_no_wrap_tags');
        $this->vars['removeTags'] = Config::get('waka.wformwidgets::froala.html_remove_tags');
        $this->vars['lineBreakerTags'] = Config::get('waka.wformwidgets::froala.html_line_breaker_tags');

        $this->vars['imageStyles'] = Config::get('waka.wformwidgets::froala.html_style_image');
        $this->vars['linkStyles'] = Config::get('waka.wformwidgets::froala.html_style_link');
        $this->vars['paragraphStyles'] = Config::get('waka.wformwidgets::froala.html_style_paragraph');
        $this->vars['paragraphFormats'] = Config::get('waka.wformwidgets::froala.html_paragraph_formats');
        $this->vars['tableStyles'] = Config::get('waka.wformwidgets::froala.html_style_table');
        $this->vars['tableCellStyles'] = Config::get('waka.wformwidgets::froala.html_style_table_cell');
    }

    /**
     * Determine the toolbar buttons to use based on config.
     * @return string
     */
    protected function evalToolbarButtons()
    {
        $buttons = $this->toolbarButtons;
        
        if (is_string($buttons)) {
            $buttons = array_map(function ($button) {
                return strlen($button) ? $button : '|';
            }, explode('|', $buttons));
        }

        return $buttons;
    }

    public function onLoadPageLinksForm()
    {
        //trace_log("onLoadPageLinksForm");
        $this->vars['links'] = $this->getPageLinksArray();
        return $this->makePartial('~/modules/backend/formwidgets/richeditor/partials/_page_links_form.php');
    }

    /**
     * @inheritDoc
     */
    protected function loadAssets()
    {
        $this->addCss('/modules/backend/formwidgets/richeditor/assets/css/richeditor.css', 'core');
        $this->addJs('/modules/backend/formwidgets/richeditor/assets/js/build-min.js', 'core');

        if (Config::get('develop.decompileBackendAssets', false)) {
            $scripts = Backend::decompileAsset($this->getAssetPath('/modules/backend/formwidgets/richeditor/assets/js/build-plugins.js'));
            foreach ($scripts as $script) {
                $this->addJs($script, 'core');
            }
        } else {
            $this->addJs('/modules/backend/formwidgets/richeditor/assets/js/build-plugins-min.js');
        }

        $this->addJs('/modules/backend/formwidgets/codeeditor/assets/js/build-min.js', 'core');

        if ($lang = $this->getValidEditorLang()) {
            $this->addJs('/modules/backend/formwidgets/richeditor/assets/vendor/froala/js/languages/'.$lang.'.js');
        }
    }

    /**
     * Returns a valid language code for Redactor.
     * @return string|mixed
     */
    protected function getValidEditorLang()
    {
        $locale = App::getLocale();

        // English is baked in
        if ($locale == 'en') {
            return null;
        }

        $locale = str_replace('-', '_', strtolower($locale));
        $path = base_path('modules/backend/formwidgets/richeditor/assets/vendor/froala/js/languages/'.$locale.'.js');

        return File::exists($path) ? $locale : false;
    }

    /**
     * Returns a list of registered page link types.
     * This is reserved functionality for separating the links by type.
     * @return array Returns an array of registered page link types
     */
    protected function getPageLinkTypes()
    {
        $result = [];

        /**
         * @event backend.richeditor.listTypes
         * Register additional "page link types" to the RichEditor FormWidget
         *
         * Example usage:
         *
         *     Event::listen('backend.richeditor.listTypes', function () {
         *          return [
         *              'my-identifier' => #'author.plugin :: lang.richeditor.link_types.my_identifier',
         *          ];
         *     });
         *
         */
        $apiResult = Event::fire('backend.richeditor.listTypes');
        if (is_array($apiResult)) {
            foreach ($apiResult as $typeList) {
                if (!is_array($typeList)) {
                    continue;
                }

                foreach ($typeList as $typeCode => $typeName) {
                    $result[$typeCode] = $typeName;
                }
            }
        }

        return $result;
    }

    protected function getPageLinks($type)
    {
        $result = [];

        /**
         * @event backend.richeditor.getTypeInfo
         * Register additional "page link types" to the RichEditor FormWidget
         *
         * Example usage:
         *
         *     Event::listen('backend.richeditor.getTypeInfo', function ($type) {
         *          if ($type === 'my-identifier') {
         *              return [
         *                  'https://example.com/page1' => 'Page 1',
         *                  'https://example.com/parent-page' => [
         *                      'title' => 'Parent Page',
         *                      'links' => [
         *                          'https://example.com/child-page' => 'Child Page',
         *                      ],
         *                  ],
         *              ];
         *          }
         *     });
         *
         */
        $apiResult = Event::fire('backend.richeditor.getTypeInfo', [$type]);
        if (is_array($apiResult)) {
            foreach ($apiResult as $typeInfo) {
                if (!is_array($typeInfo)) {
                    continue;
                }

                foreach ($typeInfo as $name => $value) {
                    $result[$name] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * Returns a single collection of available page links.
     * This implementation has room to place links under
     * different groups based on the link type.
     * @return array
     */
    protected function getPageLinksArray()
    {
        $links = [];
        $types = $this->getPageLinkTypes();

        $links[] = ['name' => Lang::get('backend::lang.pagelist.select_page'), 'url' => false];

        $iterator = function ($links, $level = 0) use (&$iterator) {
            $result = [];

            foreach ($links as $linkUrl => $link) {
                /*
                 * Remove scheme and host from URL
                 */
                $baseUrl = Request::getSchemeAndHttpHost();
                if (strpos($linkUrl, $baseUrl) === 0) {
                    $linkUrl = substr($linkUrl, strlen($baseUrl));
                }

                /*
                 * Root page fallback.
                 */
                if (strlen($linkUrl) === 0) {
                    $linkUrl = '/';
                }

                $linkName = str_repeat('&nbsp;', $level * 4);
                $linkName .= is_array($link) ? array_get($link, 'title', '') : $link;
                $result[] = ['name' => $linkName, 'url' => $linkUrl];

                if (is_array($link)) {
                    $result = array_merge(
                        $result,
                        $iterator(array_get($link, 'links', []), $level + 1)
                    );
                }
            }

            return $result;
        };

        foreach ($types as $typeCode => $typeName) {
            $links = array_merge($links, $iterator($this->getPageLinks($typeCode)));
        }

        return $links;
    }
}
