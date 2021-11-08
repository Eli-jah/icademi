# rm -f bootstrap/cache/*.php
# composer u -o -vvv --no-dev
# cp .env.example .env
# php artisan key:generate
# php artisan storage:link
# php artisan optimize
# cur_dir=$(pwd)
# chown -R www:www ${cur_dir}/bootstrap/cache
# chown -R www:www ${cur_dir}/storage
# chmod -R 777 ${cur_dir}/bootstrap/cache
# chmod -R 777 ${cur_dir}/storage
# rm ${cur_dir}/init.sh -f

composer install
cp .env.example .env
php artisan key:generate
php artisan storage:link
# php artisan make:auth
php artisan migrate
php artisan db:seed
php artisan passport:install
# php artisan vendor:publish --tag=passport-components
php artisan admin:install
# php artisan vendor:publish --provider="Encore\Admin\AdminServiceProvider"

# php artisan migrate:refresh --seed
php artisan optimize
