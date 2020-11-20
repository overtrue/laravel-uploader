<?php

namespace Overtrue\LaravelUploader;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Fluent;
use Overtrue\LaravelUploader\Events\FileUploaded;
use Overtrue\LaravelUploader\Events\FileUploading;

class Strategy
{
    protected string $disk;
    protected string $directory;
    protected array $mimes = [];
    protected string $name;
    protected int $maxSize = 0;
    protected string $filenameType;
    protected UploadedFile $file;

    /**
     * @param  array  $config
     * @param  UploadedFile  $file
     */
    public function __construct(array $config, UploadedFile $file)
    {
        $config = new Fluent($config);

        $this->file = $file;
        $this->disk = $config->get('disk', \config('filesystems.default'));
        $this->mimes = $config->get('mimes', ['*']);
        $this->name = $config->get('name', 'file');
        $this->directory = $config->get('directory');
        $this->maxSize = $this->filesize2bytes($config->get('max_size', 0));
        $this->filenameType = $config->get('filename_type', 'md5_file');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDisk()
    {
        return $this->disk;
    }

    /**
     * @return array|mixed
     */
    public function getMimes()
    {
        return $this->mimes;
    }

    /**
     * @return int|string
     */
    public function getMaxSize()
    {
        return $this->maxSize;
    }

    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        switch ($this->filenameType) {
            case 'original':
                return $this->file->getClientOriginalName();
            case 'md5_file':
                return md5_file($this->file->getRealPath()).'.'.$this->file->getClientOriginalExtension();

                break;
            case 'random':
            default:
                return $this->file->hashName();
        }
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @return bool
     */
    public function isValidMime()
    {
        return $this->mimes === ['*'] || \in_array($this->file->getClientMimeType(), $this->mimes);
    }

    /**
     * @return bool
     */
    public function isValidSize()
    {
        return $this->file->getSize() <= $this->maxSize || 0 === $this->maxSize;
    }

    public function validate()
    {
        if (!$this->isValidMime()) {
            \abort(422, \sprintf('Invalid mime "%s".', $this->file->getClientMimeType()));
        }

        if (!$this->isValidSize()) {
            \abort(422, \sprintf('File has too large size("%s").', $this->file->getSize()));
        }
    }

    /**
     * @param  array  $options
     *
     * @return \Overtrue\LaravelUploader\Response
     */
    public function upload(array $options = [])
    {
        $this->validate();

        $path = \sprintf('%s/%s', \rtrim($this->formatDirectory($this->directory), '/'), $this->getFilename());

        $stream = fopen($this->file->getRealPath(), 'r');

        Event::dispatch(new FileUploading($this->file));

        $result = Storage::disk($this->disk)->put($path, $stream, $options);
        $response = new Response($result ? $path : false, $this, $this->file);

        Event::dispatch(new FileUploaded($this->file, $response, $this));

        if (is_resource($stream)) {
            fclose($stream);
        }

        return $response;
    }

    /**
     * Replace date variable in dir path.
     *
     * @param  string  $dir
     *
     * @return string
     */
    protected function formatDirectory(string $dir)
    {
        $replacements = [
            '{Y}' => date('Y'),
            '{m}' => date('m'),
            '{d}' => date('d'),
            '{H}' => date('H'),
            '{i}' => date('i'),
            '{s}' => date('s'),
        ];

        return str_replace(array_keys($replacements), $replacements, $dir);
    }

    /**
     * @param  mixed  $humanFileSize
     *
     * @return int
     */
    protected function filesize2bytes($humanFileSize)
    {
        $bytesUnits = array(
            'K' => 1024,
            'M' => 1024 * 1024,
            'G' => 1024 * 1024 * 1024,
            'T' => 1024 * 1024 * 1024 * 1024,
            'P' => 1024 * 1024 * 1024 * 1024 * 1024,
        );

        $bytes = floatval($humanFileSize);

        if (preg_match('~([KMGTP])$~si', rtrim($humanFileSize, 'B'), $matches)
            && !empty($bytesUnits[\strtoupper($matches[1])])) {
            $bytes *= $bytesUnits[\strtoupper($matches[1])];
        }

        return intval(round($bytes, 2));
    }
}
