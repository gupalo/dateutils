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
     * @param string|DateTimeInterface|null $date
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
            } else {
                $result = new DateTime($date);
            }
        } catch (Throwable $e) {
            $result = null;
        }

        return $result;
    }

    public static function dayBegin(DateTimeInterface $date): DateTime
    {
        $result = self::fromInterface($date);
        $result->setTime(0, 0);

        return $result;
    }

    public static function dayEnd(DateTimeInterface $date): DateTime
    {
        $result = self::fromInterface($date);
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

    public static function dailyPeriodTemplate(DateTimeInterface $minDate, DateTimeInterface $maxDate, $template, $format = 'Y-m-d'): array
    {
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

    public static function addDays(int $countDays, DateTimeInterface $date = null): DateTime
    {
        $date = self::create($date);

        try {
            $date->add(new DateInterval(sprintf('P%sD', $countDays)));
        } catch (Throwable $e) {
            $date->setTimestamp($date->getTimestamp() + $countDays * 86400);
        }

        return $date;
    }

    public static function subDays(int $countDays, DateTime $date = null): DateTime
    {
        $date = self::create($date);

        try {
            $date->sub(new DateInterval(sprintf('P%sD', $countDays)));
        } catch (Throwable $e) {
            $date->setTimestamp($date->getTimestamp() - $countDays * 86400);
        }

        return $date;
    }

    public static function subHours(int $countHours, DateTime $date = null): DateTime
    {
        $date = self::create($date);

        try {
            $date->sub(new DateInterval(sprintf('PT%sH', $countHours)));
        } catch (Throwable $e) {
            $date->setTimestamp($date->getTimestamp() - $countHours * 60);
        }

        return $date;
    }

    public static function addHours(int $countHours, DateTime $date = null): DateTime
    {
        $date = self::create($date);

        try {
            $date->add(new DateInterval(sprintf('PT%sH', $countHours)));
        } catch (Throwable $e) {
            $date->setTimestamp($date->getTimestamp() - $countHours * 60);
        }

        return $date;
    }

    public static function subMinutes(int $countMintutes, DateTime $date = null): DateTime
    {
        $date = self::create($date);

        try {
            $date->sub(new DateInterval(sprintf('PT%sM', $countMintutes)));
        } catch (Throwable $e) {
            $date->setTimestamp($date->getTimestamp() - $countMintutes * 60);
        }

        return $date;
    }

    public static function addMinutes(int $countMintutes, DateTime $date = null): DateTime
    {
        $date = self::create($date);

        try {
            $date->add(new DateInterval(sprintf('PT%sM', $countMintutes)));
        } catch (Throwable $e) {
            $date->setTimestamp($date->getTimestamp() + $countMintutes * 60);
        }

        return $date;
    }

    public static function hourBegin(DateTime $date = null): DateTime
    {
        $date = self::create($date);

        return self::create($date->format('Y-m-d H:00:00'));
    }

    public static function today(): DateTime
    {
        return self::dayBegin(self::now());
    }

    private static function fromInterface(DateTimeInterface $date = null): DateTime
    {
        if ($date === null) {
            $result = self::now();
        } elseif ($date instanceof DateTime) {
            $result = clone $date;
        } else {
            $result = self::create()->setTimestamp($date->getTimestamp());
        }

        return $result;
    }

    public static function percentHourPassed(DateTime $date = null)
    {
        $date = self::create($date);

        return (float)(60 * (int)$date->format('i') + (int)$date->format('s')) / 3600;
    }

    public static function diffCalendarMonths(DateTimeInterface $date1, DateTimeInterface $date2): int
    {
        $years = (int)$date1->format('Y') - (int)$date2->format('Y');
        $months = (int)$date1->format('m') - (int)$date2->format('m');

        return $years * 12 + $months;
    }

    public static function diffCalendarWeeks(DateTimeInterface $date1, DateTimeInterface $date2)
    {
        $weekDay1 = (int)$date1->format('N') - 1;
        $weekDay2 = (int)$date2->format('N') - 1;
        $days = $date1->diff($date2)->days;
        if ($date2 > $date1) {
            $days = -$days;
        }

        return ($days - $weekDay1 + $weekDay2) / 7;
    }
}
