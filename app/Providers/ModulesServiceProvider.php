<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Main service provider that loads all modules.
 * This is the single entry point for all module registrations.
 */
class ModulesServiceProvider extends ServiceProvider
{
    /**
     * List of module service providers to register.
     * Add new modules here as they are created.
     *
     * @var array<class-string<ServiceProvider>>
     */
    protected array $modules = [
        \Src\Modules\Perguruan\PerguruanServiceProvider::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        foreach ($this->modules as $module) {
            if (class_exists($module)) {
                $this->app->register($module);
            }
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Modules are booted automatically by Laravel
    }
}
