<?php namespace Waka\Wformwidgets\FormWidgets;

use Backend\Classes\FormWidgetBase;

/**
 * treejs Form Widget
 */
class Treejs extends FormWidgetBase
{
    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'waka_wformwidget_treejs';

    //
    public $treeOptions = [];
    public $textvar = 'name';

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->fillFromConfig([
            'treeOptions',
        ]);
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('treejs');
    }

    /**
     * Prepares the form widget view data
     */
    public function prepareVars()
    {
        $this->vars['name'] = $this->formField->getName();
        $treeOptions = $this->model->{$this->treeOptions}();
        //trace_log($treeOptions);
        $treeCorrected = $this->remapDatas($treeOptions);
        $this->vars['treeOptions'] = $treeCorrected;
        //$this->vars['value'] = $this->getLoadValue();
        $this->vars['model'] = $this->model;
    }

    /**
     * @inheritDoc
     */
    public function loadAssets()
    {
        $this->addCss('css/style.min.css', 'waka.wformwidget');
        $this->addJs('js/jstree.js', 'waka.wformwidget');
        $this->addJs('js/treejs.js', 'waka.wformwidget');
    }

    public function remapDatas($datas) {
        $values = $this->getLoadValue();
        if(!is_array($values)) {
            $values = explode(',',$values);
        }
        
        
        foreach($datas as $key => $row) {
            $row['text'] = $row['name'];
            if(in_array($row['name'], $values)) {
                $row['state'] = [
                    'selected' => true,
                    'open' => false,
                    
                    
                ];
            }
            if($row['children'] ?? false) {
                $row['children'] = $this->remapDatas($row['children']);
            }
            $datas[$key] = $row;
        }
        return $datas;
    }
}
