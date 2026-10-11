# Changelog

## 4.0.0 - Unreleased

### Breaking changes

- Require PHP `^8.3` and Laravel `^13.30`; remove Laravel 9–12 support.
- Throw `League\Flysystem\UnableToWriteFile` when storage reports a failed write instead of returning an empty-path success response. Do not dispatch `FileUploaded` for failed writes.

### Fixes

- Explicitly close upload streams when storage, response generation, or an uploaded-event listener throws, preserving the original exception.
- Look up the actual disk driver so local disks use their own URLs instead of a remote base URI.
- Normalize the default null base URI without a PHP deprecation.
- Replace strategy MIME lists atomically, including empty and wildcard lists, while preserving inheritance when omitted.

### Tests

- Add Testbench integration coverage for routes, published configuration, real file bytes, response types, validation, events, URLs, write failures, and stream cleanup.
- Add Laravel 13 CI with secure lowest dependencies on PHP 8.3 and highest dependencies on PHP 8.3/8.4/8.5, plus platform checks, security audits, and Pint.

See [the migration guide](MIGRATING.md) before upgrading.
