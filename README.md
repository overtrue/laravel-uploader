# Laravel Uploader

:palm_tree: An upload component that allows you to save more time playing LOL.

## Installing

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

## Usage

1. Add the uploader component to right position of your form:

    ```php
    @uploader('images')
    ```

    or assign form name:

    ```php
    @uploader('images', ['name' => 'images'])
    ```

    or set max files:

    ```php
    @uploader('images', ['max' => 10])
    ```

    and strategy (default: 'default'):

    ```php
    @uploader('images', ['strategy' => 'avatar'])
    ```

2. Don't forget import uploader assets at the end of your template:

    ```php
    @uploader('assets')
    ```

## License

MIT