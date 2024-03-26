<?php

namespace SaguiAi\MixpostAdapter\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use SaguiAi\MixpostAdapter\Facades\Settings;
use SaguiAi\MixpostAdapter\PostingSchedule;

class PostingScheduleTimesCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        if (!$value) {
            return PostingSchedule::defaultTimes();
        }

        return json_decode($value, true);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        $userTimezone = Settings::get('timezone');
        $default = PostingSchedule::defaultTimes();

        $array = Arr::map($default, function ($day, $dayIndex) use ($value, $userTimezone) {
            $day['status'] = $value[$dayIndex]['status'] ?? true;

            $day['times'] = collect($value[$dayIndex]['times'] ?? [])
                ->map(function ($time) use ($userTimezone) {
                    $hour = (int)Str::before($time['value'], ':');
                    $minute = (int)Str::after($time['value'], ':');

                    return [
                        'value' => Carbon::createFromTime($hour, $minute, 0, $userTimezone)
                            ->tz('UTC')
                            ->format('H:i')
                    ];
                })
                ->unique('value')
                ->sortBy('value')
                ->values();

            return $day;
        });

        return json_encode($array);
    }
}
