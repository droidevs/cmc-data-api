<?php

namespace App\Http\Resources;

use App\Models\Affectation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Affectation */
class AffectationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'groupe_id'         => $this->groupe_id,
            'module_id'         => $this->module_id,
            'module_code'       => $this->module_code,

            // Présentiel trainer (Mle Affecté Présentiel Actif)
            'formateur_mle'     => $this->formateur_mle,

            // Synchronous trainer (Mle Affecté Syn Actif) — nullable
            'formateur_mle_syn' => $this->formateur_mle_syn,

            // Mode from AvancementProgramme (Résidentiel | Alternance)
            'mode'              => $this->mode,

            // Présentiel hours (MH Affectée Présentiel)
            'mh_affecte'        => $this->mh_affecte,

            // Synchronous hours (MH Affectée Sync) — nullable
            'mh_affecte_syn'    => $this->mh_affecte_syn,

            // Computed total (mh_affecte + mh_affecte_syn)
            'mh_totale'         => $this->mh_totale,

            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,

            // Relationships (loaded on demand)
            'groupe'            => new GroupeResource($this->whenLoaded('groupe')),
            'module'            => new ModuleResource($this->whenLoaded('module')),
            'formateur'         => new FormateurResource($this->whenLoaded('formateur')),
            'formateur_syn'     => new FormateurResource($this->whenLoaded('formateurSyn')),
            'seances'           => SeanceResource::collection($this->whenLoaded('seances')),
        ];
    }
}
