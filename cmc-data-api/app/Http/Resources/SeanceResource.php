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
            'id'             => $this->id,
            'affectation_id' => $this->affectation_id,

            // Type: cours | cc | efm | exam
            'type'           => $this->type,

            'date'           => $this->date?->toDateString(),
            'time_range_id'  => $this->time_range_id,

            // Nullable: online/synchronous sessions have no physical room
            'espace_id'      => $this->espace_id,

            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,

            // Relationships (loaded on demand)
            'affectation'    => new AffectationResource($this->whenLoaded('affectation')),
            'time_range'     => new TimeRangeResource($this->whenLoaded('timeRange')),
            'espace'         => new EspaceResource($this->whenLoaded('espace')),
            'notes'          => NoteResource::collection($this->whenLoaded('notes')),
        ];
    }
}
