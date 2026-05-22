<?php

namespace NextDeveloper\IPAAS\Actions\Accounts;

use NextDeveloper\Commons\Actions\AbstractAction;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Commons\Exceptions\NotAllowedException;
use NextDeveloper\IPAAS\Database\Models\Accounts;

/**
 * This class handles the disabling of a service for an IPaaS account.
 */
class DisableService extends AbstractAction
{
    public const EVENTS = [
        'disable-service:NextDeveloper\IPAAS\Accounts',
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
        $this->setProgress(0, 'Starting to disable service');

        $this->model->updateQuietly([
            'is_service_enabled' => false,
        ]);

        CacheHelper::deleteKeys(get_class($this->model), $this->model->uuid);

        $this->setProgress(100, 'Service disabled');
    }
}
