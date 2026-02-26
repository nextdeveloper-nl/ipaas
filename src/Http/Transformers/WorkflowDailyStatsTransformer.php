<?php

namespace NextDeveloper\IPAAS\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\IPAAS\Database\Models\WorkflowDailyStats;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\IPAAS\Http\Transformers\AbstractTransformers\AbstractWorkflowDailyStatsTransformer;

/**
 * Class WorkflowDailyStatsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\IPAAS\Http\Transformers
 */
class WorkflowDailyStatsTransformer extends AbstractWorkflowDailyStatsTransformer
{

    /**
     * @param WorkflowDailyStats $model
     *
     * @return array
     */
    public function transform(WorkflowDailyStats $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('WorkflowDailyStats', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('WorkflowDailyStats', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}
