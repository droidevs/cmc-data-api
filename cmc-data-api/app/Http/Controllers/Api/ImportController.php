<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\ImportRequest;
use App\Services\ImportService;
use Illuminate\Http\JsonResponse;

/**
 * POST /api/v1/import
 *
 * Accepts a multipart upload with:
 *   file  – Excel (.xlsx | .xls) or CSV file
 *   type  – "formateurs" | "stagiaires"
 *   mode  – "upsert" (default) | "insert_only"
 *
 * Returns a JSON summary of the import result.
 */
class ImportController
{
    public function __construct(private readonly ImportService $service) {}

    public function import(ImportRequest $request): JsonResponse
    {
        $file = $request->file('file');
        $type = $request->input('type');
        $mode = $request->importMode();

        $result = $this->service->import($file, $type, $mode);

        $hasErrors  = !empty($result['errors']);
        $statusCode = $hasErrors ? 207 : 200; // 207 Multi-Status when rows had errors

        return response()->json([
            'success'  => true,
            'type'     => $type,
            'mode'     => $mode,
            'summary'  => [
                'inserted' => $result['inserted'],
                'updated'  => $result['updated'],
                'skipped'  => $result['skipped'],
                'errors'   => count($result['errors']),
            ],
            'row_errors' => $result['errors'],
        ], $statusCode);
    }
}
