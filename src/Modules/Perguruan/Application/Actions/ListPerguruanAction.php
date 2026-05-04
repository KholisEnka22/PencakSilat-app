<?php

declare(strict_types=1);

namespace Src\Modules\Perguruan\Application\Actions;

use Src\Modules\Perguruan\Domain\Models\Perguruan;
use Illuminate\Pagination\LengthAwarePaginator;

class ListPerguruanAction
{
    public function execute(array $filters = []): LengthAwarePaginator
    {
        $search  = $filters['search'] ?? null;
        $sortBy  = $filters['sort_by'] ?? 'created_at';
        $sortDir = $filters['sort_dir'] ?? 'desc';
        $perPage = (int) ($filters['per_page'] ?? 15);

        if (! in_array(strtolower((string) $sortDir), ['asc', 'desc'], true)) {
            $sortDir = 'desc';
        }

        return Perguruan::query()
            ->when(
                $search,
                fn ($query) => $query->where('name', 'like', "%{$search}%")
            )
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage);
    }
}