<?php

declare(strict_types=1);

namespace Src\Modules\Perguruan\Presentation\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PerguruanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // TODO: tambahkan field
        ];
    }
}