<?php

namespace App\Http\Resources;

use App\Models\Espace;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Espace */
class EspaceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pole_id' => $this->pole_id,
            'libelle' => $this->libelle,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'pole' => new PoleResource($this->whenLoaded('pole')),
        ];
    }
}

