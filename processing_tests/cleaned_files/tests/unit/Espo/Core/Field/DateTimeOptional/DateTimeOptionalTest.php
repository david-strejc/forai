<?php
//FORAI:F2819;DEF[C2416:DateTimeOptionalTest,F12276:testFromString1,F12277:testFromString2,F12278:testFromDateTime1,F12279:testFromDateTime2,F12280:testBad1,F12281:testBad2,F12282:testEmpty,F12283:testGetDateTime,F12284:testGetMethods,F12285:testAdd,F12286:testSubtract,F12287:testModify,F12288:testWithTimezone,F12289:getGetTimezone,F12290:testDiff,F12291:testNow,F12292:testWithTime1,F12293:testWithTime2,F12294:testComparison,F12295:testAddDays,F12296:testAddMonths,F12297:testAddYears,F12298:testAddHours,F12299:testAddMinutes,F12300:testAddSeconds,F12301:testFromTimestamp];IMP[];EXP[C2416,F12276,F12277,F12278,F12279,F12280,F12281,F12282,F12283,F12284,F12285,F12286,F12287,F12288,F12289,F12290,F12291,F12292,F12293,F12294,F12295,F12296,F12297,F12298,F12299,F12300,F12301];LANG[php]//

/************************************************************************
 * This file is part of EspoCRM.
 *
 * EspoCRM – Open Source CRM application.
 * Copyright (C) 2014-2025 Yurii Kuznietsov, Taras Machyshyn, Oleksii Avramenko
 * Website: https://www.espocrm.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "EspoCRM" word.
 ************************************************************************/

namespace tests\unit\Espo\Core\Field\DateTimeOptionalTest;

use Espo\Core\{
    Field\DateTime,
    Field\DateTimeOptional};

use DateTimeImmutable;
use DateTimeZone;
use RuntimeException;
use DateInterval;

class DateTimeOptionalTest extends \PHPUnit\Framework\TestCase
{
    public function testFromString1()
    {
        $value = DateTimeOptional::fromString('2021-05-01 10:20:30');

        $this->assertEquals('2021-05-01 10:20:30', $value->toString());

        $this->assertFalse($value->isAllDay());
    }

    public function testFromString2()
    {
        $value = DateTimeOptional::fromString('2021-05-01 10:20');

        $this->assertEquals('2021-05-01 10:20:00', $value->toString());

        $this->assertFalse($value->isAllDay());
    }

    public function testFromDateTime1()
    {
        $dt = new DateTimeImmutable('2021-05-01 10:20:30', new DateTimeZone('UTC'));

        $value = DateTimeOptional::fromDateTime($dt);

        $this->assertEquals('2021-05-01 10:20:30', $value->toString());
    }

    public function testFromDateTime2()
    {
        $dt = new DateTimeImmutable('2021-05-01 10:20:30', new DateTimeZone('Europe/Kiev'));

        $value = DateTimeOptional::fromDateTime($dt);

        $this->assertEquals('2021-05-01 07:20:30', $value->toString());
    }

    public function testBad1()
    {
        $this->expectException(RuntimeException::class);

        DateTimeOptional::fromString('2021-05-A 10:20:30');
    }

    public function testBad2()
    {
        $this->expectException(RuntimeException::class);

        DateTimeOptional::fromString('2021-05-1 10:20:30');
    }

    public function testEmpty()
    {
        $this->expectException(RuntimeException::class);

        DateTimeOptional::fromString('');
    }

    public function testGetDateTime()
    {
        $value = DateTimeOptional::fromString('2021-05-01 10:20:30');

        $this->assertEquals('2021-05-01', $value->toDateTime()->format('Y-m-d'));
    }

    public function testGetMethods()
    {
        $value = DateTimeOptional::fromString('2021-05-01 10:20:30');

        $dt = new DateTimeImmutable('2021-05-01 10:20:30', new DateTimeZone('UTC'));

        $this->assertEquals(1, $value->getDay());
        $this->assertEquals(5, $value->getMonth());
        $this->assertEquals(2021, $value->getYear());
        $this->assertEquals(6, $value->getDayOfWeek());
        $this->assertEquals(10, $value->getHour());
        $this->assertEquals(20, $value->getMinute());
        $this->assertEquals(30, $value->getSecond());

        $this->assertEquals($dt->getTimestamp(), $value->getTimestamp());
    }

    public function testAdd()
    {
        $value = DateTimeOptional::fromString('2021-05-01 10:20:30');

        $modifiedValue = $value->add(DateInterval::createFromDateString('1 day'));

        $this->assertEquals('2021-05-02 10:20:30', $modifiedValue->toString());

        $this->assertNotSame($modifiedValue, $value);
    }

    public function testSubtract()
    {
        $value = DateTimeOptional::fromString('2021-05-01 10:20:30');

        $modifiedValue = $value->subtract(DateInterval::createFromDateString('1 day'));

        $this->assertEquals('2021-04-30 10:20:30', $modifiedValue->toString());

        $this->assertNotSame($modifiedValue, $value);
    }

    public function testModify()
    {
        $value = DateTimeOptional::fromString('2021-05-01 10:20:30');

        $modifiedValue = $value->modify('+1 month');

        $this->assertEquals('2021-06-01 10:20:30', $modifiedValue->toString());

        $this->assertNotSame($modifiedValue, $value);
    }

    public function testWithTimezone()
    {
        $value = DateTimeOptional
            ::fromString('2021-05-01 10:20:30')
            ->withTimezone(new DateTimeZone('Europe/Kiev'));

        $this->assertEquals('2021-05-01 10:20:30', $value->toString());

        $this->assertEquals(13, $value->getHour());
    }

    public function getGetTimezone()
    {
        $value = DateTimeOptional
            ::fromString('2021-05-01 10:20:30')
            ->withTimezone(new DateTimeZone('Europe/Kiev'));

        $this->assertEquals(new DateTimeZone('Europe/Kiev'), $value->getTimezone());
    }

    public function testDiff(): void
    {
        $value1 = DateTimeOptional::fromString('2021-05-01 10:10:30');
        $value2 = DateTimeOptional::fromString('2021-05-01 10:20:30');

        $this->assertEquals(10, $value1->diff($value2)->i);
        $this->assertEquals(0, $value1->diff($value2)->invert);
    }

    public function testNow(): void
    {
        $value = DateTimeOptional::createNow();

        $this->assertNotNull($value);
    }


    public function testWithTime1(): void
    {
        $value = DateTimeOptional::fromString('2021-05-01 10:10:30');

        $this->assertEquals(
            '2021-05-01 00:00:00',
            $value->withTime(0, 0, 0)->toString()
        );

        $this->assertEquals(
            '2021-05-01 00:10:30',
            $value->withTime(0, null, null)->toString()
        );

        $this->assertEquals(
            '2021-05-01 10:00:00',
            $value->withTime(null, 0)->toString()
        );

        $this->assertEquals(
            '2021-05-01 10:00:10',
            $value->withTime(null, 0, 10)->toString()
        );
    }

    public function testWithTime2(): void
    {
        $value = DateTimeOptional::fromString('2021-05-01');

        $this->assertEquals(
            '2021-05-01 00:00:00',
            $value->withTime(0, 0, 0)->toString()
        );

        $this->assertEquals(
            '2021-05-01 00:00:00',
            $value->withTime(0, null, null)->toString()
        );

        $this->assertEquals(
            '2021-05-01 00:00:00',
            $value->withTime(null, 0)->toString()
        );

        $this->assertEquals(
            '2021-05-01 00:00:10',
            $value->withTime(null, 0, 10)->toString()
        );
    }

    public function testComparison(): void
    {
        $value = DateTimeOptional::fromString('2021-05-01 10:10:30')
            ->withTimezone(new DateTimeZone('Europe/Kiev'));

        $this->assertTrue(
            $value->isEqualTo(
                $value->withTimezone(new DateTimeZone('UTC'))
            )
        );

        $this->assertFalse(
            $value->isEqualTo(
                $value->modify('+1 minute')
            )
        );

        $this->assertFalse(
            $value->isGreaterThan(
                $value->modify('+1 minute')
            )
        );

        $this->assertFalse(
            $value->isLessThan(
                $value->modify('-1 minute')
            )
        );

        $this->assertTrue(
            $value->isGreaterThan(
                $value->modify('-1 minute')
            )
        );

        $this->assertTrue(
            $value->isLessThan(
                $value->modify('+1 minute')
            )
        );

        $this->assertTrue(
            $value->isGreaterThanOrEqualTo(
                $value
            )
        );

        $this->assertTrue(
            $value->isLessThanOrEqualTo(
                $value
            )
        );

        $this->assertTrue(
            $value->isGreaterThanOrEqualTo(
                $value->modify('-1 minute')
            )
        );

        $this->assertTrue(
            $value->isLessThanOrEqualTo(
                $value->modify('+1 minute')
            )
        );
    }

    public function testAddDays(): void
    {
        $value = DateTimeOptional::fromString('2023-01-01 00:00:00');

        $this->assertEquals(
            DateTimeOptional::fromString('2023-01-02 00:00:00'),
            $value->addDays(1)
        );

        $this->assertEquals(
            DateTimeOptional::fromString('2023-01-03 00:00:00'),
            $value->addDays(2)
        );

        $this->assertEquals(
            DateTimeOptional::fromString('2022-12-31 00:00:00'),
            $value->addDays(-1)
        );
    }

    public function testAddMonths(): void
    {
        $value = DateTimeOptional::fromString('2023-01-01 00:00:00');

        $this->assertEquals(
            DateTimeOptional::fromString('2023-02-01 00:00:00'),
            $value->addMonths(1)
        );

        $this->assertEquals(
            DateTimeOptional::fromString('2023-03-01 00:00:00'),
            $value->addMonths(2)
        );

        $this->assertEquals(
            DateTimeOptional::fromString('2022-12-01 00:00:00'),
            $value->addMonths(-1)
        );
    }

    public function testAddYears(): void
    {
        $value = DateTimeOptional::fromString('2023-01-01 00:00:00');

        $this->assertEquals(
            DateTimeOptional::fromString('2024-01-01 00:00:00'),
            $value->addYears(1)
        );

        $this->assertEquals(
            DateTimeOptional::fromString('2025-01-01 00:00:00'),
            $value->addYears(2)
        );

        $this->assertEquals(
            DateTimeOptional::fromString('2022-01-01 00:00:00'),
            $value->addYears(-1)
        );
    }

    public function testAddHours(): void
    {
        $value = DateTimeOptional::fromString('2023-01-01 00:00:00');

        $this->assertEquals(
            DateTimeOptional::fromString('2023-01-01 01:00:00'),
            $value->addHours(1)
        );

        $this->assertEquals(
            DateTimeOptional::fromString('2022-12-31 23:00:00'),
            $value->addHours(-1)
        );

        $this->assertEquals(
            $value,
            $value->addHours(0)
        );
    }

    public function testAddMinutes(): void
    {
        $value = DateTimeOptional::fromString('2023-01-01 00:00:00');

        $this->assertEquals(
            DateTimeOptional::fromString('2023-01-01 00:01:00'),
            $value->addMinutes(1)
        );

        $this->assertEquals(
            DateTimeOptional::fromString('2022-12-31 23:59:00'),
            $value->addMinutes(-1)
        );

        $this->assertEquals(
            $value,
            $value->addMinutes(0)
        );
    }

    public function testAddSeconds(): void
    {
        $value = DateTimeOptional::fromString('2023-01-01 00:00:00');

        $this->assertEquals(
            DateTimeOptional::fromString('2023-01-01 00:00:01'),
            $value->addSeconds(1)
        );

        $this->assertEquals(
            DateTimeOptional::fromString('2022-12-31 23:59:59'),
            $value->addSeconds(-1)
        );

        $this->assertEquals(
            $value,
            $value->addSeconds(0)
        );
    }

    public function testFromTimestamp(): void
    {
        $timestamp = 1664959621;

        $value = DateTimeOptional::fromTimestamp($timestamp);

        $this->assertEquals($timestamp, $value->getTimestamp());
    }
}
