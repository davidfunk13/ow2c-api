<?php

namespace App\Providers;

use App\Models\Game;
use App\Models\GameRound;
use App\Models\PlaySession;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Battlenet\Provider;
use SocialiteProviders\Manager\SocialiteWasCalled;

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
        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('battlenet', Provider::class);
        });

        $this->bindOwnedModels();
    }

    /**
     * Resolve owned models through the authenticated user so ownership is enforced by
     * construction — a route that binds one of these can never leak another user's row,
     * and a missing authorization guard is not possible. Non-owners get findOrFail's 404,
     * so existence never leaks either.
     */
    private function bindOwnedModels(): void
    {
        Route::bind('game', fn (string $value): Game => request()->user()->games()->findOrFail($value));

        Route::bind('round', fn (string $value): GameRound => GameRound::whereHas(
            'game',
            fn ($query) => $query->where('user_id', request()->user()->id)
        )->findOrFail($value));

        Route::bind('play_session', fn (string $value): PlaySession => request()->user()
            ->playSessions()
            ->findOrFail($value));
    }
}
