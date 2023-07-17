<?php

namespace Aliwebto\Otp\Channels;

use Illuminate\Notifications\Notification;
use SoapClient;

class SmsChannel
{
    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $data = $notification->toSms($notifiable);
        ini_set("soap.wsdl_cache_enabled", "0");
        $sms = new SoapClient("http://api.payamak-panel.com/post/Send.asmx?wsdl", array("encoding" => "UTF-8"));
        $smsData = [
            "username" => config("otp.sms.melipayamak.username"),
            "password" => config("otp.sms.melipayamak.password"),
            "text" => [$data["code"]],
            "to" => $data["phone"],
            "bodyId" => config("otp.sms.melipayamak.patternId")
        ];
        $send_Result = $sms->SendByBaseNumber($smsData)->SendByBaseNumberResult;
    }
}
