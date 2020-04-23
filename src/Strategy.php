<?php

/*
 * This file is part of the overtrue/laravel-uploader.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\LaravelUploader;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Fluent;
use Overtrue\LaravelUploader\Events\FileUploaded;
use Overtrue\LaravelUploader\Events\FileUploading;

/**
 * Class Strategy.
 */
class Strategy
{
    /**
     * @var string
     */
    protected $disk;

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var array
     */
    protected $mimes = [];

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $maxSize = 0;

    /**
     * @var string
     */
    protected $filenameType;

    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Strategy constructor.
     *
     * @param array                    $config
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(array $config, Request $request)
    {
        $config = new Fluent($config);

        $this->request = $request;
        $this->disk = $config->get('disk', \config('filesystems.default'));
        $this->mimes = $config->get('mimes', ['*']);
        $this->name = $config->get('name', 'file');
        $this->directory = $config->get('directory');
        $this->maxSize = $config->get('max_size', 0);
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
     * @return int
     */
    public function getMaxSize()
    {
        return $this->maxSize;
    }

    public function getFile()
    {
        return $this->request->file($this->name);
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        switch ($this->filenameType) {
            case 'original':
                return $this->getFile()->getClientOriginalName();
            case 'md5_file':
                return md5_file($this->getFile()->getRealPath()).'.'.$this->getFile()->getClientOriginalExtension();

                break;
            case 'random':
            default:
                return $this->getFile()->hashName();
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
        return $this->mimes === ['*'] || \in_array($this->getFile()->getClientMimeType(), $this->mimes);
    }

    /**
     * @return bool
     */
    public function isValidSize()
    {
        $maxSize = $this->filesize2bytes($this->maxSize);

        return $this->getFile()->getSize() <= $maxSize || 0 === $maxSize;
    }

    public function validate()
    {
        if (!$this->request->hasFile($this->getName())) {
            \abort(422, 'no file found.');
        }

        $file = $this->getFile();

        if (!$this->isValidMime($file->getClientMimeType())) {
            \abort(422, \sprintf('Invalid mime "%s".', $file->getClientMimeType()));
        }

        if (!$this->isValidSize($file->getSize())) {
            \abort(422, \sprintf('File has too large size("%s").', $file->getSize()));
        }
    }

    /**
     * @param array $options
     *
     * @return \Overtrue\LaravelUploader\Response
     */
    public function upload(array $options = [])
    {
        $this->validate();

        $file = $this->getFile();

        $path = \sprintf('%s/%s', \rtrim($this->formatDirectory($this->directory), '/'), $this->getFilename($file));

        $stream = fopen($file->getRealPath(), 'r');

        Event::dispatch(new FileUploading($file));

        $result = Storage::disk($this->disk)->put($path, $stream, $options);
        $response = new Response($result ? $path : false, $this, $file);

        Event::dispatch(new FileUploaded($file, $response, $this));

        if (is_resource($stream)) {
            fclose($stream);
        }

        return $response;
    }

    /**
     * Replace date variable in dir path.
     *
     * @param string $dir
     *
     * @return string
     */
    protected function formatDirectory($dir)
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
     * @param string $humanFileSize
     *
     * @return int
     */
    protected function filesize2bytes($humanFileSize)
    {
        $bytes = 0;

        $bytesUnits = array(
            'K' => 1024,
            'M' => 1024 * 1024,
            'G' => 1024 * 1024 * 1024,
            'T' => 1024 * 1024 * 1024 * 1024,
            'P' => 1024 * 1024 * 1024 * 1024 * 1024,
        );

        $bytes = floatval($humanFileSize);

        if (preg_match('~([KMGTP])$~si', rtrim($humanFileSize, 'B'), $matches) && !empty($bytesUnits[\strtoupper($matches[1])])) {
            $bytes *= $bytesUnits[\strtoupper($matches[1])];
        }

        return intval(round($bytes, 2));
    }
}
