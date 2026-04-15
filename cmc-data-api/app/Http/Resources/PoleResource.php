<?php

namespace App\Http\Resources;

use App\Models\Pole;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Pole */
class PoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'libelle' => $this->libelle,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'espaces' => EspaceResource::collection($this->whenLoaded('espaces')),
            'formateurs' => FormateurResource::collection($this->whenLoaded('formateurs')),
            'filieres' => FiliereResource::collection($this->whenLoaded('filieres')),
        ];
    }
}

