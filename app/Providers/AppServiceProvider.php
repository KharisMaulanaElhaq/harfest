<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
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
        if (file_exists(app_path('Helpers/helpers.php'))) {
            require_once app_path('Helpers/helpers.php');
        }

        Carbon::setLocale('id');

        Gate::define('is-admin', fn (User $user) => $user->role === UserRole::Admin || $user->role === 'admin');

        Blade::directive('datetimeWib', function ($expression) {
            return "<?php echo format_wib({$expression}); ?>";
        });
    }
}
