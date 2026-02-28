<?php

namespace NextDeveloper\IPAAS\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\IPAAS\Database\Models\PlatformHealthPerspective;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\IPAAS\Http\Transformers\AbstractTransformers\AbstractPlatformHealthPerspectiveTransformer;

/**
 * Class PlatformHealthPerspectiveTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\IPAAS\Http\Transformers
 */
class PlatformHealthPerspectiveTransformer extends AbstractPlatformHealthPerspectiveTransformer
{

    /**
     * @param PlatformHealthPerspective $model
     *
     * @return array
     */
    public function transform(PlatformHealthPerspective $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('PlatformHealthPerspective', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('PlatformHealthPerspective', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}
