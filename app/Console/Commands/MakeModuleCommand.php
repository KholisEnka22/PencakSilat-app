<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeModuleCommand extends Command
{
    protected $signature = 'make:module {name : Nama module (contoh: Anggota)}';

    protected $description = 'Generate struktur DDD module baru di src/Modules';

    private string $moduleName;
    private string $modulePath;
    private string $namespace;

    public function handle(): int
    {
        $this->moduleName = Str::studly($this->argument('name'));
        $this->modulePath = base_path("src/Modules/{$this->moduleName}");
        $this->namespace  = "Src\\Modules\\{$this->moduleName}";

        if (is_dir($this->modulePath)) {
            $this->newLine();
            $this->components->error("Module [{$this->moduleName}] sudah ada.");
            $this->line("  <fg=yellow>Path:</> src/Modules/{$this->moduleName}");
            $this->newLine();

            return self::FAILURE;
        }

        $this->showHeader();

        $this->components->info("Menyiapkan struktur module [{$this->moduleName}]...");
        $this->newLine();

        $this->withProgressBar(
            [
                'Membuat struktur folder DDD',
                'Membuat file Action, DTO, Model, Controller, Request, Resource, Provider, dan Routes',
                'Menyiapkan instruksi registrasi ServiceProvider',
            ],
            function (string $step): void {
                match ($step) {
                    'Membuat struktur folder DDD' => $this->createDirectories(),
                    'Membuat file Action, DTO, Model, Controller, Request, Resource, Provider, dan Routes' => $this->createFiles(),
                    'Menyiapkan instruksi registrasi ServiceProvider' => $this->reminderRegisterProvider(),
                    default => null,
                };
            }
        );

        $this->newLine(2);
        $this->showSuccessMessage();

        return self::SUCCESS;
    }

    private function showHeader(): void
    {
        $this->newLine();

        $this->line('<fg=cyan>╔══════════════════════════════════════════════════════╗</>');
        $this->line('<fg=cyan>║</> <fg=green;options=bold>🥋 DDD MODULE GENERATOR</>                            <fg=cyan>║</>');
        $this->line('<fg=cyan>╚══════════════════════════════════════════════════════╝</>');

        $this->newLine();

        $this->line("  <fg=gray>Module</>    : <fg=white;options=bold>{$this->moduleName}</>");
        $this->line("  <fg=gray>Namespace</> : <fg=white>{$this->namespace}</>");
        $this->line("  <fg=gray>Path</>      : <fg=white>src/Modules/{$this->moduleName}</>");

        $this->newLine();
    }

    private function showSuccessMessage(): void
    {
        $this->line('<fg=green>╔══════════════════════════════════════════════════════╗</>');
        $this->line('<fg=green>║</> <fg=white;options=bold>✅ MODULE BERHASIL DIBUAT DENGAN RAPI</>              <fg=green>║</>');
        $this->line('<fg=green>╚══════════════════════════════════════════════════════╝</>');

        $this->newLine();

        $this->line("  <fg=gray>Module</>       : <fg=green;options=bold>{$this->moduleName}</>");
        $this->line("  <fg=gray>Lokasi</>       : <fg=cyan>src/Modules/{$this->moduleName}</>");
        $this->line("  <fg=gray>Architecture</> : <fg=yellow>DDD / Modular Structure</>");

        $this->newLine();

        $this->components->info('Langkah berikutnya:');
        $this->line("  <fg=yellow>1.</> Daftarkan ServiceProvider module ini.");
        $this->line("  <fg=yellow>2.</> Buat migration table untuk module <fg=cyan>{$this->snake()}</>.");
        $this->line("  <fg=yellow>3.</> Sesuaikan fillable, validation rules, DTO, dan Inertia page.");
        $this->line("  <fg=yellow>4.</> Jalankan route:list untuk memastikan route module terbaca.");

        $this->newLine();

        $this->line('<fg=green;options=bold>🚀 Selesai! Module siap dikembangkan. Gas lanjut bangun fiturnya.</>');
        $this->newLine();
    }

    private function createDirectories(): void
    {
        $this->newLine();
        $this->line('<fg=yellow>📁 Membuat struktur folder...</>');

        $dirs = [
            'Application/Actions',
            'Application/DTOs',
            'Domain/Models',
            'Presentation/Controllers',
            'Presentation/Requests',
            'Presentation/Resources',
        ];

        foreach ($dirs as $dir) {
            $path = "{$this->modulePath}/{$dir}";

            if (! is_dir($path)) {
                mkdir($path, 0755, true);

                $this->line("  <fg=green>CREATE</> <fg=gray>directory</> src/Modules/{$this->moduleName}/{$dir}");
            }
        }
    }

    private function createFiles(): void
    {
        $this->newLine();
        $this->line('<fg=yellow>🧩 Membuat file module...</>');

        $files = [
            'Application/Actions/Create{Name}Action.php' => $this->stubAction('Create'),
            'Application/Actions/Update{Name}Action.php' => $this->stubAction('Update'),
            'Application/Actions/Delete{Name}Action.php' => $this->stubAction('Delete'),
            'Application/Actions/List{Name}Action.php'   => $this->stubAction('List'),

            'Application/DTOs/{Name}Data.php' => $this->stubDto(),

            'Domain/Models/{Name}.php' => $this->stubModel(),

            'Presentation/Controllers/{Name}Controller.php' => $this->stubControllerWeb(),

            'Presentation/Requests/Create{Name}Request.php' => $this->stubRequest('Create'),
            'Presentation/Requests/Update{Name}Request.php' => $this->stubRequest('Update'),

            'Presentation/Resources/{Name}Resource.php' => $this->stubResource(),

            '{Name}ServiceProvider.php' => $this->stubServiceProvider(),
            'routes.php'                => $this->stubRoutesWeb(),
        ];

        foreach ($files as $relativePath => $content) {
            $resolvedPath = str_replace('{Name}', $this->moduleName, $relativePath);
            $fullPath     = "{$this->modulePath}/{$resolvedPath}";

            file_put_contents($fullPath, $content);

            $this->line("  <fg=blue>FILE</>   <fg=gray>created</> src/Modules/{$this->moduleName}/{$resolvedPath}");
        }
    }

    private function stubAction(string $prefix): string
    {
        $name = $this->moduleName;
        $ns   = $this->namespace;

        $dtoImport   = "use {$ns}\\Application\\DTOs\\{$name}Data;";
        $modelImport = "use {$ns}\\Domain\\Models\\{$name};";

        if ($prefix === 'List') {
            return <<<PHP
            <?php

            declare(strict_types=1);

            namespace {$ns}\Application\Actions;

            {$modelImport}
            use Illuminate\Pagination\LengthAwarePaginator;

            class List{$name}Action
            {
                public function execute(array \$filters = []): LengthAwarePaginator
                {
                    \$search  = \$filters['search'] ?? null;
                    \$sortBy  = \$filters['sort_by'] ?? 'created_at';
                    \$sortDir = \$filters['sort_dir'] ?? 'desc';
                    \$perPage = (int) (\$filters['per_page'] ?? 15);

                    if (! in_array(strtolower((string) \$sortDir), ['asc', 'desc'], true)) {
                        \$sortDir = 'desc';
                    }

                    return {$name}::query()
                        ->when(
                            \$search,
                            fn (\$query) => \$query->where('name', 'like', "%{\$search}%")
                        )
                        ->orderBy(\$sortBy, \$sortDir)
                        ->paginate(\$perPage);
                }
            }
            PHP;
        }

        if ($prefix === 'Create') {
            return <<<PHP
            <?php

            declare(strict_types=1);

            namespace {$ns}\Application\Actions;

            {$dtoImport}
            {$modelImport}

            class Create{$name}Action
            {
                public function execute({$name}Data \$data): {$name}
                {
                    return {$name}::create(\$data->toArray());
                }
            }
            PHP;
        }

        if ($prefix === 'Update') {
            return <<<PHP
            <?php

            declare(strict_types=1);

            namespace {$ns}\Application\Actions;

            {$dtoImport}
            {$modelImport}

            class Update{$name}Action
            {
                public function execute({$name} \$model, {$name}Data \$data): {$name}
                {
                    \$model->update(\$data->toArray());

                    return \$model->refresh();
                }
            }
            PHP;
        }

        return <<<PHP
        <?php

        declare(strict_types=1);

        namespace {$ns}\Application\Actions;

        {$modelImport}

        class Delete{$name}Action
        {
            public function execute({$name} \$model): bool
            {
                return \$model->delete();
            }
        }
        PHP;
    }

    private function stubDto(): string
    {
        $name = $this->moduleName;
        $ns   = $this->namespace;

        return <<<PHP
        <?php

        declare(strict_types=1);

        namespace {$ns}\\Application\\DTOs;

        use Spatie\\LaravelData\\Data;

        class {$name}Data extends Data
        {
            public function __construct(
                public readonly string \$name,
                // TODO: tambahkan field sesuai kebutuhan
            ) {}
        }
        PHP;
    }

    private function stubModel(): string
    {
        $name  = $this->moduleName;
        $ns    = $this->namespace;
        $table = Str::snake(Str::plural($name));

        return <<<PHP
        <?php

        declare(strict_types=1);

        namespace {$ns}\Domain\Models;

        use Illuminate\Database\Eloquent\Model;

        class {$name} extends Model
        {
            protected \$table = '{$table}';

            protected \$fillable = [
                'name',
                // TODO: tambahkan columns sesuai migration
            ];

            protected function casts(): array
            {
                return [
                    'created_at' => 'datetime',
                    'updated_at' => 'datetime',
                    // TODO: tambahkan casts
                ];
            }
        }
        PHP;
    }

    private function stubControllerWeb(): string
    {
        $name  = $this->moduleName;
        $ns    = $this->namespace;
        $camel = $this->camel();

        return <<<PHP
        <?php

        declare(strict_types=1);

        namespace {$ns}\Presentation\Controllers;

        use App\Http\Controllers\Controller;
        use {$ns}\Application\Actions\Create{$name}Action;
        use {$ns}\Application\Actions\Delete{$name}Action;
        use {$ns}\Application\Actions\List{$name}Action;
        use {$ns}\Application\Actions\Update{$name}Action;
        use {$ns}\\Application\\DTOs\\{$name}Data;
        use {$ns}\\Domain\\Models\\{$name};
        use {$ns}\Presentation\Requests\Create{$name}Request;
        use {$ns}\Presentation\Requests\Update{$name}Request;
        use Illuminate\Http\RedirectResponse;
        use Inertia\Inertia;
        use Inertia\Response;

        class {$name}Controller extends Controller
        {
            public function index(List{$name}Action \$action): Response
            {
                return Inertia::render('{$name}/Index', [
                    '{$camel}s' => \$action->execute(request()->only([
                        'search',
                        'sort_by',
                        'sort_dir',
                        'per_page',
                    ])),
                ]);
            }

            public function create(): Response
            {
                return Inertia::render('{$name}/Create');
            }

            public function store(
                Create{$name}Request \$request,
                Create{$name}Action \$action,
            ): RedirectResponse {
                \$action->execute({$name}Data::from(\$request->validated()));

                return redirect()
                    ->route('{$this->snake()}.index')
                    ->with('success', '{$name} berhasil ditambahkan.');
            }

            public function show({$name} \${$camel}): Response
            {
                return Inertia::render('{$name}/Show', [
                    '{$camel}' => \${$camel},
                ]);
            }

            public function edit({$name} \${$camel}): Response
            {
                return Inertia::render('{$name}/Edit', [
                    '{$camel}' => \${$camel},
                ]);
            }

            public function update(
                Update{$name}Request \$request,
                Update{$name}Action \$action,
                {$name} \${$camel},
            ): RedirectResponse {
                \$action->execute(\${$camel}, {$name}Data::from(\$request->validated()));

                return redirect()
                    ->route('{$this->snake()}.index')
                    ->with('success', '{$name} berhasil diperbarui.');
            }

            public function destroy(
                Delete{$name}Action \$action,
                {$name} \${$camel},
            ): RedirectResponse {
                \$action->execute(\${$camel});

                return redirect()
                    ->route('{$this->snake()}.index')
                    ->with('success', '{$name} berhasil dihapus.');
            }
        }
        PHP;
    }

    private function stubRequest(string $prefix): string
    {
        $name = $this->moduleName;
        $ns   = $this->namespace;

        return <<<PHP
        <?php

        declare(strict_types=1);

        namespace {$ns}\\Presentation\\Requests;

        use Illuminate\\Foundation\\Http\\FormRequest;

        class {$prefix}{$name}Request extends FormRequest
        {
            public function authorize(): bool
            {
                return true;
            }

            public function rules(): array
            {
                return [
                    'name' => ['required', 'string', 'max:255'],
                    // TODO: tambahkan rules
                ];
            }
        }
        PHP;
    }

    private function stubResource(): string
    {
        $name = $this->moduleName;
        $ns   = $this->namespace;

        return <<<PHP
        <?php

        declare(strict_types=1);

        namespace {$ns}\\Presentation\\Resources;

        use Illuminate\\Http\\Request;
        use Illuminate\\Http\\Resources\\Json\\JsonResource;

        class {$name}Resource extends JsonResource
        {
            public function toArray(Request \$request): array
            {
                return [
                    'id'         => \$this->id,
                    'name'       => \$this->name,
                    'created_at' => \$this->created_at,
                    'updated_at' => \$this->updated_at,
                    // TODO: tambahkan field
                ];
            }
        }
        PHP;
    }

    private function stubServiceProvider(): string
    {
        $name = $this->moduleName;
        $ns   = $this->namespace;

        return <<<PHP
        <?php

        declare(strict_types=1);

        namespace {$ns};

        use Illuminate\\Support\\ServiceProvider;

        class {$name}ServiceProvider extends ServiceProvider
        {
            public function register(): void
            {
                //
            }

            public function boot(): void
            {
                \$this->loadRoutesFrom(__DIR__.'/routes.php');
            }
        }
        PHP;
    }

    private function stubRoutesWeb(): string
    {
        $name       = $this->moduleName;
        $ns         = $this->namespace;
        $snake      = $this->snake();
        $camel      = $this->camel();
        $controller = "{$ns}\\Presentation\\Controllers\\{$name}Controller";

        return <<<PHP
        <?php

        declare(strict_types=1);

        use Illuminate\\Support\\Facades\\Route;
        use {$controller};

        Route::middleware(['web', 'auth', 'perguruan'])
            ->prefix('{$snake}')
            ->name('{$snake}.')
            ->group(function () {
                Route::get('/',             [{$name}Controller::class, 'index'])   ->name('index');
                Route::get('/create',       [{$name}Controller::class, 'create'])  ->name('create');
                Route::post('/',            [{$name}Controller::class, 'store'])   ->name('store');
                Route::get('/{{$camel}}',      [{$name}Controller::class, 'show'])    ->name('show');
                Route::get('/{{$camel}}/edit', [{$name}Controller::class, 'edit'])    ->name('edit');
                Route::put('/{{$camel}}',      [{$name}Controller::class, 'update'])  ->name('update');
                Route::delete('/{{$camel}}',   [{$name}Controller::class, 'destroy']) ->name('destroy');
            });
        PHP;
    }

    private function snake(): string
    {
        return Str::snake($this->moduleName);
    }

    private function camel(): string
    {
        return Str::camel($this->moduleName);
    }

    private function reminderRegisterProvider(): void
    {
        $provider = "\\Src\\Modules\\{$this->moduleName}\\{$this->moduleName}ServiceProvider::class";

        $this->newLine();
        $this->components->warn('Jangan lupa daftarkan ServiceProvider di:');
        $this->line("  <fg=yellow>app/Providers/ModulesServiceProvider.php</>");
        $this->newLine();
        $this->line("  <fg=cyan>  {$provider},</>");
        $this->newLine();
    }
}
