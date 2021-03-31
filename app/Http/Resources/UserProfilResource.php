<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserProfilResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {
        $jeux = [];
        foreach ($this->ludo_perso as $achat) {
            $jeux [] = [
                "jeu" => new jeuxResource($achat),
                "lieu" => $achat->achat->lieu,
                "prix" => $achat->achat->prix,
                "date_achat" => $achat->achat->date_achat,
            ];
        }
        return [
            "id" => $this->id,
            "nom" => $this->nom,
            "prenom" => $this->prenom,
            "pseudo" => $this->pseudo,
            "email" => $this->email,
            "jeux" => $jeux,
        ];
//        return parent::toArray($request);
    }
}
