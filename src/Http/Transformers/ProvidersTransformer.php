<?php

namespace NextDeveloper\IPAAS\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\IPAAS\Database\Models\Providers;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\IPAAS\Http\Transformers\AbstractTransformers\AbstractProvidersTransformer;

/**
 * Class ProvidersTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\IPAAS\Http\Transformers
 */
class ProvidersTransformer extends AbstractProvidersTransformer
{

    /**
     * @param Providers $model
     *
     * @return array
     */
    public function transform(Providers $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('Providers', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('Providers', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}
