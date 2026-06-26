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
            'id'            => $this->id,
            'seance_id'     => $this->seance_id,

            // CEF (MatriculeEtudiant / CEF column in BasePlateEvaluation)
            'stagiaire_cef' => $this->stagiaire_cef,

            // Grade: 0–20 (NOTE PASS / MOY ANN / etc. from BasePlateEvaluation)
            'valeur'        => $this->valeur,

            // Type: cc | efm | tp | th | syn | exam
            // Derived from TYPE D'EPREUVE in BasePlateEvaluation
            'type'          => $this->type,

            // DECISION column: Admis | Redoublant | Abandon | Rattrapage
            'decision'      => $this->decision,

            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,

            // Relationships (loaded on demand)
            'seance'    => new SeanceResource($this->whenLoaded('seance')),
            'stagiaire' => new StagiaireResource($this->whenLoaded('stagiaire')),
        ];
    }
}
