# Laravel Uploader

:palm_tree: An upload component that allows you to save more time playing LOL.

## Installing

1. register provider and configuration.
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

2. Routing

    You can register routes in `routes/web.php` or other routes file:

    ```php
    \LaravelUploader::routes();
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

## PHP 扩展包开发

> 想知道如何从零开始构建 PHP 扩展包？
>
> 请关注我的实战课程，我会在此课程中分享一些扩展开发经验 —— [《PHP 扩展包实战教程 - 从入门到发布》](https://learnku.com/courses/creating-package)

## License

MIT
