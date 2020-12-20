# 

[![Latest Version on Packagist](https://img.shields.io/packagist/v/nddcoder/php-offline-license.svg?style=flat-square)](https://packagist.org/packages/nddcoder/php-offline-license)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/nddcoder/php-offline-license/run-tests?label=tests)](https://github.com/nddcoder/php-offline-license/actions?query=workflow%3ATests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/nddcoder/php-offline-license.svg?style=flat-square)](https://packagist.org/packages/nddcoder/php-offline-license)


This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require nddcoder/php-offline-license
```

## Basic Usage

```php
$phpOfflineLicense = new Nddcoder\PhpOfflineLicense::create('secret');
$license = $phpOfflineLicense->generate(); //uuid like string
$licenseInfo = $phpOfflineLicense->getInfo($license); //LicenseInfo object
$isValid = $phpOfflineLicense->validate($license);//true or false
```

### Generate license with expire time

```php
$phpOfflineLicense->generate(time() + 86400); //license expire time after 1 day
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Dung Nguyen Dang](https://github.com/dangdungcntt)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
