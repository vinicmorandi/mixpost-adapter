<?php

namespace SaguiAi\MixpostAdapter\Support;

use Illuminate\Support\Facades\Storage;
use SaguiAi\MixpostAdapter\Contracts\MediaConversion;

class MediaConversionData
{
    protected MediaConversion $conversion;

    public function __construct(MediaConversion $conversion)
    {
        $this->setConversion($conversion);
    }

    public static function conversion(MediaConversion $conversion): static
    {
        return new static($conversion);
    }

    public function get(): array
    {
        $reflection = new \ReflectionClass($this->conversion);

        $path = $this->conversion->getPath();
        $disk = $this->conversion->getToDisk();

        return [
            'engine' => $this->conversion->getEngineName(),
            'path' => $this->conversion->getPath(),
            'disk' => $this->conversion->getToDisk(),
            'size' => Storage::disk($disk)->size($path),
            'name' => $reflection->getProperty('name')->getValue($this->conversion),
        ];
    }

    private function setConversion(MediaConversion $conversion): static
    {
        $this->conversion = $conversion;

        return $this;
    }
}
