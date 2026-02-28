<?php

namespace NextDeveloper\IPAAS\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\IPAAS\Database\Models\ExecutionDailyStats;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\IPAAS\Http\Transformers\AbstractTransformers\AbstractExecutionDailyStatsTransformer;

/**
 * Class ExecutionDailyStatsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\IPAAS\Http\Transformers
 */
class ExecutionDailyStatsTransformer extends AbstractExecutionDailyStatsTransformer
{

    /**
     * @param ExecutionDailyStats $model
     *
     * @return array
     */
    public function transform(ExecutionDailyStats $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('ExecutionDailyStats', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('ExecutionDailyStats', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}
