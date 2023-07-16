<?php

namespace Aliwebto\Otp;


use Aliwebto\Otp\Models\PhoneCode;

class Otp
{
    public static function generate($phone)
    {
        $phoneRegex= "/^(\\+98|0)?9\\d{9}$/";
        throw_if(!preg_match($phoneRegex,$phone),"phone number is wrong");
        $code = self::getCode();
        return PhoneCode::create([
            "code" => md5($code),
            "phone" => $phone
        ]);
    }
    private static function getDigits(): array
    {

        if (config("otp.code.type") == "full"){
            $digits = [0,1,2,3,4,5,6,7,8,9];
        }

        if (config("otp.code.type") == "simple"){
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
        $number =  implode("", array_slice($digits, 0, $length));
        while (strlen($number) < 5){
            shuffle($digits);
            $number .= implode("", array_slice($digits, 0, $length-strlen($number)));
        }
        return  $number;
    }
}
