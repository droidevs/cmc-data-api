<?php

namespace App\Http\Resources;

use App\Models\Stagiaire;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Stagiaire */
class StagiaireResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // Primary key (MatriculeEtudiant)
            'cef'            => $this->cef,
            'groupe_id'      => $this->groupe_id,

            // Identity
            'cni'            => $this->cni,
            'nom'            => $this->nom,
            'prenom'         => $this->prenom,

            // Arabic name fields (Nom_Arabe / Prenom_arabe from lister_minimized)
            'nom_arabe'      => $this->nom_arabe,
            'prenom_arabe'   => $this->prenom_arabe,

            // Computed full name accessor
            'nom_complet'    => $this->nom_complet,

            'date_naissance' => $this->date_naissance?->toDateString(),
            'genre'          => $this->genre,

            // Contact (NTelelephone / Adresse from lister_minimized)
            'telephone'      => $this->telephone,
            'adresse'        => $this->adresse,

            // Academic
            'niveau_scolaire' => $this->niveau_scolaire,

            // EtudiantActif from lister_minimized
            'actif'          => $this->actif,

            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,

            // Relationships (loaded on demand)
            'groupe'         => new GroupeResource($this->whenLoaded('groupe')),
            'notes'          => NoteResource::collection($this->whenLoaded('notes')),
        ];
    }
}
