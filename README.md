# Laravel Uploader

:palm_tree: An upload component for Laravel.

[![Sponsor me](https://github.com/overtrue/overtrue/blob/master/sponsor-me-button-s.svg?raw=true)](https://github.com/sponsors/overtrue)

## Requirements

- PHP `^8.3`
- Laravel `^13.30`

Upgrading from 3.x? Read [the 4.0 migration guide](MIGRATING.md), especially the new failed-write exception contract.

## Installing

1. Install package: 

    ```sh
    $ composer require overtrue/laravel-uploader:^4.0
    ```

    Publish the configuration and translations before using the uploader:

    ```sh
    $ php artisan vendor:publish --provider=Overtrue\\LaravelUploader\\UploadServiceProvider
    ```

    The provider publishes configuration; it does not automatically merge defaults. Review your existing `config/uploader.php` when upgrading, preserving custom strategies.

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
string $size;
string $path;
?string $mime;
string $url;
string $relativeUrl;
string $filename;
string $originalName;
\Illuminate\Http\UploadedFile   $file;
\Overtrue\LaravelUploader\Strategy $strategy;
```

### Failed uploads

A storage write returning `false` throws `League\Flysystem\UnableToWriteFile`, even for a disk configured with `throw => false`. No success response or `FileUploaded` event is produced. Exceptions already thrown by storage or event listeners continue to propagate. See [catch guidance and retry considerations](MIGRATING.md#failed-writes-throw).

### URLs and MIME overrides

`uploader.base_uri` overrides URLs for non-local disks only. Local disks use their own URL configuration.

A custom strategy's `mimes` list replaces the entire default list. Omit it to inherit defaults, use `[]` to reject all types, or `['*']` to allow every type. MIME checks use client-provided metadata, not content inspection; apply any additional validation your application needs.

## Testing

```sh
composer install
composer test
composer check-style
composer audit
```

CI verifies Laravel 13 with lowest compatible dependencies on PHP 8.3, and highest compatible dependencies on PHP 8.3, 8.4, and 8.5. The lowest dependency set is checked on the minimum PHP runtime: older transitive dependencies can emit deprecations on PHP 8.4/8.5. Deprecations are not suppressed. Tests use temporary local storage and mocked remote disks; they require no cloud-storage credentials.

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

## License

MIT
