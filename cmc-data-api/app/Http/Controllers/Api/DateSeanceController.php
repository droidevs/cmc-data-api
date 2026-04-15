<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;

class DateSeanceController extends ApiController
{
    /**
     * Legacy endpoint: DateSeance was removed in favor of Seance(date + time_range_id).
     */
    public function index()
    {
        return response()->json([
            'message' => 'date-seances is deprecated. Use seances (date + time_range_id) and time-ranges instead.',
        ], Response::HTTP_GONE);
    }

    public function show()
    {
        return $this->index();
    }

    public function store()
    {
        return $this->index();
    }

    public function update()
    {
        return $this->index();
    }

    public function destroy()
    {
        return $this->index();
    }
}

