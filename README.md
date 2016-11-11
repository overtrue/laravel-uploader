# Laravel Uploader

:video_game: An upload component that allows you to save more time playing games.

# Installing

```sh
$ composer require overtrue/laravel-uploader -vvv
```

then register the package service provider, add the following line to `providers` section of `config/app.php`:


```php
Overtrue\LaravelUploader\UploadServiceProvider::class,
```

and publish the assets using command:

```sh
$ php artisan vendor:publish --provider=Overtrue\\LaravelUploader\\UploadServiceProvider
```

# Usage

TODO

# License

MIT