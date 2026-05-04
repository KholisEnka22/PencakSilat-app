<?php

declare(strict_types=1);

namespace Shared\DTOs;

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
