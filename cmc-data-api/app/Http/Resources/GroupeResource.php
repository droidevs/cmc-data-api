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
            'id' => $this->id,
            'annee_id' => $this->annee_id,
            'code' => $this->code,
            'effectif' => $this->effectif,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'annee' => new AnneeResource($this->whenLoaded('annee')),
            'stagiaires' => StagiaireResource::collection($this->whenLoaded('stagiaires')),
            'affectations' => AffectationResource::collection($this->whenLoaded('affectations')),
        ];
    }
}

