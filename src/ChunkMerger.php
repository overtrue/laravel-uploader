<?php

namespace Overtrue\LaravelUploader;

use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class ChunkMerger
{
    public static function merge(string $formDirectory, ?string $disk = 'local', ?string $toFile = null): File
    {
        $disk = Storage::disk($disk);
        $toFile = $toFile ?? \tempnam(\sys_get_temp_dir(), 'chunk_merged_');
        $directory = \dirname($toFile);

        $disk->makeDirectory($directory);

        $mergedFile = new File($disk->path($toFile));
        $mergedFileInfo = $mergedFile->openFile('ab+');

        foreach ($disk->files($formDirectory) as $chunk) {
            $chunkFileInfo = (new File($disk->path($chunk)))->openFile('rb');

            while (!$chunkFileInfo->eof()) {
                $mergedFileInfo->fwrite($chunkFileInfo->fread(4096));
            }
        }

        return $mergedFile;
    }
}
