<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JeuxResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {
        return [
            "id" => $this->id,
            "nom" => $this->nom,
            "description" => $this->description,
            "regles" => $this->regles,
            "langue" => $this->langue,
            "url_media" => url($this->url_media),
            "age" => $this->age,
            "poids" => $this->poids,
            "nombre_joueurs" => $this->nombre_joueurs,
            "categorie" => $this->categorie,
            "duree" => $this->duree,
            "user_id" => $this->user,
            "theme_id" => $this->theme,
            "editeur_id" => $this->editeur,
            "mecaniques" => MecaniquesResource::collection($this->mecaniques)
        ];
//        return parent::toArray($request);
    }
}
