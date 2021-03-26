<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Log;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;
use Validator;

class AuthController extends Controller {

    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|between:2,100',
            'prenom' => 'required|string|between:2,100',
            'pseudo' => 'required|string|between:2,100',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'We need to know your email address!',
            'email.email' => 'not valid email address format',
            'email.unique' => 'email address already used',
            'nom.required' => 'We need to know your lastname',
            'prenom.required' => 'We need to know your firstname',
            'pseudo.required' => 'We need to know your pseudo',
            'password.required' => 'Password is required',
            'password.min' => 'Password must contains 6 letters min',
            'between'=> ':attribute doit contenir entre :min et :max '
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


        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return ResponseBuilder::success('User successfully registered', 200, null);

    }


    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return ResponseBuilder::error(400, null, ['requête invalide'], 400);
        }
        $credentials = $request->only('email', 'password');
        Log::info("coucou : ". implode(" ",$credentials));

        if (!$token = auth()->attempt($credentials)) {
            return ResponseBuilder::error(401, null, ['Authentification invalide'], 401);
        }

        return $this->createNewToken($token);

    }

    public function userProfile() {
        $data = auth()->user();
        return ResponseBuilder::success($data, 200, null);
    }


    public function logout() {
        auth()->logout();
        return ResponseBuilder::success('User successfully signed out', 204, null);
    }

    protected function createNewToken($token) {
        $data = [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 15,
            'user' => auth()->user()
        ];

        return ResponseBuilder::success($data, 200, null);

    }


    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }

}
