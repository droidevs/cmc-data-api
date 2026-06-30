<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web;

namespace App\Http\Controllers\Web;

use Illuminate\View\View;

/**
 * GET /import
 *
 * Renders the import page (resources/views/import.blade.php).
 * The page itself submits to POST /api/v1/import via fetch — this
 * controller has no other responsibility than serving the view.
 */
class ImportWebController extends WebController
{
    public function index(): View
    {
        return view('imports.index');
    }
}
