## Commandes à exécuter pour le projet tutoré 

```shell
cp .env.example .env
mkdir -p storage/jwt
openssl genrsa -passout pass:"un secret" -out storage/jwt/private.pem -aes256 4096
openssl rsa -passin pass:"un secret" -pubout -in storage/jwt/private.pem -out storage/jwt/public.pem
mkdir -p database/datas
touch database/datas/ludotheque.sqlite
composer update
php artisan jwt:secret
php artisan migrate:fresh 
php artisan db:seed
```


>   Pour plus de sécurité, il faudrait utiliser un mot de passe différent pour `pass`.
> 
> Si vous utilisez un mot de passe différent, il faut modifier la valeur de la variable `JWT_PASSPHRASE` 
> dans le fichier `.env`.



## Base de données

**A FAIRE**


```shell
cp .env.example .env
```


Modifications dans le fichier `.env`. **(déjà fait)**

```shell
DB_CONNECTION=sqlite
DB_HOST=127.0.0.1
DB_PORT=3306
```

il faut créer un fichier database/datas/ludotheque.sqlite

## Pour mettre en place la base de données 

**A FAIRE**


```shell
mkdir -p database/datas
touch database/datas/ludotheque.sqlite
composer update
php artisan migrate:fresh 
php artisan db:seed
```



## Dépendances

*   Format des réponses API **(déjà fait)**
    
    
    ```shell
    composer require marcin-orlowski/laravel-api-response-builder
    ```

    ```shell
    php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
    ```

    ```shell
    php artisan vendor:publish --provider="MarcinOrlowski\ResponseBuilder\ResponseBuilderServiceProvider"
    ```


*   Authentification **(déjà fait)**

    ```shell
    composer require tymon/jwt-auth
    ```
    
    ```shell
    php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
    ```

    **A FAIRE**
    ```shell
    php artisan jwt:secret
    mkdir -p storage/jwt
    openssl genrsa -passout pass:"un secret" -out storage/jwt/private.pem -aes256 4096
    openssl rsa -passin pass:"un secret" -pubout -in storage/jwt/private.pem -out storage/jwt/public.pem
    ```

    A ajouter à la fin du fichier `.env` **(déjà fait)**

    ```shell
    JWT_ALGO=RS256
    JWT_PUBLIC_KEY=jwt/public.pem
    JWT_PRIVATE_KEY=jwt/private.pem
    JWT_PASSPHRASE="un secret"
    ```

    Vérifier la présence des lignes suivantes dans le fichier `config/jwt.php` à l'intérieur du tableau 'keys'

    ```shell
    'public' => 'file://'.storage_path(env('JWT_PUBLIC_KEY')),
    'private' => 'file://'.storage_path(env('JWT_PRIVATE_KEY')),
    ```


Vérifier la présence des lignes suivantes dans le fichier `app/Models/User`

```php
<?php 
namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
class User extends Authenticatable implements JWTSubject 
{
  public function getJWTIdentifier()
  {
    return $this->id;
  }

  public function getJWTCustomClaims()
  {
    return [
      // Here you will put claims for your JWT: ip, device, permissions
    ];
  }
}
```

*La fonction `getJWTCustomClaims` n'est pas à compléter*

Vérifier la présence des lignes suivantes dans le fichier `config/auth.php`

```php
'defaults' => [
    'guard' => 'api',
    'passwords' => 'users',
],
'guards' => [
    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],
],
```


Création d'un contrôleur **(déjà fait)**

```shell
php artisan make:controller AuthController
```

Démarrer le serveur :

`php artisan serve`

## Les requêtes

Pour tester les requêtes, l'utilisation d'un outil est recomandé. 

Voici des exemples :

- [Advanced REST client](https://chrome.google.com/webstore/detail/advanced-rest-client/hgmloofddffdnphfgcellkdfbfbjeloo?hl=fr) extension pour chrome
- [RESTED](https://addons.mozilla.org/fr/firefox/addon/rested/?utm_source=addons.mozilla.org&utm_medium=referral&utm_content=search) extension pour firefox
- [Postman](https://www.postman.com/) Logiciel multi OS (très performant, très professionel mais nécessite une création de compte)

Toutes les requêtes commence par : `http://127.0.0.1:8000/api`

### Les requêtes d'authentification

-   **POST** `/login` demande de connexion.

    -   Doit comporter les propriétes suivantes :
        ```
        {
            "email":"robert.duchmol@domain.fr",
            "password":"secret00"
        }
        ```

    -   Renvoie :
    
        ```
        {
            "success": true,
            "code": 200,
            "locale": "fr",
            "message": "Request Ok",
            "data": {
                "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYxNjc3NDA5NCwiZXhwIjoxNjE2Nzc3Njk0LCJuYmYiOjE2MTY3NzQwOTQsImp0aSI6ImtOWXZOVkN1a080aE9NRDciLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.z9JAjnROjkqFKA2l6U-LuBi89giuUkFdZkKXpK_eKGE",
                "token_type": "bearer",
                "expires_in": 900,
                "user": {
                    "id": 1,
                    "nom": "Duchmol",
                    "prenom": "Robert",
                    "pseudo": "Marianne de la Cordier",
                    "email": "robert.duchmol@domain.fr",
                    "email_verified_at": "2021-03-26T15:54:40.000000Z",
                    "created_at": "2021-03-26T15:54:40.000000Z",
                    "updated_at": "2021-03-26T15:54:40.000000Z"
                }
            }
        }
        ```
        
        ou
        
        ```
        {
            "success": false,
            "code": 401,
            "locale": "fr",
            "message": "Authentification invalide",
            "data": {
                "values": [
                    "Authentification invalide"
                ]
            },
            "debug": []
        }
        ```

-   **POST** `/register` demande d'enregistrement.

    -   Doit comporter les propriétes suivantes :
    
        ```
        {
            "pseudo":"Julie Duchmol",
            "nom":"Duchmol",
            "prenom":"Julie",
            "email":"julie.duchmol@domain.fr",
            "password":"secret"
        }
        ```

    -   Renvoie :
    
        ```
        {
            "success": true,
            "code": 200,
            "locale": "fr",
            "message": "Request Ok",
            "data": {
                "value": "User successfully registered"
            }
        }
        ```
        
        ou un erreur.

-   **POST** `/logout` demande de déconnexion.

    -   Doit contenir un jeton valide dans l'entête

    -   Renvoie :
    
        ```
        {
            "success": true,
            "code": 204,
            "locale": "fr",
            "message": "Requête vide bien exécutée",
            "data": {
                "value": "User successfully signed out"
            }
        }
        ```
        
        ou une erreur.

-   **GET** `/refresh` demande de prolongation du jeton.
    -   Doit contenir un jeton valide dans l'entête

-   **GET** `/user-profile` demande de profil.

    -   Doit contenir un jeton valide dans l'entête


    -   Renvoie :
    
        ```
        {
            "success": true,
            "code": 200,
            "locale": "fr",
            "message": "Request Ok",
            "data": {
                "item": {
                    "id": 1,
                    "nom": "Duchmol",
                    "prenom": "Robert",
                    "pseudo": "Marianne de la Cordier",
                    "email": "robert.duchmol@domain.fr",
                    "email_verified_at": "2021-03-26T15:54:40.000000Z",
                    "created_at": "2021-03-26T15:54:40.000000Z",
                    "updated_at": "2021-03-26T15:54:40.000000Z"
                }
            }
        }
        ```
        
        ou une erreur.

### Les requêtes d'accès aux jeux

-   **GET** `/jeux` demande de la liste des jeux.

    -   Accepte les paramètres suivants :
    
        -   `user=id` : renvoie la liste des jeux ajoutés par l'utilisateur `id` dans la base de données,
        -   `theme=id` : renvoie la liste des jeux appartenant au thème ayant l'identification `id` dans la base de données,
        -   `editeur=id` : renvoie la liste des jeux de l'éditeur ayant l'identification `id` dans la base de données,
        -   `age=min` : renvoie la liste des jeux qui acceptent des joueurs d'un age égal ou inférieur à `min`,
        -   `nbJoueurs=min` : renvoie la liste des jeux qui acceptent un nombre de joueurs égal ou inférieur à `min`,
        -   `sort=critère` : renvoie la liste des jeux triés en fonction du critère (critère peut prendre la valeur `note` ou `nom`),
        -   `page=num` : renvoie la liste des jeux de la page `num`, chaque page contient `size` jeux (par défaut `size = 5`)
        -   `size=val` : indique la valeur de la taille d'une page (par défaut `size = 5`).

-   **GET** `/jeux/{id}` demande les détails d'un jeu identifié par la clé `id`.

    -   Renvoie
    
        ```
        {
            "success": true,
            "code": 200,
            "locale": "fr",
            "message": "Request Ok",
            "data": {
                "item": {
                    "id": 15,
                    "nom": "sed assumenda",
                    "description": "Itaque sed veniam iure est et. Culpa accusamus aut commodi explicabo ullam nemo quia. Enim doloribus nihil quisquam fugit. Provident est velit totam ipsum velit. Laudantium amet blanditiis hic aut vel. Molestias ratione et corrupti. Consequatur dolorem nihil quo tenetur quibusdam corrupti.",
                    "regles": "<html><head><title>Esse repellat laborum necessitatibus consequatur enim consequatur.</title></head><body><form action=\"example.org\" method=\"POST\"><label for=\"username\">amet</label><input type=\"text\" id=\"username\"><label for=\"password\">id</label><input type=\"password\" id=\"password\"></form><div class=\"earum\"><p>Quae temporibus dolorum exercitationem id eligendi eum cum et sit.</p><span>Repellat.</span><span>Quod voluptatibus pariatur voluptatum inventore enim.</span></div><div id=\"46344\"><a href=\"example.com\">Quia quia tempora.</a><ul><li>Animi perferendis.</li><li>Inventore accusantium voluptatibus consequuntur aut animi.</li><li>Sit.</li><li>Incidunt illo itaque quo quia similique.</li><li>Quis ipsa laudantium.</li></ul></div></body></html>\n",
                    "langue": "Anglais",
                    "url_media": "http://localhost:8000/images/no-image.png",
                    "age": "14",
                    "poids": "2.487",
                    "nombre_joueurs": "4",
                    "categorie": "Jeu d'Ambiance",
                    "duree": "Plus d'une heure",
                    "user_id": {
                        "id": 3,
                        "nom": "Mathieu",
                        "prenom": "Thérèse",
                        "pseudo": "Élisabeth Raynaud",
                        "email": "wchevallier@example.org",
                        "email_verified_at": "2021-03-26T15:54:40.000000Z",
                        "created_at": "2021-03-26T15:54:40.000000Z",
                        "updated_at": "2021-03-26T15:54:40.000000Z"
                    },
                    "theme_id": {
                        "id": 6,
                        "nom": "Fantastique & Héroïc Fantasy"
                    },
                    "editeur_id": {
                        "id": 26,
                        "nom": "Raise Dead Editions"
                    },
                    "commentaires": [
                        {
                            "id": 11,
                            "commentaire": "Nihil error ducimus dolore. Id eius aperiam corporis quia in commodi. Nostrum consequatur dolorem deserunt vel enim voluptatem. Et ut maxime est quisquam voluptatum.",
                            "date_com": "2020-12-25 09:49:55",
                            "note": "1",
                            "jeu_id": "15",
                            "user_id": "8"
                        },
                        ...
                    ],
                    "statistiques": {
                        "noteMax": "5",
                        "noteMin": "1",
                        "noteMoyenne": 2.6666666666666665,
                        "nbCommentaires": 6,
                        "nbCommentairesTotal": 200,
                        "rang": 4,
                        "nbJeuxTheme": 5
                    },
                    "tarif": {
                        "prixMax": "136.73",
                        "prixMin": "95.14",
                        "prixMoyen": 109.18666666666667,
                        "nbAchats": 3
                    }
                }
            }
        }        
        ```

-   **POST** `/jeux` demande de création d'un jeu.

    -   Doit contenir un jeton valide dans l'entête
    -   et les informations dans le corps
    
        ```
        {
            "nom": "Le jeu Catane",
            "description": "À vous les joies et les peines de l'exploration de l'île de Catane. Prenez le contrôle d'un maximum de territoires en construisant villages, villes, ports et routes. Profitez au mieux des ressources de cette île si accueillante tout en commerçant avec vos voisins. Mais faites attention au brigand noir. La présence de ce terrible chevalier hante l'île et peut freiner vos ardeurs de colonisateurs.",
            "theme": 7,
            "editeur": 25,
            "langue": "Francais",
            "age": "12",
            "poids": 4.8,
            "nombre_joueurs": 8,
            "categorie": "Jeux de cartes",
            "duree": "1 heure 15mns",
            "regles": "<html><head></head><body><p>C'est le jeu</p></body></html>"
        }        
        ```
        
    -   Renvoie
    
        ```
        {
            "success": true,
            "code": 0,
            "locale": "fr",
            "message": "response-builder::builder.ok",
            "data": {
                "item": {
                    "id": 51,
                    "nom": "Le jeu Catane",
                    "description": "À vous les joies et les peines de l'exploration de l'île de Catane. Prenez le contrôle d'un maximum de territoires en construisant villages, villes, ports et routes. Profitez au mieux des ressources de cette île si accueillante tout en commerçant avec vos voisins. Mais faites attention au brigand noir. La présence de ce terrible chevalier hante l'île et peut freiner vos ardeurs de colonisateurs.",
                    "regles": "<html><head></head><body><p>C'est le jeu</p></body></html>",
                    "langue": "Francais",
                    "url_media": "http://localhost:8000/images/no-image.png",
                    "age": "12",
                    "poids": 4.8,
                    "nombre_joueurs": 8,
                    "categorie": "Jeux de cartes",
                    "duree": "1 heure 15mns",
                    "user_id": {
                        "id": 1,
                        "nom": "Duchmol",
                        "prenom": "Robert",
                        "pseudo": "Marianne de la Cordier",
                        "email": "robert.duchmol@domain.fr",
                        "email_verified_at": "2021-03-26T15:54:40.000000Z",
                        "created_at": "2021-03-26T15:54:40.000000Z",
                        "updated_at": "2021-03-26T15:54:40.000000Z"
                    },
                    "theme_id": {
                        "id": 7,
                        "nom": "Histoire & Antiquité"
                    },
                    "editeur_id": {
                        "id": 25,
                        "nom": "Pleasant Company Games"
                    }
                }
            }
        }        
        ```

### Les requêtes d'accès aux commentaires

-   **POST** `/commentaires` ajoute un commentaire au jeu.

    -    Doit contenir un jeton valide dans l'entête
    -    et les informations dans le corps
    
        ```
        {
            "note": 2.5,
            "commentaire": "Il fera beau demain",
            "jeu_id": 10,
            "date_com": "2021-02-06 03:52:02"
        }
        ```
        
    -   Renvoie
    
        ```
        {
            "success": true,
            "code": 0,
            "locale": "fr",
            "message": "response-builder::builder.ok",
            "data": {
                "item": {
                    "commentaire": "Il fera beau demain",
                    "note": 2.5,
                    "date_com": {
                        "date": "2021-03-26 18:52:08.722022",
                        "timezone_type": 3,
                        "timezone": "CET"
                    },
                    "jeu_id": 10,
                    "user_id": 1,
                    "id": 201
                }
            }
        }
        ```

-   **DELETE** `/commentaires/{id}` supprime un commentaire au jeu.

    -    Doit contenir un jeton valide dans l'entête
    -   Renvoie
    
        ```
        {
            "success": true,
            "code": 204,
            "locale": "fr",
            "message": "Requête vide bien exécutée",
            "data": {
                "value": "Dépense supprimée"
            }
        }
        ```

### Les requêtes d'accès aux utilisateurs

-   **GET** `/users/{id}` affiche les détails d'un utilisateur identifié par `id`.

    -    Doit contenir un jeton valide dans l'entête
    -   Renvoie
    
        ```
         {
            "success": true,
            "code": 200,
            "locale": "fr",
            "message": "Request Ok",
            "data": {
                "item": {
                    "id": 1,
                    "nom": "Duchmol",
                    "prenom": "Robert",
                    "pseudo": "Marianne de la Cordier",
                    "email": "robert.duchmol@domain.fr",
                    "jeux": [
                        {
                            "jeu": {
                                "id": 1,
                                "nom": "maxime fugit",
                                "description": "Modi ut tenetur qui cupiditate. Doloribus assumenda rerum quo. Repellendus facere earum alias et. Eum dicta vel porro esse. Enim aliquam esse quo dolores cum aspernatur. Eos ut perferendis aut temporibus et.",
                                "regles": "<html><head><title>Excepturi magni vero est.</title></head><body><form action=\"example.org\" method=\"POST\"><label for=\"username\">ut</label><input type=\"text\" id=\"username\"><label for=\"password\">aspernatur</label><input type=\"password\" id=\"password\"></form><div class=\"dolores\"></div><div id=\"12188\"></div></body></html>\n",
                                "langue": "français",
                                "url_media": "http://localhost:8000/images/no-image.png",
                                "age": "18",
                                "poids": "1.085",
                                "nombre_joueurs": "4",
                                "categorie": "Jeu d'Ambiance",
                                "duree": "une heure",
                                "user_id": {
                                    "id": 7,
                                    "nom": "Gerard",
                                    "prenom": "Joseph",
                                    "pseudo": "Susanne Pasquier",
                                    "email": "louis50@example.org",
                                    "email_verified_at": "2021-03-26T15:54:40.000000Z",
                                    "created_at": "2021-03-26T15:54:40.000000Z",
                                    "updated_at": "2021-03-26T15:54:40.000000Z"
                                },
                                "theme_id": {
                                    "id": 8,
                                    "nom": "Horreur & Post-Apocalytique"
                                },
                                "editeur_id": {
                                    "id": 28,
                                    "nom": "Sphere Games"
                                }
                            },
                            "lieu": "quas",
                            "prix": "154.29",
                            "date_achat": "2020-10-28 05:52:40"
                        },
                        .... liste de jeux
                    ]
                }
            }
        }
        ```

-   **PUT** `/users/{id}` modifie les informations d'un utilisateur identifié par `id`.
    -    Doit contenir un jeton valide dans l'entête
    
        ```
        {
            "pseudo":"Julie Duchmol",
            "nom":"Duchmol",
            "prenom":"Julie",
            "email":"julie.duchmol@gmail.com",
            "_method": "PUT"
        }
        ```
        
    -   Renvoie
    
        ```
        {
            "success": true,
            "code": 200,
            "locale": "fr",
            "message": "Request Ok",
            "data": {
                "value": "User successfully registered"
            }
        }
        ```
        
-   **POST** `/users/{id}/achat` ajoute l'achat d'un jeu pour un utilisateur identifié par `id`.

    -    Doit contenir un jeton valide dans l'entête
    
        ```
        {
            "lieu": "Maison de campagne",
            "prix": 123.47,
            "date_achat": "2021-03-26 00:14:19",
            "jeu_id": 44
        }
        ```
        
    -   Renvoie
    
        ```
         {
            "success": true,
            "code": 200,
            "locale": "fr",
            "message": "Request Ok",
            "data": {
                "item": {
                    "id": 1,
                    "nom": "Duchmol",
                    "prenom": "Robert",
                    "pseudo": "Marianne de la Cordier",
                    "email": "robert.duchmol@domain.fr",
                    "jeux": [
                        {
                            "jeu": {
                                "id": 1,
                                "nom": "maxime fugit",
                                "description": "Modi ut tenetur qui cupiditate. Doloribus assumenda rerum quo. Repellendus facere earum alias et. Eum dicta vel porro esse. Enim aliquam esse quo dolores cum aspernatur. Eos ut perferendis aut temporibus et.",
                                "regles": "<html><head><title>Excepturi magni vero est.</title></head><body><form action=\"example.org\" method=\"POST\"><label for=\"username\">ut</label><input type=\"text\" id=\"username\"><label for=\"password\">aspernatur</label><input type=\"password\" id=\"password\"></form><div class=\"dolores\"></div><div id=\"12188\"></div></body></html>\n",
                                "langue": "français",
                                "url_media": "http://localhost:8000/images/no-image.png",
                                "age": "18",
                                "poids": "1.085",
                                "nombre_joueurs": "4",
                                "categorie": "Jeu d'Ambiance",
                                "duree": "une heure",
                                "user_id": {
                                    "id": 7,
                                    "nom": "Gerard",
                                    "prenom": "Joseph",
                                    "pseudo": "Susanne Pasquier",
                                    "email": "louis50@example.org",
                                    "email_verified_at": "2021-03-26T15:54:40.000000Z",
                                    "created_at": "2021-03-26T15:54:40.000000Z",
                                    "updated_at": "2021-03-26T15:54:40.000000Z"
                                },
                                "theme_id": {
                                    "id": 8,
                                    "nom": "Horreur & Post-Apocalytique"
                                },
                                "editeur_id": {
                                    "id": 28,
                                    "nom": "Sphere Games"
                                }
                            },
                            "lieu": "quas",
                            "prix": "154.29",
                            "date_achat": "2020-10-28 05:52:40"
                        },
                        .... liste de jeux
                        {
                            "jeu": {
                                "id": 44,
                                "nom": "tempore impedit",
                                "description": "Ut magni architecto inventore est qui voluptas. Sed non ipsa qui. Non veritatis natus eligendi nihil vero qui delectus. Dignissimos ut pariatur aut distinctio sunt. Totam dolorem necessitatibus sunt qui. Eligendi et qui repudiandae aliquam neque quidem.",
                                "regles": "<html><head><title>Aut eos perspiciatis illum.</title></head><body><form action=\"example.com\" method=\"POST\"><label for=\"username\">et</label><input type=\"text\" id=\"username\"><label for=\"password\">ut</label><input type=\"password\" id=\"password\"></form><div class=\"magnam\">Cum dolores dolore velit perferendis quia vero.<a href=\"example.org\">Nulla.</a></div></body></html>\n",
                                "langue": "français",
                                "url_media": "http://localhost:8000/images/no-image.png",
                                "age": "18",
                                "poids": "0.469",
                                "nombre_joueurs": "7",
                                "categorie": "Jeu d'Ambiance",
                                "duree": "- de 10 Minute",
                                "user_id": {
                                    "id": 5,
                                    "nom": "Jacob",
                                    "prenom": "Tristan",
                                    "pseudo": "Benjamin-Alain Pottier",
                                    "email": "zacharie.gomez@example.com",
                                    "email_verified_at": "2021-03-26T15:54:40.000000Z",
                                    "created_at": "2021-03-26T15:54:40.000000Z",
                                    "updated_at": "2021-03-26T15:54:40.000000Z"
                                },
                                "theme_id": {
                                    "id": 4,
                                    "nom": "Cartoon & Dessin"
                                },
                                "editeur_id": {
                                    "id": 33,
                                    "nom": "XII Singes"
                                }
                            },
                            "lieu": "Maison de campagne",
                            "prix": "123.47",
                            "date_achat": "2021-03-26 00:14:19"
                        }
                    ]
                }
            }
        }
        ```

-   **POST** `/users/{id}/vente` supprime l'achat d'un jeu pour un utilisateur identifié par `id`.

    -   Doit contenir un jeton valide dans l'entête
    -   Renvoie la même chose que précédemment avec le jeu en moins


## Les requêtes d'accès aux autres donnees

-   **GET** `/mecanics` demande de la liste des mécaniques.

    -   Renvoie

        ```
        {
            "success": true,
            "code": 200,
            "locale": "fr",
            "message": "Request Ok",
            "data": {
                "items": [
                    {
                        "id": 1,
                        "nom": "Abstrait"
                    },
                    {
                        "id": 2,
                        "nom": "Humour"
                    },
                    {
                        "id": 3,
                        "nom": "Jeu de plateau"
                    },
                    {
                        "id": 4,
                        "nom": "Enquêtes & Mystères"
                    },
                    ...
                ]
            }
        }
       ```

-   **GET** `/editeurs` demande de la liste des éditeurs.

    -   Renvoie

        ```
        {
            "success": true,
            "code": 200,
            "locale": "fr",
            "message": "Request Ok",
            "data": {
                "items": [
                    {
                        "id": 1,
                        "nom": "1-2-3-Games"
                    },
                    {
                        "id": 2,
                        "nom": "ADG"
                    },
                    {
                        "id": 3,
                        "nom": "Arkhane Asylum Publishing"
                    },
                    {
                        "id": 4,
                        "nom": "Bayard Jeux"
                    },
                    ...
                ]
            }
        }
       ```

-   **GET** `/themes` demande de la liste des thèmes.

    -   Renvoie

        ```
        {
            "success": true,
            "code": 200,
            "locale": "fr",
            "message": "Request Ok",
            "data": {
                "items": [
                    {
                        "id": 1,
                        "nom": "Abstrait, lettres & mots"
                    },
                    {
                        "id": 2,
                        "nom": "Animaux & Nature"
                    },
                    {
                        "id": 3,
                        "nom": "Autres"
                    },
                    {
                        "id": 4,
                        "nom": "Cartoon & Dessin"
                    },
                    ...
                ]
            }
        }
       ```



