<?php

namespace App\Handlers\Events;

use App\Events\NewRegisteredUser;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LinkUserToExistingContacts
{
    /**
     * Handle the event.
     *
     * @param  NewRegisteredUser  $event
     * @return void
     */
    public function handle(NewRegisteredUser $event)
    {
        logger()->info(__METHOD__);
        logger()->info("Linking <{$event->user->email}> to user");

        $event->user->linkToContacts();
    }
}
