<?php

namespace SaguiAi\MixpostAdapter\Abstracts;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config as ConfigApp;
use SaguiAi\MixpostAdapter\Models\Config as ConfigModel;
use SaguiAi\MixpostAdapter\Contracts\Config as ConfigContract;

abstract class Config implements ConfigContract
{
    public function __construct(public readonly ?Request $request = null)
    {

    }

    public function save(array $data = []): void
    {
        foreach ($this->form() as $name => $_) {
            $payload = Arr::get($data, $name, $this->request->input($name));

            $this->insert($name, $payload);
            $this->putCache($name, $payload);
        }
    }

    public function insert(string $name, mixed $payload): void
    {
        ConfigModel::updateOrCreate(['name' => $name, 'group' => $this->group()], [
            'payload' => $payload
        ]);
    }

    public function get(string $name)
    {
        return $this->getCache($name, function () use ($name) {
            $payload = ConfigModel::get(
                property: "{$this->group()}.$name",
                default: Arr::get($this->form(), $name)
            );

            $this->putCache($name, $payload);

            return $payload;
        });
    }

    public function all(): array
    {
        return Arr::map($this->form(), function ($_, $name) {
            return $this->get($name);
        });
    }

    public function putCache(string $name, mixed $default = null): void
    {
        Cache::put($this->resolveCacheKey($name), $default);
    }

    public function getCache(string $name, mixed $default = null)
    {
        return Cache::get($this->resolveCacheKey($name), $default);
    }

    public function forgetCache(?string $name = null): void
    {
        if (!$name) {
            foreach ($this->all() as $name) {
                $this->forgetCache($name);
            }

            return;
        }

        Cache::forget($this->resolveCacheKey($name));
    }

    private function resolveCacheKey(string $key): string
    {
        return ConfigApp::get('mixpost.cache_prefix') . ".configs.{$this->group()}.$key";
    }
}
