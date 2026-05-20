<?php

namespace NextDeveloper\IPAAS\Actions\Accounts;

use NextDeveloper\Commons\Actions\AbstractAction;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Commons\Exceptions\NotAllowedException;
use NextDeveloper\IPAAS\Database\Models\Accounts;

/**
 * This class handles the enabling of a service for an IPaaS account.
 */
class EnableService extends AbstractAction
{
    public const EVENTS = [
        'enable-service:NextDeveloper\IPAAS\Accounts',
    ];

    /**
     * @throws NotAllowedException
     */
    public function __construct(Accounts $accounts)
    {
        $this->model = $accounts;
        parent::__construct();
    }

    public function handle(): void
    {
        $this->setProgress(0, 'Starting to enable service');

        $this->model->updateQuietly([
            'is_service_enabled' => true,
        ]);

        CacheHelper::deleteKeys(get_class($this->model), $this->model->uuid);

        $this->setProgress(100, 'Service enabled');
    }
}
