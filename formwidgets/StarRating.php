<?php namespace Waka\Wformwidgets\FormWidgets;

use Backend\Classes\FormWidgetBase;
use Backend\Classes\FormField;

/**
 * StarRating Form Widget
 */
class StarRating extends FormWidgetBase
{
    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'waka_wformwidgets_star_rating';

    /**
     * @inheritDoc
     */
    public function init()
    {
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('starrating');
    }

    /**
     * Prepares the form widget view data
     */
    public function prepareVars()
    {
        $this->vars['name'] = $this->formField->getName();
        $this->vars['value'] = $this->getLoadValue();
        $this->vars['field'] = $this->formField;
        $this->vars['model'] = $this->model;
    }

    /**
     * @inheritDoc
     */
    public function loadAssets()
    {
        $this->addCss('css/starrating.css', 'Waka.Wformwidgets');
        $this->addJs('js/starrating.js', 'Waka.Wformwidgets');
    }

    /**
     * @inheritDoc
     */
    public function getSaveValue($value)
    {
        if(!$value) {
            return FormField::NO_SAVE_DATA;
        } else {
            return $value;
        }
        
    }
}
