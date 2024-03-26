<?php

namespace SaguiAi\MixpostAdapter\Concerns;

use Illuminate\Support\Arr;
use SaguiAi\MixpostAdapter\Support\AnonymousResourceCollectionWithParameters;

trait ResourceHasParameters
{
    protected array $additionalFields = [];
    protected array $only = [];
    protected array $except = [];

    public function additionalFields($value): static
    {
        $this->additionalFields = $value;

        return $this;
    }

    public function only(array $value): static
    {
        $this->only = $value;

        return $this;
    }

    public function except(array $value): static
    {
        $this->except = $value;

        return $this;
    }

    public function toArray($request): array
    {
        return $this->mergeWithAdditionalFields($this->computedFields());
    }

    protected function computedFields(): array
    {
        $fields = $this->fields();

        if (!empty($this->only)) {
            return Arr::only($fields, $this->only);
        }

        if (!empty($this->except)) {
            return Arr::except($fields, $this->except);
        }

        return $fields;
    }

    protected function mergeWithAdditionalFields(array $value): array
    {
        return array_merge($value, $this->additionalFields);
    }

    public static function collection($resource)
    {
        return tap(new AnonymousResourceCollectionWithParameters($resource, static::class), function ($collection) {
            if (property_exists(static::class, 'preserveKeys')) {
                $collection->preserveKeys = (new static([]))->preserveKeys === true;
            }
        });
    }
}
