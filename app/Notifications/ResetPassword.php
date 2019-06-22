<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword as Notification;

class ResetPassword extends Notification
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('Te enviamos este email porque recibimos una solucitud de reseteo de contraseña para tu cuenta.')
            ->action('Reiniciar Contraseña', url(config('app.client_url').'/password/reset/'.$this->token).'?email='.urlencode($notifiable->email))
            ->line('Si no solicitaste reiniciar tu contraseña, ignora este email.');
    }
}
