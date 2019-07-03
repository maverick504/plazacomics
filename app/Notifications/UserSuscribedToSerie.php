<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserSuscribedToSerie extends Notification
{
    use Queueable;

    protected $user;
    protected $serie;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $serie)
    {
        $this->user = $user;
        $this->serie = $serie;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the database representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'user_id' => $this->user->id,
            'serie_id' => $this->serie->id,
            'icon_url' => $this->user->avatar_url,
            'message' => '**' . $this->user->username . '** se ha suscrito a tu serie, **' . $this->serie->name . '**.'
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
