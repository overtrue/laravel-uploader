# Laravel Uploader

:palm_tree: An upload component for Laravel.

[![Sponsor me](https://github.com/overtrue/overtrue/blob/master/sponsor-me-button-s.svg?raw=true)](https://github.com/sponsors/overtrue)

## Installing

1. Install package: 

    ```sh
    $ composer require overtrue/laravel-uploader -vvv
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
       'as' => 'files.upload', 
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
\Overtrue\LaravelUploader\Strategy $strategy;
```

## Recommend clients

- [Element UI - Upload](https://element.eleme.cn/#/zh-CN/component/upload)
- [Plupload](https://www.plupload.com/)
- [Dropzone](https://www.dropzonejs.com/)


## :heart: Sponsor me 

[![Sponsor me](https://github.com/overtrue/overtrue/blob/master/sponsor-me.svg?raw=true)](https://github.com/sponsors/overtrue)

如果你喜欢我的项目并想支持它，[点击这里 :heart:](https://github.com/sponsors/overtrue)


## Project supported by JetBrains

Many thanks to Jetbrains for kindly providing a license for me to work on this and other open-source projects.

[![](https://resources.jetbrains.com/storage/products/company/brand/logos/jb_beam.svg)](https://www.jetbrains.com/?from=https://github.com/overtrue)

## PHP 扩展包开发

> 想知道如何从零开始构建 PHP 扩展包？
>
> 请关注我的实战课程，我会在此课程中分享一些扩展开发经验 —— [《PHP 扩展包实战教程 - 从入门到发布》](https://learnku.com/courses/creating-package)

## License

MIT
