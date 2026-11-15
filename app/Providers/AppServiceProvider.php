<?php

namespace App\Providers;

use App\Models\Message;
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
        View::composer('*', function ($view) {
            $user = auth()->user();

            $view->with('currentUser', $user);

            if (! $user) {
                $view->with('unreadMessagesCount', 0);
                $view->with('latestNotifications', collect());

                return;
            }

            $view->with('unreadMessagesCount', Message::where('receiver_id', $user->id)->where('is_read', false)->count());
            $view->with(
                'latestNotifications',
                Message::with('sender')
                    ->where('receiver_id', $user->id)
                    ->latest()
                    ->limit(5)
                    ->get()
            );
        });
    }
}
