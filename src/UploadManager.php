<?php

namespace Overtrue\LaravelUploader;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Overtrue\LaravelUploader\Events\ChunkUploaded;
use Overtrue\LaravelUploader\Events\ChunkUploading;
use Overtrue\LaravelUploader\Events\FileUploaded;
use Overtrue\LaravelUploader\Events\FileUploading;

class UploadManager
{
    public function upload(Request $request, string | Strategy $strategy)
    {
        $driver = $this->getDriver($strategy);

        if ($driver->supportChunkUpload() && $driver->isChunkRequest($request)) {
            return $this->performChunkUpload(chunk: $driver->getChunk($request, $strategy->getConfig()), strategy: $strategy);
        }

        return $this->performSingleUpload(file: $request->file($strategy->getFormName()), strategy: $strategy);
    }

    public function uploadFromFile(string $path, string | Strategy $strategy)
    {
    }

    public function uploadFromContents(string $contents, string | Strategy $strategy)
    {
    }

    public function performSingleUpload(UploadedFile $file, Strategy $strategy): Result
    {
        $path = $strategy->getPath($file);

        Event::dispatch(new FileUploading(file: $file, strategy: $strategy));

        $result = $this->saveFile($file, $strategy, $path);

        Event::dispatch(new FileUploaded(file: $file, strategy: $strategy, result: $result));

        return $result;
    }

    public function performChunkUpload(Chunk $chunk, Strategy $strategy): Result
    {
        $disk = Storage::disk($strategy->getChunkDisk());
        $directory = $disk->makeDirectory($chunk->getChunksId());
        $path = $chunk->getIndex().'.part';

        Event::dispatch(new ChunkUploading(chunk: $chunk, strategy: $strategy));

        $result = $this->saveFile($chunk->getFile(), $strategy, $path);

        Event::dispatch(new ChunkUploaded(chunk: $chunk, result: $result, strategy: $strategy));

        if ($chunk->isLast()) {
            $file = ChunkMerger::merge(\dirname($path), $strategy->getDisk());
            $uploadedFile = new UploadedFile($file->getPath(), $chunk->getFileOriginalName(), null, null, true);

            return $this->performSingleUpload($uploadedFile, $strategy);
        }

        return $result;
    }

    public function saveFile(UploadedFile $file, Strategy $strategy, string $path): Result
    {
        $stream = fopen($file->getRealPath(), 'r');

        Storage::disk($strategy->getDisk())->put($path, $stream);

        $result = new Result(file: $file, path: $path, strategy: $strategy);

        if (is_resource($stream)) {
            fclose($stream);
        }

        return $result;
    }

    public function getDriver(string | Strategy $strategy): Contracts\Driver | Contracts\ChunkDriver
    {
        /* @var \Overtrue\LaravelUploader\Contracts\Driver|\Overtrue\LaravelUploader\Contracts\ChunkDriver $driver */
        return \app(DriverManager::class)->driver($strategy->getDriver());
    }
}
