<?php
//FORAI:F2817;DEF[C2414:CurrencyTest,F12247:testValue,F12248:testAdd,F12249:testSubtract,F12250:testMultiply,F12251:testDivide,F12252:testRound1,F12253:testRound2,F12254:testRound3,F12255:testRound4,F12256:testRound5,F12257:testBadAdd,F12258:testGetBadCode,F12259:testCreateFromEntity,F12260:testCreatableFromEntityTrue,F12261:testCreatableFromEntityFalse,F12262:testCompare1,F12263:testCompare2,F12264:testCompare3,F12265:testCompare4,F12266:testIsNegative1,F12267:testIsNegative2,F12268:testIsNegative3];IMP[];EXP[C2414,F12247,F12248,F12249,F12250,F12251,F12252,F12253,F12254,F12255,F12256,F12257,F12258,F12259,F12260,F12261,F12262,F12263,F12264,F12265,F12266,F12267,F12268];LANG[php]//

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

namespace tests\unit\Espo\Core\Field\Currency;

use Espo\Core\{
    Field\Currency,
    Field\Currency\CurrencyFactory,
};

use Espo\ORM\Entity;

use RuntimeException;

class CurrencyTest extends \PHPUnit\Framework\TestCase
{
    public function testValue()
    {
        $value = Currency::create(2.0, 'USD');

        $this->assertEquals(2.0, $value->getAmount());
        $this->assertEquals('USD', $value->getCode());
    }

    public function testAdd()
    {
        $value = (new Currency(2.0, 'USD'))->add(
            new Currency(1.0, 'USD')
        );

        $this->assertEquals(3.0, $value->getAmount());
        $this->assertEquals('USD', $value->getCode());
    }

    public function testSubtract()
    {
        $value = (new Currency(2.0, 'USD'))->subtract(
            new Currency(3.0, 'USD')
        );

        $this->assertEquals(-1.0, $value->getAmount());
        $this->assertEquals('USD', $value->getCode());
    }

    public function testMultiply()
    {
        $value = (new Currency(2.0, 'USD'))->multiply(3.0);

        $this->assertEquals(6.0, $value->getAmount());
        $this->assertEquals('USD', $value->getCode());
    }

    public function testDivide()
    {
        $value = (new Currency(6.0, 'USD'))->divide(3.0);

        $this->assertEquals(2.0, $value->getAmount());
        $this->assertEquals('USD', $value->getCode());
    }

    public function testRound1()
    {
        $value = (new Currency(2.306, 'USD'))->round(2);

        $this->assertEquals(2.31, $value->getAmount());
        $this->assertEquals('USD', $value->getCode());
    }

    public function testRound2()
    {
        $value = (new Currency(2.306, 'USD'))->round(4);

        $this->assertEquals(2.306, $value->getAmount());
    }

    public function testRound3()
    {
        $value = (new Currency(2.306, 'USD'))->round(0);

        $this->assertEquals(2, $value->getAmount());
    }

    public function testRound4()
    {
        $value = (new Currency(-2.306, 'USD'))->round(2);

        $this->assertEquals(-2.31, $value->getAmount());
    }

    public function testRound5()
    {
        $value = (new Currency(-2.5, 'USD'))->round(0);

        $this->assertEquals(-3, $value->getAmount());
    }

    public function testBadAdd()
    {
        $this->expectException(RuntimeException::class);

        (new Currency(2.0, 'USD'))->add(
            new Currency(1.0, 'EUR')
        );
    }

    public function testGetBadCode()
    {
        $this->expectException(RuntimeException::class);

        new Currency(2.0, '');
    }

    public function testCreateFromEntity()
    {
        $entity = $this->createMock(Entity::class);

        $entity
            ->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['test', 10.0],
                ['testCurrency', 'USD'],
            ]);

        $entity
            ->expects($this->any())
            ->method('has')
            ->willReturnMap([
                ['test', true],
                ['testCurrency', true],
            ]);

        $factory = new CurrencyFactory();

        $value = $factory->createFromEntity($entity, 'test');

        $this->assertEquals(10.0, $value->getAmount());
        $this->assertEquals('USD', $value->getCode());
    }

    public function testCreatableFromEntityTrue()
    {
        $entity = $this->createMock(Entity::class);

        $entity
            ->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['test', 5],
                ['testCurrency', 'USD'],
            ]);

        $factory = new CurrencyFactory();

        $this->assertTrue(
            $factory->isCreatableFromEntity($entity, 'test')
        );
    }

    public function testCreatableFromEntityFalse()
    {
        $entity = $this->createMock(Entity::class);

        $entity
            ->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['test', 5.0],
                ['testCurrency', null],
            ]);

        $factory = new CurrencyFactory();

        $this->assertFalse(
            $factory->isCreatableFromEntity($entity, 'test')
        );
    }

    public function testCompare1(): void
    {
        $this->assertEquals(
            1,
            Currency::create(2.0, 'USD')
                ->compare(
                    Currency::create(1.0, 'USD')
                )
        );
    }

    public function testCompare2(): void
    {
        $this->assertEquals(
            0,
            Currency::create(2.1, 'USD')
                ->compare(
                    Currency::create(2.1, 'USD')
                )
        );
    }

    public function testCompare3(): void
    {
        $this->assertEquals(
            -1,
            Currency::create(2.1, 'USD')
                ->compare(
                    Currency::create(3.1, 'USD')
                )
        );
    }

    public function testCompare4(): void
    {
        $this->expectException(RuntimeException::class);

        Currency::create(2.1, 'EUR')
            ->compare(
                Currency::create(2.1, 'USD')
            );
    }

    public function testIsNegative1(): void
    {
        $this->assertTrue(Currency::create(-1.0, 'USD')->isNegative());
    }

    public function testIsNegative2(): void
    {
        $this->assertFalse(Currency::create(1.0, 'USD')->isNegative());
    }

    public function testIsNegative3(): void
    {
        $this->assertFalse(Currency::create(0.0, 'USD')->isNegative());
    }
}
