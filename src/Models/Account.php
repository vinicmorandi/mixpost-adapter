<?php

namespace SaguiAi\MixpostAdapter\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use SaguiAi\MixpostAdapter\Casts\AccountMediaCast;
use SaguiAi\MixpostAdapter\Casts\EncryptArrayObject;
use SaguiAi\MixpostAdapter\Concerns\Model\HasUuid;
use SaguiAi\MixpostAdapter\Concerns\OwnedByWorkspace;
use SaguiAi\MixpostAdapter\Contracts\SocialProvider;
use SaguiAi\MixpostAdapter\Support\AccountSuffix;

class Account extends Model
{
    use HasFactory;
    use HasUuid;
    use OwnedByWorkspace;

    protected $table = 'mixpost_accounts';

    protected $fillable = [
        'uuid',
        'name',
        'username',
        'media',
        'provider',
        'provider_id',
        'data',
        'authorized',
        'access_token'
    ];

    protected $casts = [
        'media' => AccountMediaCast::class,
        'data' => 'array',
        'authorized' => 'boolean',
        'access_token' => EncryptArrayObject::class
    ];

    protected $hidden = [
        'access_token'
    ];

    protected static function booted()
    {
        static::updated(function ($account) {
            if ($account->wasChanged('media') && Arr::has($account->getOriginal('media'), 'disk')) {
                Storage::disk($account->getOriginal('media')['disk'])->delete($account->getOriginal('media')['path']);
            }
        });

        static::deleted(function ($account) {
            if ($account->media && Arr::has($account->media, 'disk')) {
                Storage::disk($account->media['disk'])->delete($account->media['path']);
            }
        });
    }

    public function scopeProvider(Builder $query, string|SocialProvider $provider): void
    {
        $query->where('provider', $provider instanceof SocialProvider ? $provider->identifier() : $provider);
    }

    public function suffix(): string
    {
        return AccountSuffix::getValue($this->data ?? []);
    }

    public function image(): ?string
    {
        if ($this->media) {
            return Storage::disk($this->media['disk'])->url($this->media['path']);
        }

        return null;
    }

    public function values(): array
    {
        return [
            'account_id' => $this->id,
            'provider_id' => $this->provider_id,
            'provider' => $this->provider,
            'name' => $this->name,
            'username' => $this->username,
            'data' => $this->data
        ];
    }

    public function providerOptions()
    {
        $rules = config('mixpost.social_provider_options');

        return Arr::get($rules, $this->provider);
    }

    public function relationships(): array
    {
        return Arr::get($this->data, 'relationships', []);
    }

    public function isAuthorized(): bool
    {
        return $this->authorized;
    }

    public function isUnauthorized(): bool
    {
        return !$this->authorized;
    }

    public function setUnauthorized(): void
    {
        $this->authorized = false;
        $this->save();
    }

    public function setAuthorized(): void
    {
        $this->authorized = true;
        $this->save();
    }

    public function updateAccessToken(array $data): void
    {
        $this->access_token = $data;

        $this->save();
    }
}
