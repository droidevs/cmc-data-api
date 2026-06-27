<?php

namespace App\Http\Resources;

use App\Models\Avancement;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Avancement */
class AvancementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'groupe_id' => $this->groupe_id,
            'module_id' => $this->module_id,
            'mh_realisee_presentiel' => $this->mh_realisee_presentiel,
            'mh_realisee_syn' => $this->mh_realisee_syn,
            'mh_realisee_globale' => $this->mh_realisee_globale,
            'taux_realisation_presentiel' => $this->taux_realisation_presentiel,
            'taux_realisation_syn' => $this->taux_realisation_syn,
            'taux_realisation_globale' => $this->taux_realisation_globale,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'groupe' => new GroupeResource($this->whenLoaded('groupe')),
            'module' => new ModuleResource($this->whenLoaded('module')),
        ];
    }
}
