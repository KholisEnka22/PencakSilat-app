# Panduan Instalasi — Pencak Silat Manager

> Stack: Laravel 11 · Inertia.js · React · MySQL 8 · JWT (tymon/jwt-auth) · Spatie Permission

---

## Step 1 — Buat Project Laravel 11

```bash
composer create-project laravel/laravel pencaksilat-app "11.*"
cd pencaksilat-app
```

---

## Step 2 — Install Semua Package PHP

```bash
# Inertia.js server-side adapter
composer require inertiajs/inertia-laravel

# Spatie Permission (role & permission management)
composer require spatie/laravel-permission

# JWT Auth untuk mobile API
composer require tymon/jwt-auth

# Spatie Laravel Data (DTOs — strongly typed data objects)
composer require spatie/laravel-data

# Ziggy (share Laravel named routes ke React)
composer require tightenco/ziggy

# IDE Helper (development only)
composer require --dev barryvdh/laravel-ide-helper

# Laravel Telescope (debugging — development only)
composer require --dev laravel/telescope
```

---

## Step 3 — Install Frontend Dependencies

```bash
# Inertia React adapter
npm install @inertiajs/react

# React
npm install react react-dom

# Vite plugin React — pakai versi @4 agar tidak error
npm install --save-dev @vitejs/plugin-react@4

# Ziggy frontend (share routes ke JS)
npm install ziggy-js
```

---

## Step 4 — Publish Config Semua Package

```bash
# Inertia — publish middleware
php artisan inertia:middleware

# Spatie Permission — publish migration & config
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# JWT — publish config + generate secret key
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
php artisan jwt:secret

# Spatie Laravel Data — publish config
php artisan vendor:publish --provider="Spatie\LaravelData\LaravelDataServiceProvider" --tag="data-config"

# Telescope — install assets & migration
php artisan telescope:install

# IDE Helper (opsional, untuk autocomplete di IDE)
php artisan ide-helper:generate
php artisan ide-helper:models --nowrite
```

---

## Step 5 — Konfigurasi `.env`

```env
APP_NAME="Pencak Silat Manager"
APP_ENV=local
APP_KEY=                        # auto-generate saat install
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_LOCALE=id
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pencaksilat_db
DB_USERNAME=root
DB_PASSWORD=

# JWT
JWT_SECRET=                     # auto-generate saat php artisan jwt:secret
JWT_TTL=60                      # access token expire: 60 menit
JWT_REFRESH_TTL=20160           # refresh token expire: 14 hari

# Session (web/Inertia)
SESSION_DRIVER=database
SESSION_LIFETIME=120

CACHE_STORE=database            # ganti ke redis jika sudah setup Redis
QUEUE_CONNECTION=database
```

---

## Step 6 — Konfigurasi `config/auth.php`

Ubah guard `api` agar menggunakan driver `jwt`:

```php
'guards' => [
    'web' => [
        'driver'   => 'session',
        'provider' => 'users',
    ],

    'api' => [
        'driver'   => 'jwt',        // <-- ganti dari 'token' ke 'jwt'
        'provider' => 'users',
    ],
],
```

---

## Step 7 — Daftarkan Inertia Middleware

Buka `bootstrap/app.php`, tambahkan middleware Inertia ke grup `web`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\HandleInertiaRequests::class,
    ]);
})
```

---

## Step 8 — Setup `HandleInertiaRequests.php`

File ini sudah dibuat saat `php artisan inertia:middleware`. Isi dengan shared data:

```php
<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user()?->only(
                    'id',
                    'name',
                    'email',
                    'avatar',
                    'perguruan_id',
                    'rayon_id',
                ),
                'roles'       => $request->user()?->getRoleNames(),
                'permissions' => $request->user()?->getAllPermissions()->pluck('name'),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
        ]);
    }
}
```

---

## Step 9 — Setup `vite.config.js`

```js
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import react from "@vitejs/plugin-react";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/js/app.jsx"],
            refresh: true,
        }),
        react(),
    ],
    resolve: {
        alias: {
            "@": "/resources/js",
        },
    },
});
```

---

## Step 10 — Setup `resources/js/app.jsx`

Buat file `resources/js/app.jsx` (rename dari `app.js`):

```jsx
import { createRoot } from "react-dom/client";
import { createInertiaApp } from "@inertiajs/react";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";

createInertiaApp({
    title: (title) => `${title} — Pencak Silat Manager`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.jsx`,
            import.meta.glob("./Pages/**/*.jsx"),
        ),
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
    progress: {
        color: "#e53e3e",
    },
});
```

---

## Step 11 — Setup Blade Root Template

Buat atau ubah `resources/views/app.blade.php`:

```html
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title inertia>{{ config('app.name') }}</title>

        <link
            rel="icon"
            type="image/x-icon"
            href="{{ asset('img/abitour.jpg') }}"
        />
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link
            href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"
            rel="stylesheet"
        />

        <!-- Scripts -->
        @routes @viteReactRefresh @vite(['resources/js/app.tsx',
        'resources/css/app.css']) @inertiaHead
    </head>

    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
```

---

## Step 12 — Buat Folder Struktur Modules

```bash
mkdir -p app/Modules
```

Tambahkan autoload untuk namespace Modules di `composer.json`:

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "App\\Modules\\": "app/Modules/"
    }
},
```

Lalu regenerate autoload:

```bash
composer dump-autoload
```

---

## Step 13 — Jalankan Migration Awal

```bash
# Buat database dulu jika belum ada
mysql -u root -p -e "CREATE DATABASE pencaksilat_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Jalankan semua migration
php artisan migrate
```

> Migration yang dijalankan: default Laravel (users, sessions, cache, jobs) + Spatie permission tables + Telescope tables.

---

## Step 14 — Verifikasi Instalasi

```bash
# Cek status package & konfigurasi
php artisan about

# Jalankan dev server (dua terminal terpisah)
php artisan serve
npm run dev
```

Buka browser ke `http://localhost:8000` — jika tidak ada error, instalasi berhasil.

---

## Checklist Akhir

| Komponen          | Package                           | Status |
| ----------------- | --------------------------------- | ------ |
| Framework         | Laravel 11                        | ✅     |
| Frontend SPA      | Inertia.js + React                | ✅     |
| Build tool        | Vite + @vitejs/plugin-react@4     | ✅     |
| Role & Permission | spatie/laravel-permission         | ✅     |
| Mobile Auth       | tymon/jwt-auth                    | ✅     |
| DTOs              | spatie/laravel-data               | ✅     |
| Route sharing     | tightenco/ziggy                   | ✅     |
| Debugging         | laravel/telescope (dev)           | ✅     |
| IDE Autocomplete  | barryvdh/laravel-ide-helper (dev) | ✅     |

---

## Struktur Module (Referensi)

Setiap modul mengikuti struktur berikut:

```
app/Modules/{NamaModul}/
├── Application/
│   ├── Actions/
│   │   ├── Create{Nama}Action.php
│   │   ├── Delete{Nama}Action.php
│   │   ├── List{Nama}Action.php
│   │   └── Update{Nama}Action.php
│   └── DTOs/
│       └── {Nama}Data.php
├── Domain/
│   └── Models/
│       └── {Nama}.php
├── Presentation/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   └── Mobile/
│   │   │       └── {Nama}Controller.php
│   │   └── {Nama}Controller.php
│   ├── Requests/
│   │   ├── Create{Nama}Request.php
│   │   └── Update{Nama}Request.php
│   └── Resources/
│       └── {Nama}Resource.php
├── {Nama}ServiceProvider.php
├── routes_api.php
└── routes.php
```

> Setiap file PHP wajib menggunakan `declare(strict_types=1);` di baris pertama setelah tag `<?php`.

---

## Langkah Selanjutnya

Setelah instalasi selesai, lanjut ke setup modul dalam urutan berikut:

1. **Auth** — Model User (implements JWTSubject), guard middleware, Spatie role seeder
2. **Perguruan** — Entitas utama, scope untuk semua modul lain
3. **Rayon** — Child dari Perguruan
4. **Anggota** — Core business logic
