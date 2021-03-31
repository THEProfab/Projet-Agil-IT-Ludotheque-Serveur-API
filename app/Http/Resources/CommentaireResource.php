<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentaireResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {
        return [
            "id" => $this->id,
            "commentaire" => $this->commentaire,
            "date_com" => $this->date_com,
            "note" => $this->note,
            "jeu_id" => $this->jeu_id,
            "user" => $this->user
        ];
        //return parent::toArray($request);
    }
}
