<?php


namespace Overtrue\LaravelUploader;


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Fluent;

class Strategy
{
    protected $disk;
    protected $directory;
    protected $mimes = [];
    protected $name;
    protected $maxFileSize = 0;
    protected $filenameHash;

    public function __construct(array $config)
    {
        $config = new Fluent($config);

        $this->disk = $config->get('disk', \config('filesystems.default'));
        $this->directory = $config->get('directory');
        $this->mimes = $config->get('mimes', ['*']);
        $this->name = $config->get('name', 'file');
    }

    public function getInputName()
    {
        return $this->inputName;
    }

    /**
     * @param \Illuminate\Http\UploadedFile $file
     *
     * @return string|null
     */
    public function getFilename(UploadedFile $file)
    {
        switch ($this->filenameHash) {
            case 'original':
                return $file->getClientOriginalName();
            case 'md5_file':
                return md5_file($file->getRealPath()).'.'.$file->guessExtension();

                break;
            case 'random':
            default:
                return $file->hashName();
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
     * @param string $mime
     *
     * @return bool
     */
    public function isValidMime(string $mime)
    {
        return $this->mimes === ['*'] || \in_array($mime, $this->mimes);
    }

    /**
     * Replace date variable in dir path.
     *
     * @param string $dir
     *
     * @return string
     */
    protected function formatDir($dir)
    {
        $replacements = [
            '{Y}' => date('Y'),
            '{m}' => date('m'),
            '{d}' => date('d'),
            '{H}' => date('H'),
            '{i}' => date('i'),
        ];

        return str_replace(array_keys($replacements), $replacements, $dir);
    }
}