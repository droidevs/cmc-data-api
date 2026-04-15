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
            'code_module' => $this->code_module,
            'annee_id' => $this->annee_id,
            'libelle' => $this->libelle,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'annee' => new AnneeResource($this->whenLoaded('annee')),
            'affectations' => AffectationResource::collection($this->whenLoaded('affectations')),
        ];
    }
}

