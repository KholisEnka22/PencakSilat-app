# Instalasi Project Pencak Silat App

## Buat Project Laravel 11

```bash
composer create-project laravel/laravel pencaksilat-app "11.*"
cd pencaksilat-app
```

---

## 2. Install Package Backend Laravel

Install package backend yang dibutuhkan:

```bash
# Inertia.js (server-side)
composer require inertiajs/inertia-laravel

# Spatie Permission
composer require spatie/laravel-permission

# JWT Auth (mobile)
composer require tymon/jwt-auth

# Laravel Data (untuk DTOs — sangat recommended untuk DDD)
composer require spatie/laravel-data

# Ziggy (share Laravel routes ke Vue/React via Inertia)
composer require tightenco/ziggy

# IDE Helper (development only)
composer require --dev barryvdh/laravel-ide-helper

# Laravel Telescope (debugging, development only)
composer require --dev laravel/telescope
```

## 3. Install Dependency Node.js

Install dependency bawaan project:

```bash
# Install Node dependencies
npm install

# Inertia client + React
npm install @inertiajs/react react react-dom

# Vite plugin React
npm install --save-dev @vitejs/plugin-react@4

# Ziggy frontend
npm install ziggy-js
```

---
