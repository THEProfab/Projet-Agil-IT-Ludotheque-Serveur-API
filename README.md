
## Base de données

Modifications dans le fichier `.env`.

```shell
DB_CONNECTION=sqlite
DB_HOST=127.0.0.1
DB_PORT=3306
```

il faut créer un fichier database/datas/ludotheque.sqlite

## Pour mettre en place la base de données

```shell
mkdir -p database/datas
touch database/datas/ludotheque.sqlite
php artisan migrate:fresh 
php artisan db:seed
```



## Dépendances

*   Format des réponses API
    
    ```shell
    composer require marcin-orlowski/laravel-api-response-builder
    ```

    ```shell
    php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
    ```

    ```shell
    php artisan vendor:publish --provider="MarcinOrlowski\ResponseBuilder\ResponseBuilderServiceProvider"
    ```


*   Authentification

    ```shell
    composer require tymon/jwt-auth
    ```
    
    ```shell
    php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
    ```

    ```shell
    php artisan jwt:secret
    mkdir -p storage/jwt
    openssl genrsa -passout pass:"un secret" -out storage/jwt/private.pem -aes256 4096
    openssl rsa -passin pass:"un secret" -pubout -in storage/jwt/private.pem -out storage/jwt/public.pem
    ```

    A ajouter dans le fichier `.env`
    ```shell
    JWT_ALGO=RS256
    JWT_PUBLIC_KEY=jwt/public.pem
    JWT_PRIVATE_KEY=jwt/private.pem
    JWT_PASSPHRASE="un secret"
    ```

    A ajouter dans le fichier `config/jwt.php`
   ```shell
    'public' => 'file://'.storage_path(env('JWT_PUBLIC_KEY')),
    'private' => 'file://'.storage_path(env('JWT_PRIVATE_KEY')),
    ```


    Modification de la classe modèle `User`

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

    Modification du fichier config/auth.php
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

    Création d'un contrôleur

    ```shell
    php artisan make:controller AuthController
    ```
