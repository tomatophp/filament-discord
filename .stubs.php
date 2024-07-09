<?php

namespace Filament\Notifications;
use Illuminate\Database\Eloquent\Model;

{
    /*
     * @method static sendToDiscord(Model $user)
     */
    class Notification
    {
        public function sendToDiscord(?Model $user=null, ?string $image=null): static {}
    }
}
