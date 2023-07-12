<?php namespace Waka\WformWidgets\FormWidgets;

use ApplicationException;
use Backend\Classes\FormWidgetBase;
use ColorThief\ColorThief;
use Lang;
use Illuminate\Support\Facades\File;

/**
 * Color picker
 * Renders a color picker field.
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class ColorPickerAnalyser extends FormWidgetBase
{
    //
    // Configurable properties
    //

    /**
     * @var array Default available colors
     */
    public $availableColors = [
        '#1abc9c', '#16a085',
        '#2ecc71', '#27ae60',
        '#3498db', '#2980b9',
        '#9b59b6', '#8e44ad',
        '#34495e', '#2b3e50',
        '#f1c40f', '#f39c12',
        '#e67e22', '#d35400',
        '#e74c3c', '#c0392b',
        '#ecf0f1', '#bdc3c7',
        '#95a5a6', '#7f8c8d',
    ];

    /**
     * @var bool Allow empty value
     */
    public $allowEmpty = false;

    /**
     * @var bool Show opacity slider
     */
    public $showAlpha = false;

    

    //
    // Object properties
    //

    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'colorpicker';

    /**
     * @inheritDoc
     */
    protected $colorsFrom = 'logo_c';

    /**
     * @var string Method to get url or localPath if needed
     */
    public $pathMethod = '';


    /**
     * @var bool Show opacity slider
     */
    public $fromMedia = false;

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->fillFromConfig([
            'availableColors',
            'allowEmpty',
            'showAlpha',
            'colorsFrom',
            'pathMethod',
            'fromMedia'
        ]);
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('colorpickeranalyser');
    }

    /**
     * Prepares the list data
     */
    public function prepareVars()
    {
        $this->vars['name'] = $this->getFieldName();
        $this->vars['value'] = $value = $this->getLoadValue();
        $this->vars['availableColors'] = $availableColors = $this->getAvailableColors();
        $this->vars['allowEmpty'] = $this->allowEmpty;
        $this->vars['showAlpha'] = $this->showAlpha;
        $this->vars['isCustomColor'] = !in_array($value, $availableColors);
    }

    /**
     * Gets the appropriate list of colors.
     *
     * @return array
     */
    protected function getAvailableColors()
    {
        $path = null;
        // trace_log('fromMedia', $this->fromMedia);
        // trace_log('pathMethod',$this->pathMethod);
        if($this->fromMedia) {
            //Si le fichier vient des médias on recupère simplement l'url et on complète avec pathMedia
            $mediaPath = $this->model[$this->colorsFrom];
            $path = storage_path('app/media/' . $mediaPath);
        } else {
            //Sion c'est un objet. 
            $file = $this->model->{$this->colorsFrom}()->withDeferred($this->sessionKey);
            if($this->pathMethod) {
                //Il y a une methode specifique pour atteindre le media
                $path = $file->first()->{$this->pathMethod}();
            } else {
                $path = $file->first()->getLocalPath();
            }
        }
        $newColors = $this->checkImageFile($path);
        trace_log($newColors);
        if($newColors) {
            return $newColors;
        } else {
            return $this->availableColors;
        }

        
    }

    private function checkImageFile($path) {
        if(\Str::startsWith($path,'http') || File::exists($path)) {
            return ColorThief::getPalette($path,10,10,null,'hex');
        } else {
            \Log::info('image existe pas');
            return null;
        }



    }

    /**
     * @inheritDoc
     */
    protected function loadAssets()
    {
        $this->addCss('vendor/spectrum/spectrum.css', 'Waka.WformWidgets');
        $this->addJs('vendor/spectrum/spectrum.js', 'Waka.WformWidgets');
        $this->addCss('css/colorpicker.css', 'Waka.WformWidgets');
        $this->addJs('js/colorpicker.js', 'Waka.WformWidgets');
    }

    /**
     * @inheritDoc
     */
    public function getSaveValue($value)
    {
        return strlen($value) ? $value : null;
    }
}
