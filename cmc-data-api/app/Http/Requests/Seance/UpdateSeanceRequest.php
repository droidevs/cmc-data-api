<?php

namespace App\Http\Requests\Seance;

use App\Models\Espace;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for updating an existing Séance (PATCH semantics).
 *
 * All fields are optional — only send what you want to change.
 * The espace conflict check excludes the current seance so that
 * re-saving the same espace on the same date/time is allowed.
 */
class UpdateSeanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $seance = $this->route('seance');

        return [
            'affectation_id' => ['sometimes', 'integer', 'exists:affectations,id'],

            'type' => ['sometimes', 'string', 'in:cours,cc,efm,exam'],

            'date' => ['sometimes', 'date', 'date_format:Y-m-d'],

            'time_range_id' => ['sometimes', 'integer', 'exists:time_ranges,id'],

            'espace_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:espaces,id',
                function (string $attribute, mixed $value, \Closure $fail) use ($seance) {
                    if ($value === null) {
                        return;
                    }

                    $espace = Espace::find($value);
                    if (! $espace) {
                        return;
                    }

                    // Use incoming values or fall back to the current seance values
                    $date        = $this->input('date', $seance?->date?->toDateString());
                    $timeRangeId = $this->input('time_range_id', $seance?->time_range_id);

                    if ($date && $timeRangeId && ! $espace->isAvailable($date, (int) $timeRangeId, $seance?->id)) {
                        $fail("Cet espace est déjà réservé pour cette date et ce créneau horaire.");
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'affectation_id.exists'  => 'Cette affectation n\'existe pas.',
            'type.in'                => 'Le type doit être : cours, cc, efm ou exam.',
            'date.date_format'       => 'La date doit être au format YYYY-MM-DD.',
            'time_range_id.exists'   => 'Ce créneau horaire n\'existe pas.',
            'espace_id.exists'       => 'Cet espace n\'existe pas.',
        ];
    }
}
