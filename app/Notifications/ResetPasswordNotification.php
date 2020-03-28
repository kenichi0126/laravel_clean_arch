<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    protected $title = 'SMART  パスワードを再設定してください';

    /**
     * Create a new notification instance.
     *
     * @param mixed $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $expire = \Config::get('auth.passwords.members.expire');
        $email = $notifiable->getEmailForPasswordReset();
        return (new MailMessage)
            ->subject($this->title)
            ->view(
                'mail.reset_password',
                [
              'resetUrl' => \Config::get('app.url') . '/reset_password?' . $this->token,
              'expire' => $expire,
              'email' => $email,
            ]
            );
    }
}
