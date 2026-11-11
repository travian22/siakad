<?php

namespace App\Providers;

use App\Models\PeriodeAkademik;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\View\View as BladeView;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request): Limit {
            $username = Str::transliterate(Str::lower($request->string('username')->toString()));

            return Limit::perMinute(5)->by($username.'|'.$request->ip());
        });

        View::composer('components.layouts.app', function (BladeView $view): void {
            $activePeriode = Schema::hasTable('periode_akademik')
                ? PeriodeAkademik::currentActive()
                : null;

            $view->with('activePeriode', $activePeriode);
        });
    }
}
