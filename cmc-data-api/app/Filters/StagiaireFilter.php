<?php

declare(strict_types=1);

namespace App\Filters;

/**
 * Advanced filters for Stagiaire.
 *
 * Supported query params:
 *   q                 - free text search (nom, prenom, cef, cni)
 *   nom               - partial match on nom
 *   prenom            - partial match on prenom
 *   cef               - exact match
 *   cni               - exact match
 *   genre             - F | H
 *   actif             - 1 | 0
 *   groupe_id         - exact
 *   groupe_code       - partial match on groupes.code (joins)
 *   annee_id          - exact, via groupe.annee_id
 *   filiere_code      - via groupe.annee.filiere_code
 *   pole_id           - via groupe.annee.filiere.pole_id
 *   niveau_id         - via groupe.annee.filiere.niveau_id
 *   age_min / age_max - derived from date_naissance
 *   date_naissance_from / date_naissance_to
 *   sort              - cef|nom|prenom|date_naissance (prefix "-" for desc)
 */
class StagiaireFilter extends QueryFilter
{
    protected function filters(): array
    {
        return [
            'q' => 'search',
            'nom' => 'filterNom',
            'prenom' => 'filterPrenom',
            'cef' => 'filterCef',
            'cni' => 'filterCni',
            'genre' => 'filterGenre',
            'actif' => 'filterActif',
            'groupe_id' => 'filterGroupeId',
            'groupe_code' => 'filterGroupeCode',
            'annee_id' => 'filterAnneeId',
            'filiere_code' => 'filterFiliereCode',
            'pole_id' => 'filterPoleId',
            'niveau_id' => 'filterNiveauId',
            'age_min' => 'filterAgeMin',
            'age_max' => 'filterAgeMax',
            'date_naissance_from' => 'filterDateNaissanceFrom',
            'date_naissance_to' => 'filterDateNaissanceTo',
        ];
    }

    protected function sortable(): array
    {
        return ['cef', 'nom', 'prenom', 'date_naissance', 'created_at'];
    }

    protected function search(mixed $value): void
    {
        $term = (string) $value;
        $this->builder->where(function ($q) use ($term) {
            $q->where('nom', 'like', "%{$term}%")
                ->orWhere('prenom', 'like', "%{$term}%")
                ->orWhere('cef', 'like', "%{$term}%")
                ->orWhere('cni', 'like', "%{$term}%");
        });
    }

    protected function filterNom(mixed $value): void
    {
        $this->like('nom', $value);
    }

    protected function filterPrenom(mixed $value): void
    {
        $this->like('prenom', $value);
    }

    protected function filterCef(mixed $value): void
    {
        $this->exact('cef', $value);
    }

    protected function filterCni(mixed $value): void
    {
        $this->exact('cni', $value);
    }

    protected function filterGenre(mixed $value): void
    {
        $this->inList('genre', $value);
    }

    protected function filterActif(mixed $value): void
    {
        $this->boolean('actif', $value);
    }

    protected function filterGroupeId(mixed $value): void
    {
        $this->inList('groupe_id', $value);
    }

    protected function filterGroupeCode(mixed $value): void
    {
        $term = $value;
        $this->builder->whereHas('groupe', fn ($q) => $q->where('code', 'like', "%{$term}%"));
    }

    protected function filterAnneeId(mixed $value): void
    {
        $this->builder->whereHas('groupe', fn ($q) => $q->where('annee_id', $value));
    }

    protected function filterFiliereCode(mixed $value): void
    {
        $this->builder->whereHas('groupe.annee', fn ($q) => $q->where('filiere_code', $value));
    }

    protected function filterPoleId(mixed $value): void
    {
        $this->builder->whereHas('groupe.annee.filiere', fn ($q) => $q->where('pole_id', $value));
    }

    protected function filterNiveauId(mixed $value): void
    {
        $this->builder->whereHas('groupe.annee.filiere', fn ($q) => $q->where('niveau_id', $value));
    }

    protected function filterAgeMin(mixed $value): void
    {
        // age >= min  <=>  date_naissance <= today - min years
        $this->builder->whereDate('date_naissance', '<=', now()->subYears((int) $value)->toDateString());
    }

    protected function filterAgeMax(mixed $value): void
    {
        // age <= max  <=>  date_naissance >= today - (max+1) years
        $this->builder->whereDate('date_naissance', '>=', now()->subYears((int) $value + 1)->toDateString());
    }

    protected function filterDateNaissanceFrom(mixed $value): void
    {
        $this->dateFrom('date_naissance', $value);
    }

    protected function filterDateNaissanceTo(mixed $value): void
    {
        $this->dateTo('date_naissance', $value);
    }
}
