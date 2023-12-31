<?php namespace Waka\Wformwidgets\FormWidgets;

use Lang;
use ApplicationException;
use Backend\Classes\FormWidgetBase;
use Backend\Classes\FormField;

/**
 * Record Finder
 * Renders a record finder field.
 *
 *    user:
 *        label: User
 *        type: recordsfinder
 *        list: ~/plugins/winter/user/models/user/columns.yaml
 *        recordsPerPage: 10
 *        title: Find Record
 *        prompt: Click the Find button to find a user
 *        keyFrom: id
 *        nameFrom: name
 *        descriptionFrom: email
 *        conditions: email = "bob@example.com"
 *        scope: whereActive
 *        searchMode: all
 *        searchScope: searchUsers
 *        useRelation: false
 *        modelClass: Winter\User\Models\User
 *
 * @package winter\wn-backend-module
 * @author Alexey Bobkov, Samuel Georges
 */
class RecordsFinder extends FormWidgetBase
{
    use \Backend\Traits\FormModelWidget;

    const MODE_ARRAY = 'array';
    const MODE_STRING = 'string';
    const MODE_RELATION = 'relation';

    //
    // Configurable properties
    //

    /**
     * @var string Field name to use for key.
     */
    public $keyFrom = 'id';

    /**
     * @var string Relation column to display for the name
     */
    public $nameFrom = 'name';

    /**
     * @var string Relation column to display for the description
     */
    public $descriptionFrom;

    /**
     * @var string Text to display for the title of the popup list form
     */
    public $title = 'waka.wformwidgets::lang.recordsfinder.find_record';

    /**
     * @var string Prompt to display if no record is selected.
     */
    public $prompt = 'Click the %s button to find a record';

    /**
     * @var int Maximum rows to display for each page.
     */
    public $recordsPerPage = 10;

    /**
     * @var string Use a custom scope method for the list query.
     */
    public $scope;

    /**
     * @var string Filters the relation using a raw where query statement.
     */
    public $conditions;

    /**
     * @var string If searching the records, specifies a policy to use.
     * - all: result must contain all words
     * - any: result can contain any word
     * - exact: result must contain the exact phrase
     */
    public $searchMode;

    /**
     * @var string Use a custom scope method for performing searches.
     */
    public $searchScope;

    /**
     * @var boolean Flag for using the name of the field as a relation name to interact with directly on the parent model. Default: true. Disable to return just the selected model's ID
     */
    public $useRelation = true;

    /**
     * @var string Class of the model to use for listing records when useRelation = false
     */
    public $modelClass;

    //
    // Object properties
    //

    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'recordsfinder';

    /**
     * @var Model Relationship model
     */
    public $relationModel;

    /**
     * @var \Backend\Classes\WidgetBase Reference to the widget used for viewing (list or form).
     */
    protected $listWidget;

    /**
     * @var \Backend\Classes\WidgetBase Reference to the widget used for searching.
     */
    protected $searchWidget;

    /**
     * @var string Mode for the return value. Values: string, array, relation.
     */
    public $mode = 'string';

    /**
     * @var mixed Predefined options settings. Set to true to get from model.
     */
    public $options;

    private $loadedValue;


    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->fillFromConfig([
            'title',
            'prompt',
            'keyFrom',
            'mode',
            'nameFrom',
            'descriptionFrom',
            'scope',
            'conditions',
            'searchMode',
            'searchScope',
            'recordsPerPage',
            'useRelation',
            'modelClass',
            
        ]);

        if (!$this->useRelation && !class_exists($this->modelClass)) {
            throw new ApplicationException(Lang::get('waka.wformwidgets::lang.recordsfinder.invalid_model_class', ['modelClass' => $this->modelClass]));
        }

        $modelKey = $this->getRecordModel()->getKeyName();
        if ($this->keyFrom === 'id' && $modelKey !== 'id') {
            $this->keyFrom = $modelKey;
        }

        if (post('recordsfinder_flag')) {
            $this->listWidget = $this->makeListWidget();
            $this->listWidget->bindToController();

            $this->searchWidget = $this->makeSearchWidget();
            $this->searchWidget->bindToController();

            $this->listWidget->setSearchTerm($this->searchWidget->getActiveTerm());

            /*
             * Link the Search Widget to the List Widget
             */
            $this->searchWidget->bindEvent('search.submit', function () {
                $this->listWidget->setSearchTerm($this->searchWidget->getActiveTerm());
                return $this->listWidget->onRefresh();
            });
        }
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('container');
    }

    public function onRefresh()
    {
        //trace_log('onRefresh');
        //trace_log($this->valueFrom);
        $relationModel = $this->getRelationModel();

        $values = json_decode(post($this->getFieldName(), true));

        if ($this->useRelation) {
            $existingValues = array_keys($this->getLoadValue());
            $childModelValues = $relationModel->whereIn('id', $values);
            $childModelValues = $childModelValues->whereNotIn('id', $existingValues)->get();
            list($model, $attribute) = $this->resolveModelAttribute($this->valueFrom);
            foreach($childModelValues as $value) {
                $model->{$attribute}()->add($value, $this->sessionKey);
            }
        } else {
            $this->formField->value = post($this->getFieldName());
        }

        $this->prepareVars();
        return ['#'.$this->getId('container') => $this->makePartial('recordsfinder')];
    }

    public function onValidateSelection() {
        $checkedIds = post('checked');
        if(!$checkedIds) return;

        $relationModel = $this->getRelationModel();

        $values = $checkedIds;

        if ($this->useRelation) {
            $existingValues = array_keys($this->getLoadValue());
            $childModelValues = $relationModel->whereIn('id', $values);
            $childModelValues = $childModelValues->whereNotIn('id', $existingValues)->get();
            list($model, $attribute) = $this->resolveModelAttribute($this->valueFrom);
            foreach($childModelValues as $value) {
                $model->{$attribute}()->add($value, $this->sessionKey);
            }
        } else {
            $this->formField->value = post($this->getFieldName());
        }

        $this->prepareVars();
        return ['#'.$this->getId('container') => $this->makePartial('recordsfinder')];
        
    }

    

    public function onClearRecord()
    {
        if ($this->useRelation) {
            list($model, $attribute) = $this->resolveModelAttribute($this->valueFrom);
            $relationModel = $this->getRelationModel();
            $values = json_decode(post($this->getFieldName(), true));
            $childModelValues = $relationModel->whereIn('id', $values)->get();
            foreach($childModelValues as $value) {
                $model->{$attribute}()->remove($value, $this->sessionKey);
            }
        } else {
            $this->formField->value = null;
        }

        $this->prepareVars();
        return ['#'.$this->getId('container') => $this->makePartial('recordsfinder')];
    }

    

    public function onClearOneRecord()
    {
        $idToDelete = post('clearRecordId');
        if(!$idToDelete) {
            return;
        }
        if ($this->useRelation) {
            $modelToDelete = $this->getRelationModel()->where('id', $idToDelete)->first();
            list($model, $attribute) = $this->resolveModelAttribute($this->valueFrom);
            $model->{$attribute}()->remove($modelToDelete, $this->sessionKey);
        } else {
            $this->formField->value = null;
        }

        $this->prepareVars();
        return ['#'.$this->getId('container') => $this->makePartial('recordsfinder')];
    }

    /**
     * Prepares the list data
     */
    public function prepareVars()
    {
        if ($this->formField->disabled) {
            $this->previewMode = true;
        }
        $this->vars['field'] = $this->formField;
        $this->vars['selectedValues'] = $this->getLoadValue();
        $this->vars['listWidget'] = $this->listWidget;
        $this->vars['searchWidget'] = $this->searchWidget;
        $this->vars['title'] = $this->title;
        $this->vars['prompt'] = str_replace('%s', '<i class="icon-th-list"></i>', e(trans($this->prompt)));
    }

    /**
     * @inheritDoc
     */
    protected function loadAssets()
    {
        $this->addJs('js/recordsfinder.js', 'core');
        $this->addCss('css/recordsfinder.css', 'core');
    }

    /**
     * @inheritDoc
     */
    public function getSaveValue($value)
    {
        if ($this->mode === static::MODE_RELATION) {
            return FormField::NO_SAVE_DATA;
        }

        if (is_array($value) && $this->mode === static::MODE_STRING) {
            return implode($this->getSeparatorCharacter(), $value);
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getLoadValue()
    {
        
        if ($this->mode === static::MODE_RELATION) {
            return $this->loadedValue = $this->getRelationObject()->withDeferred($this->sessionKey)->lists('name', 'id') ?? [];
        } else {
            $value = parent::getLoadValue();
            return $this->loadedValue = $value;
        }
    }

    /**
     * Returns an array suitable for saving against a relation (array of keys).
     * This method also creates non-existent tags.
     * @return array
     */
    protected function hydrateRelationSaveValue($names)
    {
        if (!$names) {
            return $names;
        }

        $relationModel = $this->getRelationModel();
        $existingTags = $relationModel
            ->whereIn($this->nameFrom, $names)
            ->lists($this->nameFrom, $relationModel->getKeyName())
        ;

        return array_keys($existingTags);
    }

    public function getKeyValue()
    {
        if (!$this->relationModel) {
            return null;
        }
        return $this->useRelation ?
            $this->relationModel->{$this->keyFrom} :
            $this->formField->value;
    }
    public function getNameValue()
    {
        if (!$this->relationModel || !$this->nameFrom) {
            return null;
        }

        return $this->relationModel->{$this->nameFrom};
    }

    public function getDescriptionValue()
    {
        if (!$this->relationModel || !$this->descriptionFrom) {
            return null;
        }

        return $this->relationModel->{$this->descriptionFrom};
    }

    public function onFindRecord()
    {
        //trace_log('on find record');
        $this->prepareVars();

        // Attach the parent element ID to the popup
        $this->vars['parentElementId'] = $this->getId('popupTrigger');

        /*
         * Purge the search term stored in session
         */
        if ($this->searchWidget) {
            $this->listWidget->setSearchTerm(null);
            $this->searchWidget->setActiveTerm(null);
        }

        return $this->makePartial('recordsfinder_form');
    }

    /**
     * Gets the base model instance used by this field
     *
     * @return \Winter\Storm\Database\Model
     */
    protected function getRecordModel()
    {
        $model = null;
        if ($this->useRelation) {
            $model = $this->getRelationModel();
        } else {
            $model = new $this->modelClass;
        }
        return $model;
    }

    protected function makeListWidget()
    {
        $config = $this->makeConfig($this->getConfig('list'));
        //trace_log($config);

        $config->model = $this->getRecordModel();
        $config->alias = $this->alias . 'List';
        $config->showSetup = false;
        $config->showCheckboxes = true;
        $config->recordsPerPage = $this->recordsPerPage;
        $config->recordOnClick = sprintf("$('#%s').recordsFinder('updateRecord', this, ':" . $this->keyFrom . "')", $this->getId());
        $widget = $this->makeWidget('Backend\Widgets\Lists', $config);

        $widget->setSearchOptions([
            'mode' => $this->searchMode,
            'scope' => $this->searchScope,
        ]);

        $existingValues = $this->getLoadValue();
        $existingIds = array_keys($existingValues);
        //trace_log($this->scope);

        $widget->bindEvent('list.extendQueryBefore', function ($query) use ($existingIds) {
            $query->whereNotIn('id',$existingIds);
        });

        if ($sqlConditions = $this->conditions) {
            $widget->bindEvent('list.extendQueryBefore', function ($query) use ($sqlConditions) {
                $query->whereRaw($sqlConditions);
            });
        }
        
        elseif ($scopeMethod = $this->scope) {
            $widget->bindEvent('list.extendQueryBefore', function ($query) use ($scopeMethod) {
                $query->$scopeMethod($this->model);
            });
        }
        else {
            if ($this->useRelation) {
                $widget->bindEvent('list.extendQueryBefore', function ($query) {
                    $this->getRelationObject()->addDefinedConstraintsToQuery($query);
                });
            }
        }

        return $widget;
    }

    protected function makeSearchWidget()
    {
        $config = $this->makeConfig();
        $config->alias = $this->alias . 'Search';
        $config->growable = false;
        $config->prompt = 'waka.wformwidgets::lang.list.search_prompt';
        $widget = $this->makeWidget('Backend\Widgets\Search', $config);
        $widget->cssClasses[] = 'recordsfinder-search';
        return $widget;
    }
}
