<?php

namespace Overtrue\LaravelUploader;

use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\UploadedFile;
use Overtrue\LaravelUploader\Contracts\Strategy as StrategyInterface;

class Strategy implements StrategyInterface
{
    protected Repository $config;
    protected int $maxSize = 0;
    protected array $disks;
    protected string $formName;
    protected array $allowedMimes = [];
    protected string $filenameType;

    public function __construct(array $config)
    {
        $this->config = new Config($config);

        $this->formName = $this->config->get('form_name', 'file');
        $this->maxSize = $this->size2bytes($this->config->get('max_size', '2m'));
        $this->filenameType = $this->config->get('filename_type', 'md5_file');
        $this->disks = (array)$this->config->get('disks', \config('filesystems.default'));
        $this->allowedMimes = $this->config->get('allowed_mimes', ['image/jpeg', 'image/png', 'image/bmp', 'image/gif']);
    }

    public function getFormName(): string
    {
        return $this->formName;
    }

    public function getDisks(): array
    {
        return $this->disks;
    }

    public function getAllowedMimes(): array
    {
        return $this->allowedMimes;
    }

    public function getMaxSize(): int
    {
        return $this->maxSize;
    }

    public function getPath(UploadedFile $uploadedFile): string
    {
        $directory = \rtrim($this->config->get('directory', ''), '/');

        if (!empty($directory)) {
            $directory = $this->replacePathVariables($directory);
        }

        return join(
            \DIRECTORY_SEPARATOR,
            [
                $directory,
                match ($this->filenameType) {
                    'original' => $uploadedFile->getClientOriginalName(),
                    'md5_file' => md5_file($uploadedFile->getRealPath()) . '.' . $uploadedFile->getClientOriginalExtension(),
                    default => $uploadedFile->hashName(),
                },
            ]
        );
    }

    protected function replacePathVariables(string $path): string
    {
        $replacements = [
            '{Y}' => date('Y'),
            '{m}' => date('m'),
            '{d}' => date('d'),
            '{H}' => date('H'),
            '{i}' => date('i'),
            '{s}' => date('s'),
        ];

        return str_replace(array_keys($replacements), $replacements, $path);
    }

    protected function size2bytes($humanFileSize): int
    {
        $bytesUnits = [
            'K' => 1024,
            'M' => 1024 * 1024,
            'G' => 1024 * 1024 * 1024,
            'T' => 1024 * 1024 * 1024 * 1024,
            'P' => 1024 * 1024 * 1024 * 1024 * 1024,
        ];

        $bytes = floatval($humanFileSize);

        if (preg_match('~([KMGTP])$~si', rtrim($humanFileSize, 'B'), $matches)
            && !empty($bytesUnits[\strtoupper($matches[1])])) {
            $bytes *= $bytesUnits[\strtoupper($matches[1])];
        }

        return intval(round($bytes, 2));
    }

    public function getChunkCountKey(): string
    {
        return $this->config->get('chunk_count_key', 'chunk_count');
    }

    public function getChunkIndexKey(): string
    {
        return $this->config->get('chunk_index_key', 'chunk_index');
    }

    public function getChunkMaxSize(): int
    {
        return $this->size2bytes($this->config->get('chunk_index_key', 'chunk_index'));
    }
}
