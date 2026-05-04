<?php

declare(strict_types=1);

namespace Src\Modules\Perguruan\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class Perguruan extends Model
{
    protected $table = 'perguruans';

    protected $fillable = [
        'name',
        'abbreviation',
        'decree_number',
        'decree_date',
        'logo',
        'email',
        'phone',
        'website',
        'street_address',
        'village_id',
        'district_id',
        'regency_id',
        'province_id',
        'postal_code',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'decree_date' => 'date',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }
}
