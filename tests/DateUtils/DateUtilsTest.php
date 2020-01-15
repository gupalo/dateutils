<?php

namespace Gupalo\Tests\DateUtils;

use Gupalo\DateUtils\DateUtils;
use PHPUnit\Framework\TestCase;

class DateUtilsTest extends TestCase
{
    public function testCreate(): void
    {
        $date = DateUtils::now();
        $this->assertSame(time(), $date->getTimestamp());

        $this->assertSame(strtotime('2019-10-17 12:34:56'), DateUtils::create('2019-10-17 12:34:56')->getTimestamp());
    }

    public function testNow(): void
    {
        $date = DateUtils::now();
        $this->assertSame(time(), $date->getTimestamp());
    }

    public function testDailyPeriod(): void
    {
        $period = DateUtils::dailyPeriod(DateUtils::subDays(3), DateUtils::now());

        $counter = 0;
        foreach ($period as $date) {
            $counter++;
            $this->assertSame('00:00:00', $date->format('H:i:s'));
        }
        $this->assertSame(4, $counter);
    }

    public function testDailyPeriodTemplate(): void
    {
        $period = DateUtils::dailyPeriodTemplate(DateUtils::subDays(3), DateUtils::now(), ['date' => null, 'aaa' => 'bbb']);

        $result = [
            DateUtils::subDays(3)->format(DateUtils::FORMAT_SHORT) => [
                'date' => DateUtils::subDays(3)->format(DateUtils::FORMAT_SHORT),
                'aaa' => 'bbb',
            ],
            DateUtils::subDays(2)->format(DateUtils::FORMAT_SHORT) => [
                'date' => DateUtils::subDays(2)->format(DateUtils::FORMAT_SHORT),
                'aaa' => 'bbb',
            ],
            DateUtils::subDays(1)->format(DateUtils::FORMAT_SHORT) => [
                'date' => DateUtils::subDays(1)->format(DateUtils::FORMAT_SHORT),
                'aaa' => 'bbb',
            ],
            DateUtils::subDays(0)->format(DateUtils::FORMAT_SHORT) => [
                'date' => DateUtils::subDays(0)->format(DateUtils::FORMAT_SHORT),
                'aaa' => 'bbb',
            ],
        ];
        $this->assertSame($result, $period);
    }

    public function testDayBegin(): void
    {
        $date = DateUtils::dayBegin(DateUtils::now());
        $this->assertSame(date('Y-m-d 00:00:00'), $date->format('Y-m-d H:i:s'));
    }

    public function testDayEnd(): void
    {
        $date = DateUtils::dayEnd(DateUtils::now());
        $this->assertSame(date('Y-m-d 23:59:59'), $date->format('Y-m-d H:i:s'));
    }

    public function testDaysAgo(): void
    {
        $date = DateUtils::subDays(2);
        $this->assertSame(time() - 2 * 86400, $date->getTimestamp());
    }

    public function testMinutesAgo(): void
    {
        $date = DateUtils::subMinutes(20);
        $this->assertSame(time() - 20 * 60, $date->getTimestamp());
    }

    public function testAddDays(): void
    {
        $date = DateUtils::addDays(2);
        $this->assertSame(time() + 2 * 86400, $date->getTimestamp());
    }

    public function testSubDays(): void
    {
        $date = DateUtils::subDays(2);
        $this->assertSame(time() - 2 * 86400, $date->getTimestamp());
    }

    public function testAddMinutes(): void
    {
        $date = DateUtils::addMinutes(20);
        $this->assertSame(time() + 20 * 60, $date->getTimestamp());
    }

    public function testSubHours(): void
    {
        $date = DateUtils::subHours(2);
        $this->assertSame(time() - 2 * 3600, $date->getTimestamp());
    }

    public function testAddHours(): void
    {
        $date = DateUtils::addHours(2);
        $this->assertSame(time() + 2 * 3600, $date->getTimestamp());
    }

    public function testHourBegin(): void
    {
        $date = DateUtils::hourBegin(DateUtils::now());
        $this->assertSame(date('Y-m-d H:00:00'), $date->format('Y-m-d H:i:s'));
    }

    public function testToday(): void
    {
        $date = DateUtils::today();
        $this->assertSame(date('Y-m-d 00:00:00'), $date->format('Y-m-d H:i:s'));
    }

    public function testPercentHourPassed(): void
    {
        $date = DateUtils::today()->setTime(19, 30, 00);
        $this->assertSame(0.5, DateUtils::percentHourPassed($date));

        $date = DateUtils::today()->setTime(19, 45, 00);
        $this->assertSame(0.75, DateUtils::percentHourPassed($date));
    }

    public function testDiffCalendarMonths(): void
    {
        $this->assertSame(0, DateUtils::diffCalendarMonths(DateUtils::create('2019-10-21'), DateUtils::create('2019-10-15')));
        $this->assertSame(0, DateUtils::diffCalendarMonths(DateUtils::create('2019-10-15'), DateUtils::create('2019-10-21')));
        $this->assertSame(1, DateUtils::diffCalendarMonths(DateUtils::create('2019-10-15'), DateUtils::create('2019-09-29')));
        $this->assertSame(14, DateUtils::diffCalendarMonths(DateUtils::create('2019-10-15'), DateUtils::create('2018-08-29')));
        $this->assertSame(-14, DateUtils::diffCalendarMonths(DateUtils::create('2018-08-29'), DateUtils::create('2019-10-15')));
    }

    public function testDiffCalendarWeeks(): void
    {
        $this->assertSame(0, DateUtils::diffCalendarWeeks(DateUtils::create('2019-10-27'), DateUtils::create('2019-10-21')));
        $this->assertSame(0, DateUtils::diffCalendarWeeks(DateUtils::create('2019-10-21'), DateUtils::create('2019-10-27')));
        $this->assertSame(0, DateUtils::diffCalendarWeeks(DateUtils::create('2019-10-21'), DateUtils::create('2019-10-21')));
        $this->assertSame(1, DateUtils::diffCalendarWeeks(DateUtils::create('2019-10-21'), DateUtils::create('2019-10-20')));
        $this->assertSame(1, DateUtils::diffCalendarWeeks(DateUtils::create('2019-10-21'), DateUtils::create('2019-10-14')));
        $this->assertSame(-2, DateUtils::diffCalendarWeeks(DateUtils::create('2019-10-07'), DateUtils::create('2019-10-21')));
        $this->assertSame(52, DateUtils::diffCalendarWeeks(DateUtils::create('2019-10-15'), DateUtils::create('2018-10-15')));
    }

    public function testFormatShort(): void
    {
        $this->assertSame(date('Y-m-d'), DateUtils::now()->format(DateUtils::FORMAT_SHORT));
    }

    public function testFormatFull(): void
    {
        $this->assertSame(date('Y-m-d H:i:s'), DateUtils::now()->format(DateUtils::FORMAT_FULL));
    }
}
