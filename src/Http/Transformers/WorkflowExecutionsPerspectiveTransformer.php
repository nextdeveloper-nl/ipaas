<?php

namespace NextDeveloper\IPAAS\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\IPAAS\Database\Models\WorkflowExecutionsPerspective;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\IPAAS\Http\Transformers\AbstractTransformers\AbstractWorkflowExecutionsPerspectiveTransformer;

/**
 * Class WorkflowExecutionsPerspectiveTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\IPAAS\Http\Transformers
 */
class WorkflowExecutionsPerspectiveTransformer extends AbstractWorkflowExecutionsPerspectiveTransformer
{

    /**
     * @param WorkflowExecutionsPerspective $model
     *
     * @return array
     */
    public function transform(WorkflowExecutionsPerspective $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('WorkflowExecutionsPerspective', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('WorkflowExecutionsPerspective', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}
