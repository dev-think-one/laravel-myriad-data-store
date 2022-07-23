# Myriad data downloader.

[![Packagist License](https://img.shields.io/packagist/l/yaroslawww/laravel-myriad-data-store?color=%234dc71f)](https://github.com/yaroslawww/laravel-myriad-data-store/blob/main/LICENSE.md)
[![Packagist Version](https://img.shields.io/packagist/v/yaroslawww/laravel-myriad-data-store)](https://packagist.org/packages/yaroslawww/laravel-myriad-data-store)
[![Total Downloads](https://img.shields.io/packagist/dt/yaroslawww/laravel-myriad-data-store)](https://packagist.org/packages/yaroslawww/laravel-myriad-data-store)
[![Build Status](https://scrutinizer-ci.com/g/yaroslawww/laravel-myriad-data-store/badges/build.png?b=main)](https://scrutinizer-ci.com/g/yaroslawww/laravel-myriad-data-store/build-status/main)
[![Code Coverage](https://scrutinizer-ci.com/g/yaroslawww/laravel-myriad-data-store/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/yaroslawww/laravel-myriad-data-store/?branch=main)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yaroslawww/laravel-myriad-data-store/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/yaroslawww/laravel-myriad-data-store/?branch=main)

Download specific myriad app data to your internal database.

## Installation

Install the package via composer:

```shell
composer require yaroslawww/laravel-myriad-data-store
```

Optionally you can publish the config file with:

```shell
php artisan vendor:publish --provider="MyriadDataStore\ServiceProvider" --tag="config"
```

This package works on top of [`yaroslawww/laravel-myriad-soap`](https://packagist.org/packages/yaroslawww/laravel-myriad-soap) - please follow this configuration.

## Usage

Easy way to download current database to your system is:

```shell
# Create all required tables 
# Note: All related IDs are not foreign keys - to allow download in any order
php artisan migrate
# Download related data
php artisan myriad-download:despatch-types
php artisan myriad-download:titles
php artisan myriad-download:issues
php artisan myriad-download:contact-types
php artisan myriad-download:product-types
php artisan myriad-download:order-package-types
php artisan myriad-download:order-status-types
# Use tinker to make multiple batch:
php artisan tinker
> for ($i=0;$i<3200;$i++) {$st=($i*125)+1; \Artisan::call("myriad-download:contacts {$st} --count=125 --queue=myriad");}
> for ($i=0;$i<3200;$i++) {$st=($i*125)+1; \Artisan::call("myriad-download:contacts-orders-basic {$st} --count=125 --queue=myriad");}
```


## Credits

- [![Think Studio](https://yaroslawww.github.io/images/sponsors/packages/logo-think-studio.png)](https://think.studio/) 
