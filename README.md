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

# Settings (optional)

## Customize

```sh
# Change email blade themes
php artisan vendor:publish --tag=webi-email

# Change lang translations
php artisan vendor:publish --tag=webi-lang

# Create config
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

## Mail images

```blade
<img src="{{ Storage::url($post->image->path) }}" alt="">
<img src="{{ $message->embed(asset('/images/image.jpg')) }}">
<img src="{{ $message->embed(public_path() . '/images/image.jpg') }}" />
<img src="data:image/png;base64,{{base64_encode(file_get_contents(resource_path('images/image.png')))}}" alt="">
```

## Test package

Tests readme file location

```sh
vendor/atomjoy/webi/tests/README.md
```
