# Laravel OTP Authentication package for iranian

[![Latest Version on Packagist](https://img.shields.io/packagist/v/aliwebto/otp.svg?style=flat-square)](https://packagist.org/packages/aliwebto/otp)
[![Total Downloads](https://img.shields.io/packagist/dt/aliwebto/otp.svg?style=flat-square)](https://packagist.org/packages/aliwebto/otp)
![GitHub Actions](https://github.com/aliwebto/otp/actions/workflows/main.yml/badge.svg)

With this package you can easily handle OTP login/register in laravel .
## Installation

You can install the package via composer:

```bash
composer require aliwebto/otp
```
```bash
php artisan vendor:publish --provider="Aliwebto\Otp\OtpServiceProvider"
```
```bash
php artisan migrate
```


## Usage
```php
use Aliwebto\Otp\Otp;

// generate and send code
Otp::generate("09xxxxxxxxx");


// check entered code
Otp::check("code","09xxxxxxxxx");


// regenerate and send new code
Otp::regenerate("09xxxxxxxxx");


// get regenerate code cooldown in seconds
$code = Otp::lastCode("09xxxxxxxxx");
$seconds = Otp::regenerateCooldown($code);


// check code and login/register

$createUserIfNotExist = true;
$newUserEmail = random_int(100000,9999999)."@aliwento.com";
$newUserName = "User";

$isLoggedIn = Otp::authenticate("CODE","09xxxxxxxxx",$createUserIfNotExist,$newUserEmail,$newUserName);


```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email aliwebto@gmail.com instead of using the issue tracker.

## Credits

-   [Alireza Zarei](https://github.com/aliwebto)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

