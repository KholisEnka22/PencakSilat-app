<?php

declare(strict_types=1);

namespace Modules\Perguruan\Application\Actions;

use Modules\Perguruan\Domain\Models\Perguruan;

class DeletePerguruanAction
{
    public function execute(Perguruan $model): bool
    {
        return $model->delete();
    }
}