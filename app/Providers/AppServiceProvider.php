<?php

namespace App\Providers;

use App\View\Composers\NavigationComposer;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Throw an error instead of silently ignoring fields that are not in $fillable.
        Model::preventSilentlyDiscardingAttributes();

        // Prepare the menu data in a class, so the Blade file only displays it.
        View::composer('partials.role-nav', NavigationComposer::class);

        // Row-level security: the moment Laravel knows who is logged in (before route binding
        // looks up a reservation), tell PostgreSQL, so its policies can filter rows.
        Event::listen(Authenticated::class, function (Authenticated $event) {
            DB::select("select set_config('app.user_id', ?, false)", [(string) $event->user->getAuthIdentifier()]);
        });
    }
}
