<?php

namespace SaguiAi\MixpostAdapter;

use Carbon\CarbonPeriod;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use SaguiAi\MixpostAdapter\Facades\Settings;
use SaguiAi\MixpostAdapter\Models\Post;
use SaguiAi\MixpostAdapter\Models\PostingSchedule as PostingScheduleModel;

class PostingSchedule
{
    public static function timesUserTimezone(): array
    {
        $userTimezone = Settings::get('timezone');

        return Arr::map(self::timesUTCTimezone(), function ($day) use ($userTimezone) {
            $day['times'] = Arr::map($day['times'], function ($time) use ($userTimezone) {
                $hour = (int)Str::before($time['value'], ':');
                $minute = (int)Str::after($time['value'], ':');

                $time['value'] = Carbon::createFromTime($hour, $minute, 0, 'UTC')
                    ->tz($userTimezone)
                    ->format('H:i');

                return $time;
            });

            return $day;
        });
    }

    public static function timesUTCTimezone()
    {
        $postingSchedule = PostingScheduleModel::first();

        if (!$postingSchedule) {
            return self::defaultTimes();
        }

        return $postingSchedule->times;
    }

    public static function defaultTimes(): array
    {
        // We define times according to the UTC timezone
        $times = [
            [
                'value' => '06:30',
            ],
            [
                'value' => '10:30',
            ],
            [
                'value' => '15:30',
            ],
            [
                'value' => '17:40',
            ]
        ];

        $mondayToSaturday = Arr::map(range(1, 6), function ($id) use ($times) {
            return [
                'id' => $id,
                'status' => true,
                'times' => $times
            ];
        });

        return array_merge($mondayToSaturday, [
            [
                'id' => 0,
                'status' => true,
                'times' => $times
            ]
        ]);
    }

    public static function hasAvailableTimes(): bool
    {
        $times = self::timesUTCTimezone();

        $filter = Arr::where($times, function ($day) {
            return $day['status'] && !empty(Arr::wrap($day['times']));
        });

        return !empty($filter);
    }

    public static function getNextScheduleDateTime(): ?Carbon
    {
        $maxWeeks = 52; // 1 Year

        // Sort week days
        $times = array_values(Arr::sort(self::timesUTCTimezone(), function ($day) {
            return $day['id'];
        }));

        $startDate = Carbon::now('UTC');

        $scheduledPosts = Post::query()
            ->select('scheduled_at')
            ->scheduled()
            ->where('scheduled_at', '>=', $startDate)
            ->get();

        foreach (range(0, ($maxWeeks - 1)) as $weekIndex) {
            // If weekIndex is equal to 0, we are starting with current week
            $week = $weekIndex === 0 ? $startDate : $startDate->addWeek();

            $startOfWeek = $week->copy()->startOfWeek();
            $endOfWeek = $week->copy()->endOfWeek();

            $weekDays = CarbonPeriod::create(
                $startOfWeek,
                $endOfWeek
            );

            foreach ($weekDays as $day) {
                if (!$day->isToday() && $day->isPast()) {
                    continue;
                }

                $dayTimes = $times[$day->dayOfWeek];

                if ($dayTimes['status']) {
                    foreach ($dayTimes['times'] as $time) {
                        $nextDateTime = "{$day->toDateString()} {$time['value']}:00";

                        $foundInPosts = false;

                        foreach ($scheduledPosts as $post) {
                            if ($post->scheduled_at->toDateTimeString() === $nextDateTime) {
                                $foundInPosts = true;
                                break;
                            }
                        }

                        if (!$foundInPosts) {
                            $result = Carbon::parse($nextDateTime, 'UTC');

                            // Check if this time is already past today
                            if ($result->isPast()) {
                                continue;
                            }

                            return $result;
                        }
                    }
                }
            }
        }

        return null;
    }
}
