<?php

namespace App\Notifications;

use App\Models\Debtor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CreatePasswordMail extends Notification
{
    use Queueable;

    private $token;
    private $debtor;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $token, Debtor $debtor)
    {
        $this->token = $token;
        $this->debtor = $debtor;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting('Dag ' . $this->debtor->name_1)
            ->line('Er is geprobeerd in te loggen in uw account maar u heeft nog geen wachtwoord!')
            ->action('Wachtwoord instellen', url("/user/" . $this->debtor->getKey() . "/reset-password/" . $this->token))
            ->line("Deze link is 24 uur geldig, was u dit niet? dan kunt u deze mail negeren");
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
