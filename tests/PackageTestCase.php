<?php

namespace Aliwebto\OTP\Tests;

use Aliwebto\Otp\OtpServiceProvider;
use Orchestra\Testbench\TestCase;

class PackageTestCase extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            OtpServiceProvider::class,
        ];
    }
}
