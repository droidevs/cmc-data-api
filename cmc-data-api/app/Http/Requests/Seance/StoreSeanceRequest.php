<?php

namespace App\Http\Requests\Seance;

use App\Models\Espace;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for creating a new Séance.
 *
 * A séance is a scheduled teaching session tied to an Affectation
 * (which carries the groupe + module + formateur context).
 *
 * Type values mirror the BasePlateEvaluation "TYPE D'EPREUVE" and
 * general scheduling practice:
 *   cours  — regular class session (CDJ = Cours De Jour)
 *   cc     — contrôle continu (NB CC column in AvancementProgramme)
 *   efm    — épreuve de fin de module (Séance EFM column)
 *   exam   — examen (tp/th final evaluation)
 *
 * espace_id is nullable: synchronous/online sessions have no physical room.
 * When provided, we validate the espace is not already booked for the same
 * date + time_range_id (conflict check).
 */
class StoreSeanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'affectation_id' => ['required', 'integer', 'exists:affectations,id'],

            'type' => ['required', 'string', 'in:cours,cc,efm,exam'],

            'date' => ['required', 'date', 'date_format:Y-m-d'],

            'time_range_id' => ['required', 'integer', 'exists:time_ranges,id'],

            // Nullable: synchronous sessions have no physical room
            'espace_id' => [
                'nullable',
                'integer',
                'exists:espaces,id',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if ($value === null) {
                        return;
                    }

                    $espace = Espace::find($value);
                    if (! $espace) {
                        return; // already caught by exists rule
                    }

                    $date        = $this->input('date');
                    $timeRangeId = $this->input('time_range_id');

                    if ($date && $timeRangeId && ! $espace->isAvailable($date, (int) $timeRangeId)) {
                        $fail("Cet espace est déjà réservé pour cette date et ce créneau horaire.");
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'affectation_id.required' => 'L\'affectation est obligatoire.',
            'affectation_id.exists'   => 'Cette affectation n\'existe pas.',
            'type.required'           => 'Le type de séance est obligatoire.',
            'type.in'                 => 'Le type doit être : cours, cc, efm ou exam.',
            'date.required'           => 'La date est obligatoire.',
            'date.date_format'        => 'La date doit être au format YYYY-MM-DD.',
            'time_range_id.required'  => 'Le créneau horaire est obligatoire.',
            'time_range_id.exists'    => 'Ce créneau horaire n\'existe pas.',
            'espace_id.exists'        => 'Cet espace n\'existe pas.',
        ];
    }
}
