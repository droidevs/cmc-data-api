<?php

namespace App\Http\Resources;

use App\Models\TypeFormation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin TypeFormation */
class TypeFormationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'libelle' => $this->libelle,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'filieres' => FiliereResource::collection($this->whenLoaded('filieres')),
        ];
    }
}

