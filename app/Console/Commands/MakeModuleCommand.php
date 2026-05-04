<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeModuleCommand extends Command
{
    protected $signature = 'make:module {name : Nama module, contoh: Anggota}';

    protected $description = 'Generate struktur module baru di src/Modules';

    private string $moduleName;

    private string $modulePath;

    private string $namespace;

    public function handle(): int
    {
        $this->moduleName = Str::studly((string) $this->argument('name'));
        $this->modulePath = base_path("src/Modules/{$this->moduleName}");
        $this->namespace = "Modules\\{$this->moduleName}";

        if (is_dir($this->modulePath)) {
            $this->components->error("Module [{$this->moduleName}] sudah ada.");
            $this->line("Path: src/Modules/{$this->moduleName}");

            return self::FAILURE;
        }

        $this->showHeader();
        $this->createDirectories();
        $this->createFiles();
        $this->reminderRegisterProvider();
        $this->showSuccessMessage();

        return self::SUCCESS;
    }

    private function showHeader(): void
    {
        $this->newLine();
        $this->components->info("Membuat module [{$this->moduleName}]");
        $this->line("Namespace : {$this->namespace}");
        $this->line("Path      : src/Modules/{$this->moduleName}");
        $this->newLine();
    }

    private function createDirectories(): void
    {
        $dirs = [
            'Application/Actions',
            'Application/DTOs',
            'Domain/Enums',
            'Domain/Models',
            'Presentation/Controllers/Api',
            'Presentation/Requests',
            'Presentation/Resources',
        ];

        foreach ($dirs as $dir) {
            $path = "{$this->modulePath}/{$dir}";

            if (! is_dir($path)) {
                mkdir($path, 0755, true);
            }

            $this->line("CREATE directory src/Modules/{$this->moduleName}/{$dir}");
        }
    }

    private function createFiles(): void
    {
        $files = [
            'Application/Actions/Create{Name}Action.php' => $this->stubCreateAction(),
            'Application/Actions/Update{Name}Action.php' => $this->stubUpdateAction(),
            'Application/Actions/Delete{Name}Action.php' => $this->stubDeleteAction(),
            'Application/Actions/List{Name}Action.php' => $this->stubListAction(),
            'Application/DTOs/{Name}Data.php' => $this->stubDto(),
            'Domain/Models/{Name}.php' => $this->stubModel(),
            'Presentation/Controllers/{Name}Controller.php' => $this->stubWebController(),
            'Presentation/Controllers/Api/{Name}Controller.php' => $this->stubApiController(),
            'Presentation/Requests/Create{Name}Request.php' => $this->stubRequest('Create'),
            'Presentation/Requests/Update{Name}Request.php' => $this->stubRequest('Update'),
            'Presentation/Resources/{Name}Resource.php' => $this->stubResource(),
            '{Name}ServiceProvider.php' => $this->stubServiceProvider(),
            'routes.php' => $this->stubRoutesWeb(),
            'routes_api.php' => $this->stubRoutesApi(),
        ];

        foreach ($files as $relativePath => $content) {
            $resolvedPath = str_replace('{Name}', $this->moduleName, $relativePath);
            $fullPath = "{$this->modulePath}/{$resolvedPath}";

            file_put_contents($fullPath, $content);

            $this->line("CREATE file src/Modules/{$this->moduleName}/{$resolvedPath}");
        }
    }

    private function stubCreateAction(): string
    {
        return $this->render(<<<'PHP'
        <?php

        declare(strict_types=1);

        namespace {{NAMESPACE}}\Application\Actions;

        use {{NAMESPACE}}\Application\DTOs\{{NAME}}Data;
        use {{NAMESPACE}}\Domain\Models\{{NAME}};

        class Create{{NAME}}Action
        {
            public function execute({{NAME}}Data $data): {{NAME}}
            {
                return {{NAME}}::create($data->toArray());
            }
        }
        PHP);
    }

    private function stubUpdateAction(): string
    {
        return $this->render(<<<'PHP'
        <?php

        declare(strict_types=1);

        namespace {{NAMESPACE}}\Application\Actions;

        use {{NAMESPACE}}\Application\DTOs\{{NAME}}Data;
        use {{NAMESPACE}}\Domain\Models\{{NAME}};

        class Update{{NAME}}Action
        {
            public function execute({{NAME}} $model, {{NAME}}Data $data): {{NAME}}
            {
                $model->update($data->toArray());

                return $model->refresh();
            }
        }
        PHP);
    }

    private function stubDeleteAction(): string
    {
        return $this->render(<<<'PHP'
        <?php

        declare(strict_types=1);

        namespace {{NAMESPACE}}\Application\Actions;

        use {{NAMESPACE}}\Domain\Models\{{NAME}};

        class Delete{{NAME}}Action
        {
            public function execute({{NAME}} $model): bool
            {
                return $model->delete();
            }
        }
        PHP);
    }

    private function stubListAction(): string
    {
        return $this->render(<<<'PHP'
        <?php

        declare(strict_types=1);

        namespace {{NAMESPACE}}\Application\Actions;

        use Illuminate\Pagination\LengthAwarePaginator;
        use {{NAMESPACE}}\Domain\Models\{{NAME}};

        class List{{NAME}}Action
        {
            /**
             * @param array<string, mixed> $filters
             */
            public function execute(array $filters = []): LengthAwarePaginator
            {
                $search = $filters['search'] ?? null;
                $sortBy = $this->resolveSortBy($filters['sort_by'] ?? 'created_at');
                $sortDir = $this->resolveSortDirection($filters['sort_dir'] ?? 'desc');
                $perPage = max(1, min((int) ($filters['per_page'] ?? 15), 100));

                return {{NAME}}::query()
                    ->when($search, function ($query) use ($search): void {
                        $query->where('name', 'like', "%{$search}%");
                    })
                    ->orderBy($sortBy, $sortDir)
                    ->paginate($perPage)
                    ->withQueryString();
            }

            private function resolveSortBy(mixed $sortBy): string
            {
                $allowed = [
                    'id',
                    'name',
                    'is_active',
                    'created_at',
                    'updated_at',
                ];

                return in_array($sortBy, $allowed, true) ? (string) $sortBy : 'created_at';
            }

            private function resolveSortDirection(mixed $sortDir): string
            {
                return in_array(strtolower((string) $sortDir), ['asc', 'desc'], true)
                    ? strtolower((string) $sortDir)
                    : 'desc';
            }
        }
        PHP);
    }

    private function stubDto(): string
    {
        return $this->render(<<<'PHP'
        <?php

        declare(strict_types=1);

        namespace {{NAMESPACE}}\Application\DTOs;

        use Illuminate\Http\Request;
        use Shared\Contracts\DTOInterface;

        class {{NAME}}Data implements DTOInterface
        {
            public function __construct(
                public readonly string $name,
                public readonly bool $is_active = true,
            ) {}

            public static function fromRequest(Request $request): static
            {
                return static::fromArray($request->validated());
            }

            /**
             * @param array<string, mixed> $data
             */
            public static function fromArray(array $data): static
            {
                return new static(
                    name: (string) $data['name'],
                    is_active: array_key_exists('is_active', $data) ? (bool) $data['is_active'] : true,
                );
            }

            /**
             * @return array<string, mixed>
             */
            public function toArray(): array
            {
                return [
                    'name' => $this->name,
                    'is_active' => $this->is_active,
                ];
            }
        }
        PHP);
    }

    private function stubModel(): string
    {
        return $this->render(<<<'PHP'
        <?php

        declare(strict_types=1);

        namespace {{NAMESPACE}}\Domain\Models;

        use Shared\Models\BaseModel;

        class {{NAME}} extends BaseModel
        {
            protected $table = '{{TABLE}}';

            protected $fillable = [
                'name',
                'is_active',
            ];

            protected function casts(): array
            {
                return array_merge(parent::casts(), [
                    'is_active' => 'boolean',
                ]);
            }
        }
        PHP);
    }

    private function stubWebController(): string
    {
        return $this->render(<<<'PHP'
        <?php

        declare(strict_types=1);

        namespace {{NAMESPACE}}\Presentation\Controllers;

        use App\Http\Controllers\Controller;
        use Illuminate\Http\RedirectResponse;
        use Inertia\Inertia;
        use Inertia\Response;
        use {{NAMESPACE}}\Application\Actions\Create{{NAME}}Action;
        use {{NAMESPACE}}\Application\Actions\Delete{{NAME}}Action;
        use {{NAMESPACE}}\Application\Actions\List{{NAME}}Action;
        use {{NAMESPACE}}\Application\Actions\Update{{NAME}}Action;
        use {{NAMESPACE}}\Application\DTOs\{{NAME}}Data;
        use {{NAMESPACE}}\Domain\Models\{{NAME}};
        use {{NAMESPACE}}\Presentation\Requests\Create{{NAME}}Request;
        use {{NAMESPACE}}\Presentation\Requests\Update{{NAME}}Request;

        class {{NAME}}Controller extends Controller
        {
            public function index(List{{NAME}}Action $action): Response
            {
                return Inertia::render('{{NAME}}/Index', [
                    '{{PLURAL_CAMEL}}' => $action->execute(request()->only([
                        'search',
                        'sort_by',
                        'sort_dir',
                        'per_page',
                    ])),
                ]);
            }

            public function create(): Response
            {
                return Inertia::render('{{NAME}}/Create');
            }

            public function store(
                Create{{NAME}}Request $request,
                Create{{NAME}}Action $action,
            ): RedirectResponse {
                $action->execute({{NAME}}Data::fromArray($request->validated()));

                return redirect()
                    ->route('{{SNAKE}}.index')
                    ->with('success', '{{NAME}} berhasil ditambahkan.');
            }

            public function show({{NAME}} ${{CAMEL}}): Response
            {
                return Inertia::render('{{NAME}}/Show', [
                    '{{CAMEL}}' => ${{CAMEL}},
                ]);
            }

            public function edit({{NAME}} ${{CAMEL}}): Response
            {
                return Inertia::render('{{NAME}}/Edit', [
                    '{{CAMEL}}' => ${{CAMEL}},
                ]);
            }

            public function update(
                Update{{NAME}}Request $request,
                Update{{NAME}}Action $action,
                {{NAME}} ${{CAMEL}},
            ): RedirectResponse {
                $action->execute(${{CAMEL}}, {{NAME}}Data::fromArray($request->validated()));

                return redirect()
                    ->route('{{SNAKE}}.index')
                    ->with('success', '{{NAME}} berhasil diperbarui.');
            }

            public function destroy(
                Delete{{NAME}}Action $action,
                {{NAME}} ${{CAMEL}},
            ): RedirectResponse {
                $action->execute(${{CAMEL}});

                return redirect()
                    ->route('{{SNAKE}}.index')
                    ->with('success', '{{NAME}} berhasil dihapus.');
            }
        }
        PHP);
    }

    private function stubApiController(): string
    {
        return $this->render(<<<'PHP'
        <?php

        declare(strict_types=1);

        namespace {{NAMESPACE}}\Presentation\Controllers\Api;

        use Illuminate\Http\JsonResponse;
        use {{NAMESPACE}}\Application\Actions\Create{{NAME}}Action;
        use {{NAMESPACE}}\Application\Actions\Delete{{NAME}}Action;
        use {{NAMESPACE}}\Application\Actions\List{{NAME}}Action;
        use {{NAMESPACE}}\Application\Actions\Update{{NAME}}Action;
        use {{NAMESPACE}}\Application\DTOs\{{NAME}}Data;
        use {{NAMESPACE}}\Domain\Models\{{NAME}};
        use {{NAMESPACE}}\Presentation\Requests\Create{{NAME}}Request;
        use {{NAMESPACE}}\Presentation\Requests\Update{{NAME}}Request;
        use {{NAMESPACE}}\Presentation\Resources\{{NAME}}Resource;
        use Shared\Http\Controllers\ApiController;

        class {{NAME}}Controller extends ApiController
        {
            public function index(List{{NAME}}Action $action): JsonResponse
            {
                $items = $action->execute(request()->only([
                    'search',
                    'sort_by',
                    'sort_dir',
                    'per_page',
                ]));

                return $this->paginated(
                    {{NAME}}Resource::collection($items),
                    'Data {{SNAKE}} berhasil diambil.',
                );
            }

            public function store(
                Create{{NAME}}Request $request,
                Create{{NAME}}Action $action,
            ): JsonResponse {
                $model = $action->execute({{NAME}}Data::fromArray($request->validated()));

                return $this->created(
                    new {{NAME}}Resource($model),
                    '{{NAME}} berhasil ditambahkan.',
                );
            }

            public function show({{NAME}} ${{CAMEL}}): JsonResponse
            {
                return $this->success(
                    new {{NAME}}Resource(${{CAMEL}}),
                    'Detail {{SNAKE}} berhasil diambil.',
                );
            }

            public function update(
                Update{{NAME}}Request $request,
                Update{{NAME}}Action $action,
                {{NAME}} ${{CAMEL}},
            ): JsonResponse {
                $model = $action->execute(
                    ${{CAMEL}},
                    {{NAME}}Data::fromArray($request->validated()),
                );

                return $this->success(
                    new {{NAME}}Resource($model),
                    '{{NAME}} berhasil diperbarui.',
                );
            }

            public function destroy(
                Delete{{NAME}}Action $action,
                {{NAME}} ${{CAMEL}},
            ): JsonResponse {
                $action->execute(${{CAMEL}});

                return $this->success(message: '{{NAME}} berhasil dihapus.');
            }
        }
        PHP);
    }

    private function stubRequest(string $prefix): string
    {
        return $this->render(<<<'PHP'
        <?php

        declare(strict_types=1);

        namespace {{NAMESPACE}}\Presentation\Requests;

        use Illuminate\Foundation\Http\FormRequest;

        class {{PREFIX}}{{NAME}}Request extends FormRequest
        {
            public function authorize(): bool
            {
                return true;
            }

            public function rules(): array
            {
                return [
                    'name' => ['required', 'string', 'max:150'],
                    'is_active' => ['nullable', 'boolean'],
                ];
            }
        }
        PHP, [
            '{{PREFIX}}' => $prefix,
        ]);
    }

    private function stubResource(): string
    {
        return $this->render(<<<'PHP'
        <?php

        declare(strict_types=1);

        namespace {{NAMESPACE}}\Presentation\Resources;

        use Illuminate\Http\Request;
        use Illuminate\Http\Resources\Json\JsonResource;

        class {{NAME}}Resource extends JsonResource
        {
            public function toArray(Request $request): array
            {
                return [
                    'id' => $this->id,
                    'name' => $this->name,
                    'is_active' => (bool) $this->is_active,
                    'created_at' => $this->created_at?->toISOString(),
                    'updated_at' => $this->updated_at?->toISOString(),
                ];
            }
        }
        PHP);
    }

    private function stubServiceProvider(): string
    {
        return $this->render(<<<'PHP'
        <?php

        declare(strict_types=1);

        namespace {{NAMESPACE}};

        use Illuminate\Support\ServiceProvider;

        class {{NAME}}ServiceProvider extends ServiceProvider
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
        PHP);
    }

    private function stubRoutesWeb(): string
    {
        return $this->render(<<<'PHP'
        <?php

        declare(strict_types=1);

        use Illuminate\Support\Facades\Route;
        use {{NAMESPACE}}\Presentation\Controllers\{{NAME}}Controller;

        Route::middleware(['web', 'auth', 'perguruan'])
            ->prefix('{{SNAKE}}')
            ->name('{{SNAKE}}.')
            ->group(function (): void {
                Route::get('/', [{{NAME}}Controller::class, 'index'])->name('index');
                Route::get('/create', [{{NAME}}Controller::class, 'create'])->name('create');
                Route::post('/', [{{NAME}}Controller::class, 'store'])->name('store');
                Route::get('/{{ROUTE_PARAM}}', [{{NAME}}Controller::class, 'show'])->name('show');
                Route::get('/{{ROUTE_PARAM}}/edit', [{{NAME}}Controller::class, 'edit'])->name('edit');
                Route::put('/{{ROUTE_PARAM}}', [{{NAME}}Controller::class, 'update'])->name('update');
                Route::delete('/{{ROUTE_PARAM}}', [{{NAME}}Controller::class, 'destroy'])->name('destroy');
            });
        PHP);
    }

    private function stubRoutesApi(): string
    {
        return $this->render(<<<'PHP'
        <?php

        declare(strict_types=1);

        use Illuminate\Support\Facades\Route;
        use {{NAMESPACE}}\Presentation\Controllers\Api\{{NAME}}Controller;

        Route::middleware(['api', 'auth:api', 'perguruan'])
            ->prefix('api/v1/{{SNAKE}}')
            ->name('api.v1.{{SNAKE}}.')
            ->group(function (): void {
                Route::get('/', [{{NAME}}Controller::class, 'index'])->name('index');
                Route::post('/', [{{NAME}}Controller::class, 'store'])->name('store');
                Route::get('/{{ROUTE_PARAM}}', [{{NAME}}Controller::class, 'show'])->name('show');
                Route::put('/{{ROUTE_PARAM}}', [{{NAME}}Controller::class, 'update'])->name('update');
                Route::delete('/{{ROUTE_PARAM}}', [{{NAME}}Controller::class, 'destroy'])->name('destroy');
            });
        PHP);
    }

    /**
     * @param  array<string, string>  $extra
     */
    private function render(string $stub, array $extra = []): string
    {
        $replacements = array_merge([
            '{{NAMESPACE}}' => $this->namespace,
            '{{NAME}}' => $this->moduleName,
            '{{SNAKE}}' => $this->snake(),
            '{{CAMEL}}' => $this->camel(),
            '{{PLURAL_CAMEL}}' => Str::plural($this->camel()),
            '{{TABLE}}' => Str::snake(Str::pluralStudly($this->moduleName)),
            '{{ROUTE_PARAM}}' => '{'.$this->camel().'}',
        ], $extra);

        return str_replace(array_keys($replacements), array_values($replacements), $stub).PHP_EOL;
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
        $provider = "\\Modules\\{$this->moduleName}\\{$this->moduleName}ServiceProvider::class";

        $this->newLine();
        $this->components->warn('Daftarkan ServiceProvider module ini di app/Providers/ModulesServiceProvider.php:');
        $this->line("    {$provider},");
        $this->newLine();
    }

    private function showSuccessMessage(): void
    {
        $this->components->info("Module [{$this->moduleName}] berhasil dibuat.");
        $this->line('Langkah berikutnya: buat migration, daftarkan provider, lalu sesuaikan fields sesuai kebutuhan modul.');
        $this->newLine();
    }
}
