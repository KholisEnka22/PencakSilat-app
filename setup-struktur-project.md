# Setup Struktur Project — Pencak Silat Manager

> Menyesuaikan directory ke `src/` untuk Modules & Shared

---

## Step 1 — Buat Folder Struktur Sekaligus

Jalankan perintah berikut dari root project:

```bash
# ── BACKEND: src/ ─────────────────────────────────────────────
mkdir src
mkdir src\Modules
mkdir src\Shared
mkdir src\Shared\Actions
mkdir src\Shared\Contracts
mkdir src\Shared\DTOs
mkdir src\Shared\Enums
mkdir src\Shared\Exceptions
mkdir src\Shared\Http
mkdir src\Shared\Http\Middleware
mkdir src\Shared\Http\Responses
mkdir src\Shared\Models
mkdir src\Shared\Scopes
mkdir src\Shared\Traits

# ── FRONTEND: resources/js/ ────────────────────────────────────
mkdir resources\js\Pages
mkdir resources\js\Pages\Auth
mkdir resources\js\Components
mkdir resources\js\Components\ui
mkdir resources\js\Components\form
mkdir resources\js\Components\layout
mkdir resources\js\Hooks
mkdir resources\js\Context
mkdir resources\js\Utils
```

---

## Step 2 — Update `composer.json`

Ubah bagian `autoload` agar Laravel mengenali namespace `src/`:

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Src\\Modules\\": "src/Modules/",
        "Src\\Shared\\": "src/Shared/"
    }
},
"autoload-dev": {
    "psr-4": {
        "Tests\\": "tests/"
    }
}
```

Lalu jalankan:

```bash
composer dump-autoload
```

> **Catatan**: Namespace untuk module menggunakan prefix `Src\` bukan `App\`
> agar jelas mana yang Laravel core (`App\`) dan mana yang domain kita (`Src\`).

---

## Step 3 — Update `bootstrap/app.php`

Daftarkan semua ServiceProvider dari setiap modul di sini.
Untuk sekarang tambahkan dulu struktur dasarnya:

```php
<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);

        // Middleware alias untuk dipakai di routes
        $middleware->alias([
            'role'       => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'perguruan'  => \Src\Shared\Http\Middleware\EnsureBelongsToPerguruan::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withProviders([
        // Module ServiceProviders didaftarkan di sini setiap kali modul baru dibuat
        // Contoh:
        // \Src\Modules\Auth\AuthServiceProvider::class,
        // \Src\Modules\Perguruan\PerguruanServiceProvider::class,
    ])
    ->create();
```

---

## Step 4 — Update `routes/web.php`

```php
<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

/*
 * Route dari setiap modul di-load via ServiceProvider masing-masing modul.
 * Contoh di dalam AuthServiceProvider:
 *
 * public function boot(): void
 * {
 *     $this->loadRoutesFrom(__DIR__.'/routes.php');
 * }
 */
```

---

## Step 5 — Update `routes/api.php`

```php
<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    /*
     * Route API dari setiap modul di-load via ServiceProvider masing-masing modul.
     * Contoh di dalam AuthServiceProvider:
     *
     * public function boot(): void
     * {
     *     $this->loadRoutesFrom(__DIR__.'/routes_api.php');
     * }
     */
});
```

---

## Step 6 — Buat File Shared (Fondasi Lintas Modul)

### `src/Shared/Contracts/ActionInterface.php`

```php
<?php

declare(strict_types=1);

namespace Src\Shared\Contracts;

interface ActionInterface
{
    public function execute(): mixed;
}
```

---

### `src/Shared/Enums/RoleEnum.php`

```php
<?php

declare(strict_types=1);

namespace Src\Shared\Enums;

enum RoleEnum: string
{
    case SuperAdmin      = 'super_admin';
    case AdminPerguruan  = 'admin_perguruan';
    case PelatihRayon    = 'pelatih_rayon';
    case Member          = 'member';

    public function label(): string
    {
        return match($this) {
            self::SuperAdmin     => 'Super Admin',
            self::AdminPerguruan => 'Admin Perguruan',
            self::PelatihRayon   => 'Pelatih Rayon',
            self::Member         => 'Member',
        };
    }
}
```

---

### `src/Shared/Enums/StatusEnum.php`

```php
<?php

declare(strict_types=1);

namespace Src\Shared\Enums;

enum StatusEnum: string
{
    case Active   = 'active';
    case Inactive = 'inactive';

    public function label(): string
    {
        return match($this) {
            self::Active   => 'Aktif',
            self::Inactive => 'Tidak Aktif',
        };
    }

    public function isActive(): bool
    {
        return $this === self::Active;
    }
}
```

---

### `src/Shared/Models/BaseModel.php`

```php
<?php

declare(strict_types=1);

namespace Src\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class BaseModel extends Model
{
    use SoftDeletes;

    /**
     * Default: semua kolom non-guarded.
     * Tiap model turunan wajib define $fillable sendiri.
     */
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }
}
```

---

### `src/Shared/Scopes/BelongsToPerguruanScope.php`

```php
<?php

declare(strict_types=1);

namespace Src\Shared\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class BelongsToPerguruanScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Super admin bisa lihat semua data lintas perguruan
        if (Auth::check() && Auth::user()->hasRole('super_admin')) {
            return;
        }

        // User lain hanya bisa lihat data perguruan mereka sendiri
        if (Auth::check() && Auth::user()->perguruan_id) {
            $builder->where(
                $model->getTable().'.perguruan_id',
                Auth::user()->perguruan_id
            );
        }
    }
}
```

---

### `src/Shared/Traits/HasPerguruanScope.php`

```php
<?php

declare(strict_types=1);

namespace Src\Shared\Traits;

use Src\Shared\Scopes\BelongsToPerguruanScope;

trait HasPerguruanScope
{
    public static function bootHasPerguruanScope(): void
    {
        static::addGlobalScope(new BelongsToPerguruanScope());
    }
}
```

---

### `src/Shared/Traits/HasAuditColumns.php`

```php
<?php

declare(strict_types=1);

namespace Src\Shared\Traits;

use Illuminate\Support\Facades\Auth;

trait HasAuditColumns
{
    public static function bootHasAuditColumns(): void
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }
}
```

---

### `src/Shared/DTOs/PaginationData.php`

```php
<?php

declare(strict_types=1);

namespace Src\Shared\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Max;

class PaginationData extends Data
{
    public function __construct(
        public readonly ?string $search   = null,
        public readonly string  $sort_by  = 'created_at',
        public readonly string  $sort_dir = 'desc',

        #[Min(1)]
        public readonly int $page     = 1,

        #[Min(5), Max(100)]
        public readonly int $per_page = 15,
    ) {}
}
```

---

### `src/Shared/Http/Responses/ApiResponse.php`

```php
<?php

declare(strict_types=1);

namespace Src\Shared\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(
        mixed $data    = null,
        string $message = 'Success',
        int $status    = 200,
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    public static function error(
        string $message = 'Error',
        mixed $errors   = null,
        int $status     = 400,
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }

    public static function unauthorized(
        string $message = 'Unauthorized',
    ): JsonResponse {
        return self::error($message, null, 401);
    }

    public static function forbidden(
        string $message = 'Forbidden',
    ): JsonResponse {
        return self::error($message, null, 403);
    }

    public static function notFound(
        string $message = 'Data tidak ditemukan',
    ): JsonResponse {
        return self::error($message, null, 404);
    }
}
```

---

### `src/Shared/Http/Middleware/EnsureBelongsToPerguruan.php`

```php
<?php

declare(strict_types=1);

namespace Src\Shared\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBelongsToPerguruan
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Super admin boleh akses semua
        if ($user?->hasRole('super_admin')) {
            return $next($request);
        }

        // User lain wajib punya perguruan_id
        if (! $user?->perguruan_id) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun tidak terhubung ke perguruan manapun.',
                ], 403);
            }

            abort(403, 'Akun tidak terhubung ke perguruan manapun.');
        }

        return $next($request);
    }
}
```

---

### `src/Shared/Exceptions/NotFoundException.php`

```php
<?php

declare(strict_types=1);

namespace Src\Shared\Exceptions;

use Exception;

class NotFoundException extends Exception
{
    public function __construct(string $resource = 'Data')
    {
        parent::__construct("{$resource} tidak ditemukan.", 404);
    }
}
```

---

### `src/Shared/Exceptions/ForbiddenException.php`

```php
<?php

declare(strict_types=1);

namespace Src\Shared\Exceptions;

use Exception;

class ForbiddenException extends Exception
{
    public function __construct(string $message = 'Anda tidak memiliki akses.')
    {
        parent::__construct($message, 403);
    }
}
```

---

## Step 7 — Buat File Shared Frontend

### `resources/js/Utils/constants.js`

```js
export const APP_NAME = "Pencak Silat Manager";

export const ROLES = {
    SUPER_ADMIN: "super_admin",
    ADMIN_PERGURUAN: "admin_perguruan",
    PELATIH_RAYON: "pelatih_rayon",
    MEMBER: "member",
};

export const PAGINATION_DEFAULT = {
    perPage: 15,
    page: 1,
};
```

---

### `resources/js/Utils/formatter.js`

```js
/**
 * Format angka ke format Rupiah
 * @param {number} amount
 * @returns {string} e.g. "Rp 150.000"
 */
export function formatRupiah(amount) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(amount);
}

/**
 * Format tanggal ke format Indonesia
 * @param {string|Date} date
 * @param {boolean} withTime
 * @returns {string} e.g. "12 Januari 2025" atau "12 Januari 2025, 14:30"
 */
export function formatDate(date, withTime = false) {
    if (!date) return "-";

    const options = {
        day: "numeric",
        month: "long",
        year: "numeric",
        ...(withTime && { hour: "2-digit", minute: "2-digit" }),
    };

    return new Intl.DateTimeFormat("id-ID", options).format(new Date(date));
}

/**
 * Truncate string
 * @param {string} str
 * @param {number} length
 * @returns {string}
 */
export function truncate(str, length = 50) {
    if (!str) return "-";
    return str.length > length ? str.slice(0, length) + "..." : str;
}
```

---

### `resources/js/Utils/permissions.js`

```js
/**
 * Cek apakah user punya role tertentu
 * @param {string[]} userRoles - roles dari auth context
 * @param {string|string[]} role
 * @returns {boolean}
 */
export function hasRole(userRoles = [], role) {
    const roles = Array.isArray(role) ? role : [role];
    return roles.some((r) => userRoles.includes(r));
}

/**
 * Cek apakah user punya permission tertentu
 * @param {string[]} userPermissions - permissions dari auth context
 * @param {string|string[]} permission
 * @returns {boolean}
 */
export function hasPermission(userPermissions = [], permission) {
    const permissions = Array.isArray(permission) ? permission : [permission];
    return permissions.some((p) => userPermissions.includes(p));
}

/**
 * Cek apakah user adalah super admin
 * @param {string[]} userRoles
 * @returns {boolean}
 */
export function isSuperAdmin(userRoles = []) {
    return userRoles.includes("super_admin");
}
```

---

### `resources/js/Context/AuthContext.jsx`

```jsx
import { createContext, useContext } from "react";
import { usePage } from "@inertiajs/react";
import { hasRole, hasPermission, isSuperAdmin } from "@/Utils/permissions";

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
    const { auth } = usePage().props;

    const value = {
        user: auth.user,
        roles: auth.roles ?? [],
        permissions: auth.permissions ?? [],

        hasRole: (role) => hasRole(auth.roles ?? [], role),
        hasPermission: (permission) =>
            hasPermission(auth.permissions ?? [], permission),
        isSuperAdmin: () => isSuperAdmin(auth.roles ?? []),
        isLoggedIn: () => !!auth.user,
    };

    return (
        <AuthContext.Provider value={value}>{children}</AuthContext.Provider>
    );
}

export function useAuth() {
    const context = useContext(AuthContext);
    if (!context) {
        throw new Error("useAuth harus digunakan di dalam AuthProvider");
    }
    return context;
}
```

---

### `resources/js/Context/FlashContext.jsx`

```jsx
import { createContext, useContext, useEffect, useState } from "react";
import { usePage } from "@inertiajs/react";

const FlashContext = createContext(null);

export function FlashProvider({ children }) {
    const { flash } = usePage().props;
    const [message, setMessage] = useState({ success: null, error: null });

    useEffect(() => {
        if (flash?.success || flash?.error) {
            setMessage({ success: flash.success, error: flash.error });

            // Auto-clear setelah 4 detik
            const timer = setTimeout(() => {
                setMessage({ success: null, error: null });
            }, 4000);

            return () => clearTimeout(timer);
        }
    }, [flash]);

    return (
        <FlashContext.Provider value={{ message, setMessage }}>
            {children}
        </FlashContext.Provider>
    );
}

export function useFlash() {
    const context = useContext(FlashContext);
    if (!context) {
        throw new Error("useFlash harus digunakan di dalam FlashProvider");
    }
    return context;
}
```

---

### `resources/js/Hooks/usePermission.js`

```js
import { useAuth } from "@/Context/AuthContext";

export function usePermission() {
    const { hasRole, hasPermission, isSuperAdmin, roles, permissions } =
        useAuth();

    return {
        hasRole,
        hasPermission,
        isSuperAdmin,
        roles,
        permissions,
        can: (permission) => hasPermission(permission),
        is: (role) => hasRole(role),
    };
}
```

---

### `resources/js/Hooks/useDebounce.js`

```js
import { useEffect, useState } from "react";

/**
 * Delay update nilai hingga user berhenti mengetik
 * @param {any} value
 * @param {number} delay - milliseconds
 * @returns {any} debouncedValue
 */
export function useDebounce(value, delay = 400) {
    const [debouncedValue, setDebouncedValue] = useState(value);

    useEffect(() => {
        const timer = setTimeout(() => {
            setDebouncedValue(value);
        }, delay);

        return () => clearTimeout(timer);
    }, [value, delay]);

    return debouncedValue;
}
```

---

### `resources/js/Hooks/useWilayah.js`

```js
import { useState, useEffect } from "react";
import axios from "axios";

/**
 * Hook cascading dropdown wilayah Indonesia
 * Provinsi → Kabupaten → Kecamatan → Desa
 */
export function useWilayah(initialValues = {}) {
    const [provinces, setProvinces] = useState([]);
    const [regencies, setRegencies] = useState([]);
    const [districts, setDistricts] = useState([]);
    const [villages, setVillages] = useState([]);

    const [selected, setSelected] = useState({
        province_id: initialValues.province_id ?? "",
        regency_id: initialValues.regency_id ?? "",
        district_id: initialValues.district_id ?? "",
        village_id: initialValues.village_id ?? "",
    });

    const [loading, setLoading] = useState({
        provinces: false,
        regencies: false,
        districts: false,
        villages: false,
    });

    // Load provinsi saat pertama kali
    useEffect(() => {
        setLoading((prev) => ({ ...prev, provinces: true }));
        axios
            .get("/api/v1/wilayah/provinces")
            .then((res) => setProvinces(res.data.data))
            .finally(() =>
                setLoading((prev) => ({ ...prev, provinces: false })),
            );
    }, []);

    // Load kabupaten saat provinsi berubah
    useEffect(() => {
        if (!selected.province_id) {
            setRegencies([]);
            setDistricts([]);
            setVillages([]);
            return;
        }
        setLoading((prev) => ({ ...prev, regencies: true }));
        axios
            .get(`/api/v1/wilayah/regencies/${selected.province_id}`)
            .then((res) => setRegencies(res.data.data))
            .finally(() =>
                setLoading((prev) => ({ ...prev, regencies: false })),
            );
    }, [selected.province_id]);

    // Load kecamatan saat kabupaten berubah
    useEffect(() => {
        if (!selected.regency_id) {
            setDistricts([]);
            setVillages([]);
            return;
        }
        setLoading((prev) => ({ ...prev, districts: true }));
        axios
            .get(`/api/v1/wilayah/districts/${selected.regency_id}`)
            .then((res) => setDistricts(res.data.data))
            .finally(() =>
                setLoading((prev) => ({ ...prev, districts: false })),
            );
    }, [selected.regency_id]);

    // Load desa saat kecamatan berubah
    useEffect(() => {
        if (!selected.district_id) {
            setVillages([]);
            return;
        }
        setLoading((prev) => ({ ...prev, villages: true }));
        axios
            .get(`/api/v1/wilayah/villages/${selected.district_id}`)
            .then((res) => setVillages(res.data.data))
            .finally(() =>
                setLoading((prev) => ({ ...prev, villages: false })),
            );
    }, [selected.district_id]);

    function onChange(field, value) {
        setSelected((prev) => {
            // Reset cascading ke bawah saat parent berubah
            const reset = {};
            if (field === "province_id") {
                reset.regency_id = "";
                reset.district_id = "";
                reset.village_id = "";
            }
            if (field === "regency_id") {
                reset.district_id = "";
                reset.village_id = "";
            }
            if (field === "district_id") {
                reset.village_id = "";
            }
            return { ...prev, ...reset, [field]: value };
        });
    }

    return {
        provinces,
        regencies,
        districts,
        villages,
        selected,
        loading,
        onChange,
    };
}
```

---

### `resources/js/app.jsx` (update dengan Providers)

```jsx
import { createRoot } from "react-dom/client";
import { createInertiaApp } from "@inertiajs/react";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { AuthProvider } from "@/Context/AuthContext";
import { FlashProvider } from "@/Context/FlashContext";

createInertiaApp({
    title: (title) => `${title} — Pencak Silat Manager`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.jsx`,
            import.meta.glob("./Pages/**/*.jsx"),
        ),
    setup({ el, App, props }) {
        createRoot(el).render(
            <AuthProvider>
                <FlashProvider>
                    <App {...props} />
                </FlashProvider>
            </AuthProvider>,
        );
    },
    progress: {
        color: "#e53e3e",
    },
});
```

---

## Struktur Akhir

```
root/
├── app/                            # Laravel core (tidak diubah)
│   └── Http/
│       └── Middleware/
│           └── HandleInertiaRequests.php
│
├── src/                            # Domain kita
│   ├── Modules/
│   │   └── {NamaModul}/
│   │       ├── Application/
│   │       │   ├── Actions/
│   │       │   └── DTOs/
│   │       ├── Domain/
│   │       │   └── Models/
│   │       ├── Presentation/
│   │       │   ├── Controllers/
│   │       │   │   ├── Api/Mobile/
│   │       │   │   └── {Nama}Controller.php
│   │       │   ├── Requests/
│   │       │   └── Resources/
│   │       ├── {Nama}ServiceProvider.php
│   │       ├── routes_api.php
│   │       └── routes.php
│   │
│   └── Shared/
│       ├── Actions/
│       ├── Contracts/
│       │   └── ActionInterface.php
│       ├── DTOs/
│       │   └── PaginationData.php
│       ├── Enums/
│       │   ├── RoleEnum.php
│       │   └── StatusEnum.php
│       ├── Exceptions/
│       │   ├── ForbiddenException.php
│       │   └── NotFoundException.php
│       ├── Http/
│       │   ├── Middleware/
│       │   │   └── EnsureBelongsToPerguruan.php
│       │   └── Responses/
│       │       └── ApiResponse.php
│       ├── Models/
│       │   └── BaseModel.php
│       ├── Scopes/
│       │   └── BelongsToPerguruanScope.php
│       └── Traits/
│           ├── HasAuditColumns.php
│           └── HasPerguruanScope.php
│
├── resources/js/
│   ├── app.jsx
│   ├── Pages/
│   ├── Components/
│   │   ├── ui/
│   │   ├── form/
│   │   └── layout/
│   ├── Hooks/
│   │   ├── useDebounce.js
│   │   ├── usePermission.js
│   │   └── useWilayah.js
│   ├── Context/
│   │   ├── AuthContext.jsx
│   │   └── FlashContext.jsx
│   └── Utils/
│       ├── constants.js
│       ├── formatter.js
│       └── permissions.js
│
├── bootstrap/app.php
├── routes/
│   ├── web.php
│   └── api.php
└── composer.json
```

---

## Checklist

| Task                                                | Status |
| --------------------------------------------------- | ------ |
| Folder struktur `src/Modules` & `src/Shared` dibuat | ✅     |
| `composer.json` autoload diupdate                   | ✅     |
| `composer dump-autoload` dijalankan                 | ✅     |
| `bootstrap/app.php` dikonfigurasi                   | ✅     |
| Spatie middleware alias didaftarkan                 | ✅     |
| Semua file `Shared` dibuat                          | ✅     |
| Context, Hooks, Utils frontend dibuat               | ✅     |
| `app.jsx` wrap dengan providers                     | ✅     |

---

## Langkah Selanjutnya

Struktur sudah siap. Lanjut ke **Modul Auth**:

1. Model `User` — implements `JWTSubject`, tambah relasi ke `Perguruan` & `Rayon`
2. `AuthServiceProvider` — load routes web & api
3. Actions — `LoginAction`, `LogoutAction`, `RefreshTokenAction`
4. Controller web (Inertia) & mobile (JWT API)
5. Seeder roles & permissions via Spatie
