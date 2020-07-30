<?php

namespace Gupalo\Tests\DateUtils;

use DateTime;
use DateTimeImmutable;
use Gupalo\DateUtils\DateUtils;
use PHPUnit\Framework\TestCase;

class DateUtilsTest extends TestCase
{
    public function testCreate(): void
    {
        self::assertEqualsWithDelta(time(), DateUtils::now()->getTimestamp(), 1);

        self::assertSame(strtotime('2019-10-17 12:34:56'), DateUtils::create('2019-10-17 12:34:56')->getTimestamp());
        self::assertSame(strtotime('2019-10-17 12:34:56'), DateUtils::create(new DateTime('2019-10-17 12:34:56'))->getTimestamp());
        self::assertSame(strtotime('2019-10-17 12:34:56'), DateUtils::create(new DateTimeImmutable('2019-10-17 12:34:56'))->getTimestamp());
    }

    public function testCreateNull(): void
    {
        self::assertSame(strtotime('2019-10-17 12:34:56'), DateUtils::createNull('2019-10-17 12:34:56')->getTimestamp());
        self::assertNull(DateUtils::createNull(null));
    }

    public function testCreateError(): void
    {
        self::assertSame(0, DateUtils::create('INVALID_DATE')->getTimestamp());
    }

    public function testFormat(): void
    {
        self::assertSame(date('Y-m-d H:i:s'), DateUtils::format());
    }

    public function testFormatNull(): void
    {
        self::assertNull(DateUtils::formatNull(null));
        self::assertSame('-', DateUtils::formatNull(null, '-'));
        self::assertSame('2020-01-02 03:04:05', DateUtils::formatNull('2020-01-02 03:04:05'));
    }

    public function testFormatShort(): void
    {
        self::assertSame(date('Y-m-d'), DateUtils::formatShort());
    }

    public function testFormatShortNull(): void
    {
        self::assertNull(DateUtils::formatShortNull(null));
        self::assertSame('-', DateUtils::formatShortNull(null, '-'));
        self::assertSame('2020-01-02', DateUtils::formatShortNull('2020-01-02 03:04:05'));
    }

    public function testNow(): void
    {
        $date = DateUtils::now();
        self::assertSame(time(), $date->getTimestamp());
    }

    public function testDailyPeriod(): void
    {
        $period = DateUtils::dailyPeriod(DateUtils::subDays(3), DateUtils::now());

        $counter = 0;
        foreach ($period as $date) {
            $counter++;
            self::assertSame('00:00:00', $date->format('H:i:s'));
        }
        self::assertSame(4, $counter);
    }

    public function testDailyPeriod_Error(): void
    {
        $period = DateUtils::dailyPeriod(null, null, 'INVALID_SPEC');

        self::assertNull($period);
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
        self::assertSame($result, $period);
    }

    public function testDailyPeriodTemplate_NotArray(): void
    {
        $period = DateUtils::dailyPeriodTemplate(DateUtils::subDays(1), DateUtils::now(), fn($date) => ['d' => DateUtils::formatShort($date), 'q' => 7]);

        $result = [
            DateUtils::subDays(1)->format(DateUtils::FORMAT_SHORT) => ['d' => DateUtils::subDays(1)->format(DateUtils::FORMAT_SHORT), 'q' => 7],
            DateUtils::subDays(0)->format(DateUtils::FORMAT_SHORT) => ['d' => DateUtils::subDays(0)->format(DateUtils::FORMAT_SHORT), 'q' => 7],
        ];
        self::assertSame($result, $period);
    }

    public function testDailyPeriodTemplate_Callable(): void
    {
        $period = DateUtils::dailyPeriodTemplate(DateUtils::subDays(1), DateUtils::now(), 5);

        $result = [
            DateUtils::subDays(1)->format(DateUtils::FORMAT_SHORT) => 5,
            DateUtils::subDays(0)->format(DateUtils::FORMAT_SHORT) => 5,
        ];
        self::assertSame($result, $period);
    }

    public function testDayBegin(): void
    {
        $date = DateUtils::dayBegin(DateUtils::now());
        self::assertSame(date('Y-m-d 00:00:00'), $date->format('Y-m-d H:i:s'));
    }

    public function testDayBeginNull(): void
    {
        self::assertSame(date('Y-m-d 00:00:00'), DateUtils::dayBeginNull(DateUtils::now())->format('Y-m-d H:i:s'));
        self::assertNull(DateUtils::dayBeginNull(null));
    }

    public function testDayEnd(): void
    {
        $date = DateUtils::dayEnd(DateUtils::now());
        self::assertSame(date('Y-m-d 23:59:59'), $date->format('Y-m-d H:i:s'));
    }

    public function testDayEndNull(): void
    {
        self::assertSame(date('Y-m-d 23:59:59'), DateUtils::dayEndNull(DateUtils::now())->format('Y-m-d H:i:s'));
        self::assertNull(DateUtils::dayEndNull(null));
    }

    public function testDaysAgo(): void
    {
        $date = DateUtils::subDays(2);
        self::assertSame(time() - 2 * 86400, $date->getTimestamp());
    }

    public function testMinutesAgo(): void
    {
        $date = DateUtils::subMinutes(20);
        self::assertSame(time() - 20 * 60, $date->getTimestamp());
    }

    public function testAddDays(): void
    {
        $date = DateUtils::addDays(2);
        self::assertSame(time() + 2 * 86400, $date->getTimestamp());
    }

    public function testSubDays(): void
    {
        $date = DateUtils::subDays(2);
        self::assertSame(time() - 2 * 86400, $date->getTimestamp());
    }

    public function testSubHours(): void
    {
        $date = DateUtils::subHours(2);
        self::assertSame(time() - 2 * 3600, $date->getTimestamp());
    }

    public function testAddHours(): void
    {
        $date = DateUtils::addHours(2);
        self::assertSame(time() + 2 * 3600, $date->getTimestamp());
    }

    public function testAddMinutes(): void
    {
        $date = DateUtils::addMinutes(20);
        self::assertSame(time() + 20 * 60, $date->getTimestamp());
    }

    public function testAddMinutes_InvalidValueAndUseTimestamp(): void
    {
        $date = DateUtils::addMinutes(-20);
        self::assertSame(time() - 20 * 60, $date->getTimestamp());
    }

    public function testSubMinutes(): void
    {
        $date = DateUtils::subMinutes(20);
        self::assertSame(time() - 20 * 60, $date->getTimestamp());
    }

    public function testSubMinutes_InvalidValueAndUseTimestamp(): void
    {
        $date = DateUtils::subMinutes(-20);
        self::assertSame(time() + 20 * 60, $date->getTimestamp());
    }

    public function testWeekBegin(): void
    {
        $date = DateUtils::weekBegin('2020-04-16 12:34:56');
        self::assertSame('2020-04-13 00:00:00', DateUtils::format($date));
    }

    public function testWeekEnd(): void
    {
        $date = DateUtils::weekEnd('2020-04-16 12:34:56');
        self::assertSame('2020-04-19 23:59:59', DateUtils::format($date));
    }

    public function testNextMonday(): void
    {
        $date = DateUtils::nextMonday('2020-04-16 12:34:56');
        self::assertSame('2020-04-20 00:00:00', DateUtils::format($date));
    }

    public function testHourBegin(): void
    {
        $date = DateUtils::hourBegin(DateUtils::now());
        self::assertSame(date('Y-m-d H:00:00'), $date->format('Y-m-d H:i:s'));
    }

    public function testHourEnd(): void
    {
        $date = DateUtils::hourEnd(DateUtils::now());
        self::assertSame(date('Y-m-d H:59:59'), $date->format('Y-m-d H:i:s'));
    }

    public function testToday(): void
    {
        $date = DateUtils::today();
        self::assertSame(date('Y-m-d 00:00:00'), $date->format('Y-m-d H:i:s'));
    }

    public function testTodayEnd(): void
    {
        $date = DateUtils::todayEnd();
        self::assertSame(date('Y-m-d 23:59:59'), $date->format('Y-m-d H:i:s'));
    }

    public function testYesterday(): void
    {
        $date = DateUtils::yesterday();
        self::assertSame(date('Y-m-d 00:00:00', strtotime('yesterday')), $date->format('Y-m-d H:i:s'));
    }

    public function testYesterdayEnd(): void
    {
        $date = DateUtils::yesterdayEnd();
        self::assertSame(date('Y-m-d 23:59:59', strtotime('yesterday')), $date->format('Y-m-d H:i:s'));
    }

    public function testTomorrow(): void
    {
        $date = DateUtils::tomorrow();
        self::assertSame(date('Y-m-d 00:00:00', strtotime('tomorrow')), $date->format('Y-m-d H:i:s'));
    }

    public function testTomorrowEnd(): void
    {
        $date = DateUtils::tomorrowEnd();
        self::assertSame(date('Y-m-d 23:59:59', strtotime('tomorrow')), $date->format('Y-m-d H:i:s'));
    }

    public function testPercentHourPassed(): void
    {
        $date = DateUtils::today()->setTime(19, 30, 00);
        self::assertSame(0.5, DateUtils::percentHourPassed($date));

        $date = DateUtils::today()->setTime(19, 45, 00);
        self::assertSame(0.75, DateUtils::percentHourPassed($date));
    }

    public function testDiffDays(): void
    {
        self::assertSame(6, DateUtils::diffDays(DateUtils::create('2019-10-27'), DateUtils::create('2019-10-21')));
        self::assertSame(-6, DateUtils::diffDays(DateUtils::create('2019-10-21'), DateUtils::create('2019-10-27')));
        self::assertSame(0, DateUtils::diffDays(DateUtils::create('2019-10-21'), DateUtils::create('2019-10-21')));

        self::assertSame(6  /*  6.5 */, DateUtils::diffDays(DateUtils::create('2019-10-27 12:00:00'), DateUtils::create('2019-10-21 00:00:00')));
        self::assertSame(-6 /* -5.5 */, DateUtils::diffDays(DateUtils::create('2019-10-21 12:00:00'), DateUtils::create('2019-10-27 00:00:00')));
        self::assertSame(0  /*  0.5 */, DateUtils::diffDays(DateUtils::create('2019-10-21 12:00:00'), DateUtils::create('2019-10-21 00:00:00')));
    }

    public function testDiffCalendarMonths(): void
    {
        self::assertSame(0, DateUtils::diffCalendarMonths(DateUtils::create('2019-10-21'), DateUtils::create('2019-10-15')));
        self::assertSame(0, DateUtils::diffCalendarMonths(DateUtils::create('2019-10-15'), DateUtils::create('2019-10-21')));
        self::assertSame(1, DateUtils::diffCalendarMonths(DateUtils::create('2019-10-15'), DateUtils::create('2019-09-29')));
        self::assertSame(14, DateUtils::diffCalendarMonths(DateUtils::create('2019-10-15'), DateUtils::create('2018-08-29')));
        self::assertSame(-14, DateUtils::diffCalendarMonths(DateUtils::create('2018-08-29'), DateUtils::create('2019-10-15')));
    }

    public function testDiffCalendarWeeks(): void
    {
        self::assertSame(0, DateUtils::diffCalendarWeeks(DateUtils::create('2019-10-27'), DateUtils::create('2019-10-21')));
        self::assertSame(0, DateUtils::diffCalendarWeeks(DateUtils::create('2019-10-21'), DateUtils::create('2019-10-27')));
        self::assertSame(0, DateUtils::diffCalendarWeeks(DateUtils::create('2019-10-21'), DateUtils::create('2019-10-21')));
        self::assertSame(1, DateUtils::diffCalendarWeeks(DateUtils::create('2019-10-21'), DateUtils::create('2019-10-20')));
        self::assertSame(1, DateUtils::diffCalendarWeeks(DateUtils::create('2019-10-21'), DateUtils::create('2019-10-14')));
        self::assertSame(-2, DateUtils::diffCalendarWeeks(DateUtils::create('2019-10-07'), DateUtils::create('2019-10-21')));
        self::assertSame(52, DateUtils::diffCalendarWeeks(DateUtils::create('2019-10-15'), DateUtils::create('2018-10-15')));
    }

    public function testFormatConstant(): void
    {
        self::assertSame(date('Y-m-d'), DateUtils::now()->format(DateUtils::FORMAT_SHORT));
        self::assertSame(date('Y-m-d H:i:s'), DateUtils::now()->format(DateUtils::FORMAT_FULL));
    }

    public function testTime(): void
    {
        self::assertEqualsWithDelta(time(), DateUtils::time(), 2);
        self::assertEqualsWithDelta(time(), DateUtils::time(null), 2);
        self::assertSame((new DateTime('2020-01-01 01:02:03'))->getTimestamp(), DateUtils::time('2020-01-01 01:02:03'));
    }

    public function testTimeNull(): void
    {
        self::assertNull(DateUtils::timeNull());
        self::assertNull(DateUtils::timeNull(null));

        self::assertEqualsWithDelta(time(), DateUtils::timeNull('now'), 2);
        self::assertSame((new DateTime('2020-01-01 01:02:03'))->getTimestamp(), DateUtils::timeNull('2020-01-01 01:02:03'));
    }

    public function testIsSameDay(): void
    {
        self::assertTrue(DateUtils::isSameDay('2020-01-01', '2020-01-01'));
        self::assertTrue(DateUtils::isSameDay('2020-01-01 00:00:00', '2020-01-01 22:00:01'));

        self::assertFalse(DateUtils::isSameDay(null, null));
        self::assertFalse(DateUtils::isSameDay(null, '2020-01-01'));
        self::assertFalse(DateUtils::isSameDay('2020-01-01', null));
    }
}
