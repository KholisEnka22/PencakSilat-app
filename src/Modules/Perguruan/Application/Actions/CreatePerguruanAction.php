<?php

declare(strict_types=1);

namespace Modules\Perguruan\Application\Actions;

use Modules\Perguruan\Application\DTOs\PerguruanData;
use Modules\Perguruan\Domain\Models\Perguruan;

class CreatePerguruanAction
{
    public function execute(PerguruanData $data): Perguruan
    {
        return Perguruan::create($data->toArray());
    }
}