<?php

namespace Gupalo\DateUtils;

use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeInterface;
use Throwable;

class DateUtils
{
    public const FORMAT_SHORT = 'Y-m-d';
    public const FORMAT_FULL = 'Y-m-d H:i:s';

    /**
     * @param string|DateTimeInterface|int|null $date
     * @return DateTime
     */
    public static function create($date = 'now'): DateTime
    {
        try {
            if ($date === null) {
                $result = new DateTime();
            } elseif ($date instanceof DateTime) {
                $result = clone $date;
            } elseif ($date instanceof DateTimeInterface) {
                $result = (new DateTime())->setTimestamp($date->getTimestamp());
            } elseif (is_int($date)) {
                $result = (new DateTime())->setTimestamp($date);
            } else {
                $result = new DateTime($date);
            }
        } catch (Throwable $e) {
            $result = (new DateTime())->setTimestamp(0);
        }

        return $result;
    }

    /**
     * @param string|DateTimeInterface|int|null $date
     * @return ?DateTime
     */
    public static function createNull($date): ?DateTime
    {
        return ($date !== null) ? self::create($date) : null;
    }

    /**
     * @param string|DateTimeInterface|int|null $date
     * @param string $format
     * @return string
     */
    public static function format($date = null, string $format = self::FORMAT_FULL): string
    {
        return self::create($date)->format($format);
    }

    /**
     * @param string|DateTimeInterface|int|null $date
     * @param string|null $default
     * @param string $format
     * @return ?string
     */
    public static function formatNull($date = null, ?string $default = null, string $format = self::FORMAT_FULL): ?string
    {
        return ($date === null) ? $default : self::format($date, $format);
    }

    /**
     * @param string|DateTimeInterface|int|null $date
     * @return string
     */
    public static function formatShort($date = null): string
    {
        return self::create($date)->format(self::FORMAT_SHORT);
    }

    /**
     * @param string|DateTimeInterface|int|null $date
     * @param string|null $default
     * @return ?string
     */
    public static function formatShortNull($date = null, ?string $default = null): ?string
    {
        return ($date === null) ? $default : self::formatShort($date);
    }

    /**
     * @param string|DateTimeInterface|int|null $date
     * @return DateTime
     */
    public static function dayBegin($date = null): DateTime
    {
        $result = self::create($date);
        $result->setTime(0, 0);

        return $result;
    }

    /**
     * @param string|DateTimeInterface|int|null $date
     * @return ?DateTime
     */
    public static function dayBeginNull($date = null): ?DateTime
    {
        return ($date === null) ? null : self::dayBegin($date);
    }

    /**
     * @param string|DateTimeInterface|int|null $date
     * @return DateTime
     */
    public static function dayEnd($date = null): DateTime
    {
        $result = self::create($date);
        $result->setTime(23, 59, 59);

        return $result;
    }

    /**
     * @param string|DateTimeInterface|int|null $date
     * @return ?DateTime
     */
    public static function dayEndNull($date = null): ?DateTime
    {
        return ($date === null) ? null : self::dayEnd($date);
    }

    /**
     * @param string|DateTimeInterface|int|null $minDate
     * @param string|DateTimeInterface|int|null $maxDate
     * @param string $intervalSpec
     * @return DatePeriod|DateTime[]|null
     */
    public static function dailyPeriod($minDate, $maxDate, string $intervalSpec = 'P1D'): ?DatePeriod
    {
        try {
            $result = new DatePeriod(self::dayBegin($minDate), new DateInterval($intervalSpec), self::dayEnd($maxDate));
        } catch (Throwable $e) {
            $result = null;
        }

        return $result;
    }

    /**
     * @param string|DateTimeInterface|int|null $minDate
     * @param string|DateTimeInterface|int|null $maxDate
     * @param array|callable|string|int|null $template
     * @param string $format
     * @return DatePeriod|DateTime[]|null
     */
    public static function dailyPeriodTemplate($minDate, $maxDate, $template = [], $format = self::FORMAT_SHORT): array
    {
        $result = [];

        $dates = self::dailyPeriod($minDate, $maxDate);
        $isCallable = is_callable($template);
        foreach ($dates as $date) {
            $item = $isCallable ? $template($date) : $template;
            if (is_array($item) && array_key_exists('date', $item)) {
                $item['date'] = $date->format($format);
            }
            $result[$date->format($format)] = $item;
        }

        return $result;
    }

    public static function now(): DateTime
    {
        return self::create();
    }

    /**
     * @param int $countDays
     * @param string|DateTimeInterface|int|null $date
     * @return DateTime
     */
    public static function addDays(int $countDays, $date = null): DateTime
    {
        $date = self::create($date);

        self::addInterval($date, sprintf('P%sD', $countDays), $countDays * 86400);

        return $date;
    }

    /**
     * @param int $countDays
     * @param string|DateTimeInterface|int|null $date
     * @return DateTime
     */
    public static function subDays(int $countDays, $date = null): DateTime
    {
        $date = self::create($date);

        self::subInterval($date, sprintf('P%sD', $countDays), $countDays * 86400);

        return $date;
    }

    /**
     * @param int $countHours
     * @param string|DateTimeInterface|int|null $date
     * @return DateTime
     */
    public static function addHours(int $countHours, $date = null): DateTime
    {
        $date = self::create($date);

        self::addInterval($date, sprintf('PT%sH', $countHours),  $countHours * 3600);

        return $date;
    }

    /**
     * @param int $countHours
     * @param string|DateTimeInterface|int|null $date
     * @return DateTime
     */
    public static function subHours(int $countHours, $date = null): DateTime
    {
        $date = self::create($date);

        self::subInterval($date, sprintf('PT%sH', $countHours),  $countHours * 3600);

        return $date;
    }

    /**
     * @param int $countMintutes
     * @param string|DateTimeInterface|int|null $date
     * @return DateTime
     */
    public static function addMinutes(int $countMintutes, $date = null): DateTime
    {
        $date = self::create($date);

        self::addInterval($date, sprintf('PT%sM', $countMintutes),  $countMintutes * 60);

        return $date;
    }

    /**
     * @param int $countMintutes
     * @param string|DateTimeInterface|int|null $date
     * @return DateTime
     */
    public static function subMinutes(int $countMintutes, $date = null): DateTime
    {
        $date = self::create($date);

        self::subInterval($date, sprintf('PT%sM', $countMintutes),  $countMintutes * 60);

        return $date;
    }

    /**
     * @param string|DateTimeInterface|int|null $date
     * @return DateTimeInterface
     */
    public static function weekBegin($date = null): DateTimeInterface
    {
        return self::dayBegin(strtotime('monday this week', self::dayBegin($date)->getTimestamp()));
    }

    /**
     * @param string|DateTimeInterface|int|null $date
     * @return DateTimeInterface
     */
    public static function weekEnd($date = null): DateTimeInterface
    {
        return self::dayEnd(strtotime('sunday this week', self::dayBegin($date)->getTimestamp()));
    }

    /**
     * @param string|DateTimeInterface|int|null $date
     * @return DateTimeInterface
     */
    public static function nextMonday($date = null): DateTimeInterface
    {
        return self::dayBegin(strtotime('next monday', self::dayBegin($date)->getTimestamp()));
    }

    /**
     * @param string|DateTimeInterface|int|null $date
     * @return DateTime
     */
    public static function hourBegin($date = null): DateTime
    {
        $date = self::create($date);

        return self::create($date->format('Y-m-d H:00:00'));
    }

    /**
     * @param string|DateTimeInterface|int|null $date
     * @return DateTime
     */
    public static function hourEnd($date = null): DateTime
    {
        $date = self::create($date);

        return self::create($date->format('Y-m-d H:59:59'));
    }

    public static function today(): DateTime
    {
        return self::dayBegin();
    }

    public static function todayEnd(): DateTime
    {
        return self::dayEnd();
    }

    public static function yesterday(): DateTime
    {
        return self::dayBegin(self::subDays(1));
    }

    public static function yesterdayEnd(): DateTime
    {
        return self::dayEnd(self::subDays(1));
    }

    public static function tomorrow(): DateTime
    {
        return self::dayBegin(self::addDays(1));
    }

    public static function tomorrowEnd(): DateTime
    {
        return self::dayEnd(self::addDays(1));
    }

    /**
     * @param string|DateTimeInterface|int|null $date
     * @return float
     */
    public static function percentHourPassed($date = null)
    {
        $date = self::create($date);

        return (float)(60 * (int)$date->format('i') + (int)$date->format('s')) / 3600;
    }

    public static function diffDays($date1, $date2): int
    {
        $date1 = self::dayBegin($date1);
        $date2 = self::dayBegin($date2);

        $diff = $date1->diff($date2);

        return ($diff->invert ? 1 : -1) * $diff->days;
    }

    /**
     * @param string|DateTimeInterface|int|null $date1
     * @param string|DateTimeInterface|int|null $date2
     * @return int
     */
    public static function diffCalendarMonths($date1, $date2): int
    {
        $date1 = self::create($date1);
        $date2 = self::create($date2);

        $years = (int)$date1->format('Y') - (int)$date2->format('Y');
        $months = (int)$date1->format('m') - (int)$date2->format('m');

        return $years * 12 + $months;
    }

    /**
     * @param string|DateTimeInterface|int|null $date1
     * @param string|DateTimeInterface|int|null $date2
     * @return int
     */
    public static function diffCalendarWeeks($date1, $date2)
    {
        $date1 = self::create($date1);
        $date2 = self::create($date2);

        $weekDay1 = (int)$date1->format('N') - 1;
        $weekDay2 = (int)$date2->format('N') - 1;
        $days = $date1->diff($date2)->days;
        if ($date2 > $date1) {
            $days = -$days;
        }

        return ($days - $weekDay1 + $weekDay2) / 7;
    }

    private static function addInterval(DateTimeInterface $date, string $intervalSpec, int $seconds): void
    {
        try {
            $date->add(new DateInterval($intervalSpec));
        } catch (Throwable $e) {
            $date->setTimestamp($date->getTimestamp() + $seconds);
        }
    }

    private static function subInterval(DateTimeInterface $date, string $intervalSpec, int $seconds): void
    {
        try {
            $date->sub(new DateInterval($intervalSpec));
        } catch (Throwable $e) {
            $date->setTimestamp($date->getTimestamp() - $seconds);
        }
    }
}
