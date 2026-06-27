<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

/**
 * Base class for all Blade/web controllers.
 * Thin shell — all query logic lives in the Service layer.
 */
abstract class WebController extends Controller {}
