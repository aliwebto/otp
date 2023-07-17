<?php

namespace Aliwebto\Otp;

use Aliwebto\Otp\Models\PhoneCode;
use Aliwebto\Otp\Notifications\SendLoginSmsNotification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class Otp
{
    public static function generate($phone)
    {
        $phone = self::simplifyPhone($phone);
        self::checkPhoneIsValid($phone);
        $code = self::getCode();
        Notification::route('phone', $phone)
            ->notify(new SendLoginSmsNotification($code,"Your code is {code}",$phone));
        return PhoneCode::create([
            "code" => Hash::make($code),
            "phone" => $phone
        ]);
    }
    protected static function simplifyPhone($phone){
        return preg_replace('#^(\+98|0)?#', '', $phone);
    }
    public static function lastCode($phone): PhoneCode
    {
        $phone = self::simplifyPhone($phone);
        return PhoneCode::where("phone", $phone)->latest()->first();
    }

    private static function checkPhoneIsValid($phone)
    {
        $phone = self::simplifyPhone($phone);
        $phoneRegex = "/^(\\+98|0)?9\\d{9}$/";
        throw_if(!preg_match($phoneRegex, $phone), "phone number is wrong");
    }

    private static function getDigits(): array
    {

        if (config("otp.code.type") == "full") {
            $digits = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        }

        if (config("otp.code.type") == "simple") {
            $digits = [];
            while (count($digits) < 3) {
                $random_digit = rand(0, 9);
                if (!in_array($random_digit, $digits)) {
                    $digits[] = $random_digit;
                }
            }
        }
        return $digits;
    }

    private static function getCode(): string
    {
        $digits = self::getDigits();
        $length = config("otp.code.length");
        if (empty($digits) || count($digits) > 10) {
            return "Invalid input";
        }
        shuffle($digits);
        $number = implode("", array_slice($digits, 0, $length));
        while (strlen($number) < 5) {
            shuffle($digits);
            $number .= implode("", array_slice($digits, 0, $length - strlen($number)));
        }
        return $number;
    }

    public static function regenerate($phone)
    {
        $phone = self::simplifyPhone($phone);
        throw_if(config("otp.features.resend") == false, "Resend feature isn't active");
        self::checkPhoneIsValid($phone);
        $lastCode = self::lastCode($phone);
        if ($lastCode) {
            throw_if(self::checkRegenerateCooldown($lastCode), "Regenerate Cooldown");
        }
        PhoneCode::where("phone", $phone)->delete();
        return self::generate($phone);
    }

    public static function checkRegenerateCooldown(PhoneCode $code): bool
    {
        $cooldown = config("otp.security.resendTime");
        return self::regenerateCooldown($code) !== 0;
    }

    public static function regenerateCooldown(PhoneCode $code): int
    {
        $cooldown = config("otp.security.resendTime");
        return ($code->created_at->addSeconds(intval($cooldown)) < now()) ? 0 : now()->diffInRealSeconds($code->created_at->addSeconds(intval($cooldown)));
    }

    public static function checkLifeSpan(PhoneCode $code): bool
    {
        $lifespan = config("otp.code.time");
        return $code->created_at > now()->subMinutes(intval($lifespan));
    }

    public static function checkValid($code, $phone): bool
    {
        $phone = self::simplifyPhone($phone);
        $phone_code = PhoneCode::where("phone", $phone)->first();
        if (!is_null($phone_code)) {
            return Hash::check($code, $phone_code->code);
        }
        return false;
    }

    public static function check($code, $phone): bool
    {
        $phone = self::simplifyPhone($phone);
        $phone_code = PhoneCode::where("phone", $phone)->first();
        if (!is_null($phone_code)) {
            return self::checkValid($code, $phone) and self::checkLifeSpan($phone_code);
        }
        return false;
    }

    public static function authenticate($code, $phone, $createUserIfNotExist = false, $email = null, $name = null)
    {
        $phone = self::simplifyPhone($phone);
        if (self::check($code, $phone)) {
            $user = User::where("phone",$phone)->first();
            if (!is_null($user)){
                Auth::loginUsingId($user->id,true);
                PhoneCode::where("phone", $phone)->delete();
                return true;
            }elseif($createUserIfNotExist){
                throw_if(is_null($email),"email is required when createUserIfNotExist is true");
                throw_if(is_null($name),"name is required when createUserIfNotExist is true");
                $user = User::create([
                    "email" => $email,
                    "password" => Hash::make(Str::random(32)),
                    "phone" => $phone,
                    "name" => $name
                ]);
                Auth::loginUsingId($user->id,true);
                PhoneCode::where("phone", $phone)->delete();
                return true;

            }
        }
        return false;
    }
}
