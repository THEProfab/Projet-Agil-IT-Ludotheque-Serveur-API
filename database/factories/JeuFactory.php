<?php

namespace Database\Factories;

use App\Models\Editeur;
use App\Models\Jeu;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JeuFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Jeu::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() {
        $theme_ids = Theme::all()->pluck('id');
        $editeurs_ids = Editeur::all()->pluck('id');
        $user_ids = User::all()->pluck('id');
        return [
            'nom' => $this->faker->words(2, true),
            'description' => $this->faker->text(300),
            'regles' => $this->faker->randomHtml(4, 4),
            'url_media' => "images/no-image.png",
            'user_id' => $this->faker->randomElement($user_ids),
            'theme_id' => $this->faker->randomElement($theme_ids),
            'editeur_id' => $this->faker->randomElement($editeurs_ids),
            'duree' => $this->faker->randomElement(['- de 10 Minute', 'Entre 10 et 20 Min', 'Une demi heure', 'une heure', 'Plus d\'une heure']),
            'langue' => $this->faker->randomElement(['français', 'Anglais', 'Allemand']),
            'nombre_joueurs' => $this->faker->numberBetween('2', '10' ),
            'age' => $this->faker->randomElement(['4', '6', '10', '14', '18']),
            'categorie' => $this->faker->randomElement(['Cartes à  jouer', 'Escape Game', 'Jeu d\'Ambiance', 'Jeu de Cartes', 'Jeu de dés', 'Jeu de lettres', 'Jeu de logique', 'Jeu de pions', 'Jeu de plateau'
                , 'jeu de rôle', 'jeu de tuiles', 'Murder Party']),
        ];
    }
}
