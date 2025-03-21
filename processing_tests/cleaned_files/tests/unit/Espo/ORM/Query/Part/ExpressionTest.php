<?php
//FORAI:F2680;DEF[C2277:ExpressionTest,F11129:setUp,F11130:testAlias1,F11131:testAlias2,F11132:testColumn1,F11133:testColumn2,F11134:testColumn3,F11135:testColumn4,F11136:testColumn5,F11137:testValue1,F11138:testValue2,F11139:testValue3,F11140:testValue4,F11141:testValue6,F11142:testValue7,F11143:testValue8,F11144:testFuncIf1,F11145:testFuncIf2,F11146:testFuncLike,F11147:testFuncEqual,F11148:testFuncNotEqual,F11149:testIn,F11150:testCoalesce,F11151:testIfNull,F11152:testMonth,F11153:testWeek0,F11154:testWeek1,F11155:testDayOfWeek,F11156:testDayOfMonth,F11157:testYear,F11158:testYearFiscsal,F11159:testNow,F11160:testConvertTimezone,F11161:testConcat,F11162:testReplace,F11163:testAdd,F11164:testRound,F11165:testAnd,F11166:testOr,F11167:testNot];IMP[];EXP[C2277,F11129,F11130,F11131,F11132,F11133,F11134,F11135,F11136,F11137,F11138,F11139,F11140,F11141,F11142,F11143,F11144,F11145,F11146,F11147,F11148,F11149,F11150,F11151,F11152,F11153,F11154,F11155,F11156,F11157,F11158,F11159,F11160,F11161,F11162,F11163,F11164,F11165,F11166,F11167];LANG[php]//

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

namespace tests\unit\Espo\ORM\Query\Part;

use Espo\ORM\Query\Part\Expression as Expr;

use RuntimeException;

class ExpressionTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
    }

    public function testAlias1(): void
    {
        $actual = Expr::alias('test')->getValue();

        $expected = '#test';

        $this->assertEquals($expected, $actual);
    }

    public function testAlias2(): void
    {
        $actual = Expr::alias('test.someAlias')->getValue();

        $expected = 'test.#someAlias';

        $this->assertEquals($expected, $actual);
    }

    public function testColumn1(): void
    {
        $actual = Expr::column('test')->getValue();

        $expected = 'test';

        $this->assertEquals($expected, $actual);
    }

    public function testColumn2(): void
    {
        $actual = Expr::column('alias.test')->getValue();

        $expected = 'alias.test';

        $this->assertEquals($expected, $actual);
    }

    public function testColumn3(): void
    {
        $actual = Expr::column('@alias1.test')->getValue();

        $expected = '@alias1.test';

        $this->assertEquals($expected, $actual);
    }

    public function testColumn4(): void
    {
        $this->expectException(RuntimeException::class);

        Expr::column('@alias@test');
    }

    public function testColumn5(): void
    {
        $this->expectException(RuntimeException::class);

        Expr::column('^test');
    }

    public function testValue1(): void
    {
        $actual = Expr::value('"test"')->getValue();

        $expected = "'" . '"test"' . "'";

        $this->assertEquals($expected, $actual);
    }

    public function testValue2(): void
    {
        $actual = Expr::value("'test'")->getValue();

        $expected = "'" . "\\'test\\'" . "'";

        $this->assertEquals($expected, $actual);
    }

    public function testValue3(): void
    {
        $actual = Expr::value(10)->getValue();

        $expected = '10';

        $this->assertEquals($expected, $actual);
    }

    public function testValue4(): void
    {
        $actual = Expr::value(10.5)->getValue();

        $expected = '10.5';

        $this->assertEquals($expected, $actual);
    }

    public function testValue6(): void
    {
        $actual = Expr::value(true)->getValue();

        $expected = 'TRUE';

        $this->assertEquals($expected, $actual);
    }

    public function testValue7(): void
    {
        $actual = Expr::value(false)->getValue();

        $expected = 'FALSE';

        $this->assertEquals($expected, $actual);
    }

    public function testValue8(): void
    {
        $actual = Expr::value(null)->getValue();

        $expected = 'NULL';

        $this->assertEquals($expected, $actual);
    }

    public function testFuncIf1(): void
    {
        $actual = Expr::if(
            Expr::column('test'),
            '1',
            2
        )->getValue();

        $expected = "IF:(test, '1', 2)";

        $this->assertEquals($expected, $actual);
    }

    public function testFuncIf2(): void
    {
        $actual = Expr::if(
            Expr::column('test'),
            Expr::column('hello.man'),
            true
        )->getValue();

        $expected = "IF:(test, hello.man, TRUE)";

        $this->assertEquals($expected, $actual);
    }

    public function testFuncLike(): void
    {
        $actual = Expr::like(
            Expr::column('test'),
            'test%'
        )->getValue();

        $expected = "LIKE:(test, 'test%')";

        $this->assertEquals($expected, $actual);
    }

    public function testFuncEqual(): void
    {
        $actual = Expr::equal(
            Expr::column('test'),
            1
        )->getValue();

        $expected = "EQUAL:(test, 1)";

        $this->assertEquals($expected, $actual);
    }

    public function testFuncNotEqual(): void
    {
        $actual = Expr::notEqual(
            Expr::column('test'),
            1
        )->getValue();

        $expected = "NOT_EQUAL:(test, 1)";

        $this->assertEquals($expected, $actual);
    }

    public function testIn(): void
    {
        $actual = Expr::in(
            Expr::column('test'),
            [
                Expr::value(1),
                2
            ]
        )->getValue();

        $expected = "IN:(test, 1, 2)";

        $this->assertEquals($expected, $actual);
    }

    public function testCoalesce(): void
    {
        $actual = Expr::coalesce(
            Expr::column('test1'),
            Expr::column('test2')
        )->getValue();

        $expected = "COALESCE:(test1, test2)";

        $this->assertEquals($expected, $actual);
    }

    public function testIfNull(): void
    {
        $actual = Expr::ifNull(
            Expr::column('test1'),
            ''
        )->getValue();

        $expected = "IFNULL:(test1, '')";

        $this->assertEquals($expected, $actual);
    }

    public function testMonth(): void
    {
        $actual = Expr::month(
            Expr::column('test')
        )->getValue();

        $expected = "MONTH_NUMBER:(test)";

        $this->assertEquals($expected, $actual);
    }

    public function testWeek0(): void
    {
        $actual = Expr::week(
            Expr::column('test')
        )->getValue();

        $expected = "WEEK_NUMBER:(test)";

        $this->assertEquals($expected, $actual);
    }

    public function testWeek1(): void
    {
        $actual = Expr::week(
            Expr::column('test'),
            1
        )->getValue();

        $expected = "WEEK_NUMBER_1:(test)";

        $this->assertEquals($expected, $actual);
    }

    public function testDayOfWeek(): void
    {
        $actual = Expr::dayOfWeek(
            Expr::column('test')
        )->getValue();

        $expected = "DAYOFWEEK:(test)";

        $this->assertEquals($expected, $actual);
    }

    public function testDayOfMonth(): void
    {
        $actual = Expr::dayOfMonth(
            Expr::column('test')
        )->getValue();

        $expected = "DAYOFMONTH:(test)";

        $this->assertEquals($expected, $actual);
    }

    public function testYear(): void
    {
        $actual = Expr::year(
            Expr::column('test')
        )->getValue();

        $expected = "YEAR:(test)";

        $this->assertEquals($expected, $actual);
    }

    public function testYearFiscsal(): void
    {
        $actual = Expr::yearFiscal(
            Expr::column('test'),
            10
        )->getValue();

        $expected = "YEAR_10:(test)";

        $this->assertEquals($expected, $actual);
    }

    public function testNow(): void
    {
        $actual = Expr::now(
        )->getValue();

        $expected = "NOW:()";

        $this->assertEquals($expected, $actual);
    }

    public function testConvertTimezone(): void
    {
        $actual = Expr::convertTimezone(
            Expr::column('test'),
            -10.5
        )->getValue();

        $expected = "TZ:(test, -10.5)";

        $this->assertEquals($expected, $actual);
    }

    public function testConcat(): void
    {
        $actual = Expr::concat(
            Expr::column('test'),
            ' ',
            'test'
        )->getValue();

        $expected = "CONCAT:(test, ' ', 'test')";

        $this->assertEquals($expected, $actual);
    }

    public function testReplace(): void
    {
        $actual = Expr::replace(
            Expr::column('test'),
            'test',
            'hello'
        )->getValue();

        $expected = "REPLACE:(test, 'test', 'hello')";

        $this->assertEquals($expected, $actual);
    }

    public function testAdd(): void
    {
        $actual = Expr::add(
            Expr::column('test'),
            1
        )->getValue();

        $expected = "ADD:(test, 1)";

        $this->assertEquals($expected, $actual);
    }

    public function testRound(): void
    {
        $actual = Expr::round(
            Expr::column('test'),
            1
        )->getValue();

        $expected = "ROUND:(test, 1)";

        $this->assertEquals($expected, $actual);
    }

    public function testAnd(): void
    {
        $actual = Expr::and(
            Expr::column('test1'),
            Expr::column('test2')
        )->getValue();

        $expected = "AND:(test1, test2)";

        $this->assertEquals($expected, $actual);
    }

    public function testOr(): void
    {
        $actual = Expr::or(
            Expr::column('test1'),
            Expr::column('test2')
        )->getValue();

        $expected = "OR:(test1, test2)";

        $this->assertEquals($expected, $actual);
    }

    public function testNot(): void
    {
        $actual = Expr::not(
            Expr::column('test')
        )->getValue();

        $expected = "NOT:(test)";

        $this->assertEquals($expected, $actual);
    }
}
