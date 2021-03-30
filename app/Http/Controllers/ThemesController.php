<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use Illuminate\Http\Request;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class ThemesController extends Controller {
    function index() {
        $themes = Theme::all();
        return ResponseBuilder::success($themes, 200, null);
    }
}
