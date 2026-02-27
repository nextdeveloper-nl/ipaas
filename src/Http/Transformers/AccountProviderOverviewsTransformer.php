<?php

namespace NextDeveloper\IPAAS\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\IPAAS\Database\Models\AccountProviderOverviews;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\IPAAS\Http\Transformers\AbstractTransformers\AbstractAccountProviderOverviewsTransformer;

/**
 * Class AccountProviderOverviewsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\IPAAS\Http\Transformers
 */
class AccountProviderOverviewsTransformer extends AbstractAccountProviderOverviewsTransformer
{

    /**
     * @param AccountProviderOverviews $model
     *
     * @return array
     */
    public function transform(AccountProviderOverviews $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('AccountProviderOverviews', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('AccountProviderOverviews', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}
