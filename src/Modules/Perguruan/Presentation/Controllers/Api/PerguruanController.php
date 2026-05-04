<?php

declare(strict_types=1);

namespace Modules\Perguruan\Presentation\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Modules\Perguruan\Application\Actions\CreatePerguruanAction;
use Modules\Perguruan\Application\Actions\DeletePerguruanAction;
use Modules\Perguruan\Application\Actions\ListPerguruanAction;
use Modules\Perguruan\Application\Actions\UpdatePerguruanAction;
use Modules\Perguruan\Application\DTOs\PerguruanData;
use Modules\Perguruan\Domain\Models\Perguruan;
use Modules\Perguruan\Presentation\Requests\CreatePerguruanRequest;
use Modules\Perguruan\Presentation\Requests\UpdatePerguruanRequest;
use Modules\Perguruan\Presentation\Resources\PerguruanResource;
use Shared\Http\Controllers\ApiController;

class PerguruanController extends ApiController
{
    public function index(ListPerguruanAction $action): JsonResponse
    {
        $perguruans = $action->execute(request()->only([
            'search',
            'sort_by',
            'sort_dir',
            'per_page',
        ]));

        return $this->paginated(
            PerguruanResource::collection($perguruans),
            'Data perguruan berhasil diambil.',
        );
    }

    public function store(
        CreatePerguruanRequest $request,
        CreatePerguruanAction $action,
    ): JsonResponse {
        $perguruan = $action->execute(PerguruanData::fromArray($request->validated()));

        return $this->created(
            new PerguruanResource($perguruan),
            'Perguruan berhasil ditambahkan.',
        );
    }

    public function show(Perguruan $perguruan): JsonResponse
    {
        return $this->success(
            new PerguruanResource($perguruan),
            'Detail perguruan berhasil diambil.',
        );
    }

    public function update(
        UpdatePerguruanRequest $request,
        UpdatePerguruanAction $action,
        Perguruan $perguruan,
    ): JsonResponse {
        $perguruan = $action->execute(
            $perguruan,
            PerguruanData::fromArray($request->validated()),
        );

        return $this->success(
            new PerguruanResource($perguruan),
            'Perguruan berhasil diperbarui.',
        );
    }

    public function destroy(
        DeletePerguruanAction $action,
        Perguruan $perguruan,
    ): JsonResponse {
        $action->execute($perguruan);

        return $this->success(message: 'Perguruan berhasil dihapus.');
    }
}
