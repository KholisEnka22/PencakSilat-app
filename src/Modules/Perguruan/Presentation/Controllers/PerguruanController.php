<?php

declare(strict_types=1);

namespace Modules\Perguruan\Presentation\Controllers;

use App\Http\Controllers\Controller;
use Modules\Perguruan\Application\Actions\CreatePerguruanAction;
use Modules\Perguruan\Application\Actions\DeletePerguruanAction;
use Modules\Perguruan\Application\Actions\ListPerguruanAction;
use Modules\Perguruan\Application\Actions\UpdatePerguruanAction;
use Modules\Perguruan\Application\DTOs\PerguruanData;
use Modules\Perguruan\Domain\Models\Perguruan;
use Modules\Perguruan\Presentation\Requests\CreatePerguruanRequest;
use Modules\Perguruan\Presentation\Requests\UpdatePerguruanRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PerguruanController extends Controller
{
    public function index(ListPerguruanAction $action): Response
    {
        return Inertia::render('Perguruan/Index', [
            'perguruans' => $action->execute(request()->only([
                'search',
                'sort_by',
                'sort_dir',
                'per_page',
            ])),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Perguruan/Create');
    }

    public function store(
        CreatePerguruanRequest $request,
        CreatePerguruanAction $action,
    ): RedirectResponse {
        $action->execute(PerguruanData::fromArray($request->validated()));

        return redirect()
            ->route('perguruan.index')
            ->with('success', 'Perguruan berhasil ditambahkan.');
    }

    public function show(Perguruan $perguruan): Response
    {
        return Inertia::render('Perguruan/Show', [
            'perguruan' => $perguruan,
        ]);
    }

    public function edit(Perguruan $perguruan): Response
    {
        return Inertia::render('Perguruan/Edit', [
            'perguruan' => $perguruan,
        ]);
    }

    public function update(
        UpdatePerguruanRequest $request,
        UpdatePerguruanAction $action,
        Perguruan $perguruan,
    ): RedirectResponse {
        $action->execute($perguruan, PerguruanData::fromArray($request->validated()));

        return redirect()
            ->route('perguruan.index')
            ->with('success', 'Perguruan berhasil diperbarui.');
    }

    public function destroy(
        DeletePerguruanAction $action,
        Perguruan $perguruan,
    ): RedirectResponse {
        $action->execute($perguruan);

        return redirect()
            ->route('perguruan.index')
            ->with('success', 'Perguruan berhasil dihapus.');
    }
}