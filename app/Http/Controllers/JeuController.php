<?php

namespace App\Http\Controllers;

use App\Http\Resources\jeuxResource;
use App\Models\Jeu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class JeuController extends Controller {
    function index(Request $request) {
        $userId = $request->get('user', null);
        $themeId = $request->get('theme', null);
        $editeurId = $request->get('editeur', null);
        $age = $request->get('age', null);
        $nbJoueurs = $request->get('nbJoueurs', null);
        $sort = $request->get('sort', null);
        $ordre = $request->get('ordre', null);
        $page = $request->get('page', null);
        $size = $request->get('size', 5);

        if (isset($userId)) {
            $jeux = Jeu::where('user_id', $userId)->get();
        } elseif (isset($editeurId)) {
            $jeux = Jeu::where('editeur_id', $editeurId)->get();
        } elseif (isset($themeId)) {
            $jeux = Jeu::where('theme_id', $themeId)->get();
        } elseif (isset($age)) {
            $jeux = Jeu::where('age', '<=', $age)->get();
        } elseif (isset($nbJoueurs)) {
            $jeux = Jeu::where('nombre_joueurs', '<=', $nbJoueurs)->get();
        } elseif (isset($sort) && $sort == 'note') {
            $jeux = Jeu::join('commentaires', 'jeux.id', '=', 'commentaires.jeu_id')
                ->groupBy('commentaires.jeu_id')
                ->addSelect(['jeux.*', DB::raw('AVG(note) as moyenne_notes')])
                ->orderBy('moyenne_notes', 'desc')->limit(5)->get();
        } elseif (isset($sort) && $sort == 'nom') {
            $jeux = Jeu::orderBy('nom')->get();
        } elseif (isset($page)) {
            $jeux = Jeu::orderBy('nom')->paginate($size);
        } else {
            $jeux = Jeu::all();
        }

        return ResponseBuilder::success(jeuxResource::collection($jeux), 200, null);
    }
}
