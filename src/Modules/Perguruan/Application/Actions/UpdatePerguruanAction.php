<?php

declare(strict_types=1);

namespace Modules\Perguruan\Application\Actions;

use Modules\Perguruan\Application\DTOs\PerguruanData;
use Modules\Perguruan\Domain\Models\Perguruan;

class UpdatePerguruanAction
{
    public function execute(Perguruan $model, PerguruanData $data): Perguruan
    {
        $model->update($data->toArray());

        return $model->refresh();
    }
}