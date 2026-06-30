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
            // Human-readable French label: "1ère année" / "2ème année"
            'libelle'        => $this->libelle,

            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,

            // Relationships (loaded on demand)
            'filiere' => new FiliereResource($this->whenLoaded('filiere')),
            'groupes' => GroupeResource::collection($this->whenLoaded('groupes')),
            'modules' => ModuleResource::collection($this->whenLoaded('modules')),
        ];
    }
}
