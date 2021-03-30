<?php

namespace App\Http\Controllers;

use App\Models\Editeur;
use Illuminate\Http\Request;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class EditeursController extends Controller {
    function index() {
        $editeurs = Editeur::all();
        return ResponseBuilder::success($editeurs, 200, null);
    }
}
