<?php

namespace CultOfNull\Tests;

use CultOfNull\DateUtils;
use PHPUnit\Framework\TestCase;

class DateUtilsTest extends TestCase
{
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
}
