<?php

namespace NextDeveloper\IPAAS\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\IPAAS\Database\Models\WorkflowExecutions;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\IPAAS\Http\Transformers\AbstractTransformers\AbstractWorkflowExecutionsTransformer;

/**
 * Class WorkflowExecutionsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\IPAAS\Http\Transformers
 */
class WorkflowExecutionsTransformer extends AbstractWorkflowExecutionsTransformer
{

    /**
     * @param WorkflowExecutions $model
     *
     * @return array
     */
    public function transform(WorkflowExecutions $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('WorkflowExecutions', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('WorkflowExecutions', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}
