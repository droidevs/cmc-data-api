<?php

namespace App\Http\Resources;

use App\Models\TimeRange;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin TimeRange */
class TimeRangeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'seances' => SeanceResource::collection($this->whenLoaded('seances')),
        ];
    }
}

