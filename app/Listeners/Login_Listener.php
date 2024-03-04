<?php

namespace App\Listeners;

use App\Events\LoginEvent;
use App\Notifications\Notify_Admin;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Storage;

class Login_Listener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(LoginEvent $event)
    {

        $date = now();
        $browser = request()->header('User-Agent');
        $content = $date . ' id:' . $event->admin->id . ' name:' . $event->admin->name . ' Browser Details:' . $browser;
        Storage::disk('local')->append('Log_Admin.txt', $content);
        $event->admin->notify(new Notify_Admin());
    }
}
