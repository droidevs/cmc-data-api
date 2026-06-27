<?php

namespace App\Http\Resources;

use App\Models\Groupe;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Groupe */
class GroupeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'annee_id'   => $this->annee_id,

            // Groupe column from AvancementProgramme (e.g. "DEV101", "DEVOWFS201")
            'code'       => $this->code,

            // Effectif Groupe from AvancementProgramme
            'effectif'   => $this->effectif,

            // Mode from AvancementProgramme (Résidentiel | Alternance)
            'mode'       => $this->mode,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Relationships (loaded on demand)
            'annee'        => new AnneeResource($this->whenLoaded('annee')),
            'stagiaires'   => StagiaireResource::collection($this->whenLoaded('stagiaires')),
            'affectations' => AffectationResource::collection($this->whenLoaded('affectations')),
            'avancements'  => AvancementResource::collection($this->whenLoaded('avancements')),
        ];
    }
}
