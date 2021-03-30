<?php

namespace App\Http\Controllers;

use App\Models\Mecanique;
use Illuminate\Http\Request;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class MecaniquesController extends Controller {
    function index() {
        $mecanics = Mecanique::all();
        return ResponseBuilder::success($mecanics, 200, null);
    }
}
