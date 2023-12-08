<?php

namespace App\Providers;

use App\Models\Attendee;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // check for user id matching between event and authenticated user
        Gate::define("event-authorization", fn (User $user, Event $event) => $user->id === $event->user_id);
        Gate::define("attendee-authorization", fn(User $user, Event $event, Attendee $attendee) => $user->id === $attendee->user_id || $user->id === $event->user_id);
    }
}
