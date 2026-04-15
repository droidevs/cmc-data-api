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
            'id' => $this->id,
            'groupe_id' => $this->groupe_id,
            'module_code' => $this->module_code,
            'formateur_mle' => $this->formateur_mle,
            'mode' => $this->mode,
            'mh_affecte' => $this->mh_affecte,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'groupe' => new GroupeResource($this->whenLoaded('groupe')),
            'module' => new ModuleResource($this->whenLoaded('module')),
            'formateur' => new FormateurResource($this->whenLoaded('formateur')),
            'seances' => SeanceResource::collection($this->whenLoaded('seances')),
        ];
    }
}

