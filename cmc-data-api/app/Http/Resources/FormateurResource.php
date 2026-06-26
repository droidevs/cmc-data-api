<?php

namespace App\Http\Resources;

use App\Models\Formateur;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Formateur */
class FormateurResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // Primary key (Mle from Base_Formateurs)
            'mle'           => $this->mle,
            'pole_id'       => $this->pole_id,

            // Nom et Prénom from Base_Formateurs
            'nom_prenom'    => $this->nom_prenom,

            // Statut: OFPPT | Vacataire | Contractuel
            'statut'        => $this->statut,

            'email_edu'     => $this->email_edu,

            // MHS: monthly teaching hour quota (default 26 from Base_Formateurs)
            'mhs'           => $this->mhs,

            // Mutualisé fields from Base_Formateurs
            'mutualise'     => $this->mutualise,
            'efp_mutualise' => $this->efp_mutualise,

            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,

            // Relationships (loaded on demand)
            'pole'          => new PoleResource($this->whenLoaded('pole')),
            'affectations'  => AffectationResource::collection($this->whenLoaded('affectations')),
        ];
    }
}
