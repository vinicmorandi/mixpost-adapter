<?php

namespace SaguiAi\MixpostAdapter\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use SaguiAi\MixpostAdapter\Concerns\Model\HasUuid;
use SaguiAi\MixpostAdapter\Concerns\OwnedByWorkspace;
use SaguiAi\MixpostAdapter\Enums\PostScheduleStatus;
use SaguiAi\MixpostAdapter\Enums\PostStatus;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;

class Post extends Model
{
    use HasFactory;
    use HasUuid;
    use OwnedByWorkspace;
    use SoftDeletes;

    public $table = 'mixpost_posts';

    protected $fillable = [
        'uuid',
        'user_id',
        'status',
        'schedule_status',
        'scheduled_at',
        'published_at'
    ];

    protected $casts = [
        'status' => PostStatus::class,
        'schedule_status' => PostScheduleStatus::class,
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    protected function scheduledAt(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->attributes['scheduled_at'] ? Carbon::parse($this->attributes['scheduled_at'])->shiftTimezone('UTC') : null,
        );
    }

    protected function publishedAt(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->attributes['published_at'] ? Carbon::parse($this->attributes['published_at'])->shiftTimezone('UTC') : null,
        );
    }

    public function accounts(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'mixpost_post_accounts', 'post_id', 'account_id')
            ->withPivot(['provider_post_id', 'data', 'errors'])
            ->orderByPivot('id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(PostVersion::class, 'post_id', 'id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'mixpost_tag_post', 'post_id', 'tag_id')
            ->orderByPivot('id');
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', PostStatus::FAILED->value);
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', PostStatus::SCHEDULED->value);
    }

    public function hasErrors(): bool
    {
        return $this->accounts()->wherePivot('errors', '!=', null)->exists();
    }

    public function canSchedule(): bool
    {
        // TODO: check if original content is not empty
        return $this->scheduled_at && !$this->scheduled_at->isPast() && $this->accounts()->exists();
    }

    public function isScheduled(): bool
    {
        return $this->status->name === PostStatus::SCHEDULED->name;
    }

    public function isPublished(): bool
    {
        return $this->status->name === PostStatus::PUBLISHED->name;
    }

    public function isFailed(): bool
    {
        return $this->status->name === PostStatus::FAILED->name;
    }

    public function isInHistory(): bool
    {
        return $this->isPublished() || $this->isFailed();
    }

    public function isScheduleProcessing(): bool
    {
        return $this->schedule_status->name === PostScheduleStatus::PROCESSING->name;
    }

    public function setDraft(): void
    {
        $this->status = PostStatus::DRAFT->value;
        $this->schedule_status = PostScheduleStatus::PENDING;
        $this->save();
    }

    public function setScheduled(Carbon|\Carbon\Carbon|null $date = null): void
    {
        $this->status = PostStatus::SCHEDULED->value;

        if ($date) {
            $this->scheduled_at = $date;
        }

        $this->save();
    }

    public function setScheduleProcessing(): void
    {
        $this->schedule_status = PostScheduleStatus::PROCESSING;
        $this->save();
    }

    public function setPublished(): void
    {
        $this->status = PostStatus::PUBLISHED->value;
        $this->published_at = Carbon::now()->utc();
        $this->schedule_status = PostScheduleStatus::PROCESSED;
        $this->save();
    }

    public function setFailed(): void
    {
        $this->status = PostStatus::FAILED->value;
        $this->schedule_status = PostScheduleStatus::PROCESSED;
        $this->save();
    }

    public function insertProviderData(Account $account, SocialProviderResponse $response): void
    {
        $this->accounts()->updateExistingPivot($account->id, [
            'provider_post_id' => $response->id,
            'data' => $response->data ? json_encode($response->data) : null,
            'errors' => null,
        ]);
    }

    public function insertErrors(Account $account, $errors): void
    {
        // TODO: Create a column for system error in `mixpost_post_accounts`
        $this->accounts()->updateExistingPivot($account->id, [
            'errors' => json_encode($errors)
        ]);
    }
}
