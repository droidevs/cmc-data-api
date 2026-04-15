<?php

namespace App\Http\Resources;

use App\Models\Filiere;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Filiere */
class FiliereResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'code_filiere' => $this->code_filiere,
            'pole_id' => $this->pole_id,
            'niveau_id' => $this->niveau_id,
            'type_formation_id' => $this->type_formation_id,
            'libelle' => $this->libelle,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'pole' => new PoleResource($this->whenLoaded('pole')),
            'niveau' => new NiveauResource($this->whenLoaded('niveau')),
            'type_formation' => new TypeFormationResource($this->whenLoaded('typeFormation')),
            'annees' => AnneeResource::collection($this->whenLoaded('annees')),
        ];
    }
}

