<?php

/*
 *
 * Aliwebto
 * OTP
 *
 */
return [
    "code" => [
        "type" => env("OTP_CODE_TYPE", "simple"),// full - simple  | full uses all numbers 0-9 but simple uses only 3 numbers and best user experience ,
        "length" => env("OTP_CODE_LENGTH", 5), // length of code
        "time" => env("OTP_CODE_TIME", 10) // time of code lifespan in minute
    ],
    "security" => [
        "resendTime" => env("OTP_RESEND_Time", 60) // time of resend code cooldown
    ],
    "features" => [
        "resend" => true // can resend code
    ]
];
