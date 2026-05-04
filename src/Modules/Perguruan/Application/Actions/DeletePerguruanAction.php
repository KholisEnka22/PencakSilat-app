<?php

declare(strict_types=1);

namespace Src\Modules\Perguruan\Application\Actions;

use Src\Modules\Perguruan\Domain\Models\Perguruan;

class DeletePerguruanAction
{
    public function execute(Perguruan $model): bool
    {
        return $model->delete();
    }
}