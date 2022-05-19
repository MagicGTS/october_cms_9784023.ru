<?php namespace Tailor\Traits;

use October\Rain\Database\Models\DeferredBinding;
use Tailor\Models\RepeaterItem;

/**
 * DeferredContentModel modifies deferred binding to support content UUIDs
 *
 * @package october\database
 * @author Alexey Bobkov, Samuel Georges
 */
trait DeferredContentModel
{
    /**
     * bootDeferredContentModel trait for a model.
     */
    public function initializeDeferredContentModel()
    {
        $this->bindEvent('deferredBinding.newBindInstance', function ($binding) {
            if ($this instanceof RepeaterItem) {
                $extraData = ['_contentSpawnPath' => $this->content_spawn_path];
            }
            else {
                $extraData = ['_contentUuid' => $this->blueprint_uuid];
            }

            $binding->pivot_data = $extraData + $binding->pivot_data;
        });
    }

    /**
     * registerDeferredContentModel
     */
    public static function registerDeferredContentModel()
    {
        DeferredBinding::extend(function($model) {
            $model->bindEvent('deferredBinding.newMasterInstance', function($masterObject) use ($model) {
                if ($masterObject instanceof self) {
                    $pivotData = $model->pivot_data;

                    if ($masterObject instanceof RepeaterItem) {
                        $masterObject->extendWithBlueprintSpawn($pivotData['_contentSpawnPath']);
                    }
                    else {
                        $masterObject->extendWithBlueprint($pivotData['_contentUuid']);
                    }
                }
            });
        });
    }
}
