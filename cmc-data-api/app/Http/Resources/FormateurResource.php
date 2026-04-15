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
            'mle' => $this->mle,
            'pole_id' => $this->pole_id,
            'nom_prenom' => $this->nom_prenom,
            'statut' => $this->statut,
            'email_edu' => $this->email_edu,
            'mhs' => $this->mhs,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'pole' => new PoleResource($this->whenLoaded('pole')),
            'affectations' => AffectationResource::collection($this->whenLoaded('affectations')),
        ];
    }
}

