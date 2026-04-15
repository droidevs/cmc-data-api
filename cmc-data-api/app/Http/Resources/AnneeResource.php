<?php

namespace App\Http\Resources;

use App\Models\Annee;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Annee */
class AnneeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'filiere_code' => $this->filiere_code,
            'libelle' => $this->libelle,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'filiere' => new FiliereResource($this->whenLoaded('filiere')),
            'groupes' => GroupeResource::collection($this->whenLoaded('groupes')),
            'modules' => ModuleResource::collection($this->whenLoaded('modules')),
        ];
    }
}

