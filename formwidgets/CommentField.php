<?php namespace Waka\WformWidgets\FormWidgets;

use Backend\Classes\FormWidgetBase;

/**
 * CommentField Form Widget
 */
class CommentField extends FormWidgetBase
{
    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'waka_wfw_comment_field';

    public $type = "info";
    public $mode = "static";
    public $text = "Une explication";
    public $color ="info";
    public $icon = "icon-info";
    public $title = "Information";

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->fillFromConfig([
            'type',
            'mode',
            'text',
            'valueFrom',
            'color',
            'icon',
            'title',
        ]);
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('commentfield');
    }

    /**
     * Prepares the form widget view data
     */
    public function prepareVars()
    {
        $this->vars['name'] = $this->formField->getName();
        $this->vars['value'] = $this->getLoadValue();
        $this->vars['type'] = $this->type;
        $this->vars['color'] = $this->color;
        $this->vars['icon'] = $this->icon;
        $this->vars['title'] = $this->title;
        if ($this->mode == "static") {
            $this->vars['text'] = $this->text;
        } else {
            $this->vars['text'] = $this->model[$this->valueFrom];
        }
    }

    /**
     * @inheritDoc
     */
    public function loadAssets()
    {
        $this->addCss('css/commentfield.css', 'Waka.WformWidgets');
        $this->addJs('js/commentfield.js', 'Waka.WformWidgets');
    }

    /**
     * @inheritDoc
     */
    public function getSaveValue($value)
    {
        return \Backend\Classes\FormField::NO_SAVE_DATA;
    }
}
