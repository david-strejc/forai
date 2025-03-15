<?php
//FORAI:F2820;DEF[C2417:DateTimeOptionalDateTest,F12302:testFromString,F12303:testFromDateTime,F12304:testBad1,F12305:testBad2,F12306:testEmpty,F12307:testGetDateTime,F12308:testGetTimezone,F12309:testGetMethods,F12310:testAdd,F12311:testSubtract,F12312:testModify,F12313:testWithTimezone,F12314:testDiff,F12315:testToday,F12316:testAddDays,F12317:testAddMonths,F12318:testAddYears];IMP[];EXP[C2417,F12302,F12303,F12304,F12305,F12306,F12307,F12308,F12309,F12310,F12311,F12312,F12313,F12314,F12315,F12316,F12317,F12318];LANG[php]//

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

namespace tests\unit\Espo\Core\Field\DateTimeOptional;

use Espo\Core\{
    Field\DateTimeOptional,
};

use DateTimeImmutable;
use DateTimeZone;
use RuntimeException;
use DateInterval;

class DateTimeOptionalDateTest extends \PHPUnit\Framework\TestCase
{
    public function testFromString()
    {
        $value = DateTimeOptional::fromString('2021-05-01');

        $this->assertEquals('2021-05-01', $value->toString());

        $this->assertTrue($value->isAllDay());
    }

    public function testFromDateTime()
    {
        $dt = new DateTimeImmutable('2021-05-01', new DateTimeZone('UTC'));

        $value = DateTimeOptional::fromDateTimeAllDay($dt);

        $this->assertEquals('2021-05-01', $value->toString());
    }

    public function testBad1()
    {
        $this->expectException(RuntimeException::class);

        DateTimeOptional::fromString('2021-05-A');
    }

    public function testBad2()
    {
        $this->expectException(RuntimeException::class);

        DateTimeOptional::fromString('2021-05-1');
    }

    public function testEmpty()
    {
        $this->expectException(RuntimeException::class);

        DateTimeOptional::fromString('');
    }

    public function testGetDateTime()
    {
        $value = DateTimeOptional::fromString('2021-05-01');

        $this->assertEquals('2021-05-01', $value->toDateTime()->format('Y-m-d'));
    }

    public function testGetTimezone()
    {
        $value = DateTimeOptional::fromString('2021-05-01');

        $this->assertEquals(new DateTimeZone('UTC'), $value->toDateTime()->getTimezone());
    }

    public function testGetMethods()
    {
        $value = DateTimeOptional::fromString('2021-05-01');

        $dt = new DateTimeImmutable('2021-05-01', new DateTimeZone('UTC'));

        $this->assertEquals(1, $value->getDay());
        $this->assertEquals(5, $value->getMonth());
        $this->assertEquals(2021, $value->getYear());
        $this->assertEquals(6, $value->getDayOfWeek());

        $this->assertEquals($dt->getTimestamp(), $value->toTimestamp());
    }

    public function testAdd()
    {
        $value = DateTimeOptional::fromString('2021-05-01');

        $modifiedValue = $value->add(DateInterval::createFromDateString('1 day'));

        $this->assertEquals('2021-05-02', $modifiedValue->toString());

        $this->assertNotSame($modifiedValue, $value);
    }

    public function testSubtract()
    {
        $value = DateTimeOptional::fromString('2021-05-01');

        $modifiedValue = $value->subtract(DateInterval::createFromDateString('1 day'));

        $this->assertEquals('2021-04-30', $modifiedValue->toString());

        $this->assertNotSame($modifiedValue, $value);
    }

    public function testModify()
    {
        $value = DateTimeOptional::fromString('2021-05-01');

        $modifiedValue = $value->modify('+1 month');

        $this->assertEquals('2021-06-01', $modifiedValue->toString());

        $this->assertNotSame($modifiedValue, $value);
    }

    public function testWithTimezone()
    {
        $value = DateTimeOptional
            ::fromString('2021-05-01')
            ->withTimezone(new DateTimeZone('Europe/Kiev'));

        $this->assertEquals('2021-05-01 00:00:00', $value->toString());

        $this->assertEquals(new DateTimeZone('Europe/Kiev'), $value->getTimezone());

        $this->assertFalse($value->isAllDay());
    }

    public function testDiff(): void
    {
        $value1 = DateTimeOptional::fromString('2021-05-01');
        $value2 = DateTimeOptional::fromString('2021-05-02');

        $this->assertEquals(1, $value1->diff($value2)->d);
        $this->assertEquals(0, $value1->diff($value2)->invert);
    }

    public function testToday(): void
    {
        $value1 = DateTimeOptional::createToday();
        $value2 = DateTimeOptional::createToday(new DateTimeZone('Europe/Kiev'));

        $this->assertEquals(0, $value1->diff($value2)->invert);
    }

    public function testAddDays(): void
    {
        $value = DateTimeOptional::fromString('2023-01-01');

        $this->assertEquals(
            DateTimeOptional::fromString('2023-01-02'),
            $value->addDays(1)
        );

        $this->assertEquals(
            DateTimeOptional::fromString('2023-01-03'),
            $value->addDays(2)
        );

        $this->assertEquals(
            DateTimeOptional::fromString('2022-12-31'),
            $value->addDays(-1)
        );
    }

    public function testAddMonths(): void
    {
        $value = DateTimeOptional::fromString('2023-01-01');

        $this->assertEquals(
            DateTimeOptional::fromString('2023-02-01'),
            $value->addMonths(1)
        );

        $this->assertEquals(
            DateTimeOptional::fromString('2023-03-01'),
            $value->addMonths(2)
        );

        $this->assertEquals(
            DateTimeOptional::fromString('2022-12-01'),
            $value->addMonths(-1)
        );
    }

    public function testAddYears(): void
    {
        $value = DateTimeOptional::fromString('2023-01-01');

        $this->assertEquals(
            DateTimeOptional::fromString('2024-01-01'),
            $value->addYears(1)
        );

        $this->assertEquals(
            DateTimeOptional::fromString('2025-01-01'),
            $value->addYears(2)
        );

        $this->assertEquals(
            DateTimeOptional::fromString('2022-01-01'),
            $value->addYears(-1)
        );
    }
}
