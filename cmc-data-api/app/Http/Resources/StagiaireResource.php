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
            'cef' => $this->cef,
            'groupe_id' => $this->groupe_id,
            'cni' => $this->cni,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'date_naissance' => $this->date_naissance,
            'genre' => $this->genre,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'groupe' => new GroupeResource($this->whenLoaded('groupe')),
            'notes' => NoteResource::collection($this->whenLoaded('notes')),
        ];
    }
}

