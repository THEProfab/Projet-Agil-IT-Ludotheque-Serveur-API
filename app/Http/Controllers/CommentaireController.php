<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentaireResource;
use App\Http\Resources\jeuxResource;
use App\Models\Commentaire;
use App\Models\Jeu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class CommentaireController extends Controller {
    function store(Request $request) {
        $validator = Validator::make($request->all(),
            [
                'note' => 'required|numeric|between:0,5',
                'commentaire' => 'required',
                'jeu_id' => 'required',
            ],
            [
                'commentaire.required' => 'le commentaire est requis',
                'note.required' => 'la note est requise',
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

        Jeu::findOrFail($request->jeu_id);

        $commentaire = new Commentaire();
        $commentaire->commentaire = $request->commentaire;
        $commentaire->note = $request->get('note', null);
        $commentaire->date_com = new \DateTime('now');
        $commentaire->jeu_id = $request->jeu_id;
        $commentaire->user_id = Auth::user()->id;
        $commentaire->save();

        return ResponseBuilder::success(new CommentaireResource($commentaire));
    }

    function destroy($id) {
        $commentaire = Commentaire::findOrfail($id);
        $commentaire->delete();
        return ResponseBuilder::asSuccess(204)
            ->withData("Dépense supprimée")
            ->build();
    }
}
