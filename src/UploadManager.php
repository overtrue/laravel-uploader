<?php

namespace Overtrue\LaravelUploader;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Overtrue\LaravelUploader\Events\FileUploaded;
use Overtrue\LaravelUploader\Events\FileUploading;

class UploadManager
{
    protected ?string $storeAs = null;
    protected bool $validate = true;

    public function upload(Request $request, string | Strategy $strategy)
    {
    }

    public function uploadFromFile(string $path, string | Strategy $strategy)
    {
    }

    public function uploadFromContents(string $contents, string | Strategy $strategy)
    {
    }

    public function performUpload(UploadedFile $file, Strategy $strategy): Result
    {
        $path = $strategy->getPath($file);

        Event::dispatch(new FileUploading(file: $file, strategy: $strategy));

        $stream = fopen($file->getRealPath(), 'r');

        foreach ($strategy->getDisks() as $disk) {
            Storage::disk($disk)->put($path, $stream);
        }

        $result = new Result(path: $path, strategy: $strategy, file: $file);

        Event::dispatch(new FileUploaded(file: $file, strategy: $strategy, result: $result));

        if (is_resource($stream)) {
            fclose($stream);
        }

        return $result;
    }
}
