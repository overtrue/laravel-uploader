<?php

namespace Overtrue\LaravelUploader\Contracts;

interface ChunkResult extends Result
{
    public function getPercentage(): int;
}
