<?php

namespace App\Http\Resources;

use App\Models\Seance;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Seance */
class SeanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'affectation_id' => $this->affectation_id,
            'type' => $this->type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'affectation' => new AffectationResource($this->whenLoaded('affectation')),
            'date_seance' => new DateSeanceResource($this->whenLoaded('dateSeance')),
            'notes' => NoteResource::collection($this->whenLoaded('notes')),
        ];
    }
}

