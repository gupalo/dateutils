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
            $result = null;
        }

        return $result;
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
     * @return string
     */
    public static function formatShort($date = null): string
    {
        return self::create($date)->format(self::FORMAT_SHORT);
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
     * @return DateTime
     */
    public static function dayEnd($date = null): DateTime
    {
        $result = self::create($date);
        $result->setTime(23, 59, 59);

        return $result;
    }

    /**
     * @param DateTimeInterface $minDate
     * @param DateTimeInterface $maxDate
     * @return DatePeriod|DateTime[]|null
     */
    public static function dailyPeriod(DateTimeInterface $minDate, DateTimeInterface $maxDate): ?DatePeriod
    {
        try {
            $result = new DatePeriod(
                self::dayBegin($minDate),
                new DateInterval('P1D'),
                self::dayEnd($maxDate)
            );
        } catch (Throwable $e) {
            $result = null;
        }

        return $result;
    }

    public static function dailyPeriodTemplate(
        DateTimeInterface $minDate,
        DateTimeInterface $maxDate,
        $template,
        $format = self::FORMAT_SHORT
    ): array {
        $result = [];

        $dates = self::dailyPeriod($minDate, $maxDate);
        foreach ($dates as $date) {
            $item = $template;
            if (array_key_exists('date', $item)) {
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

        try {
            $date->add(new DateInterval(sprintf('P%sD', $countDays)));
        } catch (Throwable $e) {
            $date->setTimestamp($date->getTimestamp() + $countDays * 86400);
        }

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

        try {
            $date->sub(new DateInterval(sprintf('P%sD', $countDays)));
        } catch (Throwable $e) {
            $date->setTimestamp($date->getTimestamp() - $countDays * 86400);
        }

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

        try {
            $date->add(new DateInterval(sprintf('PT%sH', $countHours)));
        } catch (Throwable $e) {
            $date->setTimestamp($date->getTimestamp() - $countHours * 60);
        }

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

        try {
            $date->sub(new DateInterval(sprintf('PT%sH', $countHours)));
        } catch (Throwable $e) {
            $date->setTimestamp($date->getTimestamp() - $countHours * 60);
        }

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

        try {
            $date->add(new DateInterval(sprintf('PT%sM', $countMintutes)));
        } catch (Throwable $e) {
            $date->setTimestamp($date->getTimestamp() + $countMintutes * 60);
        }

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

        try {
            $date->sub(new DateInterval(sprintf('PT%sM', $countMintutes)));
        } catch (Throwable $e) {
            $date->setTimestamp($date->getTimestamp() - $countMintutes * 60);
        }

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

    public static function today(): DateTime
    {
        return self::dayBegin();
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
}
