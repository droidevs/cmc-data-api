<?php

namespace App\Http\Resources;

use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Module */
class ModuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // Primary key (Code Module from AvancementProgramme)
            'code_module' => $this->code_module,
            'annee_id'    => $this->annee_id,

            // Module column from AvancementProgramme
            'libelle'     => $this->libelle,

            // Régional column: O / N → true / false
            'regional'    => $this->regional,
            'mh_presentiel' => $this->mh_presentiel,
            'mh_syn'        => $this->mh_syn,
            'mh_asyn'       => $this->mh_asyn,
            'mh_totale'     => $this->mh_totale,
            'mh_planifiee_somme' => $this->mh_planifiee_somme,

            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,

            // Relationships (loaded on demand)
            'annee'        => new AnneeResource($this->whenLoaded('annee')),
            'affectations' => AffectationResource::collection($this->whenLoaded('affectations')),
            'avancements'  => AvancementResource::collection($this->whenLoaded('avancements')),
        ];
    }
}
