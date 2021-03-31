<?php

namespace App\Http\Controllers;

use App\Http\Resources\JeuxDetailsResource;
use App\Http\Resources\jeuxResource;
use App\Models\Jeu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class jeuController extends Controller {
/*    public function __construct() {
        $this->middleware('api', ['except' => ['index', 'show']]);
    }*/




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
                ->orderBy('moyenne_notes', 'desc')->get();
        } elseif (isset($sort) && $sort == 'nom') {
            $jeux = Jeu::orderBy('nom')->get();
        } elseif (isset($page)) {
            $jeux = Jeu::orderBy('nom')->paginate($size);
        } else {
            $jeux = Jeu::all();
        }

        return ResponseBuilder::success(jeuxResource::collection($jeux), 200, null);
    }

    function show($id) {
        $jeu = Jeu::findOrFail($id);
        return ResponseBuilder::success(new JeuxDetailsResource($jeu), 200, null);
    }

    function store(Request $request) {
        $this->middleware('auth:api');

        Log::info('Requête : ' . json_encode($request));
        $validator = Validator::make($request->all(),
            [
                'nom' => 'required|unique:jeux|between:10,100',
                'description' => 'required',
                'theme' => 'required',
                'editeur' => 'required',
                'langue' => 'required',
                'age' => 'required|numeric|between:1,16',
                'poids' => 'numeric|between:.1,5.0',
                'nombre_joueurs' => 'numeric|between:2,8'
            ],
            [
                'nom.required' => 'Le nom est requis',
                'nom.unique' => 'Le nom doit être unique',
                'description.required' => 'La description est requise',
                'theme.required' => 'Le thème est requis',
                'editeur.required' => 'L\'éditeur est requis',
                'langue.required' => 'la langue est requise',
                'age.required' => 'l\'age est requis',
                'numeric' => ':attribute est un entier',
                'between' => ':attribute doit être entre :min et :max',
            ]
        );
        if ($validator->fails()) {
            Log::info($validator->errors()->toArray());
            $tab = [];
            foreach ($validator->errors()->messages() as $messages) {
                foreach ($messages as $error) {
                    $tab[] = $error;
                }
            }
            return ResponseBuilder::error(422, null, $tab);
        }
        $jeu = new Jeu();
        $jeu->nom = $request->nom;
        $jeu->description = $request->description;
        $jeu->theme_id = $request->theme;
        $jeu->user_id = Auth::user()->id;
        $jeu->editeur_id = $request->editeur;
        $jeu->url_media = $request->get('url_media', 'images/no-image.png');
        $jeu->langue = $request->langue;
        $jeu->age = $request->age;
        $jeu->poids = $request->poids;
        $jeu->nombre_joueurs = $request->nombre_joueurs;
        $jeu->duree = $request->duree;
        $jeu->regles = $request->regles;
        $jeu->categorie = $request->categorie;
        $jeu->save();
        if (isset($request->mecaniques)) {
            Log::info($request->mecaniques);
            $jeu->mecaniques()->attach($request->mecaniques);
        }
        $jeu->save();

        /*
         *  Code en attente traitement de l'upload d'image
         *
                   if($request->file('image') !== null){
                    $file = $request->file('image');
                    $extension = $file->getClientOriginalExtension();

                    // File upload location
                    $location = public_path().'/images/';
                    $filename = uniqid().'.'.$extension;

                    // Upload file
                    $file->move($location, $filename);

                    $jeu->url_media = '/imagesjeux/'.$filename;
                );
        */
        $jeu->save();
        return ResponseBuilder::success(new JeuxResource($jeu));
    }


}
