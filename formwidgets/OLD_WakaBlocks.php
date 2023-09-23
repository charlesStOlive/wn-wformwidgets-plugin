<?php

namespace Waka\WFormWidgets\FormWidgets;

use Backend\FormWidgets\Repeater;
use Winter\Blocks\Classes\BlockManager;
use Winter\Storm\Html\Helper as HtmlHelper;
use Illuminate\Support\Str;

/**
 * "Blocks" FormWidget for defining and managing multiple blocks
 */
class WakaBlocks extends Repeater
{
    /**
     * List of blocks to ignore for this specific instance
     */
    public array $ignore = [];

    /**
     * List of blocks to explicitly allow for this specific instance
     */
    public array $allow = [];

    /**
     * Determine if their is code
     */
    public bool $has_code = false;

    /**
     * Permet de mettre a jours les données lorsqu'intervention d'un form exterieur. ici onSaveCopy
     */
    public array $updated_values = [];

    /**
     * MES METHODES
     */

    /**
     * rend une prévisualisation basé sur l'info preview
     */
    public function renderPreview($index = 0, $groupCode = null)
    {
        //trace_log($groupCode);
        // trace_log($this->groupDefinitions);
        $groupConfig = array_get($this->groupDefinitions, trim($groupCode));
        //trace_log($groupConfig);
        $values = array_get($this->getValueFromIndex($index), 'config', []);
        $data = [];
        if($groupConfig) {
            $data = array_merge($groupConfig, ['values' =>  $values]);
        }
        $partialPreview = '$/waka/wformwidgets/blocks/previews/default.htm';
        // if ($groupConfig['preview'] ?? false) {
        //     $partialPreview = $groupConfig['preview'];
        // }
        return $this->makePartial($partialPreview, compact('data'));
    }





    /**
     * ORIGINAL de Blocks
     */

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->fillFromConfig([
            'ignore',
            'allow',
            'has_code'
        ]);

        parent::init();
    }

    /**
     * This method overrides the base repeater processGroupMode to implement block functionality without pre-defining a
     * group.
     */
    protected function processGroupMode(): void
    {
        $definitions = [];
        foreach (BlockManager::instance()->getConfigs($this->config->blockContext ?? null) as $code => $config) {
            if (in_array($code, $this->ignore)) {
                continue;
            }

            //trace_log($code . '!', $this->allow);

            if (!empty($this->allow)) {
                $allowed = false;
                foreach ($this->allow as $pattern) {
                    if (Str::is($pattern, $code)) {
                        $allowed = true;
                        break;
                    }
                }
                if (!$allowed) {
                    continue;
                }
            }


            $config = $this->handleFieldContext($config, $this->config->blockContext ?? null);

            $definitions[$code] = [
                'code' => $code,
                'preview' => array_get($config, 'preview'),
                'name' => array_get($config, 'name'),
                'icon' => array_get($config, 'icon', 'icon-square-o'),
                'description' => array_get($config, 'description'),
                'fields' => array_get($config, 'fields')
            ];
        }

        // Sort the builder blocks by translated name label
        uasort($definitions, fn ($a, $b) => trans($a['name']) <=> trans($b['name']));

        $this->groupDefinitions = $definitions;
        $this->useGroups = true;
    }

    /**
     * Recursively iterates through fields and applies context filtering, may not handle all form types
     */
    protected function handleFieldContext(array $config, ?string $context): array
    {
        if (!$context) {
            return $config;
        }

        $target = null;

        if (isset($config['fields'])) {
            $target = &$config['fields'];
        }

        if (isset($config['tabs']['fields'])) {
            $target = &$config['tabs']['fields'];
        }

        if (!$target) {
            return $config;
        }

        foreach ($target as $key => $field) {
            if (isset($field['blockContext']) && !in_array($context, $field['blockContext'])) {
                unset($target[$key]);
                continue;
            }

            if (isset($field['form'])) {
                $target[$key]['form'] = $this->handleFieldContext($field['form'], $context);
            }
        }

        return $config;
    }

    /**
     * MODIF DE REPEATER
     */
    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('repeaterblock');
    }

    /**
     * @inheritDoc
     */
    protected function loadAssets()
    {
        $this->addCss('css/repeaterblock.css', 'Winter.Blocs');
        $this->addJs('js/repeaterblock.js', 'Winter.Blocs');
    }

    public function onAddItem()
    {
        $groupCode = post('_repeaterblock_group');

        $index = $this->getNextIndex();

        $this->prepareVars();
        $this->vars['widget'] = $this->makeItemFormWidget($index, $groupCode);
        $this->vars['indexValue'] = $index;

        $itemContainer = '@#' . $this->getId('items');

        return [
            $itemContainer => $this->makePartial('repeaterblock_item')
        ];
    }

    public function onRemoveItem()
    {
        // Useful for deleting relations
    }

    public function onLoadCopy()
    {
        $index = post('_repeaterblock_index');
        $previousData = post($this->formField->getName());
        $previousData = json_encode($previousData);

        $data = $this->getValueFromIndex($index);
        $this->vars['copied_data'] = json_encode($data);
        $this->vars['_repeaterblock_index'] = $index;
        $this->vars['_previous_data'] = $previousData;
        return $this->makePartial('popup_copy');
    }

    public function onSaveCopy()
    {
        $index = post('_repeaterblock_index');;
        $values = json_decode(post('values_copied'), true);
        $previousData = json_decode(post('_previous_data'), true);
        // trace_log(post('_previous_data'));
        // trace_log($previousData);
        $previousData[$index] = $values;
        $targets = $this->parseFieldStringKeys($this->formField->getName());
        //TODO c'est crad mais je n'ai pas trouvé d autre moyen. Je n'arrive pas a refresh avec les bonnes valeurs. 
        $class = get_class($this->model);
        $model = $class::find($this->model->id);
        //
        $field = $targets['field'];
        if (!$targets['keys'] ?? false) {
            $model->{$field} = $previousData;
        } else {
            $actualData = $model->{$field};
            // trace_log($actualData);
            array_set($actualData,  $targets['keys'], $previousData);
            $model->{$field} = $actualData;
        }
        $model->save();
        return \Redirect::refresh();
    }

    function parseFieldStringKeys($str)
    {
        if (preg_match('/^(\w+)\[([\w\[\]]+)\]$/i', $str, $matches)) {
            $modelName = $matches[1];
            $keys = explode('][', trim($matches[2], '[]'));
            $field = array_shift($keys);
            $dotNotation = implode('.', $keys);

            return [
                'model' => $modelName,
                'field' => $field,
                'keys'   => $dotNotation
            ];
        }

        // Retourner un tableau vide si la correspondance échoue
        return [];
    }



    public function onRefresh()
    {
        $index = post('_repeaterblock_index');
        $group = post('_repeaterblock_group');

        $widget = $this->makeItemFormWidget($index, $group);

        return $widget->onRefresh();
    }

    /**
     * Creates a form widget based on a field index and optional group code.
     * @param int $index
     * @param string $index
     * @return \Backend\Widgets\Form
     */
    protected function makeItemFormWidget($index = 0, $groupCode = null)
    {
        $configDefinition = $this->useGroups
            ? $this->getGroupFormFieldConfig($groupCode)
            : $this->form;

        if ($this->has_code) {
            $b_code = [
                'label' => 'code',
                'span' => 'full',
            ];
            // Réeorganiser le tableau pour que 'b_code' soit le premier élément
            $configDefinition['fields'] = array_merge(['b_code' => $b_code], $configDefinition['fields']);
        }

        $config = $this->makeConfig($configDefinition);
        $config->model = $this->model;
        $config->data = $this->getValueFromIndex($index);
        $config->alias = $this->alias . 'Form' . $index;
        $config->arrayName = $this->getFieldName() . '[' . $index . ']';
        $config->isNested = true;
        if (self::$onAddItemCalled || $this->minItems > 0) {
            $config->enableDefaults = true;
        }

        $widget = $this->makeWidget('Backend\Widgets\Form', $config);
        $widget->previewMode = $this->previewMode;
        $widget->bindToController();

        $this->indexMeta[$index] = [
            'groupCode' => $groupCode
        ];

        return $this->formWidgets[$index] = $widget;
    }
}
