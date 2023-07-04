<?php

namespace App\Notifications;

use App\Models\Debtor;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordCreatedNotification extends Notification
{
    use Queueable;

    private $password;
    private $debtor;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $password, Debtor $debtor)
    {
        $this->password = $password;
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
            ->salutation('Dag ' . $this->debtor->name)
            ->line('Er is een nieuw wachtwoord voor u aangemaakt:')
            ->line("Wachtwoord: $this->password")
            ->line("Deel dit wachtwoord met niemand anders!")
            ->action('Direct inloggen', env('EXTERNAL_SITE_LOGIN_URL', url('/')));
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
