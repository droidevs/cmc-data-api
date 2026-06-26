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
            'id'           => $this->id,
            'filiere_code' => $this->filiere_code,

            // Integer year (1 or 2) from AvancementProgramme "Année de formation"
            'libelle'      => $this->libelle,

            // Human-readable French label: "1ère année" / "2ème année"
            'label'        => $this->label,

            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,

            // Relationships (loaded on demand)
            'filiere' => new FiliereResource($this->whenLoaded('filiere')),
            'groupes' => GroupeResource::collection($this->whenLoaded('groupes')),
            'modules' => ModuleResource::collection($this->whenLoaded('modules')),
        ];
    }
}
