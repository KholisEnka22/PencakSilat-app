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
