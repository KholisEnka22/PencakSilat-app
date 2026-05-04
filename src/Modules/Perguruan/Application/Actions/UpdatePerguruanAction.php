<?php

declare(strict_types=1);

namespace Src\Modules\Perguruan\Application\Actions;

use Src\Modules\Perguruan\Application\DTOs\PerguruanData;
use Src\Modules\Perguruan\Domain\Models\Perguruan;

class UpdatePerguruanAction
{
    public function execute(Perguruan $model, PerguruanData $data): Perguruan
    {
        $model->update($data->toArray());

        return $model->refresh();
    }
}