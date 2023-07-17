<?php

namespace Aliwebto\Otp\Notifications;

use Aliwebto\Otp\Channels\SmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendLoginSmsNotification extends Notification
{
    use Queueable;

    protected mixed $code;
    protected mixed $text;
    protected mixed $phone;

    /**
     * Create a new notification instance.
     */
    public function __construct($code,$text,$phone)
    {
        $this->code = $code;
        $this->text = $text;
        $this->phone = $phone;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [SmsChannel::class];
    }


    public function toSms(object $notifiable): array
    {
        $text = str_replace("{code}",$this->code,$this->text);
        return [
            "code" => $this->code,
            "text" => $text,
            "phone" => $this->phone
        ];
    }
}
