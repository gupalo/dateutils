<?php

namespace Gupalo\Tests\DateUtils;

use DateTime;
use DateTimeImmutable;
use Gupalo\DateUtils\Dat;
use PHPUnit\Framework\TestCase;

class DatTest extends TestCase
{
    public function testCreate(): void
    {
        self::assertEqualsWithDelta(time(), Dat::now()->getTimestamp(), 1);

        self::assertSame(strtotime('2019-10-17 12:34:56'), Dat::create('2019-10-17 12:34:56')->getTimestamp());
        self::assertSame(strtotime('2019-10-17 12:34:56'), Dat::create(new DateTime('2019-10-17 12:34:56'))->getTimestamp());
        self::assertSame(strtotime('2019-10-17 12:34:56'), Dat::create(new DateTimeImmutable('2019-10-17 12:34:56'))->getTimestamp());
    }

    public function testCreateNull(): void
    {
        self::assertSame(strtotime('2019-10-17 12:34:56'), Dat::createNull('2019-10-17 12:34:56')->getTimestamp());
        self::assertNull(Dat::createNull(null));
    }

    public function testCreateError(): void
    {
        self::assertSame(0, Dat::create('INVALID_DATE')->getTimestamp());
    }

    public function testFormat(): void
    {
        self::assertSame(date('Y-m-d H:i:s'), Dat::format());
    }

    public function testFormatNull(): void
    {
        self::assertNull(Dat::formatNull(null));
        self::assertSame('-', Dat::formatNull(null, '-'));
        self::assertSame('2020-01-02 03:04:05', Dat::formatNull('2020-01-02 03:04:05'));
    }

    public function testFormatShort(): void
    {
        self::assertSame(date('Y-m-d'), Dat::formatShort());
    }

    public function testFormatShortNull(): void
    {
        self::assertNull(Dat::formatShortNull(null));
        self::assertSame('-', Dat::formatShortNull(null, '-'));
        self::assertSame('2020-01-02', Dat::formatShortNull('2020-01-02 03:04:05'));
    }

    public function testNow(): void
    {
        $date = Dat::now();
        self::assertSame(time(), $date->getTimestamp());
    }

    public function testDailyPeriod(): void
    {
        $period = Dat::dailyPeriod(Dat::subDays(3), Dat::now());

        $counter = 0;
        foreach ($period as $date) {
            $counter++;
            self::assertSame('00:00:00', $date->format('H:i:s'));
        }
        self::assertSame(4, $counter);
    }

    public function testDailyPeriod_Error(): void
    {
        $period = Dat::dailyPeriod(null, null, 'INVALID_SPEC');

        self::assertNull($period);
    }

    public function testDailyPeriodTemplate(): void
    {
        $period = Dat::dailyPeriodTemplate(Dat::subDays(3), Dat::now(), ['date' => null, 'aaa' => 'bbb']);

        $result = [
            Dat::subDays(3)->format(Dat::FORMAT_SHORT) => [
                'date' => Dat::subDays(3)->format(Dat::FORMAT_SHORT),
                'aaa' => 'bbb',
            ],
            Dat::subDays(2)->format(Dat::FORMAT_SHORT) => [
                'date' => Dat::subDays(2)->format(Dat::FORMAT_SHORT),
                'aaa' => 'bbb',
            ],
            Dat::subDays(1)->format(Dat::FORMAT_SHORT) => [
                'date' => Dat::subDays(1)->format(Dat::FORMAT_SHORT),
                'aaa' => 'bbb',
            ],
            Dat::subDays(0)->format(Dat::FORMAT_SHORT) => [
                'date' => Dat::subDays(0)->format(Dat::FORMAT_SHORT),
                'aaa' => 'bbb',
            ],
        ];
        self::assertSame($result, $period);
    }

    public function testDailyPeriodTemplate_NotArray(): void
    {
        $period = Dat::dailyPeriodTemplate(Dat::subDays(1), Dat::now(), static fn($date) => ['d' => Dat::formatShort($date), 'q' => 7]);

        $result = [
            Dat::subDays(1)->format(Dat::FORMAT_SHORT) => ['d' => Dat::subDays(1)->format(Dat::FORMAT_SHORT), 'q' => 7],
            Dat::subDays(0)->format(Dat::FORMAT_SHORT) => ['d' => Dat::subDays(0)->format(Dat::FORMAT_SHORT), 'q' => 7],
        ];
        self::assertSame($result, $period);
    }

    public function testDailyPeriodTemplate_Callable(): void
    {
        $period = Dat::dailyPeriodTemplate(Dat::subDays(1), Dat::now(), 5);

        $result = [
            Dat::subDays(1)->format(Dat::FORMAT_SHORT) => 5,
            Dat::subDays(0)->format(Dat::FORMAT_SHORT) => 5,
        ];
        self::assertSame($result, $period);
    }

    public function testDayBegin(): void
    {
        $date = Dat::dayBegin(Dat::now());
        self::assertSame(date('Y-m-d 00:00:00'), $date->format('Y-m-d H:i:s'));
    }

    public function testDayBeginNull(): void
    {
        self::assertSame(date('Y-m-d 00:00:00'), Dat::dayBeginNull(Dat::now())->format('Y-m-d H:i:s'));
        self::assertNull(Dat::dayBeginNull(null));
    }

    public function testDayEnd(): void
    {
        $date = Dat::dayEnd(Dat::now());
        self::assertSame(date('Y-m-d 23:59:59'), $date->format('Y-m-d H:i:s'));
    }

    public function testDayEndNull(): void
    {
        self::assertSame(date('Y-m-d 23:59:59'), Dat::dayEndNull(Dat::now())->format('Y-m-d H:i:s'));
        self::assertNull(Dat::dayEndNull(null));
    }

    public function testDaysAgo(): void
    {
        $date = Dat::subDays(2);
        self::assertSame(time() - 2 * 86400, $date->getTimestamp());
    }

    public function testDaysAgoFloat(): void
    {
        $date = Dat::subDaysFloat(0.5);
        self::assertSame((int)(time() - 0.5 * 86400), $date->getTimestamp());
    }

    public function testMinutesAgo(): void
    {
        $date = Dat::subMinutes(20);
        self::assertSame(time() - 20 * 60, $date->getTimestamp());
    }

    public function testAddDays(): void
    {
        $date = Dat::addDays(2);
        self::assertSame(time() + 2 * 86400, $date->getTimestamp());
    }

    public function testSubDays(): void
    {
        $date = Dat::subDays(2);
        self::assertSame(time() - 2 * 86400, $date->getTimestamp());
    }

    public function testSubHours(): void
    {
        $date = Dat::subHours(2);
        self::assertSame(time() - 2 * 3600, $date->getTimestamp());
    }

    public function testAddHours(): void
    {
        $date = Dat::addHours(2);
        self::assertSame(time() + 2 * 3600, $date->getTimestamp());
    }

    public function testAddMinutes(): void
    {
        $date = Dat::addMinutes(20);
        self::assertSame(time() + 20 * 60, $date->getTimestamp());
    }

    public function testAddMinutes_InvalidValueAndUseTimestamp(): void
    {
        $date = Dat::addMinutes(-20);
        self::assertSame(time() - 20 * 60, $date->getTimestamp());
    }

    public function testSubMinutes(): void
    {
        $date = Dat::subMinutes(20);
        self::assertSame(time() - 20 * 60, $date->getTimestamp());
    }

    public function testSubMinutes_InvalidValueAndUseTimestamp(): void
    {
        $date = Dat::subMinutes(-20);
        self::assertSame(time() + 20 * 60, $date->getTimestamp());
    }

    public function testAddSeconds(): void
    {
        $date = Dat::addSeconds(20);
        self::assertSame(time() + 20, $date->getTimestamp());
    }

    public function testAddSeconds_InvalidValueAndUseTimestamp(): void
    {
        $date = Dat::addSeconds(-20);
        self::assertSame(time() - 20, $date->getTimestamp());
    }

    public function testSubSeconds(): void
    {
        $date = Dat::subSeconds(20);
        self::assertSame(time() - 20, $date->getTimestamp());
    }

    public function testSubSeconds_InvalidValueAndUseTimestamp(): void
    {
        $date = Dat::subSeconds(-20);
        self::assertSame(time() + 20, $date->getTimestamp());
    }

    public function testWeekBegin(): void
    {
        $date = Dat::weekBegin('2020-04-16 12:34:56');
        self::assertSame('2020-04-13 00:00:00', Dat::format($date));
    }

    public function testWeekEnd(): void
    {
        $date = Dat::weekEnd('2020-04-16 12:34:56');
        self::assertSame('2020-04-19 23:59:59', Dat::format($date));
    }

    public function testNextMonday(): void
    {
        $date = Dat::nextMonday('2020-04-16 12:34:56');
        self::assertSame('2020-04-20 00:00:00', Dat::format($date));
    }

    public function testHourBegin(): void
    {
        $date = Dat::hourBegin(Dat::now());
        self::assertSame(date('Y-m-d H:00:00'), $date->format('Y-m-d H:i:s'));
    }

    public function testHourEnd(): void
    {
        $date = Dat::hourEnd(Dat::now());
        self::assertSame(date('Y-m-d H:59:59'), $date->format('Y-m-d H:i:s'));
    }

    public function testToday(): void
    {
        $date = Dat::today();
        self::assertSame(date('Y-m-d 00:00:00'), $date->format('Y-m-d H:i:s'));
    }

    public function testTodayEnd(): void
    {
        $date = Dat::todayEnd();
        self::assertSame(date('Y-m-d 23:59:59'), $date->format('Y-m-d H:i:s'));
    }

    public function testYesterday(): void
    {
        $date = Dat::yesterday();
        self::assertSame(date('Y-m-d 00:00:00', strtotime('yesterday')), $date->format('Y-m-d H:i:s'));
    }

    public function testYesterdayEnd(): void
    {
        $date = Dat::yesterdayEnd();
        self::assertSame(date('Y-m-d 23:59:59', strtotime('yesterday')), $date->format('Y-m-d H:i:s'));
    }

    public function testTomorrow(): void
    {
        $date = Dat::tomorrow();
        self::assertSame(date('Y-m-d 00:00:00', strtotime('tomorrow')), $date->format('Y-m-d H:i:s'));
    }

    public function testTomorrowEnd(): void
    {
        $date = Dat::tomorrowEnd();
        self::assertSame(date('Y-m-d 23:59:59', strtotime('tomorrow')), $date->format('Y-m-d H:i:s'));
    }

    public function testPercentHourPassed(): void
    {
        $date = Dat::today()->setTime(19, 30, 00);
        self::assertSame(0.5, Dat::percentHourPassed($date));

        $date = Dat::today()->setTime(19, 45, 00);
        self::assertSame(0.75, Dat::percentHourPassed($date));
    }

    public function testDiffDays(): void
    {
        self::assertSame(6, Dat::diffDays(Dat::create('2019-10-27'), Dat::create('2019-10-21')));
        self::assertSame(-6, Dat::diffDays(Dat::create('2019-10-21'), Dat::create('2019-10-27')));
        self::assertSame(0, Dat::diffDays(Dat::create('2019-10-21'), Dat::create('2019-10-21')));

        self::assertSame(6  /*  6.5 */, Dat::diffDays(Dat::create('2019-10-27 12:00:00'), Dat::create('2019-10-21 00:00:00')));
        self::assertSame(-6 /* -5.5 */, Dat::diffDays(Dat::create('2019-10-21 12:00:00'), Dat::create('2019-10-27 00:00:00')));
        self::assertSame(0  /*  0.5 */, Dat::diffDays(Dat::create('2019-10-21 12:00:00'), Dat::create('2019-10-21 00:00:00')));
    }

    public function testDiffCalendarMonths(): void
    {
        self::assertSame(0, Dat::diffCalendarMonths(Dat::create('2019-10-21'), Dat::create('2019-10-15')));
        self::assertSame(0, Dat::diffCalendarMonths(Dat::create('2019-10-15'), Dat::create('2019-10-21')));
        self::assertSame(1, Dat::diffCalendarMonths(Dat::create('2019-10-15'), Dat::create('2019-09-29')));
        self::assertSame(14, Dat::diffCalendarMonths(Dat::create('2019-10-15'), Dat::create('2018-08-29')));
        self::assertSame(-14, Dat::diffCalendarMonths(Dat::create('2018-08-29'), Dat::create('2019-10-15')));
    }

    public function testDiffCalendarWeeks(): void
    {
        self::assertSame(0, Dat::diffCalendarWeeks(Dat::create('2019-10-27'), Dat::create('2019-10-21')));
        self::assertSame(0, Dat::diffCalendarWeeks(Dat::create('2019-10-21'), Dat::create('2019-10-27')));
        self::assertSame(0, Dat::diffCalendarWeeks(Dat::create('2019-10-21'), Dat::create('2019-10-21')));
        self::assertSame(1, Dat::diffCalendarWeeks(Dat::create('2019-10-21'), Dat::create('2019-10-20')));
        self::assertSame(1, Dat::diffCalendarWeeks(Dat::create('2019-10-21'), Dat::create('2019-10-14')));
        self::assertSame(-2, Dat::diffCalendarWeeks(Dat::create('2019-10-07'), Dat::create('2019-10-21')));
        self::assertSame(52, Dat::diffCalendarWeeks(Dat::create('2019-10-15'), Dat::create('2018-10-15')));
    }

    public function testFormatConstant(): void
    {
        self::assertSame(date('Y-m-d'), Dat::now()->format(Dat::FORMAT_SHORT));
        self::assertSame(date('Y-m-d H:i:s'), Dat::now()->format(Dat::FORMAT_FULL));
    }

    public function testTime(): void
    {
        self::assertEqualsWithDelta(time(), Dat::time(), 2);
        self::assertEqualsWithDelta(time(), Dat::time(null), 2);
        self::assertSame((new DateTime('2020-01-01 01:02:03'))->getTimestamp(), Dat::time('2020-01-01 01:02:03'));
    }

    public function testTimeNull(): void
    {
        self::assertNull(Dat::timeNull());
        self::assertNull(Dat::timeNull(null));

        self::assertEqualsWithDelta(time(), Dat::timeNull('now'), 2);
        self::assertSame((new DateTime('2020-01-01 01:02:03'))->getTimestamp(), Dat::timeNull('2020-01-01 01:02:03'));
    }

    public function testIsSameDay(): void
    {
        self::assertTrue(Dat::isSameDay('2020-01-01', '2020-01-01'));
        self::assertTrue(Dat::isSameDay('2020-01-01 00:00:00', '2020-01-01 22:00:01'));

        self::assertFalse(Dat::isSameDay(null, null));
        self::assertFalse(Dat::isSameDay(null, '2020-01-01'));
        self::assertFalse(Dat::isSameDay('2020-01-01', null));
    }

    public function testConstSeconds(): void
    {
        self::assertSame(Dat::SECONDS_HOUR, 60 * 60);
        self::assertSame(Dat::SECONDS_DAY, 60 * 60 * 24);
        self::assertSame(Dat::SECONDS_7_DAYS, 60 * 60 * 24 * 7);
        self::assertSame(Dat::SECONDS_30_DAYS, 60 * 60 * 24 * 30);
        self::assertSame(Dat::SECONDS_360_DAYS, 60 * 60 * 24 * 360);
    }
}
