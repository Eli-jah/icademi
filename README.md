# icademi

> icademi: A Smart Academy System by Elijah Wang.

### Docker Compose

#### Structure:

- app: php.dockerfile
- db: mysql.dockerfile
- cache: redis.dockerfile
- web: nginx.dockerfile
- node: node.dockerfile

#### Commands:

- docker compose up -d
- docker compose restart {app|db|cache|web|node}
- docker compose stop
- docker compose down

### Initialization:

1. composer install
2. cp .env.example .env
3. php artisan key:generate
4. php artisan storage:link
5. php artisan make:auth
```angular2html
Authentication scaffolding generated successfully.
```
6. php artisan migrate
```angular2html
Migration table created successfully.
Migrating: 2014_10_12_000000_create_users_table
Migrated:  2014_10_12_000000_create_users_table
Migrating: 2014_10_12_100000_create_password_resets_table
Migrated:  2014_10_12_100000_create_password_resets_table
```
7. php artisan optimize

### Crontab Service

1. docker compose exec app /bin/bash
2. apt install -y cron
3. crontab -e
```angular2html
* * * * * php /var/www/html/artisan schedule:run >> /dev/null 2>&1
```
3. crontab -u root -l
4. /etc/init.d/cron help
```angular2html
/etc/init.d/cron {start|stop|status|restart|reload|force-reload}
```

### Node Environment

#### APIDOC
> Inline Documentation for RESTful web APIs
>
> Reference: [https://apidocjs.com/#install](https://apidocjs.com/#install)

1. Install
```angular2html
npm install apidoc -g
```
2. Run
```angular2html
apidoc -i src -o apidoc
```
Creates an apiDoc of all files within dir src, using the default template and put all output to apidoc directory.
3. vim generate_apidoc.sh
```angular2html
#!/bin/bash
apidoc -i app/Http/Controllers/Api/ -o public/apidoc/
```
4. chmod +x ./generate_apidoc.sh
5. Generate or Refresh apidoc
```angular2html
./generate_apidoc.sh
```
6. Visit http://{host}:{port}/apidoc/index.html

### Composer Packages

#### laravel/passport:~4.0

> composer require lcobucci/jwt=3.3.3

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

#### Carbon

> If you plan to migrate from Carbon 1 to Carbon 2:

./vendor/bin/upgrade-carbon
```angular2html
Do you want us to try the following upgrade:
- nesbot/carbon: ^2.0.0
- laravel/framework: ^5.8.0
[Y/N] N
```


