<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates the multipart file upload for POST /api/v1/import.
 *
 * Required fields
 *   file  – the Excel / CSV file (.xlsx | .xls | .csv)
 *   type  – which sheet / entity to import: "formateurs" or "stagiaires"
 *
 * Optional fields
 *   mode  – "upsert" (default) or "insert_only"
 *           upsert   → updateOrCreate on the primary key
 *           insert_only → skip rows whose PK already exists
 */
class ImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls,csv',
                'max:20480',          // 20 MB
            ],
            'type' => [
                'required',
                'string',
                'in:formateurs,stagiaires',
            ],
            'mode' => [
                'sometimes',
                'string',
                'in:upsert,insert_only',
            ],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'file.required'  => 'Please attach an Excel or CSV file.',
            'file.mimes'     => 'Only .xlsx, .xls and .csv files are accepted.',
            'file.max'       => 'The file must not exceed 20 MB.',
            'type.required'  => 'Specify the import type: "formateurs" or "stagiaires".',
            'type.in'        => 'Import type must be "formateurs" or "stagiaires".',
            'mode.in'        => 'Import mode must be "upsert" or "insert_only".',
        ];
    }

    /** Resolved import mode, defaulting to "upsert". */
    public function importMode(): string
    {
        return $this->input('mode', 'upsert');
    }
}
