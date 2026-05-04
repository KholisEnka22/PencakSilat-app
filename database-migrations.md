# Database Migration Documentation
## Sistem Manajemen Organisasi Pencak Silat

> **Stack**: Laravel 11 · MySQL / PostgreSQL  
> **Conventions**: snake_case, English column names, `id` as primary key (BIGINT UNSIGNED AUTO INCREMENT), `created_at` / `updated_at` on all tables, `deleted_at` for soft-delete tables.

---

## Table of Contents

1. [Master Data — Region (Wilayah)](#1-master-data--region-wilayah)
2. [Master Data — General](#2-master-data--general)
3. [Master Values](#3-master-values)
4. [Core — Perguruan](#4-core--perguruan)
5. [Core — Rayon](#5-core--rayon)
6. [Core — Users & Auth](#6-core--users--auth)
7. [Core — Anggota (Members)](#7-core--anggota-members)
8. [Tingkatan (Belt/Rank Levels)](#8-tingkatan-beltrank-levels)
9. [Module — Latihan (Training)](#9-module--latihan-training)
10. [Module — Ujian Kenaikan Tingkat (Grading Exam)](#10-module--ujian-kenaikan-tingkat-grading-exam)
11. [Module — Event](#11-module--event)
12. [Module — Pertandingan (Competition)](#12-module--pertandingan-competition)
13. [Module — Keuangan (Finance)](#13-module--keuangan-finance)
14. [Module — Notifikasi](#14-module--notifikasi)
15. [ERD Relationship Summary](#15-erd-relationship-summary)
16. [Migration Order](#16-migration-order)

---

## 1. Master Data — Region (Wilayah)

Data wilayah Indonesia mengacu pada struktur Kemendagri:  
**Provinsi → Kabupaten/Kota → Kecamatan → Kelurahan/Desa**

> **Sumber data**: Seed dari data wilayah administrasi Kemendagri.  
> Kode resmi wilayah disimpan di kolom `code`, sedangkan kolom `id` tetap menggunakan primary key internal database (`BIGINT UNSIGNED AUTO INCREMENT`).  
> Tabel ini **tidak** menggunakan `perguruan_id` scope karena bersifat global/publik.

---

### `provinces`

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| `id` | `BIGINT UNSIGNED` | PK AI | Primary key internal |
| `code` | `VARCHAR(2)` | UNIQUE NOT NULL | Kode Kemendagri provinsi (e.g. `"32"` = Jawa Barat) |
| `name` | `VARCHAR(100)` | NOT NULL | Nama provinsi |
| `created_at` | `TIMESTAMP` | nullable | — |
| `updated_at` | `TIMESTAMP` | nullable | — |

```php
Schema::create('provinces', function (Blueprint $table) {
    $table->id();
    $table->string('code', 2)->unique();
    $table->string('name', 100);
    $table->timestamps();
});
```

---

### `regencies` (Kabupaten / Kota)

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| `id` | `BIGINT UNSIGNED` | PK AI | Primary key internal |
| `province_id` | `BIGINT UNSIGNED` | FK → provinces | Provinsi induk |
| `code` | `VARCHAR(5)` | UNIQUE NOT NULL | Kode Kemendagri kabupaten/kota (e.g. `"32.73"`) |
| `name` | `VARCHAR(150)` | NOT NULL | Nama kabupaten/kota |
| `created_at` | `TIMESTAMP` | nullable | — |
| `updated_at` | `TIMESTAMP` | nullable | — |

```php
Schema::create('regencies', function (Blueprint $table) {
    $table->id();
    $table->foreignId('province_id')->constrained()->cascadeOnDelete();
    $table->string('code', 5)->unique();
    $table->string('name', 150);
    $table->timestamps();

    $table->index('province_id');
});
```

---

### `districts` (Kecamatan)

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| `id` | `BIGINT UNSIGNED` | PK AI | Primary key internal |
| `regency_id` | `BIGINT UNSIGNED` | FK → regencies | Kabupaten/kota induk |
| `code` | `VARCHAR(8)` | UNIQUE NOT NULL | Kode Kemendagri kecamatan (e.g. `"32.73.01"`) |
| `name` | `VARCHAR(150)` | NOT NULL | Nama kecamatan |
| `created_at` | `TIMESTAMP` | nullable | — |
| `updated_at` | `TIMESTAMP` | nullable | — |

```php
Schema::create('districts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('regency_id')->constrained()->cascadeOnDelete();
    $table->string('code', 8)->unique();
    $table->string('name', 150);
    $table->timestamps();

    $table->index('regency_id');
});
```

---

### `villages` (Kelurahan / Desa)

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| `id` | `BIGINT UNSIGNED` | PK AI | Primary key internal |
| `district_id` | `BIGINT UNSIGNED` | FK → districts | Kecamatan induk |
| `code` | `VARCHAR(13)` | UNIQUE NOT NULL | Kode Kemendagri desa/kelurahan (e.g. `"32.73.01.1001"`) |
| `name` | `VARCHAR(150)` | NOT NULL | Nama desa/kelurahan |
| `created_at` | `TIMESTAMP` | nullable | — |
| `updated_at` | `TIMESTAMP` | nullable | — |

```php
Schema::create('villages', function (Blueprint $table) {
    $table->id();
    $table->foreignId('district_id')->constrained()->cascadeOnDelete();
    $table->string('code', 13)->unique();
    $table->string('name', 150);
    $table->timestamps();

    $table->index('district_id');
});
```

> **Tip**: Untuk form alamat di frontend, gunakan cascading dropdown:  
> Provinsi → Kabupaten/Kota → Kecamatan → Desa/Kelurahan → input manual RT/RW + detail alamat.  
> Simpan relasi alamat menggunakan `province_id`, `regency_id`, `district_id`, dan `village_id` sebagai FK ke primary key internal. Kode resmi Kemendagri tetap bisa ditampilkan dari kolom `code`.


## 2. Master Data — General

Master data global yang tidak terikat satu perguruan. Dikelola oleh **Super Admin**.

---

### `master_data`

Tabel induk untuk mengelompokkan jenis master data.

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| `id` | `BIGINT UNSIGNED` | PK AI | — |
| `name` | `VARCHAR(100)` | NOT NULL | Nama grup (e.g. `"Jenis Kelamin"`, `"Status Anggota"`) |
| `slug` | `VARCHAR(100)` | UNIQUE NOT NULL | Identifier unik (e.g. `"gender"`, `"member_status"`) |
| `description` | `TEXT` | nullable | Keterangan |
| `is_active` | `BOOLEAN` | DEFAULT true | — |
| `created_at` | `TIMESTAMP` | nullable | — |
| `updated_at` | `TIMESTAMP` | nullable | — |

```php
Schema::create('master_data', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100);
    $table->string('slug', 100)->unique();
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

---

### `master_values`

Nilai-nilai dari setiap master data.

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| `id` | `BIGINT UNSIGNED` | PK AI | — |
| `master_data_id` | `BIGINT UNSIGNED` | FK → master_data | — |
| `label` | `VARCHAR(100)` | NOT NULL | Label tampilan (e.g. `"Laki-laki"`) |
| `value` | `VARCHAR(100)` | NOT NULL | Nilai tersimpan (e.g. `"male"`) |
| `description` | `TEXT` | nullable | Keterangan tambahan |
| `sort_order` | `SMALLINT UNSIGNED` | DEFAULT 0 | Urutan tampil |
| `is_active` | `BOOLEAN` | DEFAULT true | — |
| `created_at` | `TIMESTAMP` | nullable | — |
| `updated_at` | `TIMESTAMP` | nullable | — |

```php
Schema::create('master_values', function (Blueprint $table) {
    $table->id();
    $table->foreignId('master_data_id')->constrained()->cascadeOnDelete();
    $table->string('label', 100);
    $table->string('value', 100);
    $table->text('description')->nullable();
    $table->smallInteger('sort_order')->unsigned()->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->index('master_data_id');
    $table->unique(['master_data_id', 'value']);
});
```

**Contoh seed data:**

| master_data.slug | label | value |
|---|---|---|
| `gender` | Laki-laki | `male` |
| `gender` | Perempuan | `female` |
| `member_status` | Aktif | `active` |
| `member_status` | Tidak Aktif | `inactive` |
| `member_status` | Pindah | `transferred` |
| `blood_type` | A | `A` |
| `blood_type` | B | `B` |
| `blood_type` | AB | `AB` |
| `blood_type` | O | `O` |
| `religion` | Islam | `islam` |
| `religion` | Kristen | `christian` |
| `religion` | Katolik | `catholic` |
| `religion` | Hindu | `hindu` |
| `religion` | Buddha | `buddhist` |
| `religion` | Konghucu | `confucian` |
| `event_type` | Kejuaraan | `championship` |
| `event_type` | Seminar | `seminar` |
| `event_type` | Ujian Massal | `mass_exam` |
| `event_type` | Latihan Bersama | `joint_training` |
| `competition_category` | Tanding | `sparring` |
| `competition_category` | Seni | `art` |
| `age_category` | Anak-anak | `children` |
| `age_category` | Remaja | `youth` |
| `age_category` | Dewasa | `adult` |
| `weight_class` | Kelas A | `class_a` |
| `weight_class` | Kelas B | `class_b` |
| `payment_method` | Transfer Bank | `bank_transfer` |
| `payment_method` | Tunai | `cash` |
| `payment_method` | QRIS | `qris` |

---

## 3. Master Values

> **Catatan**: Kolom yang mengacu `master_values` disimpan sebagai `BIGINT UNSIGNED` FK atau `VARCHAR` (nilai `.value`).  
> Disarankan simpan FK `master_value_id` agar label bisa berubah tanpa migrasi data.

---

## 4. Core — Perguruan

### `perguruans`

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| `id` | `BIGINT UNSIGNED` | PK AI | — |
| `name` | `VARCHAR(150)` | NOT NULL | Nama perguruan |
| `abbreviation` | `VARCHAR(30)` | nullable | Singkatan |
| `decree_number` | `VARCHAR(100)` | nullable | Nomor SK pendirian |
| `decree_date` | `DATE` | nullable | Tanggal SK |
| `logo` | `VARCHAR(255)` | nullable | Path logo |
| `email` | `VARCHAR(100)` | nullable UNIQUE | — |
| `phone` | `VARCHAR(20)` | nullable | — |
| `website` | `VARCHAR(255)` | nullable | — |
| `street_address` | `TEXT` | nullable | Jalan, nomor, RT/RW |
| `village_id` | `BIGINT UNSIGNED` | FK nullable → villages | Desa/kelurahan |
| `district_id` | `BIGINT UNSIGNED` | FK nullable → districts | Kecamatan |
| `regency_id` | `BIGINT UNSIGNED` | FK nullable → regencies | Kabupaten/kota |
| `province_id` | `BIGINT UNSIGNED` | FK nullable → provinces | Provinsi |
| `postal_code` | `CHAR(5)` | nullable | — |
| `is_active` | `BOOLEAN` | DEFAULT true | — |
| `created_at` | `TIMESTAMP` | nullable | — |
| `updated_at` | `TIMESTAMP` | nullable | — |
| `deleted_at` | `TIMESTAMP` | nullable | Soft delete |

```php
Schema::create('perguruans', function (Blueprint $table) {
    $table->id();
    $table->string('name', 150);
    $table->string('abbreviation', 30)->nullable();
    $table->string('decree_number', 100)->nullable();
    $table->date('decree_date')->nullable();
    $table->string('logo')->nullable();
    $table->string('email', 100)->nullable()->unique();
    $table->string('phone', 20)->nullable();
    $table->string('website')->nullable();

    // Alamat lengkap
    $table->text('street_address')->nullable();
    $table->foreignId('village_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('regency_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('province_id')->nullable()->constrained()->nullOnDelete();
    $table->char('postal_code', 5)->nullable();

    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();
});
```

---

## 5. Core — Rayon

### `rayons`

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| `id` | `BIGINT UNSIGNED` | PK AI | — |
| `perguruan_id` | `BIGINT UNSIGNED` | FK → perguruans | — |
| `name` | `VARCHAR(150)` | NOT NULL | Nama rayon/kontingen |
| `code` | `VARCHAR(20)` | nullable | Kode rayon |
| `street_address` | `TEXT` | nullable | Lokasi latihan (jalan, RT/RW) |
| `village_id` | `BIGINT UNSIGNED` | FK nullable → villages | Desa/kelurahan |
| `district_id` | `BIGINT UNSIGNED` | FK nullable → districts | Kecamatan |
| `regency_id` | `BIGINT UNSIGNED` | FK nullable → regencies | Kabupaten/kota |
| `province_id` | `BIGINT UNSIGNED` | FK nullable → provinces | Provinsi |
| `postal_code` | `CHAR(5)` | nullable | — |
| `is_active` | `BOOLEAN` | DEFAULT true | — |
| `created_at` | `TIMESTAMP` | nullable | — |
| `updated_at` | `TIMESTAMP` | nullable | — |
| `deleted_at` | `TIMESTAMP` | nullable | Soft delete |

```php
Schema::create('rayons', function (Blueprint $table) {
    $table->id();
    $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
    $table->string('name', 150);
    $table->string('code', 20)->nullable();

    // Alamat lokasi latihan
    $table->text('street_address')->nullable();
    $table->foreignId('village_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('regency_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('province_id')->nullable()->constrained()->nullOnDelete();
    $table->char('postal_code', 5)->nullable();

    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();

    $table->index('perguruan_id');
    $table->unique(['perguruan_id', 'code']);
});
```

---

## 6. Core — Users & Auth

### `users`

Tabel akun login untuk semua aktor yang dapat mengakses sistem, seperti **super admin**, **admin perguruan**, **pelatih rayon**, dan **member mobile**.

> Catatan: Tidak semua anggota wajib memiliki akun login. Anggota yang sudah terdaftar di tabel `members` dapat melakukan **aktivasi akun mobile**. Setelah identitasnya tervalidasi, sistem membuat record `users`, assign role default `member`, lalu menghubungkannya ke `members.user_id`.

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| `id` | `BIGINT UNSIGNED` | PK AI | — |
| `perguruan_id` | `BIGINT UNSIGNED` | FK nullable → perguruans | null = super_admin |
| `rayon_id` | `BIGINT UNSIGNED` | FK nullable → rayons | Rayon utama user; nullable untuk super admin/admin tertentu |
| `name` | `VARCHAR(100)` | NOT NULL | Nama akun, bisa diambil dari `members.full_name` saat aktivasi |
| `email` | `VARCHAR(100)` | UNIQUE nullable | Email login/verifikasi; nullable jika aktivasi memakai nomor HP |
| `email_verified_at` | `TIMESTAMP` | nullable | Waktu email terverifikasi |
| `password` | `VARCHAR(255)` | nullable | Bcrypt hash; nullable saat akun masih `pending_activation` |
| `phone` | `VARCHAR(20)` | UNIQUE nullable | Nomor HP login/verifikasi OTP |
| `avatar` | `VARCHAR(255)` | nullable | Path avatar |
| `account_status` | `ENUM('pending_activation','active','suspended')` | DEFAULT `'pending_activation'` | Status akun login |
| `activated_at` | `TIMESTAMP` | nullable | Waktu akun berhasil diaktivasi |
| `last_login_at` | `TIMESTAMP` | nullable | Waktu login terakhir |
| `remember_token` | `VARCHAR(100)` | nullable | — |
| `created_at` | `TIMESTAMP` | nullable | — |
| `updated_at` | `TIMESTAMP` | nullable | — |
| `deleted_at` | `TIMESTAMP` | nullable | Soft delete |

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->foreignId('perguruan_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('rayon_id')->nullable()->constrained()->nullOnDelete();
    $table->string('name', 100);
    $table->string('email', 100)->nullable()->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password')->nullable();
    $table->string('phone', 20)->nullable()->unique();
    $table->string('avatar')->nullable();
    $table->enum('account_status', ['pending_activation', 'active', 'suspended'])
          ->default('pending_activation');
    $table->timestamp('activated_at')->nullable();
    $table->timestamp('last_login_at')->nullable();
    $table->rememberToken();
    $table->timestamps();
    $table->softDeletes();

    $table->index('perguruan_id');
    $table->index('rayon_id');
    $table->index('account_status');
});
```

> **Spatie Permission**: Tabel `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions` dibuat otomatis oleh package — tidak perlu migrasi manual.
>
> **Role akun mobile**: Saat member melakukan aktivasi akun dari aplikasi mobile, sistem membuat `users` dengan role default `member`. Jika suatu saat member menjadi pelatih, cukup tambahkan role `pelatih_rayon` pada user yang sama, tanpa membuat akun baru.

---

## 7. Core — Anggota (Members)

### `members`

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| `id` | `BIGINT UNSIGNED` | PK AI | — |
| `user_id` | `BIGINT UNSIGNED` | FK nullable UNIQUE → users | Akun login member; null = belum aktivasi akun mobile |
| `perguruan_id` | `BIGINT UNSIGNED` | FK → perguruans | Scope utama |
| `rayon_id` | `BIGINT UNSIGNED` | FK → rayons | — |
| `rank_level_id` | `BIGINT UNSIGNED` | FK → rank_levels | Tingkatan sabuk saat ini |
| `member_number` | `VARCHAR(50)` | UNIQUE nullable | Nomor KTA (auto-generate) |
| `full_name` | `VARCHAR(150)` | NOT NULL | — |
| `national_id` | `VARCHAR(16)` | UNIQUE nullable | NIK KTP |
| `gender` | `VARCHAR(10)` | NOT NULL | FK → master_values (slug: gender) |
| `place_of_birth` | `VARCHAR(100)` | nullable | — |
| `date_of_birth` | `DATE` | nullable | — |
| `blood_type` | `VARCHAR(5)` | nullable | FK → master_values (slug: blood_type) |
| `religion` | `VARCHAR(20)` | nullable | FK → master_values (slug: religion) |
| `phone` | `VARCHAR(20)` | nullable | — |
| `email` | `VARCHAR(100)` | nullable | — |
| `photo` | `VARCHAR(255)` | nullable | Path foto |
| `street_address` | `TEXT` | nullable | Jalan, nomor, RT/RW |
| `village_id` | `BIGINT UNSIGNED` | FK nullable → villages | Desa/kelurahan |
| `district_id` | `BIGINT UNSIGNED` | FK nullable → districts | Kecamatan |
| `regency_id` | `BIGINT UNSIGNED` | FK nullable → regencies | Kabupaten/kota |
| `province_id` | `BIGINT UNSIGNED` | FK nullable → provinces | Provinsi |
| `postal_code` | `CHAR(5)` | nullable | — |
| `join_date` | `DATE` | NOT NULL | Tanggal bergabung |
| `status` | `VARCHAR(20)` | NOT NULL DEFAULT `'active'` | FK → master_values (slug: member_status) |
| `notes` | `TEXT` | nullable | Catatan tambahan |
| `created_at` | `TIMESTAMP` | nullable | — |
| `updated_at` | `TIMESTAMP` | nullable | — |
| `deleted_at` | `TIMESTAMP` | nullable | Soft delete |

```php
Schema::create('members', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
    $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
    $table->foreignId('rayon_id')->constrained()->cascadeOnDelete();
    $table->foreignId('rank_level_id')->nullable()->constrained()->nullOnDelete();
    $table->string('member_number', 50)->nullable()->unique();
    $table->string('full_name', 150);
    $table->string('national_id', 16)->nullable()->unique();
    $table->string('gender', 10);
    $table->string('place_of_birth', 100)->nullable();
    $table->date('date_of_birth')->nullable();
    $table->string('blood_type', 5)->nullable();
    $table->string('religion', 20)->nullable();
    $table->string('phone', 20)->nullable();
    $table->string('email', 100)->nullable();
    $table->string('photo')->nullable();

    // Alamat lengkap
    $table->text('street_address')->nullable();
    $table->foreignId('village_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('regency_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('province_id')->nullable()->constrained()->nullOnDelete();
    $table->char('postal_code', 5)->nullable();

    $table->date('join_date');
    $table->string('status', 20)->default('active');
    $table->text('notes')->nullable();
    $table->timestamps();
    $table->softDeletes();

    $table->index('user_id');
    $table->index('perguruan_id');
    $table->index('rayon_id');
    $table->index('rank_level_id');
    $table->index('status');
});
```

> **Aktivasi akun mobile**: Member yang sudah ada di tabel `members` dapat mengaktifkan akun dari aplikasi mobile menggunakan data identitas yang cocok, misalnya nomor anggota/nomor HP + tanggal lahir. Setelah OTP/verifikasi berhasil, sistem membuat `users`, assign role default `member`, lalu mengisi `members.user_id`.
>
> **Member menjadi pelatih**: Jika member yang sama kemudian menjadi pelatih, jangan buat akun baru. Tambahkan role `pelatih_rayon` pada `users` yang sudah terhubung ke `members.user_id`.

---

### `member_rank_histories`

Riwayat perubahan tingkatan sabuk anggota.

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| `id` | `BIGINT UNSIGNED` | PK AI | — |
| `member_id` | `BIGINT UNSIGNED` | FK → members | — |
| `perguruan_id` | `BIGINT UNSIGNED` | FK → perguruans | — |
| `rank_level_id` | `BIGINT UNSIGNED` | FK → rank_levels | Tingkatan yang dicapai |
| `grading_exam_id` | `BIGINT UNSIGNED` | FK nullable → grading_exams | Dari ujian mana |
| `promoted_at` | `DATE` | NOT NULL | Tanggal naik tingkat |
| `notes` | `TEXT` | nullable | — |
| `created_at` | `TIMESTAMP` | nullable | — |
| `updated_at` | `TIMESTAMP` | nullable | — |

```php
Schema::create('member_rank_histories', function (Blueprint $table) {
    $table->id();
    $table->foreignId('member_id')->constrained()->cascadeOnDelete();
    $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
    $table->foreignId('rank_level_id')->constrained()->cascadeOnDelete();
    $table->foreignId('grading_exam_id')->nullable()->constrained()->nullOnDelete();
    $table->date('promoted_at');
    $table->text('notes')->nullable();
    $table->timestamps();

    $table->index('member_id');
    $table->index('perguruan_id');
});
```

---

### `member_transfers`

Riwayat perpindahan rayon anggota.

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| `id` | `BIGINT UNSIGNED` | PK AI | — |
| `member_id` | `BIGINT UNSIGNED` | FK → members | — |
| `perguruan_id` | `BIGINT UNSIGNED` | FK → perguruans | — |
| `from_rayon_id` | `BIGINT UNSIGNED` | FK → rayons | Rayon asal |
| `to_rayon_id` | `BIGINT UNSIGNED` | FK → rayons | Rayon tujuan |
| `transferred_at` | `DATE` | NOT NULL | — |
| `reason` | `TEXT` | nullable | Alasan pindah |
| `transferred_by` | `BIGINT UNSIGNED` | FK → users | User yang memproses |
| `created_at` | `TIMESTAMP` | nullable | — |
| `updated_at` | `TIMESTAMP` | nullable | — |

```php
Schema::create('member_transfers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('member_id')->constrained()->cascadeOnDelete();
    $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
    $table->foreignId('from_rayon_id')->constrained('rayons')->cascadeOnDelete();
    $table->foreignId('to_rayon_id')->constrained('rayons')->cascadeOnDelete();
    $table->date('transferred_at');
    $table->text('reason')->nullable();
    $table->foreignId('transferred_by')->constrained('users')->cascadeOnDelete();
    $table->timestamps();

    $table->index('member_id');
    $table->index('perguruan_id');
});
```

---

## 8. Tingkatan (Belt/Rank Levels)

> **Desain**: Tabel `rank_levels` terikat per perguruan (`perguruan_id`) karena setiap perguruan punya sistem tingkatan dan persyaratan berbeda. Bukan di master_data karena punya relasi dan aturan kompleks tersendiri.

---

### `rank_levels`

Definisi tingkatan sabuk per perguruan.

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| `id` | `BIGINT UNSIGNED` | PK AI | — |
| `perguruan_id` | `BIGINT UNSIGNED` | FK → perguruans | Scope per perguruan |
| `name` | `VARCHAR(100)` | NOT NULL | Nama tingkatan (e.g. `"Sabuk Hijau"`) |
| `code` | `VARCHAR(20)` | nullable | Kode singkat (e.g. `"SH"`) |
| `color` | `VARCHAR(50)` | nullable | Warna sabuk (e.g. `"green"`) |
| `order_level` | `TINYINT UNSIGNED` | NOT NULL | Urutan (1 = terendah) |
| `description` | `TEXT` | nullable | Deskripsi tingkatan |
| `is_active` | `BOOLEAN` | DEFAULT true | — |
| `created_at` | `TIMESTAMP` | nullable | — |
| `updated_at` | `TIMESTAMP` | nullable | — |

```php
Schema::create('rank_levels', function (Blueprint $table) {
    $table->id();
    $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
    $table->string('name', 100);
    $table->string('code', 20)->nullable();
    $table->string('color', 50)->nullable();
    $table->tinyInteger('order_level')->unsigned();
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->index('perguruan_id');
    $table->unique(['perguruan_id', 'order_level']);
    $table->unique(['perguruan_id', 'code']);
});
```

---

### `rank_requirements`

Persyaratan kenaikan tingkat per perguruan. Fleksibel karena tiap perguruan bisa beda.

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| `id` | `BIGINT UNSIGNED` | PK AI | — |
| `perguruan_id` | `BIGINT UNSIGNED` | FK → perguruans | — |
| `rank_level_id` | `BIGINT UNSIGNED` | FK → rank_levels | Tingkat yang **dituju** |
| `min_attendance_count` | `SMALLINT UNSIGNED` | DEFAULT 0 | Minimal kehadiran latihan |
| `min_active_months` | `TINYINT UNSIGNED` | DEFAULT 0 | Minimal bulan aktif di tingkat sebelumnya |
| `min_age` | `TINYINT UNSIGNED` | nullable | Minimal usia (tahun) |
| `required_skills` | `JSON` | nullable | Daftar jurus/teknik wajib dikuasai |
| `additional_requirements` | `TEXT` | nullable | Persyaratan lain dalam teks bebas |
| `exam_fee` | `DECIMAL(12,2)` | DEFAULT 0 | Biaya ujian |
| `created_at` | `TIMESTAMP` | nullable | — |
| `updated_at` | `TIMESTAMP` | nullable | — |

```php
Schema::create('rank_requirements', function (Blueprint $table) {
    $table->id();
    $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
    $table->foreignId('rank_level_id')->constrained()->cascadeOnDelete();
    $table->smallInteger('min_attendance_count')->unsigned()->default(0);
    $table->tinyInteger('min_active_months')->unsigned()->default(0);
    $table->tinyInteger('min_age')->unsigned()->nullable();
    $table->json('required_skills')->nullable();
    $table->text('additional_requirements')->nullable();
    $table->decimal('exam_fee', 12, 2)->default(0);
    $table->timestamps();

    $table->index('perguruan_id');
    $table->unique(['perguruan_id', 'rank_level_id']);
});
```

---

## 9. Module — Latihan (Training)

### `training_schedules`

Jadwal latihan per rayon.

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| `id` | `BIGINT UNSIGNED` | PK AI | — |
| `perguruan_id` | `BIGINT UNSIGNED` | FK → perguruans | — |
| `rayon_id` | `BIGINT UNSIGNED` | FK → rayons | — |
| `title` | `VARCHAR(150)` | NOT NULL | Judul/nama sesi latihan |
| `day_of_week` | `TINYINT UNSIGNED` | NOT NULL | 0=Minggu, 1=Senin, ..., 6=Sabtu |
| `start_time` | `TIME` | NOT NULL | — |
| `end_time` | `TIME` | NOT NULL | — |
| `location` | `VARCHAR(255)` | nullable | Lokasi latihan |
| `notes` | `TEXT` | nullable | — |
| `is_active` | `BOOLEAN` | DEFAULT true | — |
| `created_at` | `TIMESTAMP` | nullable | — |
| `updated_at` | `TIMESTAMP` | nullable | — |

```php
Schema::create('training_schedules', function (Blueprint $table) {
    $table->id();
    $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
    $table->foreignId('rayon_id')->constrained()->cascadeOnDelete();
    $table->string('title', 150);
    $table->tinyInteger('day_of_week')->unsigned();
    $table->time('start_time');
    $table->time('end_time');
    $table->string('location')->nullable();
    $table->text('notes')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->index('perguruan_id');
    $table->index('rayon_id');
});
```

---

### `training_sessions`

Sesi latihan aktual (realisasi jadwal).

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| `id` | `BIGINT UNSIGNED` | PK AI | — |
| `perguruan_id` | `BIGINT UNSIGNED` | FK → perguruans | — |
| `rayon_id` | `BIGINT UNSIGNED` | FK → rayons | — |
| `training_schedule_id` | `BIGINT UNSIGNED` | FK nullable → training_schedules | Bisa latihan insidental |
| `session_date` | `DATE` | NOT NULL | — |
| `start_time` | `TIME` | NOT NULL | — |
| `end_time` | `TIME` | nullable | — |
| `topic` | `VARCHAR(255)` | nullable | Materi latihan hari ini |
| `trainer_user_id` | `BIGINT UNSIGNED` | FK → users | Pelatih yang memimpin |
| `notes` | `TEXT` | nullable | — |
| `created_at` | `TIMESTAMP` | nullable | — |
| `updated_at` | `TIMESTAMP` | nullable | — |

```php
Schema::create('training_sessions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
    $table->foreignId('rayon_id')->constrained()->cascadeOnDelete();
    $table->foreignId('training_schedule_id')->nullable()->constrained()->nullOnDelete();
    $table->date('session_date');
    $table->time('start_time');
    $table->time('end_time')->nullable();
    $table->string('topic')->nullable();
    $table->foreignId('trainer_user_id')->constrained('users')->cascadeOnDelete();
    $table->text('notes')->nullable();
    $table->timestamps();

    $table->index('perguruan_id');
    $table->index('rayon_id');
    $table->index('session_date');
});
```

---

### `attendances`

Absensi anggota per sesi latihan.

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| `id` | `BIGINT UNSIGNED` | PK AI | — |
| `perguruan_id` | `BIGINT UNSIGNED` | FK → perguruans | — |
| `training_session_id` | `BIGINT UNSIGNED` | FK → training_sessions | — |
| `member_id` | `BIGINT UNSIGNED` | FK → members | — |
| `status` | `ENUM('present','absent','excused','late')` | NOT NULL DEFAULT `'present'` | — |
| `notes` | `VARCHAR(255)` | nullable | Keterangan (e.g. alasan izin) |
| `created_at` | `TIMESTAMP` | nullable | — |
| `updated_at` | `TIMESTAMP` | nullable | — |

```php
Schema::create('attendances', function (Blueprint $table) {
    $table->id();
    $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
    $table->foreignId('training_session_id')->constrained()->cascadeOnDelete();
    $table->foreignId('member_id')->constrained()->cascadeOnDelete();
    $table->enum('status', ['present', 'absent', 'excused', 'late'])->default('present');
    $table->string('notes')->nullable();
    $table->timestamps();

    $table->unique(['training_session_id', 'member_id']);
    $table->index('perguruan_id');
    $table->index('member_id');
});
```

---

## 10. Module — Ujian Kenaikan Tingkat (Grading Exam)

### `grading_exams`

Ujian kenaikan tingkat yang diselenggarakan.

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| `id` | `BIGINT UNSIGNED` | PK AI | — |
| `perguruan_id` | `BIGINT UNSIGNED` | FK → perguruans | — |
| `name` | `VARCHAR(150)` | NOT NULL | Nama ujian (e.g. `"UKT Periode I 2025"`) |
| `target_rank_level_id` | `BIGINT UNSIGNED` | FK → rank_levels | Tingkat yang diujikan |
| `exam_date` | `DATE` | NOT NULL | — |
| `location` | `VARCHAR(255)` | nullable | — |
| `examiner_name` | `VARCHAR(150)` | nullable | Nama penguji |
| `max_participants` | `SMALLINT UNSIGNED` | nullable | Kuota peserta |
| `status` | `ENUM('draft','open','closed','done')` | DEFAULT `'draft'` | — |
| `notes` | `TEXT` | nullable | — |
| `created_at` | `TIMESTAMP` | nullable | — |
| `updated_at` | `TIMESTAMP` | nullable | — |
| `deleted_at` | `TIMESTAMP` | nullable | Soft delete |

```php
Schema::create('grading_exams', function (Blueprint $table) {
    $table->id();
    $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
    $table->string('name', 150);
    $table->foreignId('target_rank_level_id')->constrained('rank_levels')->cascadeOnDelete();
    $table->date('exam_date');
    $table->string('location')->nullable();
    $table->string('examiner_name', 150)->nullable();
    $table->smallInteger('max_participants')->unsigned()->nullable();
    $table->enum('status', ['draft', 'open', 'closed', 'done'])->default('draft');
    $table->text('notes')->nullable();
    $table->timestamps();
    $table->softDeletes();

    $table->index('perguruan_id');
    $table->index('exam_date');
});
```

---

### `grading_exam_participants`

Peserta ujian kenaikan tingkat.

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| `id` | `BIGINT UNSIGNED` | PK AI | — |
| `perguruan_id` | `BIGINT UNSIGNED` | FK → perguruans | — |
| `grading_exam_id` | `BIGINT UNSIGNED` | FK → grading_exams | — |
| `member_id` | `BIGINT UNSIGNED` | FK → members | — |
| `current_rank_level_id` | `BIGINT UNSIGNED` | FK → rank_levels | Tingkat saat mendaftar |
| `score_theory` | `DECIMAL(5,2)` | nullable | Nilai teori |
| `score_practice` | `DECIMAL(5,2)` | nullable | Nilai praktek |
| `score_final` | `DECIMAL(5,2)` | nullable | Nilai akhir (bisa avg atau custom) |
| `result` | `ENUM('pass','fail','absent')` | nullable | Hasil ujian |
| `certificate_number` | `VARCHAR(100)` | nullable UNIQUE | Nomor sertifikat |
| `certificate_issued_at` | `DATE` | nullable | — |
| `notes` | `TEXT` | nullable | — |
| `registered_by` | `BIGINT UNSIGNED` | FK → users | Admin yang mendaftarkan |
| `created_at` | `TIMESTAMP` | nullable | — |
| `updated_at` | `TIMESTAMP` | nullable | — |

```php
Schema::create('grading_exam_participants', function (Blueprint $table) {
    $table->id();
    $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
    $table->foreignId('grading_exam_id')->constrained()->cascadeOnDelete();
    $table->foreignId('member_id')->constrained()->cascadeOnDelete();
    $table->foreignId('current_rank_level_id')->constrained('rank_levels')->cascadeOnDelete();
    $table->decimal('score_theory', 5, 2)->nullable();
    $table->decimal('score_practice', 5, 2)->nullable();
    $table->decimal('score_final', 5, 2)->nullable();
    $table->enum('result', ['pass', 'fail', 'absent'])->nullable();
    $table->string('certificate_number', 100)->nullable()->unique();
    $table->date('certificate_issued_at')->nullable();
    $table->text('notes')->nullable();
    $table->foreignId('registered_by')->constrained('users')->cascadeOnDelete();
    $table->timestamps();

    $table->unique(['grading_exam_id', 'member_id']);
    $table->index('perguruan_id');
    $table->index('member_id');
});
```

---

## 11. Module — Event

### `events`

Event yang diselenggarakan perguruan (kejuaraan, seminar, latihan bersama, dll).

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| `id` | `BIGINT UNSIGNED` | PK AI | — |
| `perguruan_id` | `BIGINT UNSIGNED` | FK → perguruans | Penyelenggara |
| `event_type` | `VARCHAR(30)` | NOT NULL | FK → master_values (slug: event_type) |
| `name` | `VARCHAR(200)` | NOT NULL | Nama event |
| `description` | `TEXT` | nullable | — |
| `start_date` | `DATE` | NOT NULL | — |
| `end_date` | `DATE` | NOT NULL | — |
| `registration_open_at` | `DATETIME` | nullable | Pembukaan pendaftaran |
| `registration_close_at` | `DATETIME` | nullable | Penutupan pendaftaran |
| `location_name` | `VARCHAR(200)` | nullable | Nama venue |
| `street_address` | `TEXT` | nullable | — |
| `village_id` | `BIGINT UNSIGNED` | FK nullable → villages | Desa/kelurahan |
| `district_id` | `BIGINT UNSIGNED` | FK nullable → districts | Kecamatan |
| `regency_id` | `BIGINT UNSIGNED` | FK nullable → regencies | Kabupaten/kota |
| `province_id` | `BIGINT UNSIGNED` | FK nullable → provinces | Provinsi |
| `max_participants` | `SMALLINT UNSIGNED` | nullable | Kuota total |
| `registration_fee` | `DECIMAL(12,2)` | DEFAULT 0 | — |
| `poster` | `VARCHAR(255)` | nullable | — |
| `status` | `ENUM('draft','published','closed','done','cancelled')` | DEFAULT `'draft'` | — |
| `created_by` | `BIGINT UNSIGNED` | FK → users | — |
| `created_at` | `TIMESTAMP` | nullable | — |
| `updated_at` | `TIMESTAMP` | nullable | — |
| `deleted_at` | `TIMESTAMP` | nullable | Soft delete |

```php
Schema::create('events', function (Blueprint $table) {
    $table->id();
    $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
    $table->string('event_type', 30);
    $table->string('name', 200);
    $table->text('description')->nullable();
    $table->date('start_date');
    $table->date('end_date');
    $table->dateTime('registration_open_at')->nullable();
    $table->dateTime('registration_close_at')->nullable();
    $table->string('location_name', 200)->nullable();
    $table->text('street_address')->nullable();
    $table->foreignId('village_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('regency_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('province_id')->nullable()->constrained()->nullOnDelete();
    $table->smallInteger('max_participants')->unsigned()->nullable();
    $table->decimal('registration_fee', 12, 2)->default(0);
    $table->string('poster')->nullable();
    $table->enum('status', ['draft', 'published', 'closed', 'done', 'cancelled'])->default('draft');
    $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
    $table->timestamps();
    $table->softDeletes();

    $table->index('perguruan_id');
    $table->index('status');
    $table->index('start_date');
});
```

---

### `event_registrations`

Pendaftaran anggota ke event. Admin perguruan bisa daftarkan banyak anggota sekaligus.

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| `id` | `BIGINT UNSIGNED` | PK AI | — |
| `event_id` | `BIGINT UNSIGNED` | FK → events | — |
| `perguruan_id` | `BIGINT UNSIGNED` | FK → perguruans | Perguruan peserta |
| `member_id` | `BIGINT UNSIGNED` | FK → members | — |
| `registration_number` | `VARCHAR(50)` | UNIQUE nullable | Nomor daftar (auto-generate) |
| `status` | `ENUM('pending','confirmed','cancelled')` | DEFAULT `'pending'` | — |
| `payment_status` | `ENUM('unpaid','paid','waived')` | DEFAULT `'unpaid'` | — |
| `notes` | `TEXT` | nullable | — |
| `registered_by` | `BIGINT UNSIGNED` | FK → users | Admin yang mendaftarkan |
| `confirmed_at` | `TIMESTAMP` | nullable | — |
| `created_at` | `TIMESTAMP` | nullable | — |
| `updated_at` | `TIMESTAMP` | nullable | — |

```php
Schema::create('event_registrations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('event_id')->constrained()->cascadeOnDelete();
    $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
    $table->foreignId('member_id')->constrained()->cascadeOnDelete();
    $table->string('registration_number', 50)->nullable()->unique();
    $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
    $table->enum('payment_status', ['unpaid', 'paid', 'waived'])->default('unpaid');
    $table->text('notes')->nullable();
    $table->foreignId('registered_by')->constrained('users')->cascadeOnDelete();
    $table->timestamp('confirmed_at')->nullable();
    $table->timestamps();

    $table->unique(['event_id', 'member_id']);
    $table->index('perguruan_id');
    $table->index('event_id');
    $table->index('member_id');
});
```

---

## 12. Module — Pertandingan (Competition)

### `competitions`

Pertandingan yang diadakan dalam suatu event.

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| `id` | `BIGINT UNSIGNED` | PK AI | — |
| `event_id` | `BIGINT UNSIGNED` | FK → events | Bagian dari event mana |
| `perguruan_id` | `BIGINT UNSIGNED` | FK → perguruans | Penyelenggara |
| `name` | `VARCHAR(200)` | NOT NULL | Nama kompetisi |
| `category_type` | `VARCHAR(30)` | NOT NULL | FK → master_values (slug: competition_category) |
| `age_category` | `VARCHAR(30)` | nullable | FK → master_values (slug: age_category) |
| `gender` | `VARCHAR(10)` | nullable | Jenis kelamin peserta |
| `min_weight` | `DECIMAL(5,2)` | nullable | Berat minimal (kg) |
| `max_weight` | `DECIMAL(5,2)` | nullable | Berat maksimal (kg) |
| `bracket_type` | `ENUM('single_elimination','double_elimination','round_robin')` | DEFAULT `'single_elimination'` | — |
| `status` | `ENUM('draft','open','ongoing','done')` | DEFAULT `'draft'` | — |
| `created_at` | `TIMESTAMP` | nullable | — |
| `updated_at` | `TIMESTAMP` | nullable | — |

```php
Schema::create('competitions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('event_id')->constrained()->cascadeOnDelete();
    $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
    $table->string('name', 200);
    $table->string('category_type', 30);
    $table->string('age_category', 30)->nullable();
    $table->string('gender', 10)->nullable();
    $table->decimal('min_weight', 5, 2)->nullable();
    $table->decimal('max_weight', 5, 2)->nullable();
    $table->enum('bracket_type', ['single_elimination', 'double_elimination', 'round_robin'])
          ->default('single_elimination');
    $table->enum('status', ['draft', 'open', 'ongoing', 'done'])->default('draft');
    $table->timestamps();

    $table->index('event_id');
    $table->index('perguruan_id');
});
```

---

### `competition_participants`

Peserta per kompetisi.

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| `id` | `BIGINT UNSIGNED` | PK AI | — |
| `competition_id` | `BIGINT UNSIGNED` | FK → competitions | — |
| `member_id` | `BIGINT UNSIGNED` | FK → members | — |
| `perguruan_id` | `BIGINT UNSIGNED` | FK → perguruans | Kontingen |
| `rayon_id` | `BIGINT UNSIGNED` | FK → rayons | — |
| `weight_actual` | `DECIMAL(5,2)` | nullable | Berat timbang aktual (kg) |
| `seeding` | `TINYINT UNSIGNED` | nullable | Nomor unggulan |
| `status` | `ENUM('registered','verified','disqualified')` | DEFAULT `'registered'` | — |
| `created_at` | `TIMESTAMP` | nullable | — |
| `updated_at` | `TIMESTAMP` | nullable | — |

```php
Schema::create('competition_participants', function (Blueprint $table) {
    $table->id();
    $table->foreignId('competition_id')->constrained()->cascadeOnDelete();
    $table->foreignId('member_id')->constrained()->cascadeOnDelete();
    $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
    $table->foreignId('rayon_id')->constrained()->cascadeOnDelete();
    $table->decimal('weight_actual', 5, 2)->nullable();
    $table->tinyInteger('seeding')->unsigned()->nullable();
    $table->enum('status', ['registered', 'verified', 'disqualified'])->default('registered');
    $table->timestamps();

    $table->unique(['competition_id', 'member_id']);
    $table->index('competition_id');
    $table->index('perguruan_id');
});
```

---

### `competition_brackets`

Bagan pertandingan per ronde.

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| `id` | `BIGINT UNSIGNED` | PK AI | — |
| `competition_id` | `BIGINT UNSIGNED` | FK → competitions | — |
| `round` | `TINYINT UNSIGNED` | NOT NULL | Babak ke- (1=perempat final, dst) |
| `match_number` | `SMALLINT UNSIGNED` | NOT NULL | Nomor pertandingan di babak ini |
| `participant_a_id` | `BIGINT UNSIGNED` | FK nullable → competition_participants | — |
| `participant_b_id` | `BIGINT UNSIGNED` | FK nullable → competition_participants | — |
| `winner_id` | `BIGINT UNSIGNED` | FK nullable → competition_participants | — |
| `score_a` | `VARCHAR(20)` | nullable | Skor/poin peserta A |
| `score_b` | `VARCHAR(20)` | nullable | Skor/poin peserta B |
| `match_datetime` | `DATETIME` | nullable | — |
| `notes` | `TEXT` | nullable | Catatan wasit |
| `created_at` | `TIMESTAMP` | nullable | — |
| `updated_at` | `TIMESTAMP` | nullable | — |

```php
Schema::create('competition_brackets', function (Blueprint $table) {
    $table->id();
    $table->foreignId('competition_id')->constrained()->cascadeOnDelete();
    $table->tinyInteger('round')->unsigned();
    $table->smallInteger('match_number')->unsigned();
    $table->foreignId('participant_a_id')->nullable()->constrained('competition_participants')->nullOnDelete();
    $table->foreignId('participant_b_id')->nullable()->constrained('competition_participants')->nullOnDelete();
    $table->foreignId('winner_id')->nullable()->constrained('competition_participants')->nullOnDelete();
    $table->string('score_a', 20)->nullable();
    $table->string('score_b', 20)->nullable();
    $table->dateTime('match_datetime')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();

    $table->index('competition_id');
    $table->unique(['competition_id', 'round', 'match_number']);
});
```

---

### `competition_medals`

Rekap perolehan medali per kontingen/perguruan.

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| `id` | `BIGINT UNSIGNED` | PK AI | — |
| `competition_id` | `BIGINT UNSIGNED` | FK → competitions | — |
| `perguruan_id` | `BIGINT UNSIGNED` | FK → perguruans | — |
| `member_id` | `BIGINT UNSIGNED` | FK → members | — |
| `medal_type` | `ENUM('gold','silver','bronze')` | NOT NULL | — |
| `created_at` | `TIMESTAMP` | nullable | — |
| `updated_at` | `TIMESTAMP` | nullable | — |

```php
Schema::create('competition_medals', function (Blueprint $table) {
    $table->id();
    $table->foreignId('competition_id')->constrained()->cascadeOnDelete();
    $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
    $table->foreignId('member_id')->constrained()->cascadeOnDelete();
    $table->enum('medal_type', ['gold', 'silver', 'bronze']);
    $table->timestamps();

    $table->index('competition_id');
    $table->index('perguruan_id');
});
```

---

## 13. Module — Keuangan (Finance)

### `billing_types`

Jenis tagihan yang bisa dikonfigurasi per perguruan.

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| `id` | `BIGINT UNSIGNED` | PK AI | — |
| `perguruan_id` | `BIGINT UNSIGNED` | FK → perguruans | — |
| `name` | `VARCHAR(100)` | NOT NULL | e.g. `"Iuran Bulanan"`, `"Biaya Seragam"` |
| `amount` | `DECIMAL(12,2)` | NOT NULL | Nominal default |
| `is_recurring` | `BOOLEAN` | DEFAULT false | Tagihan berulang? |
| `recurrence_period` | `ENUM('monthly','quarterly','yearly')` | nullable | — |
| `is_active` | `BOOLEAN` | DEFAULT true | — |
| `created_at` | `TIMESTAMP` | nullable | — |
| `updated_at` | `TIMESTAMP` | nullable | — |

```php
Schema::create('billing_types', function (Blueprint $table) {
    $table->id();
    $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
    $table->string('name', 100);
    $table->decimal('amount', 12, 2);
    $table->boolean('is_recurring')->default(false);
    $table->enum('recurrence_period', ['monthly', 'quarterly', 'yearly'])->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->index('perguruan_id');
});
```

---

### `invoices`

Tagihan per anggota.

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| `id` | `BIGINT UNSIGNED` | PK AI | — |
| `perguruan_id` | `BIGINT UNSIGNED` | FK → perguruans | — |
| `member_id` | `BIGINT UNSIGNED` | FK → members | — |
| `billing_type_id` | `BIGINT UNSIGNED` | FK → billing_types | — |
| `invoice_number` | `VARCHAR(50)` | UNIQUE NOT NULL | Auto-generate |
| `amount` | `DECIMAL(12,2)` | NOT NULL | — |
| `due_date` | `DATE` | NOT NULL | Jatuh tempo |
| `period_month` | `TINYINT UNSIGNED` | nullable | Bulan tagihan (1-12) |
| `period_year` | `SMALLINT UNSIGNED` | nullable | Tahun tagihan |
| `status` | `ENUM('unpaid','paid','overdue','cancelled')` | DEFAULT `'unpaid'` | — |
| `notes` | `TEXT` | nullable | — |
| `created_by` | `BIGINT UNSIGNED` | FK → users | — |
| `created_at` | `TIMESTAMP` | nullable | — |
| `updated_at` | `TIMESTAMP` | nullable | — |

```php
Schema::create('invoices', function (Blueprint $table) {
    $table->id();
    $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
    $table->foreignId('member_id')->constrained()->cascadeOnDelete();
    $table->foreignId('billing_type_id')->constrained()->cascadeOnDelete();
    $table->string('invoice_number', 50)->unique();
    $table->decimal('amount', 12, 2);
    $table->date('due_date');
    $table->tinyInteger('period_month')->unsigned()->nullable();
    $table->smallInteger('period_year')->unsigned()->nullable();
    $table->enum('status', ['unpaid', 'paid', 'overdue', 'cancelled'])->default('unpaid');
    $table->text('notes')->nullable();
    $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
    $table->timestamps();

    $table->index('perguruan_id');
    $table->index('member_id');
    $table->index('status');
    $table->index('due_date');
});
```

---

### `payments`

Bukti pembayaran / konfirmasi.

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| `id` | `BIGINT UNSIGNED` | PK AI | — |
| `invoice_id` | `BIGINT UNSIGNED` | FK → invoices | — |
| `perguruan_id` | `BIGINT UNSIGNED` | FK → perguruans | — |
| `payment_method` | `VARCHAR(30)` | NOT NULL | FK → master_values (slug: payment_method) |
| `amount_paid` | `DECIMAL(12,2)` | NOT NULL | — |
| `paid_at` | `DATETIME` | NOT NULL | Waktu bayar |
| `reference_number` | `VARCHAR(100)` | nullable | Nomor referensi transfer |
| `proof_file` | `VARCHAR(255)` | nullable | Foto bukti bayar |
| `confirmed_by` | `BIGINT UNSIGNED` | FK nullable → users | Admin yang konfirmasi |
| `confirmed_at` | `TIMESTAMP` | nullable | — |
| `notes` | `TEXT` | nullable | — |
| `created_at` | `TIMESTAMP` | nullable | — |
| `updated_at` | `TIMESTAMP` | nullable | — |

```php
Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
    $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
    $table->string('payment_method', 30);
    $table->decimal('amount_paid', 12, 2);
    $table->dateTime('paid_at');
    $table->string('reference_number', 100)->nullable();
    $table->string('proof_file')->nullable();
    $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamp('confirmed_at')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();

    $table->index('invoice_id');
    $table->index('perguruan_id');
});
```

---

## 14. Module — Notifikasi

### `notifications`

Log notifikasi yang dikirim ke pengguna.

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| `id` | `CHAR(36)` | PK | UUID |
| `type` | `VARCHAR(255)` | NOT NULL | Class notifikasi |
| `notifiable_type` | `VARCHAR(255)` | NOT NULL | Morphable type (e.g. `App\Modules\Auth\Domain\Models\User`) |
| `notifiable_id` | `BIGINT UNSIGNED` | NOT NULL | — |
| `data` | `JSON` | NOT NULL | Payload notifikasi |
| `channel` | `ENUM('database','email','push','whatsapp')` | DEFAULT `'database'` | — |
| `read_at` | `TIMESTAMP` | nullable | — |
| `sent_at` | `TIMESTAMP` | nullable | — |
| `created_at` | `TIMESTAMP` | nullable | — |
| `updated_at` | `TIMESTAMP` | nullable | — |

```php
Schema::create('notifications', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('type');
    $table->morphs('notifiable');     // notifiable_type + notifiable_id + index
    $table->json('data');
    $table->enum('channel', ['database', 'email', 'push', 'whatsapp'])->default('database');
    $table->timestamp('read_at')->nullable();
    $table->timestamp('sent_at')->nullable();
    $table->timestamps();
});
```

---

## 15. ERD Relationship Summary

```
provinces         ──< regencies ──< districts ──< villages
                                                      │
perguruans ───────────────────────────────────────────┤ (alamat)
    │                                                  │
    ├──< rayons ────────────────────────────────────────┤ (alamat)
    │       │                                           │
    │       └──< members ────────────────────────────────┤ (alamat)
    │               │
    │               ├──< member_rank_histories
    │               ├──< member_transfers
    │               ├──< attendances
    │               ├──< grading_exam_participants
    │               ├──< event_registrations
    │               ├──< competition_participants
    │               └──< invoices ──< payments
    │
    ├──< users (admin_perguruan, pelatih_rayon)
    │
    ├──< rank_levels ──< rank_requirements
    │
    ├──< training_schedules ──< training_sessions ──< attendances
    │
    ├──< grading_exams ──< grading_exam_participants
    │
    ├──< events ──< event_registrations
    │       │
    │       └──< competitions ──< competition_participants
    │                       └──< competition_brackets
    │                       └──< competition_medals
    │
    └──< billing_types ──< invoices ──< payments

master_data ──< master_values   (global, no perguruan_id)
```

---

## 16. Migration Order

Urutan harus diikuti untuk menghindari FK constraint error:

```
1.  provinces
2.  regencies
3.  districts
4.  villages
5.  master_data
6.  master_values
7.  perguruans
8.  rayons
9.  users
    └── (spatie: roles, permissions, model_has_roles, dll — via package migration)
10. rank_levels
11. rank_requirements
12. members
13. member_rank_histories
14. member_transfers
15. training_schedules
16. training_sessions
17. attendances
18. grading_exams
19. grading_exam_participants
20. events
21. event_registrations
22. competitions
23. competition_participants
24. competition_brackets
25. competition_medals
26. billing_types
27. invoices
28. payments
29. notifications
```

---

> **Catatan akhir**:
> - Semua tabel dengan data sensitif perguruan wajib ditambahkan `perguruan_id` dan scope via `BelongsToPerguruanScope`.
> - Kolom `status` yang nilainya banyak dan bisa bertambah disimpan sebagai `VARCHAR` referencing `master_values.value`. Gunakan `ENUM` hanya untuk status yang benar-benar fixed (seperti `attendances.status`).
> - Sumber wilayah yang disarankan: seed data Kemendagri. Simpan kode resmi di kolom `code`, bukan sebagai primary key, agar relasi internal tetap stabil jika format kode sumber berubah.
