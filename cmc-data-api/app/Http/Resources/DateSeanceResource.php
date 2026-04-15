<?php

namespace App\Http\Resources;

use App\Models\DateSeance;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin DateSeance */
class DateSeanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'seance_id' => $this->seance_id,
            'date' => $this->date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'seance' => new SeanceResource($this->whenLoaded('seance')),
        ];
    }
}

