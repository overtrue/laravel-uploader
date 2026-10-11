# Migrating to 4.0

## Runtime requirements

Version 4 requires PHP 8.3 or later within PHP 8, and Laravel 13.30 or later within Laravel 13. Laravel 9–12 and PHP 8.2 are no longer supported by this major version.

Upgrade the application and its other dependencies before requiring `overtrue/laravel-uploader:^4.0`.

## Failed writes throw

When the configured disk's `put()` returns `false`, `Strategy::upload()` now throws `League\Flysystem\UnableToWriteFile` with the destination path. It does not construct an upload response or dispatch `FileUploaded` for that failure. This applies even when the disk has `throw => false`.

Previously, the failed write could return a response with an empty path and dispatch a success event. Code that inspected that empty path must catch the exception instead:

```php
use League\Flysystem\UnableToWriteFile;
use Overtrue\LaravelUploader\StrategyResolver;

try {
    $response = StrategyResolver::resolveFromRequest($request, 'default')->upload();
} catch (UnableToWriteFile $exception) {
    // Report the failure or return an appropriate application error response.
    report($exception);

    return response()->json(['message' => 'The file could not be stored.'], 500);
}
```

Existing exceptions thrown by storage, response generation, or event listeners still propagate unchanged. Applications may also handle `League\Flysystem\FilesystemException` for other Flysystem failures. `FileUploading` still runs before storage is attempted. An exception after a successful write does not roll the file back, so avoid blindly retrying uploads after a response-generation or listener error.

The input stream is explicitly closed in a `finally` block on success and failure, including when an exception or listener retains a reference to it. A stream already closed by the storage implementation is not closed twice.

## Local URLs use the configured disk

`uploader.base_uri` applies only to non-local disks. A local disk named `public` now uses that disk's URL even when a remote base URI is configured. Check any application that depended on the incorrect CDN URL for local uploads; configure `filesystems.disks.public.url` for the desired local-disk URL.

Remote disks still use a configured base URI, or their own URL method when it is empty. Disks without a URL method fall back to the application URL. A null base URI remains supported without PHP deprecation warnings.

## MIME lists replace defaults

If a named strategy contains a `mimes` key, that whole list replaces the default strategy's list:

- `['image/jpeg']` allows only that listed client MIME type.
- `[]` rejects every MIME type.
- `['*']` permits all MIME types.
- An omitted `mimes` key inherits the default list.

Shorter lists no longer retain trailing default entries. Review strategies that depended on those extra entries. Other nested configuration keeps its existing recursive-merge behavior.

The package still checks `UploadedFile::getClientMimeType()`, which is client-provided metadata. This change is not content inspection. Apply application-specific content validation and access controls before accepting untrusted uploads.

## Configuration and successful responses

Publish configuration as documented in the README. The service provider still publishes rather than automatically merges the package configuration. Preserve your existing customizations when reviewing the published file; do not force-publish over them without a backup.

Successful upload response fields and types remain unchanged, including the string-valued `size`. Route registration, upload options, and successful event ordering are unchanged.
