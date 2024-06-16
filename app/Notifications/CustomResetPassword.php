<?php

namespace App\Notifications;

use Filament\Facades\Filament;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPassword extends Notification
{
    use Queueable;

    private string $token;


    /**
     * Create a new notification instance.
     * @param string $token
     */
    public function __construct(string $token)
    {
        //
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Reset Kata Sandi')
            ->greeting('Halo ' . $notifiable->nama)
            ->line('Anda menerima email ini karena kami menerima permintaan pengaturan ulang kata sandi untuk akun Anda.')
            ->action('Reset Kata Sandi', $this->resetUrl($notifiable))
            ->line('Tautan pengaturan ulang kata sandi ini akan kedaluwarsa dalam 60 minutes.')
            ->line('Jika Anda tidak meminta pengaturan ulang kata sandi, abaikan email ini.')
            ->salutation('Hormat kami, ' . config('app.name'));
    }

    protected function resetUrl(mixed $notifiable): string
    {
        return Filament::getResetPasswordUrl($this->token, $notifiable);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
