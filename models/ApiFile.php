<?php namespace Waka\Wformwidgets\Models;

use System\Models\File as WinterFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

/**
 * File attachment model
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class ApiFile extends WinterFile// copy de \Modules\System\Files et adaptation.
{
    protected $table = 'waka_wformwidgets_api_files';
    public $apiSrc = null;

    public function beforeSave() {

        parent::beforeSave();
        $table = $this->getTable();

        // filter the attributes array
        $this->attributes = collect($this->attributes)
            ->filter(function ($value, $key) use ($table) {
                return Schema::hasColumn($table, $key);
            })
            ->toArray();
    }

    protected $jsonable = [
        'api_metas',
        'api_opts',
        'api_errors',
    ];

    

    public function sendToApi() {
        throw new \ApplicationException(sprintf('La fonction SendToApi est manquante dans la classe %s', get_class($this)));
    }

    public function checkApiSrc() {
        if(!$this->apiSrc) {
            throw new \ApplicationException(sprintf('La variable apiSrc est manquante dans la classe %s', get_class($this)));
        } else {
            $this->api_src = $this->apiSrc;
        }
    }

    function filterMeta($metas)
    {
        $filteredData = [];

        foreach($this->metaToKeep as $key) {
            Arr::set($filteredData, $key, Arr::get($metas, $key));
        }
        //On srot et syncronise le title et la description des metas. je ne sais pas si c'est une bonne idée. 
        $apiTitle = $filteredData['title'] ?? null;
        $apiDescription = $filteredData['description'] ?? null;
        $this->title = $apiTitle ? $apiTitle : $this->title;
        $this->descritpion = $apiDescription ? $apiDescription : $this->descritpion;
        return $filteredData;
    }
}