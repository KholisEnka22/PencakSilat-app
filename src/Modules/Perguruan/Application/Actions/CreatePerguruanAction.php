<?php

declare(strict_types=1);

namespace Src\Modules\Perguruan\Application\Actions;

use Src\Modules\Perguruan\Application\DTOs\PerguruanData;
use Src\Modules\Perguruan\Domain\Models\Perguruan;

class CreatePerguruanAction
{
    public function execute(PerguruanData $data): Perguruan
    {
        return Perguruan::create($data->toArray());
    }
}