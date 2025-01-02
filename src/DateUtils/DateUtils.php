<?php /** @noinspection UnknownInspectionInspection */

namespace Gupalo\DateUtils;

use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeInterface;
use Throwable;

class DateUtils
{
    public const string FORMAT_SHORT = 'Y-m-d';
    public const string FORMAT_FULL = 'Y-m-d H:i:s';

    public static function create(DateTimeInterface|int|string|null $date = 'now'): DateTime
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
        } catch (Throwable) {
            $result = (new DateTime())->setTimestamp(0);
        }

        return $result;
    }

    public static function createNull(DateTimeInterface|int|string|null $date): ?DateTime
    {
        return ($date !== null) ? self::create($date) : null;
    }

    public static function format(DateTimeInterface|int|string|null $date = null, string $format = self::FORMAT_FULL): string
    {
        return self::create($date)->format($format);
    }

    public static function formatNull(
        DateTimeInterface|int|string $date = null,
        ?string $default = null,
        string $format = self::FORMAT_FULL,
    ): ?string
    {
        return ($date === null) ? $default : self::format($date, $format);
    }

    public static function formatShort(DateTimeInterface|int|string|null $date = null): string
    {
        return self::create($date)->format(self::FORMAT_SHORT);
    }

    public static function formatShortNull(DateTimeInterface|int|string|null $date = null, ?string $default = null): ?string
    {
        return ($date === null) ? $default : self::formatShort($date);
    }

    public static function dayBegin(DateTimeInterface|int|string|null $date = null): DateTime
    {
        $result = self::create($date);
        $result->setTime(0, 0);

        return $result;
    }

    public static function dayBeginNull(DateTimeInterface|int|string|null $date = null): ?DateTime
    {
        return ($date === null) ? null : self::dayBegin($date);
    }

    public static function dayEnd(DateTimeInterface|int|string|null $date = null): DateTime
    {
        $result = self::create($date);
        $result->setTime(23, 59, 59);

        return $result;
    }

    public static function dayEndNull(DateTimeInterface|int|string|null $date = null): ?DateTime
    {
        return ($date === null) ? null : self::dayEnd($date);
    }

    /**
     * @return DatePeriod|DateTime[]|null
     * @noinspection PhpDocSignatureInspection
     */
    public static function dailyPeriod(
        DateTimeInterface|int|string|null $minDate,
        DateTimeInterface|int|string|null $maxDate,
        string $intervalSpec = 'P1D',
    ): ?DatePeriod
    {
        try {
            $result = new DatePeriod(self::dayBegin($minDate), new DateInterval($intervalSpec), self::dayEnd($maxDate));
        } catch (Throwable) {
            $result = null;
        }

        return $result;
    }

    public static function dailyPeriodTemplate(
        DateTimeInterface|int|string|null $minDate,
        DateTimeInterface|int|string|null $maxDate,
        callable|int|array|string|null $template = [],
        string $format = self::FORMAT_SHORT,
    ): array
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

    public static function addDays(int $countDays, DateTimeInterface|int|string $date = null): DateTime
    {
        $date = self::create($date);

        self::addInterval($date, sprintf('P%sD', $countDays), $countDays * 86400);

        return $date;
    }

    public static function subDays(int $countDays, DateTimeInterface|int|string $date = null): DateTime
    {
        $date = self::create($date);

        self::subInterval($date, sprintf('P%sD', $countDays), $countDays * 86400);

        return $date;
    }

    public static function subDaysFloat(float $countDays, DateTimeInterface|int|string $date = null): DateTime
    {
        $date = self::create($date);

        self::subInterval($date, sprintf('P%sD', $countDays), (int)($countDays * 86400));

        return $date;
    }

    public static function addHours(int $countHours, DateTimeInterface|int|string $date = null): DateTime
    {
        $date = self::create($date);

        self::addInterval($date, sprintf('PT%sH', $countHours),  $countHours * 3600);

        return $date;
    }

    public static function subHours(int $countHours, DateTimeInterface|int|string $date = null): DateTime
    {
        $date = self::create($date);

        self::subInterval($date, sprintf('PT%sH', $countHours),  $countHours * 3600);

        return $date;
    }

    public static function addMinutes(int $countMinutes, DateTimeInterface|int|string $date = null): DateTime
    {
        $date = self::create($date);

        self::addInterval($date, sprintf('PT%sM', $countMinutes),  $countMinutes * 60);

        return $date;
    }

    public static function subMinutes(int $countMinutes, DateTimeInterface|int|string $date = null): DateTime
    {
        $date = self::create($date);

        self::subInterval($date, sprintf('PT%sM', $countMinutes),  $countMinutes * 60);

        return $date;
    }

    public static function addSeconds(int $countSeconds, DateTimeInterface|int|string $date = null): DateTime
    {
        $date = self::create($date);

        $date->setTimestamp($date->getTimestamp() + $countSeconds);

        return $date;
    }

    public static function subSeconds(int $countSeconds, DateTimeInterface|int|string $date = null): DateTime
    {
        $date = self::create($date);

        $date->setTimestamp($date->getTimestamp() - $countSeconds);

        return $date;
    }

    public static function weekBegin(DateTimeInterface|int|string|null $date = null): DateTimeInterface
    {
        return self::dayBegin(strtotime('monday this week', self::dayBegin($date)->getTimestamp()));
    }

    public static function weekEnd(DateTimeInterface|int|string|null $date = null): DateTimeInterface
    {
        return self::dayEnd(strtotime('sunday this week', self::dayBegin($date)->getTimestamp()));
    }

    public static function nextMonday(DateTimeInterface|int|string|null $date = null): DateTimeInterface
    {
        return self::dayBegin(strtotime('next monday', self::dayBegin($date)->getTimestamp()));
    }

    public static function hourBegin(DateTimeInterface|int|string|null $date = null): DateTime
    {
        $date = self::create($date);

        return self::create($date->format('Y-m-d H:00:00'));
    }

    public static function hourEnd(DateTimeInterface|int|string|null $date = null): DateTime
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

    public static function percentHourPassed(DateTimeInterface|int|string|null $date = null): float
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

    public static function diffCalendarMonths(
        DateTimeInterface|int|string|null $date1,
        DateTimeInterface|int|string|null $date2,
    ): int
    {
        $date1 = self::create($date1);
        $date2 = self::create($date2);

        $years = (int)$date1->format('Y') - (int)$date2->format('Y');
        $months = (int)$date1->format('m') - (int)$date2->format('m');

        return $years * 12 + $months;
    }

    public static function diffCalendarWeeks(
        DateTimeInterface|int|string|null $date1,
        DateTimeInterface|int|string|null $date2,
    ): int
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
        } catch (Throwable) {
            $date->setTimestamp($date->getTimestamp() + $seconds);
        }
    }

    private static function subInterval(DateTimeInterface $date, string $intervalSpec, int $seconds): void
    {
        try {
            $date->sub(new DateInterval($intervalSpec));
        } catch (Throwable) {
            $date->setTimestamp($date->getTimestamp() - $seconds);
        }
    }

    public static function time(DateTimeInterface|int|string|null $date = null): int
    {
        return self::create($date)->getTimestamp();
    }

    public static function timeNull(DateTimeInterface|int|string|null $date = null): ?int
    {
        return ($date !== null) ? self::create($date)->getTimestamp() : null;
    }

    public static function isSameDay(
        DateTimeInterface|int|string|null $date1,
        DateTimeInterface|int|string|null $date2,
    ): bool
    {
        return (
            $date1 !== null &&
            $date2 !== null &&
            self::formatShort($date1) === self::formatShort($date2)
        );
    }
}
