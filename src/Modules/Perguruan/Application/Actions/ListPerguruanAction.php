<?php

declare(strict_types=1);

namespace Modules\Perguruan\Application\Actions;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Perguruan\Domain\Models\Perguruan;

class ListPerguruanAction
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

        return Perguruan::query()
            ->when($search, function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('abbreviation', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
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
            'abbreviation',
            'email',
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
