# Laravel Uploader

:palm_tree: An upload component for Laravel.

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
    
    // custom
    \LaravelUploader::routes([
       'as' => 'file-upload', 
       'middleware' => ['auth'],
       //...
    ]); 
    ```

## Usage

### Custom controller

If you want to handle file upload, you can do it as simple as:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Overtrue\LaravelUploader\StrategyResolver;

class MyUploadController extends BaseController
{
    public function upload(Request $request)
    {
        return StrategyResolver::resolveFromRequest($request, $request->get('strategy', 'default'))->upload();
    }
}
``` 

### Custom Response

If you want update the response, you can get key information from the return value object and return a new response:

```php
    public function upload(Request $request)
    {
        $response = StrategyResolver::resolveFromRequest($request, $request->get('strategy', 'default'))->upload();
        
        return response()->json([
            'status' => 'success',
            'url' => $response->url,
            'origin_name' => $response->originalName,
            //...
        ]);
    }
```

You can get all these public properties:

```php
int $size;
string $path;
string $mime;
string $url;
string $relativeUrl;
string $filename;
string $originalName;
\Illuminate\Http\UploadedFile   $file;
\Overtrue\LaravelUploader\strategy $strategy;
```

## Recommend clients

- [Element UI - Upload](https://element.eleme.cn/#/zh-CN/component/upload)
- [Plupload](https://www.plupload.com/)
- [Dropzone](https://www.dropzonejs.com/)

## PHP 扩展包开发

> 想知道如何从零开始构建 PHP 扩展包？
>
> 请关注我的实战课程，我会在此课程中分享一些扩展开发经验 —— [《PHP 扩展包实战教程 - 从入门到发布》](https://learnku.com/courses/creating-package)

## License

MIT
