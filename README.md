# icademi

> icademi: A Smart Academy System by Elijah Wang.

### Docker Compose

#### Structure:

- app: php.dockerfile
- db: mysql.dockerfile
- cache: redis.dockerfile
- web: nginx.dockerfile

#### Commands:

- docker compose up -d
- docker compose restart app|db|cache|web
- docker compose stop
- docker compose down

### Composer Packages

#### laravel/passport:~4.0

1. vim composer.json:
```angular2html
    "require": {
        ...
        "laravel/passport": "~4.0",
        ...
        "lcobucci/jwt": "3.3.3"
    },
```
2. composer update
3. php artisan migrate
```angular2html
Migrating: 2016_06_01_000001_create_oauth_auth_codes_table
Migrated:  2016_06_01_000001_create_oauth_auth_codes_table
Migrating: 2016_06_01_000002_create_oauth_access_tokens_table
Migrated:  2016_06_01_000002_create_oauth_access_tokens_table
Migrating: 2016_06_01_000003_create_oauth_refresh_tokens_table
Migrated:  2016_06_01_000003_create_oauth_refresh_tokens_table
Migrating: 2016_06_01_000004_create_oauth_clients_table
Migrated:  2016_06_01_000004_create_oauth_clients_table
Migrating: 2016_06_01_000005_create_oauth_personal_access_clients_table
Migrated:  2016_06_01_000005_create_oauth_personal_access_clients_table
```
4. php artisan passport:install
```angular2html
Encryption keys generated successfully.
Personal access client created successfully.
Client ID: 1
Client Secret: e1X2uJyyYQc69fuabZukumUHozoVRxeG76a3hswT
Password grant client created successfully.
Client ID: 2
Client Secret: qj3dSdE9X0VJ9KuwnwvUsff1FfaCJjYcc2fk1yqo
```
5. vim app/Models/User.php:
```angular2html
use Laravel\Passport\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    ...
}
```
6. vim app/Providers/AuthServiceProvider.php:
```angular2html
use Laravel\Passport\Passport;
use Laravel\Passport\RouteRegistrar;

class AuthServiceProvider extends ServiceProvider
{
    ...
    public function boot()
    {
        $this->registerPolicies();

        // API Authentication (Passport)
        Passport::routes();
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
    }
}
```
7. vim config/auth.php:
```angular2html
'guards' => [
    ...
    'user-api' => [
        'driver' => 'passport',
        'provider' => 'users',
    ],
],
```
8. vim routes/api.php:
```angular2html
Route::post('login', 'API\PassportController@login');
Route::post('register', 'API\PassportController@register');

Route::group(['middleware' => 'auth:user-api'], function(){
    Route::get('info', 'API\PassportController@info');
});
```
9. php artisan passport:keys
10. php artisan vendor:publish --tag=passport-components


