<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Bienvenue sur DKT Solutions')
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line('Nous vous souhaitons la bienvenue sur notre plateforme de gestion de tickets.')
            ->action('Accéder à votre compte', url('/'))
            ->line('Merci de votre inscription !');
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => 'Bienvenue sur DKT Solutions !
            Nous vous souhaitons la bienvenue sur notre plateforme de gestion de tickets.
            Merci de votre inscription !',
            'type' => 'welcome'
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Bienvenue sur DKT Solutions !
            Nous vous souhaitons la bienvenue sur notre plateforme de gestion de tickets.
            Merci de votre inscription !',
            'type' => 'welcome'
        ];
    }
}
