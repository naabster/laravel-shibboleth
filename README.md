Laravel Shibboleth Service Provider
===================================

This package provides an easy way to implement Shibboleth Authentication for
Laravel 5.4

[![Build Status][12]][11]
[![Code Climate][3]][2]
[![Code Coverage][8]][7]

## Features ##

- Compatibility with Laravel 5.4
- Includes User and Group model examples
- Ability to *emulate* an IdP (via [mrclay/shibalike][13])

## Pre-Requisites ##

In order to use this plugin, we assume you already have a pre-existing
Shibboleth SP and Shibboleth IdP configured. This does not (and will not) go
into explaining how to set that up.

However, this might be helpful:
https://github.com/razorbacks/ubuntu-authentication/tree/master/shibboleth

## Installation ##

Use [composer][1] to require the latest release into your project:

    composer require razorbacks/laravel-shibboleth

If you're running Laravel >= 5.5, then you can skip this step, otherwise
you will need to manually register the service provider in your `config/app.php`
file within the `Providers` array.

```php
StudentAffairsUwm\Shibboleth\ShibbolethServiceProvider::class,
```

If you you would like to use the emulated IdP via shibalike, then you will need
to manually register it on any version - this is not automatically loaded even
in Laravel 5.5.

```php
StudentAffairsUwm\Shibboleth\ShibalikeServiceProvider::class,
```

Publish the default configuration file and entitlement migrations:

    php artisan vendor:publish --provider="StudentAffairsUwm\Shibboleth\ShibbolethServiceProvider"

You can also publish the views for the shibalike emulated IdP login:

    php artisan vendor:publish --provider="StudentAffairsUwm\Shibboleth\ShibalikeServiceProvider"

> University of Arkansas Users:
>
> To also logout with the IdP, set the the following in `config/shibboleth.php`
>
> ```php
> 'idp_logout' => '/Shibboleth.sso/Logout?return=https%3A%2F%2Fidp.uark.edu%2Fidp%2Fexit.jsp',
> ```

Run the database migrations:

    php artisan migrate

Once the migrations have run successfully, change the driver to `shibboleth` in
your `config/auth.php` file.

```php
'providers' => [
    'users' => [
        'driver' => 'shibboleth',
        'model'  => App\User::class,
    ],
],
```

Add Entitlement [`belongsToMany` relationship][14] to your User model:

```php
use StudentAffairsUwm\Shibboleth\Entitlement;

class User extends Model
{
    // ...

    /**
     * The entitlements that belong to the user.
     */
    public function entitlements()
    {
        return $this->belongsToMany(Entitlement::class);
    }
}
```

This assumes you have a `users` table with an integer primary key of `id`
so if you have a custom configuration, then you will need to manually edit the
[`database/migrations/2017_02_24_100000_create_entitlement_user_table.php`][15]
in order to match your custom table name and foreign key relationship.

Now users may login via Shibboleth by going to `https://example.com/shibboleth-login`
and logout using `https://example.com/shibboleth-logout` so you can provide a custom link
or redirect based on email address in the login form.

```php
@if (Auth::guest())
    <a href="/shibboleth-login">Login</a>
@else
    <a href="/shibboleth-logout">
        Logout {{ Auth::user()->name }}
    </a>
@endif
```

You may configure server variable mappings in `config/shibboleth.php` such as
the user's first name, last name, entitlements, etc. You can take a look at them
by reading what's been populated into the `$_SERVER` variable after authentication.

```php
<?php print_r($_SERVER);
```

You can check for an entitlement string of the current user statically:

```php
$entitlement = 'urn:mace:uark.edu:ADGroups:Computing Services:Something';

if (Entitlement::has($entitlement)) {
    // authorize something
}
```

Note that this skips the database and simply checks the `$_SERVER` variable,
so it will not work for multi-source authentication systems. If you're using
shared authorization schemas, then you'll have to use the eloquent methods.

## Local Users

This was designed to work side-by-side with the native authentication system
for projects where you want to have both Shibboleth and local users.
If you would like to allow local registration as well as authenticate Shibboleth
users, then use laravel's built-in auth system.

    php artisan make:auth

## Groups and Entitlements

The old 1.x versions required a base `App\Group` model, but this interfered with
native authorization policies not dependent on Shibboleth. Now a new model with
database migrations exists called `StudentAffairsUwm\Shibboleth\Entitlement`
which allows access control mechanisms to be built around the extraneous source.
Synchronization of these objects are independent of native groups, so
authorization can be delegated with inherent separation of concerns.

You will need to add entitlements in which you are interested to the database.
This can easily be accomplished with artisan tinker.

    php artisan tinker

```php
>>> $entitlement = new StudentAffairsUwm\Shibboleth\Entitlement;
=> StudentAffairsUwm\Shibboleth\Entitlement {#689}
>>> $entitlement->name = 'urn:mace:uark.edu:ADGroups:walton:Groups:linux02_sudoers'
=> "urn:mace:uark.edu:ADGroups:walton:Groups:linux02_sudoers"
>>> $entitlement->save()
=> true
```

Now you can draft [policies and gates][16] around these entitlements.

```php
$entitlement = 'urn:mace:uark.edu:ADGroups:Computing Services:Something';

if (Auth::user()->entitlements->contains('name', $entitlement)) {
    // authorize something
}
```

## JWTAuth Tokens ##

If you're taking advantage of token authentication with [tymon/jwt-auth][4] then
set this variable in your `.env`

    JWTAUTH=true

## Looking for Laravel 5.0 or 4? ##

Laravel 5.0 should be compatible up to tag 1.1.1

We have stopped development on the Laravel 4 version of this plugin for now.
We are welcoming pull requests, however!
Feel free to use any tag below 1.0.0 for Laravel 4 compatible versions.

[1]:https://getcomposer.org/
[2]:https://codeclimate.com/github/razorbacks/laravel-shibboleth
[3]:https://codeclimate.com/github/razorbacks/laravel-shibboleth/badges/gpa.svg
[4]:https://github.com/tymondesigns/jwt-auth
[7]:https://codecov.io/gh/razorbacks/laravel-shibboleth/branch/master
[8]:https://img.shields.io/codecov/c/github/razorbacks/laravel-shibboleth/master.svg
[11]:https://travis-ci.org/razorbacks/laravel-shibboleth
[12]:https://travis-ci.org/razorbacks/laravel-shibboleth.svg?branch=master
[13]:https://github.com/mrclay/shibalike
[14]:https://laravel.com/docs/5.4/eloquent-relationships#many-to-many
[15]:./src/database/migrations/2017_02_24_100000_create_entitlement_user_table.php
[16]:https://laravel.com/docs/5.4/authorization
