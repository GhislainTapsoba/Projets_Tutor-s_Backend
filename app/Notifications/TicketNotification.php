<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketNotification extends Notification
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
            ->subject('Mise à jour - Ticket #' . $this->ticket->reference)
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line($this->message)
            ->action('Voir votre ticket', url('/tickets/' . $this->ticket->id))
            ->line('Référence du ticket: ' . $this->ticket->reference)
            ->line('Merci de votre attention !');
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_reference' => $this->ticket->reference,
            'message' => $this->message,
            'status' => $this->ticket->status
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
            'ticket_id' => $this->ticket->id,
            'ticket_reference' => $this->ticket->reference,
            'message' => $this->message,
            'status' => $this->ticket->status
        ];
    }
}
