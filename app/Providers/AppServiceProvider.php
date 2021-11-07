<?php

namespace App\Providers;

use App\Models\Invitation;
use App\Models\School;
use App\Models\User;
use App\Observers\InvitationObserver;
use App\Observers\SchoolObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);
        School::observe(SchoolObserver::class);
        Invitation::observe(InvitationObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
