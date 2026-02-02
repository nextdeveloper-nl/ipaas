<?php

namespace NextDeveloper\IPAAS\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\IPAAS\Database\Models\Workflows;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\IPAAS\Http\Transformers\AbstractTransformers\AbstractWorkflowsTransformer;

/**
 * Class WorkflowsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\IPAAS\Http\Transformers
 */
class WorkflowsTransformer extends AbstractWorkflowsTransformer
{

    /**
     * @param Workflows $model
     *
     * @return array
     */
    public function transform(Workflows $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('Workflows', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('Workflows', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}
