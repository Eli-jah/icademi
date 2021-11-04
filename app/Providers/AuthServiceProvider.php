<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Laravel\Passport\RouteRegistrar;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        'App\Models\School' => 'App\Policies\SchoolPolicy',
        'App\Models\User' => 'App\Policies\UserPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('work-in-the-school', 'App\Policies\SchoolPolicy@work'); // for Teacher@School
        Gate::define('manage-the-school', 'App\Policies\SchoolPolicy@manage'); // for Teacher@School
        Gate::define('study-in-the-school', 'App\Policies\SchoolPolicy@study'); // for Student@School

        Gate::define('follow-the-teacher', 'App\Policies\UserPolicy@follow'); // for Student@User
        Gate::define('unfollow-the-teacher', 'App\Policies\UserPolicy@unfollow'); // for Student@User

        // API Authentication (Passport)
        Passport::routes();
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
    }
}
