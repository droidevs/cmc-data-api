<?php

namespace App\Http\Resources;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Note */
class NoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'seance_id' => $this->seance_id,
            'stagiaire_cef' => $this->stagiaire_cef,
            'valeur' => $this->valeur,
            'type' => $this->type,
            'decision' => $this->decision,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'seance' => new SeanceResource($this->whenLoaded('seance')),
            'stagiaire' => new StagiaireResource($this->whenLoaded('stagiaire')),
        ];
    }
}

