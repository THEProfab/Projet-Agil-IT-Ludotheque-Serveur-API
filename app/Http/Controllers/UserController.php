<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserProfilResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;
use Validator;

class UserController extends Controller {
    function show($id) {
        $user = User::findOrFail($id);
        return ResponseBuilder::success(new UserProfilResource($user), 200, null);
    }

    function update(Request $request, $id) {

        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|between:2,100',
            'prenom' => 'required|string|between:2,100',
            'pseudo' => 'required|string|between:2,100',
            'email' => 'required|string|email|unique:users,email' . $user->id,
        ], [
            'email.required' => 'We need to know your email address!',
            'email.email' => 'not valid email address format',
            'email.unique' => 'email address already used',
            'nom.required' => 'We need to know your lastname',
            'prenom.required' => 'We need to know your firstname',
            'pseudo.required' => 'We need to know your pseudo',
            'between' => ':attribute doit contenir entre :min et :max '
        ]);

        if ($validator->fails()) {
            $tab = [];
            foreach ($validator->errors()->messages() as $messages) {
                foreach ($messages as $error) {
                    $tab[] = $error;
                }
            }
            return ResponseBuilder::error(400, null, $tab, 400);
        }

        $user->nom = $request->nom;
        $user->prenom = $request->prenom;
        $user->pseudo = $request->pseudo;
        $user->email = $request->email;

        $user->save();

        return ResponseBuilder::success('User successfully registered', 200, null);
    }

    function ajouteAchat(Request $request, $id) {

        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'lieu' => 'required|string|between:2,100',
            'date_achat' => 'required|date',
            'prix' => 'required|numeric|between:.1,250',
            'jeu_id' => 'required|exists:jeux,id',
        ], [
            'date_achat.required' => 'La date d\'achat est requise',
            'date_achat.date' => 'format de la date incorrect',
            'lieu.required' => 'Le champ lieu est obligatoire',
            'prix.required' => 'Le prix doit être indiqué',
            'between' => ':attribute doit être entre :min et :max ',
            'jeux_id.exists' => 'la référence du jeu est incorrecte',
        ]);

        if ($validator->fails()) {
            $tab = [];
            foreach ($validator->errors()->messages() as $messages) {
                foreach ($messages as $error) {
                    $tab[] = $error;
                }
            }
            Log::error($tab);
            return ResponseBuilder::error(400, null, $tab, 400);
        }

        $jeu = $user->ludo_perso()->where('jeu_id', $request->jeu_id)->first();

        if ($jeu) {
            $user->ludo_perso()->detach($request->jeu_id);
            $user->save();
        }


        $user->ludo_perso()->attach($request->jeu_id, [
            'lieu' => $request->lieu,
            'prix' => $request->prix,
            'date_achat' => $request->date_achat
        ]);
        $user->save();
        return ResponseBuilder::success(new UserProfilResource($user), 200, null);
    }

    function supprimeAchat(Request $request, $id) {

        $user = User::findOrFail($id);


        $validator = Validator::make($request->all(), [
            'jeu_id' => 'required|exists:jeux,id',
        ], [
            'jeu_id.required' => 'la référence du jeu est obligatoire',
            'jeu_id.exists' => 'la référence du jeu est incorrecte',
        ]);

        if ($validator->fails()) {
            $tab = [];
            foreach ($validator->errors()->messages() as $messages) {
                foreach ($messages as $error) {
                    $tab[] = $error;
                }
            }
            return ResponseBuilder::error(400, null, $tab, 400);
        }

        $jeu = $user->ludo_perso()->where('jeu_id', $request->jeu_id)->first();

        if ($jeu) {
            $user->ludo_perso()->detach($request->jeu_id);
            $user->save();
        }
        return ResponseBuilder::success(new UserProfilResource($user), 200, null);
    }

}
