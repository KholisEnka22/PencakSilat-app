<?php

declare(strict_types=1);

namespace Modules\Perguruan;

use Illuminate\Support\ServiceProvider;

class PerguruanServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadRoutesFrom(__DIR__.'/routes_api.php');
    }
}
