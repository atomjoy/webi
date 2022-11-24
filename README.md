# Webi auth library

Laravel web rest api authentication library.

## Install (laravel 9, php 8.1)

First set your .env variables (mysql, smtp) and then

```sh
composer require atomjoy/webi
```

## User model

```php
// app/Models/User.php
<?php

namespace App\Models;

use Webi\Models\WebiUser;

class User extends WebiUser
{
  function __construct(array $attributes = [])
  {
    parent::__construct($attributes);

    $this->mergeFillable([
      // 'mobile', 'website'
    ]);

    $this->mergeCasts([
      // 'status' => StatusEnum::class,
      // 'email_verified_at' => 'datetime:Y-m-d H:i:s',
    ]);

    // $this->hidden[] = 'secret_hash';
  }

  protected $dispatchesEvents = [
    // 'saved' => UserSaved::class,
    // 'deleted' => UserDeleted::class,
  ];
}
```

## Create login page

```php
// routes/web.php
Route::get('/login', function() {
    return 'My login page'; // return view('vue');
})->name('login');
```

## Create activation page

```php
// routes/web.php
use Webi\Http\Controllers\WebiActivate;

// Create your own activation page for Vue, Laravel
Route::get('/activate/{id}/{code}', [YourActivationController::class, 'index'])->middleware(['webi-locale']);

// Or for tests use json controller from Webi\Http\Controllers\WebiActivate.php
Route::get('/activate/{id}/{code}', [WebiActivate::class, 'index'])->middleware(['webi-locale']);
```

## Copy translations to app lang

```sh
php artisan vendor:publish --tag=webi-lang-pl --force
```

## Create db tables

```sh
# Create tables
php artisan migrate

# Refresh tables
php artisan migrate:fresh

# Seed data (optional)
php artisan db:seed --class=WebiSeeder
```

## Run application

```sh
php artisan serve
```

## Testing

Tests readme file location

```sh
tests/README.md
```

# Settings (optional)

## Customize

```sh
# Edit email blade themes
php artisan vendor:publish --tag=webi-email

# Edit lang translations
php artisan vendor:publish --tag=webi-lang

# Edit config
php artisan vendor:publish --tag=webi-config

# Override config
php artisan vendor:publish --tag=webi-config --force

# Add the image logo to your mail
php artisan vendor:publish --tag=webi-public

# Provider
php artisan vendor:publish --provider="Webi\WebiServiceProvider.php"
```

## Tables seeder

```sh
php artisan db:seed --class=WebiSeeder
```

## Update classes

```sh
composer update

composer dump-autoload -o

composer update --no-dev
```

# Web API Requests

Send requests as json. You get the answer as json: **{"message": "", "code": "", data: []}**. For more go to: **src/Http/Requests** and **src\Http\Controllers** directories or to routes file **routes/web.php**.

### /web/api/login

Method: **POST**

```sh
Params: 'email', 'password', 'remember_me'
Data: {"message": "", "code": "", data: ['user']}
```

### /web/api/register

Method: **POST**

```sh
Params: 'name', 'email', 'password', 'password_confirmation'
Data: {"message": "", "code": "", data: ['created']}
```

### /web/api/reset

Method: **POST**

```sh
Params: 'email'
Data: {"message": "", "code": "", data: null}
```

### /web/api/activate/{id}/{code}

Method: **GET**

```sh
Params: 'id', 'code'
Data: {"message": "", "code": "", data: null}
```

### /web/api/logout

Method: **GET**

```sh
Params: without params
Data: {"message": "", "code": "", data: null}
```

### /web/api/locale/{locale}

Method: **GET**

```sh
Params: 'locale'
Data: {"message": "", "code": "", data: ['locale']}
```

### /web/api/csrf

Method: **GET**

```sh
Params: without params
Data: {"message": "", "code": "", data: ['counter', 'locale', 'session_locale']}
```

### /web/api/logged

Method: **GET**

```sh
Params: without params
Data: {"message": "", "code": "", data: ['locale', 'user']}
```

### /web/api/change-password

Method: **POST** Auth: **true**

```sh
Params: 'password_current', 'password', 'password_confirmation'
Data: {"message": "", "code": "", data: null}
```

### /web/api/test/user, /web/api/test/worker, /web/api/test/admin

Method: **GET** Auth: **true**

```sh
Params: without params
Data: {"message": "", "code": "", data: ['user', 'ip']}
```
