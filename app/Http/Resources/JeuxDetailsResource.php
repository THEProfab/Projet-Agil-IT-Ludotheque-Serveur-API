<?php

namespace App\Http\Resources;

use App\Models\Commentaire;
use App\Models\Jeu;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class jeuxDetailsResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {
        $cumul = 0.0;
        $nbAcheteurs = 0;
        $prixMin = PHP_INT_MAX;
        $prixMoyen = 0;
        $prixMax = PHP_INT_MIN;
        foreach ($this->acheteurs as $acheteur) {
            $cumul += $acheteur->achat->prix;
            $nbAcheteurs += 1;
            if ($acheteur->achat->prix > $prixMax) {
                $prixMax = $acheteur->achat->prix;
            }
            if ($acheteur->achat->prix < $prixMin) {
                $prixMin = $acheteur->achat->prix;
            }
        }
        if ($nbAcheteurs) {
            $prixMoyen = $cumul / $nbAcheteurs;
        } else {
            $prixMin = 0;
            $prixMoyen = 0;
            $prixMax = 0;
        }
        $nbCommentaires = 0;
        $noteMin = 6;
        $noteMoyenne = 0;
        $noteMax = -1;

        $cumul = 0;
        foreach ($this->commentaires as $commentaire) {
            if ($commentaire->note < $noteMin) {
                $noteMin = $commentaire->note;
            }
            if ($commentaire->note > $noteMax) {
                $noteMax = $commentaire->note;
            }
            $cumul += $commentaire->note;
            $nbCommentaires += 1;
        }
        if ($nbCommentaires) {
            $noteMoyenne = $cumul / $nbCommentaires;
        }

        $nbCommentairesTotal = Commentaire::count("*");


        $jeux = Jeu::where('theme_id', $this->theme_id)
            ->join('commentaires', 'jeux.id', '=', 'commentaires.jeu_id')
            ->groupBy('commentaires.jeu_id')
            ->addSelect(['jeux.*', DB::raw('AVG(note) as moyenne_notes')])
            ->orderBy('moyenne_notes', 'desc')->get();
        $i = 0;
        foreach ($jeux as $jeu) {
            if ($jeu->id == $this->id) {
                $rang = $i + 1;
                break;
            }
            $i += 1;
        }

        $nbJeuxTheme = $jeux->count();

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
            "commentaires" => CommentaireResource::collection($this->commentaires),
            "statistiques" => [
                "noteMax" => $noteMax,
                "noteMin" => $noteMin,
                "noteMoyenne" => $noteMoyenne,
                "nbCommentaires" => $nbCommentaires,
                "nbCommentairesTotal" => $nbCommentairesTotal,
                "rang" => $rang,
                "nbJeuxTheme" => $nbJeuxTheme,
            ],
            "tarif" => [
                "prixMax" => $prixMax,
                "prixMin" => $prixMin,
                "prixMoyen" => $prixMoyen,
                "nbAchats" => $nbAcheteurs,
            ],
        ];
    }
}

