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
            'date' => $this->date,
            'time_range_id' => $this->time_range_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'affectation' => new AffectationResource($this->whenLoaded('affectation')),
            'time_range' => new TimeRangeResource($this->whenLoaded('timeRange')),
            'notes' => NoteResource::collection($this->whenLoaded('notes')),
        ];
    }
}

