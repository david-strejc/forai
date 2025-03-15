<?php
//FORAI:F2716;DEF[C2312:EvaluatorTest<TestCase>,F11362:setUp,F11363:tearDown,F11364:testEvaluateMathExpression1,F11365:testEvaluateMathExpression2,F11366:testEvaluateMathExpression3,F11367:testEvaluateMathExpression4,F11368:testEvaluateMathExpression5,F11369:testEvaluateMathExpression6,F11370:testEvaluateMathExpression7,F11371:testEvaluateMathExpression8,F11372:testEvaluateMathExpression9,F11373:testEvaluateMathExpression10,F11374:testEvaluateMathExpression11,F11375:testEvaluateMathExpression12,F11376:testEvaluateMathExpression13,F11377:testEvaluateMathExpression14,F11378:testEvaluateMathExpression15,F11379:testEvaluateList1,F11380:testEvaluateList2,F11381:testEvaluateEmpty,F11382:testNotEqualsNull,F11383:testSummationOfMultipleIfThenElse,F11384:testStringPad,F11385:testStringMatchAll,F11386:testStringMatch,F11387:testStringMatchExtract,F11388:testStringReplace,F11389:testArrayAt,F11390:testArrayJoin,F11391:testArrayPush1,F11392:testArrayPush2,F11393:testArrayIncludes1,F11394:testArrayIncludes2,F11395:testArrayIndexOf1,F11396:testArrayIndexOf2,F11397:testArrayIndexOf3,F11398:testArrayIndexOf4,F11399:testArrayIndexOf5,F11400:testArrayUnique1,F11401:testArrayRemoveAt1,F11402:testArrayRemoveAt2,F11403:testArrayRemoveAt3,F11404:testWhileFunction,F11405:testComment1,F11406:testComment2,F11407:testComment3,F11408:testComment4,F11409:testComment5,F11410:testComment6,F11411:testComment7,F11412:testComment8,F11413:testIntValue,F11414:testFloatZeroDecimals,F11415:testJsonRetrieve1,F11416:testJsonRetrieve2,F11417:testJsonRetrieve3,F11418:testJsonRetrieve4,F11419:testJsonRetrieve5,F11420:testJsonRetrieve6,F11421:testJsonRetrieve7,F11422:testJsonRetrieve8,F11423:testNegate1,F11424:testLogicalProority,F11425:testGenerateId,F11426:testModulo1,F11427:testModulo2,F11428:testParentheses1,F11429:testSyntaxError1,F11430:testSyntaxError2,F11431:testSyntaxError3,F11432:testSyntaxError4,F11433:testSyntaxError5,F11434:testSyntaxError6,F11435:testSyntaxError7,F11436:testSyntaxError8,F11437:testSyntaxError9,F11438:testStringSplit1,F11439:testStringSplit2,F11440:testStringSplit3,F11441:testNumberParseInt1,F11442:testNumberParseInt2,F11443:testNumberParseFloat1,F11444:testNumberParseFloat2,F11445:testNumberPower1,F11446:testNumberPower2,F11447:testNullCoalescing1,F11448:testNullCoalescing2,F11449:testNullCoalescing3,F11450:testNullCoalescing4,F11451:testNullCoalescing5,F11452:testNullCoalescing6,F11453:testObjectCreate,F11454:testObjectSet,F11455:testObjectGet1,F11456:testObjectGet2,F11457:testObjectClear,F11458:testObjectHas1,F11459:testObjectHas2,F11460:testObjectClone,F11461:testJsonEncode,F11462:testEmpty1,F11463:testOnlyComment,F11464:testIfWithComment1,F11465:testIfWithComment2,F11466:testIfWithComment3,F11467:testIfWithComment4,F11468:testWhileStatement1,F11469:testWhileStatement2,F11470:testWhileStatement3,F11471:testPlus1,F11472:testPlus2,F11473:testFuncInterface1,F11474:testFuncInterface2,F11475:testFuncInterfaceException,F11476:testNoTrailingSemicolon1,F11477:testNoTrailingSemicolon2,F11478:testNoTrailingSemicolon3,F11479:testNoTrailingSemicolon4,F11480:testSemicolonAndParentheses1,F11481:testSemicolonAndParentheses2,F11482:testSemicolonAndParentheses3,F11483:testSemicolonAndParentheses4,F11484:testSemicolonAndParentheses5,F11485:testStrings1,F11486:testStrings2,F11487:testStrings3,F11488:testStrings4,F11489:testStrings5,F11490:testStrings6,F11491:testStrings7,F11492:testStrings8,F11493:testStrings9,F11494:testStrings10,F11495:testBase64,F11496:testUnsafe1,F11497:testUnsafe2,F11498:testStringsWithOperator,F11499:testAssignAndLogical1,F11500:testAssignAndLogical2,F11501:testMarkdownTransform,F11502:testLastExpressionEvaluation1,F11503:testLastExpressionEvaluation2,F11504:testLastExpressionEvaluation3,F11505:testLastExpressionEvaluation4,F11506:testLastExpressionEvaluation5,F11507:testLastExpressionEvaluationNull1,F11508:testLastExpressionEvaluationNull2];IMP[];EXP[C2312,F11362,F11363,F11364,F11365,F11366,F11367,F11368,F11369,F11370,F11371,F11372,F11373,F11374,F11375,F11376,F11377,F11378,F11379,F11380,F11381,F11382,F11383,F11384,F11385,F11386,F11387,F11388,F11389,F11390,F11391,F11392,F11393,F11394,F11395,F11396,F11397,F11398,F11399,F11400,F11401,F11402,F11403,F11404,F11405,F11406,F11407,F11408,F11409,F11410,F11411,F11412,F11413,F11414,F11415,F11416,F11417,F11418,F11419,F11420,F11421,F11422,F11423,F11424,F11425,F11426,F11427,F11428,F11429,F11430,F11431,F11432,F11433,F11434,F11435,F11436,F11437,F11438,F11439,F11440,F11441,F11442,F11443,F11444,F11445,F11446,F11447,F11448,F11449,F11450,F11451,F11452,F11453,F11454,F11455,F11456,F11457,F11458,F11459,F11460,F11461,F11462,F11463,F11464,F11465,F11466,F11467,F11468,F11469,F11470,F11471,F11472,F11473,F11474,F11475,F11476,F11477,F11478,F11479,F11480,F11481,F11482,F11483,F11484,F11485,F11486,F11487,F11488,F11489,F11490,F11491,F11492,F11493,F11494,F11495,F11496,F11497,F11498,F11499,F11500,F11501,F11502,F11503,F11504,F11505,F11506,F11507,F11508];LANG[php]//

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

namespace tests\unit\Espo\Core\Formula;

use Espo\Core\Formula\Evaluator;
use Espo\Core\Formula\Exceptions\Error;
use Espo\Core\Formula\Exceptions\UnsafeFunction;
use Espo\Core\InjectableFactory;
use Espo\Core\Formula\Exceptions\SyntaxError;
use Espo\Core\Utils\Log;
use Espo\ORM\EntityManager;

use PHPUnit\Framework\TestCase;
use tests\unit\ContainerMocker;

class EvaluatorTest extends TestCase
{
    /**
     * @var Evaluator
     */
    private $evaluator;

    protected function setUp() : void
    {
        $log = $this->createMock(Log::class);
        $entityManager = $this->createMock(EntityManager::class);

        $containerMocker = new ContainerMocker($this);

        $container = $containerMocker->create([
            'log' => $log,
            'entityManager' => $entityManager,
        ]);

        $injectableFactory = new InjectableFactory($container);

        $this->evaluator = new Evaluator($injectableFactory, [], ['test\\unsafe']);
    }

    protected function tearDown() : void
    {
        $this->evaluator = null;
    }

    public function testEvaluateMathExpression1()
    {
        $expression = "5 - (2 + 1)";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals(5 - (2 + 1), $actual);
    }

    public function testEvaluateMathExpression2(): void
    {
        $expression = "5 - 2 + 1";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals(5 - 2 + 1, $actual);
    }

    public function testEvaluateMathExpression3(): void
    {
        $expression = "5 - 2 - 1";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals(5 - 2 - 1, $actual);
    }

    public function testEvaluateMathExpression4(): void
    {
        $expression = "5-2-1";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals(5 - 2 - 1, $actual);
    }

    public function testEvaluateMathExpression5(): void
    {
        $expression = "5 * 2 / 3.0";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals(5 * 2 / 3.0, $actual);
    }

    public function testEvaluateMathExpression6(): void
    {
        $expression = "5 * 2 + 3 * 4";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals(5 * 2 + 3 * 4, $actual);
    }

    public function testEvaluateMathExpression7(): void
    {
        $expression = "5 * (2 + 3) * 4";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals(5 * (2 + 3) * 4, $actual);
    }

    public function testEvaluateMathExpression8(): void
    {
        $expression = "5 * (2 + 3) * 4 - (5 - 4)";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals(5 * (2 + 3) * 4 - (5 - 4), $actual);
    }

    public function testEvaluateMathExpression9(): void
    {
        $expression = "5 * -2";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals(5 * -2, $actual);
    }

    public function testEvaluateMathExpression10(): void
    {
        $expression = "5 *  -2";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals(5 * -2, $actual);
    }

    public function testEvaluateMathExpression11(): void
    {
        $expression = "5 *  - 2";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals(5 * -2, $actual);
    }

    public function testEvaluateMathExpression12(): void
    {
        $expression = "5 * +2";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals(5 * +2, $actual);
    }

    public function testEvaluateMathExpression13(): void
    {
        $expression = "5 + -2";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals(5 + -2, $actual);
    }

    public function testEvaluateMathExpression14(): void
    {
        $expression = "(5 - 2) + -2";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals((5 - 2) + -2, $actual);
    }

    public function testEvaluateMathExpression15(): void
    {
        $expression = "+ -2";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals(+ -2, $actual);
    }

    public function testEvaluateList1()
    {
        $expression = "list()";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals([], $actual);
    }

    public function testEvaluateList2()
    {
        $expression = "list(1)";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals([1], $actual);
    }

    public function testEvaluateEmpty()
    {
        $expression = '';
        $actual = $this->evaluator->process($expression);
        $this->assertEquals(null, $actual);
    }

    public function testNotEqualsNull()
    {
        $expression = "5 != null";
        $actual = $this->evaluator->process($expression);
        $this->assertTrue($actual);
    }

    public function testSummationOfMultipleIfThenElse()
    {
        $expression = "
            ifThenElse(
                true,
                (1 + 0 + 1) - 1 * 0.5,
                0
            )
            +
            ifThenElse(
                true,
                (1 - 0) * 0.5,
                0
            )
            +
            ifThenElse(
                true,
                (1 - 0) * 0.5,
                0
            )
        ";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals(2.5, $actual);
    }

    public function testStringPad()
    {
        $expression = "string\\pad('1', 3, '0')";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals('100', $actual);

        $expression = "string\\pad('1', 3)";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals('1  ', $actual);

        $expression = "string\\pad('11', 4, '0', 'left')";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals('0011', $actual);

        $expression = "string\\pad('11', 4, '0', 'both')";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals('0110', $actual);
    }

    public function testStringMatchAll()
    {
        $expression = "string\\matchAll('{token1} foo {token2} bar', '/{[^}]*}/')";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals(['{token1}', '{token2}'], $actual);

        $expression = "string\\matchAll('foo bar', '/{[^}]*}/')";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals(null, $actual);

        $expression = "string\\matchAll('{token1} foo {token2} bar', '/{[^}]*}/', 5)";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals(['{token2}'], $actual);
    }

    public function testStringMatch()
    {
        $expression = "string\\match('{token1} foo {token2} bar', '/{[^}]*}/')";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals('{token1}', $actual);

        $expression = "string\\match('foo bar', '/{[^}]*}/')";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals(null, $actual);

        $expression = "string\\match('{token1} foo {token2} bar', '/{[^}]*}/', 5)";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals('{token2}', $actual);
    }

    public function testStringMatchExtract(): void
    {
        $expression = "string\\matchExtract('test: 1000', '/phone\: (.*)$/')";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals(null, $actual);

        $expression = "string\\matchExtract('phone: 1000', '/phone\: (.*)$/')";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals(['1000'], $actual);

        $expression = "string\\matchExtract('phone: 1000 2000', '/phone\: (.*) (.*)$/')";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals(['1000', '2000'], $actual);
    }

    public function testStringReplace()
    {
        $expression = "string\\replace('hello {test} hello', '{test}', 'hello')";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals('hello hello hello', $actual);
    }

    public function testArrayAt()
    {
        $expression = "array\\at(list(1, 2, 4, 8, 16), 2)";
        $actual = $this->evaluator->process($expression);
        $this->assertEquals(4, $actual);
    }

    public function testArrayJoin()
    {
        $expression = "array\\join(list('0', '1'), '-')";

        $expected = '0-1';

        $actual = $this->evaluator->process($expression);

        $this->assertEquals($expected, $actual);
    }

    public function testArrayPush1(): void
    {
        $expression = "array\\push(null, 1)";

        $actual = $this->evaluator->process($expression);

        $this->assertEquals([1], $actual);
    }

    public function testArrayPush2(): void
    {
        $expression = "array\\push(list(0), 1)";

        $actual = $this->evaluator->process($expression);

        $this->assertEquals([0, 1], $actual);
    }

    public function testArrayIncludes1(): void
    {
        $expression = "array\\includes(list(1), 1)";

        $actual = $this->evaluator->process($expression);

        $this->assertTrue($actual);
    }

    public function testArrayIncludes2(): void
    {
        $expression = "array\\includes(null, 1)";

        $actual = $this->evaluator->process($expression);

        $this->assertFalse($actual);
    }

    public function testArrayIndexOf1(): void
    {
        $expression = "array\\indexOf(list(0, 1, 2), 1)";

        $actual = $this->evaluator->process($expression);

        $this->assertEquals(1, $actual);
    }

    public function testArrayIndexOf2(): void
    {
        $expression = "array\\indexOf(list('0', '1', '2'), '0')";

        $actual = $this->evaluator->process($expression);

        $this->assertSame(0, $actual);
    }

    public function testArrayIndexOf3(): void
    {
        $expression = "array\\indexOf(list('0', '1', '2'), 1)";

        $actual = $this->evaluator->process($expression);

        $this->assertSame(null, $actual);
    }

    public function testArrayIndexOf4(): void
    {
        $expression = "array\\indexOf(list(), 1)";

        $actual = $this->evaluator->process($expression);

        $this->assertSame(null, $actual);
    }

    public function testArrayIndexOf5(): void
    {
        $expression = "array\\indexOf(null, 1)";

        $actual = $this->evaluator->process($expression);

        $this->assertSame(null, $actual);
    }

    public function testArrayUnique1(): void
    {
        $expression = "array\\unique(list('0', '0', '1'))";

        $actual = $this->evaluator->process($expression);

        $this->assertSame(['0', '1'], $actual);
    }

    public function testArrayRemoveAt1(): void
    {
        $expression = "array\\removeAt(list('0', '1'), 0)";

        $actual = $this->evaluator->process($expression);

        $this->assertEquals(['1'], $actual);
    }

    public function testArrayRemoveAt2(): void
    {
        $expression = "array\\removeAt(list('0', '1'), 3)";

        $actual = $this->evaluator->process($expression);

        $this->assertEquals(['0', '1'], $actual);
    }

    public function testArrayRemoveAt3(): void
    {
        $expression = "array\\removeAt(list('0', '1'), null)";

        $actual = $this->evaluator->process($expression);

        $this->assertEquals(['0', '1'], $actual);
    }

    public function testWhileFunction()
    {
        $expression = "
            \$source = list(0, 1, 2);
            \$target = list();

            \$i = 0;
            while(\$i < array\\length(\$source),
                \$target = array\\push(
                    \$target,
                    array\\at(\$source, \$i)
                );
                \$i = \$i + 1;
            );
        ";

        $vars = (object) [];

        $this->evaluator->process($expression, null, $vars);

        $this->assertEquals([0, 1, 2], $vars->target);
    }

    public function testComment1()
    {
        $expression = "
            // test
            \$test = '1';
        ";

        $vars = (object) [];
        $this->evaluator->process($expression, null, $vars);
        $this->assertEquals('1', $vars->test);
    }

    public function testComment2()
    {
        $expression = "
            // test'test
            \$test = '1';
        ";

        $vars = (object) [];
        $this->evaluator->process($expression, null, $vars);
        $this->assertEquals('1', $vars->test);
    }

    public function testComment3()
    {
        $expression = "
            // test\"test
            \$test = '1';
        ";

        $vars = (object) [];
        $this->evaluator->process($expression, null, $vars);
        $this->assertEquals('1', $vars->test);
    }

    public function testComment4()
    {
        $expression = "
            // test)(test
            \$test = '1';
        ";

        $vars = (object) [];
        $this->evaluator->process($expression, null, $vars);
        $this->assertEquals('1', $vars->test);
    }

    public function testComment5()
    {
        $expression = "
            /* test'test
            */
            \$test = '1';
        ";

        $vars = (object) [];
        $this->evaluator->process($expression, null, $vars);
        $this->assertEquals('1', $vars->test);
    }

    public function testComment6()
    {
        $expression = "
            /* test(test
            */
            \$test = '1';
        ";

        $vars = (object) [];
        $this->evaluator->process($expression, null, $vars);
        $this->assertEquals('1', $vars->test);
    }

    public function testComment7()
    {
        $expression = "
            \$test = '/* 1 */';
        ";

        $vars = (object) [];
        $this->evaluator->process($expression, null, $vars);
        $this->assertEquals('/* 1 */', $vars->test);
    }

    public function testComment8()
    {
        $expression = "
            \$test = '// 1 */';
        ";

        $vars = (object) [];
        $this->evaluator->process($expression, null, $vars);
        $this->assertEquals('// 1 */', $vars->test);
    }

    public function testIntValue()
    {
        $expression = "0";

        $value = $this->evaluator->process($expression);

        $this->assertTrue(is_int($value));
    }

    public function testFloatZeroDecimals()
    {
        $expression = "1.0";

        $value = $this->evaluator->process($expression);

        $this->assertTrue(is_float($value));
    }

    public function testJsonRetrieve1()
    {
        $value = (object) [
            'a' => 'test',
        ];

        $expression = "json\\retrieve(\$value, 'a')";

        $result = $this->evaluator->process($expression, null, (object) [
            'value' => json_encode($value),
        ]);

        $this->assertEquals('test', $result);
    }

    public function testJsonRetrieve2()
    {
        $value =  [
            0 => 'test',
        ];

        $expression = "json\\retrieve(\$value, '0')";

        $result = $this->evaluator->process($expression, null, (object) [
            'value' => json_encode($value),
        ]);

        $this->assertEquals('test', $result);
    }

    public function testJsonRetrieve3()
    {
        $value = (object) [
            'a' => [
                'ab' => 'test'
            ],
        ];

        $expression = "json\\retrieve(\$value, 'a.ab')";

        $result = $this->evaluator->process($expression, null, (object) [
            'value' => json_encode($value),
        ]);

        $this->assertEquals('test', $result);
    }

    public function testJsonRetrieve4()
    {
        $value = (object) [
            'a' => [
                'ab' => 'test'
            ],
        ];

        $expression = "json\\retrieve(\$value, 'b.c')";

        $result = $this->evaluator->process($expression, null, (object) [
            'value' => json_encode($value),
        ]);

        $this->assertEquals(null, $result);
    }

    public function testJsonRetrieve5()
    {
        $value = (object) [
            'a' => [
                'ab' => 'test'
            ],
        ];

        $expression = "json\\retrieve(\$value, '0')";

        $result = $this->evaluator->process($expression, null, (object) [
            'value' => json_encode($value),
        ]);

        $this->assertEquals(null, $result);
    }

    public function testJsonRetrieve6()
    {
        $value =  [
            0 => (object) [
                'a' => 'test'
            ],
        ];

        $expression = "json\\retrieve(\$value, '0.a')";

        $result = $this->evaluator->process($expression, null, (object) [
            'value' => json_encode($value),
        ]);

        $this->assertEquals('test', $result);
    }

    public function testJsonRetrieve7()
    {
        $value = (object) [
            'a.b' => (object) [
                'c' => 'test'
            ],
        ];

        $expression = "json\\retrieve(\$value, 'a\\.b.c')";

        $result = $this->evaluator->process($expression, null, (object) [
            'value' => json_encode($value),
        ]);

        $this->assertEquals('test', $result);
    }

    public function testJsonRetrieve8()
    {
        $value = (object) [
            0 => 'test',
        ];

        $expression = "json\\retrieve(\$value)";

        $result = $this->evaluator->process($expression, null, (object) [
            'value' => json_encode($value),
        ]);

        $this->assertEquals($value, $result);
    }

    public function testNegate1()
    {
        $expression = "!string\contains('hello', 'test')";

        $result = $this->evaluator->process($expression);

        $this->assertTrue($result);
    }

    public function testLogicalProority()
    {
        $expression = "0 && 0 || 1";

        $result = $this->evaluator->process($expression);

        $this->assertTrue($result);
    }

    public function testGenerateId()
    {
        $expression = "util\generateId()";

        $id = $this->evaluator->process($expression);

        $this->assertIsString($id);

        $this->assertNotEmpty($id);
    }

    public function testModulo1() : void
    {
        $expression = "123 % 5";
        $actual = $this->evaluator->process($expression);

        $this->assertEquals(123 % 5, $actual);
    }

    public function testModulo2() : void
    {
        $expression = "124 % 5";
        $actual = $this->evaluator->process($expression);

        $this->assertEquals(124 % 5, $actual);
    }

    public function testParentheses1()
    {
        $expression = "
            \$test = 1;

            ifThen(
                true,
                (
                    \$hello = 2;
                    \$test = \$hello;
                )
            );
        ";

        $vars = (object) [];

        $this->evaluator->process($expression, null, $vars);

        $this->assertEquals(2, $vars->test);

    }

    public function testSyntaxError1(): void
    {
        $expression = "test = 'test";

        $this->expectException(SyntaxError::class);

        $this->evaluator->process($expression, null);
    }

    public function testSyntaxError2(): void
    {
        $expression = "test 'tests'";

        $this->expectException(SyntaxError::class);

        $this->evaluator->process($expression, null);
    }

    public function testSyntaxError3(): void
    {
        $expression = "test tests";

        $this->expectException(SyntaxError::class);

        $this->evaluator->process($expression, null);
    }

    public function testSyntaxError4(): void
    {
        $expression = "test = ";

        $this->expectException(SyntaxError::class);

        $this->evaluator->process($expression, null);
    }

    public function testSyntaxError5(): void
    {
        $expression = "test =";

        $this->expectException(SyntaxError::class);

        $this->evaluator->process($expression, null);
    }

    public function testSyntaxError6(): void
    {
        $expression = "test = $";

        $this->expectException(SyntaxError::class);

        $this->evaluator->process($expression, null);
    }

    public function testSyntaxError7(): void
    {
        $expression = "test.+test";

        $this->expectException(SyntaxError::class);

        $this->evaluator->process($expression, null);
    }

    public function testSyntaxError8(): void
    {
        $expression = "\$test.'test' = test";

        $this->expectException(SyntaxError::class);

        $this->evaluator->process($expression, null);
    }

    public function testSyntaxError9(): void
    {
        $expression = "test.test('test')";

        $this->expectException(SyntaxError::class);

        $this->evaluator->process($expression, null);
    }

    public function testStringSplit1(): void
    {
        $expression = "string\\split('1 2 3', ' ')";

        $this->assertEquals(
            ['1', '2', '3'],
            $this->evaluator->process($expression, null)
        );
    }

    public function testStringSplit2(): void
    {
        $expression = "string\\split(null, '')";

        $this->assertEquals(
            [],
            $this->evaluator->process($expression, null)
        );
    }

    public function testStringSplit3(): void
    {
        $expression = "string\\split('12', '')";

        $this->assertEquals(
            ['1', '2'],
            $this->evaluator->process($expression, null)
        );
    }

    public function testNumberParseInt1(): void
    {
        $expression = "number\\parseInt('1')";

        $this->assertEquals(
            1,
            $this->evaluator->process($expression, null)
        );
    }

    public function testNumberParseInt2(): void
    {
        $expression = "number\\parseInt(null)";

        $this->assertEquals(
            0,
            $this->evaluator->process($expression, null)
        );
    }

    public function testNumberParseFloat1(): void
    {
        $expression = "number\\parseFloat('1')";

        $this->assertEquals(
            1.0,
            $this->evaluator->process($expression, null)
        );
    }

    public function testNumberParseFloat2(): void
    {
        $expression = "number\\parseFloat(null)";

        $this->assertEquals(
            0.0,
            $this->evaluator->process($expression, null)
        );
    }

    public function testNumberPower1(): void
    {
        $expression = "number\\power(3, 2)";

        $this->assertSame(
            9,
            $this->evaluator->process($expression)
        );
    }

    public function testNumberPower2(): void
    {
        $expression = "number\\power(3.0, 2.0)";

        $this->assertSame(
            9.0,
            $this->evaluator->process($expression)
        );
    }

    public function testNullCoalescing1(): void
    {
        $expression = "null ?? 1";

        $this->assertSame(
            1,
            $this->evaluator->process($expression, null)
        );
    }

    public function testNullCoalescing2(): void
    {
        $expression = "(null) ?? 1 ?? 0";

        $this->assertSame(
            1,
            $this->evaluator->process($expression, null)
        );
    }

    public function testNullCoalescing3(): void
    {
        $expression = "(null) ?? ifThenElse(false, 1, null) ?? 0";

        $this->assertSame(
            0,
            $this->evaluator->process($expression, null)
        );
    }

    public function testNullCoalescing4(): void
    {
        $expression = "null ?? (1 ?? 0)";

        $this->assertSame(
            1,
            $this->evaluator->process($expression, null)
        );
    }

    public function testNullCoalescing5(): void
    {
        $expression = "null ?? 1 + 2";

        $this->assertSame(
            3,
            $this->evaluator->process($expression, null)
        );
    }

    public function testNullCoalescing6(): void
    {
        $expression = "null ?? true || false";

        $this->assertSame(
            true,
            $this->evaluator->process($expression, null)
        );
    }

    public function testObjectCreate(): void
    {
        $expression = "object\\create()";

        $this->assertEquals(
            (object) [],
            $this->evaluator->process($expression, null)
        );
    }

    public function testObjectSet(): void
    {
        $expression = "
            \$o = object\\create();
            object\\set(\$o, 'key', 'value');
        ";

        $vars = (object) [];

        $this->evaluator->process($expression, null, $vars);

        $this->assertEquals(
            'value',
            $vars->o->key
        );
    }

    public function testObjectGet1(): void
    {
        $expression = "
            \$o = object\\create();
            object\\set(\$o, 'key', 'value');
            \$v = object\\get(\$o, 'key');
        ";

        $vars = (object) [];

        $this->evaluator->process($expression, null, $vars);

        $this->assertEquals(
            'value',
            $vars->o->key
        );
    }

    public function testObjectGet2(): void
    {
        $expression = "
            \$o = object\\create();
            \$v = object\\get(\$o, 'key');
        ";

        $vars = (object) [];

        $this->evaluator->process($expression, null, $vars);

        $this->assertEquals(
            null,
            $vars->v
        );
    }

    public function testObjectClear(): void
    {
        $expression = "
            \$o = object\\create();
            object\\set(\$o, 'key', 'value');
            object\\clear(\$o, 'key');
        ";

        $vars = (object) [];

        $this->evaluator->process($expression, null, $vars);

        $this->assertFalse(
            property_exists($vars->o, 'key')
        );
    }

    public function testObjectHas1(): void
    {
        $expression = "
            object\\has(
                object\\set(object\\create(), 'key', 'value'),
                'key'
            )
        ";

        $this->assertEquals(
            true,
            $this->evaluator->process($expression, null)
        );
    }

    public function testObjectHas2(): void
    {
        $expression = "
            object\\has(
                object\\create(),
                'key'
            )
        ";

        $this->assertEquals(
            false,
            $this->evaluator->process($expression, null)
        );
    }

    public function testObjectClone(): void
    {
        $expression = "
            \$o1 = object\\create();
            \$o2 = object\\cloneDeep(\$o1);
        ";

        $vars = (object) [];

        $this->evaluator->process($expression, null, $vars);

        $this->assertEquals(
            $vars->o1,
            $vars->o2
        );

        $this->assertNotSame(
            $vars->o1,
            $vars->o2
        );
    }

    public function testJsonEncode(): void
    {
        $expression = "
            json\\encode(
                object\\set(object\\create(), 'key', 'value')
            )
        ";

        $this->assertEquals(
            "{\"key\":\"value\"}",
            $this->evaluator->process($expression, null)
        );
    }

    public function testEmpty1(): void
    {
        $expression = " ";

        $this->assertNull($this->evaluator->process($expression, null));
    }

    public function testOnlyComment(): void
    {
        $expression = " // test";

        $this->assertNull($this->evaluator->process($expression, null));
    }

    public function testIfWithComment1(): void
    {
        $expression = "if (true) {\$a = '1';} /* test */";

        $vars = (object) [
            'a' => '0'
        ];

        $this->evaluator->process($expression, null, $vars);

        $this->assertEquals('1', $vars->a);
    }

    public function testIfWithComment2(): void
    {
        $expression = "if (true) {\$a = '1';} ";

        $vars = (object) [
            'a' => '0'
        ];

        $this->evaluator->process($expression, null, $vars);

        $this->assertEquals('1', $vars->a);
    }

    public function testIfWithComment3(): void
    {
        $expression = "
            if (true /*test */) {
                /*
                  test
                */
                \$a = '1';
                /*
                  \$a = '3'
                */
            } // test";

        $vars = (object) [
            'a' => '0',
        ];

        $this->evaluator->process($expression, null, $vars);

        $this->assertEquals('1', $vars->a);
    }

    public function testIfWithComment4(): void
    {
        $expression = "
            if (0)
            {}//
            else if (1) {
                \$a = 1;
            }
            else {}
        ";

        $vars = (object) [
            'a' => '0',
        ];

        $this->evaluator->process($expression, null, $vars);

        $this->assertEquals(1, $vars->a);
    }

    public function testWhileStatement1(): void
    {
        $expression = "
            while (\$i < 5) {
                \$i = \$i + 1;
            }
        ";

        $vars = (object) [
            'i' => 0,
        ];

        $this->evaluator->process($expression, null, $vars);

        $this->assertEquals(5, $vars->i);
    }

    public function testWhileStatement2(): void
    {
        $expression = "
            while (\$i < 5) {
                \$i = \$i + 1;

                if (\$i == 3) {
                    break;
                }
            }
        ";

        $vars = (object) [
            'i' => 0,
        ];

        $this->evaluator->process($expression, null, $vars);

        $this->assertEquals(3, $vars->i);
    }

    public function testWhileStatement3(): void
    {
        $expression = "
            while (\$i < 5) {
                \$i = \$i + 1;

                if (\$i == 3) {
                    continue;
                }

                \$j = \$j + 1;
            }
        ";

        $vars = (object) [
            'i' => 0,
            'j' => 0,
        ];

        $this->evaluator->process($expression, null, $vars);

        $this->assertEquals(4, $vars->j);
    }

    public function testPlus1(): void
    {
        $expression = "+2";

        $value = $this->evaluator->process($expression);

        $this->assertEquals(2, $value);
    }

    public function testPlus2(): void
    {
        $expression = "+ 2";

        $value = $this->evaluator->process($expression);

        $this->assertEquals(2, $value);
    }

    public function testFuncInterface1(): void
    {
        $expression = "string\\contains(string\\concatenate('test', 'hello'), 'hello')";

        $value = $this->evaluator->process($expression);

        $this->assertEquals(true, $value);
    }

    public function testFuncInterface2(): void
    {
        $expression = "true == string\\contains('test hello', 'hello')";

        $value = $this->evaluator->process($expression);

        $this->assertEquals(true, $value);
    }

    public function testFuncInterfaceException(): void
    {
        $expression = "true == string\\contains('test hello')";

        $this->expectException(Error::class);

        $this->evaluator->process($expression);
    }

    public function testNoTrailingSemicolon1(): void
    {
        $expression = "1;2 ";

        $this->evaluator->process($expression);

        $this->assertTrue(true);
    }

    public function testNoTrailingSemicolon2(): void
    {
        $expression = "1;2 ";

        $this->evaluator->process($expression);

        $this->assertTrue(true);
    }

    public function testNoTrailingSemicolon3(): void
    {
        $expression = "1; 2 ";

        $this->evaluator->process($expression);

        $this->assertTrue(true);
    }

    public function testNoTrailingSemicolon4(): void
    {
        $expression = "ifThen(1, \$a = 1; \$a = \$a + 1)";

        $vars = (object) [];

        $this->evaluator->process($expression, null, $vars);

        $this->assertEquals(2, $vars->a);;
    }

    public function testSemicolonAndParentheses1(): void
    {
        $expression = "(2);";

        $this->evaluator->process($expression);

        $this->assertTrue(true);
    }

    public function testSemicolonAndParentheses2(): void
    {
        $expression = "(2 );";

        $this->evaluator->process($expression);

        $this->assertTrue(true);
    }

    public function testSemicolonAndParentheses3(): void
    {
        $expression = "(2);3;";

        $this->evaluator->process($expression);

        $this->assertTrue(true);
    }

    public function testSemicolonAndParentheses4(): void
    {
        $expression = "(2); (2);";

        $this->evaluator->process($expression);

        $this->assertTrue(true);
    }

    public function testSemicolonAndParentheses5(): void
    {
        $expression = "
            \$j = 0;
            \$a = list(0, 1);

            ifThen(
                1,
                \$i = 0;
                while(
                    \$i < array\length(\$a),
                    (
                        \$j = \$j + 1;
                    );
                    \$i = \$i + 1;
                )
            );
        ";

        $vars = (object) [];

        $this->evaluator->process($expression, null, $vars);

        $this->assertEquals(2, $vars->j);
    }

    public function testStrings1(): void
    {
        $expression = '"\\\\"';

        /** @noinspection PhpUnhandledExceptionInspection */
        $result = $this->evaluator->process($expression);

        $this->assertEquals("\\", $result);
    }

    public function testStrings2(): void
    {
        $expression = '"\\""';

        /** @noinspection PhpUnhandledExceptionInspection */
        $result = $this->evaluator->process($expression);

        $this->assertEquals("\"", $result);
    }

    public function testStrings3(): void
    {
        $expression = '"\\n"';

        /** @noinspection PhpUnhandledExceptionInspection */
        $result = $this->evaluator->process($expression);

        $this->assertEquals("\n", $result);
    }

    public function testStrings4(): void
    {
        $expression = '"\\\\n"';

        /** @noinspection PhpUnhandledExceptionInspection */
        $result = $this->evaluator->process($expression);

        $this->assertEquals("\\n", $result);
    }

    public function testStrings5(): void
    {
        $expression = '"test\\\\nest\\\\nest"';

        /** @noinspection PhpUnhandledExceptionInspection */
        $result = $this->evaluator->process($expression);

        $this->assertEquals("test\\nest\\nest", $result);
    }

    public function testStrings6(): void
    {
        $expression = '"\\n\\\\test"';

        /** @noinspection PhpUnhandledExceptionInspection */
        $result = $this->evaluator->process($expression);

        $this->assertEquals("\n\\test", $result);
    }

    public function testStrings7(): void
    {
        $expression = '"\\n\\\\test\\n"';

        /** @noinspection PhpUnhandledExceptionInspection */
        $result = $this->evaluator->process($expression);

        $this->assertEquals("\n\\test\n", $result);
    }

    public function testStrings8(): void
    {
        $expression = '"\\\\\\""';

        /** @noinspection PhpUnhandledExceptionInspection */
        $result = $this->evaluator->process($expression);

        $this->assertEquals("\\\"", $result);
    }

    public function testStrings9(): void
    {
        $expression = '"\\\\\\"test\\\\\\""';

        /** @noinspection PhpUnhandledExceptionInspection */
        $result = $this->evaluator->process($expression);

        $this->assertEquals("\\\"test\\\"", $result);
    }

    public function testStrings10(): void
    {
        $expression = "'test \"hello\\''";

        /** @noinspection PhpUnhandledExceptionInspection */
        $result = $this->evaluator->process($expression);

        $this->assertEquals("test \"hello'", $result);
    }

    public function testBase64(): void
    {
        $expression = "util\\base64Encode('1')";

        /** @noinspection PhpUnhandledExceptionInspection */
        $value = $this->evaluator->process($expression);

        $this->assertEquals(base64_encode('1'), $value);

        $expression = "util\\base64Decode('" . base64_encode('1') . "')";

        /** @noinspection PhpUnhandledExceptionInspection */
        $value = $this->evaluator->process($expression);

        $this->assertEquals('1', $value);
    }

    public function testUnsafe1(): void
    {
        $expression = "util\\base64Encode(test\\unsafe());";

        $this->expectException(UnsafeFunction::class);

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->evaluator->processSafe($expression);
    }

    public function testUnsafe2(): void
    {
        $expression = "test\\unsafe();";

        $this->expectException(UnsafeFunction::class);

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->evaluator->processSafe($expression);
    }

    public function testStringsWithOperator(): void
    {
        $expression = "\$a = '='";

        $vars = (object) [];

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->evaluator->process($expression, null, $vars);

        $this->assertEquals("=", $vars->a);
    }

    public function testAssignAndLogical1(): void
    {
        $expression = "\$a = 'a' == 'a'";

        $vars = (object) [];

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->evaluator->process($expression, null, $vars);

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->evaluator->process($expression);

        $this->assertTrue($vars->a);
    }

    public function testAssignAndLogical2(): void
    {
        $expression = "\$a = 'a' == 'a' && 'b' == 'b'";

        $vars = (object) [];

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->evaluator->process($expression, null, $vars);

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->evaluator->process($expression);

        $this->assertTrue($vars->a);
    }

    public function testMarkdownTransform(): void
    {
        $expression = "ext\\markdown\\transform('**test**')";

        /** @noinspection PhpUnhandledExceptionInspection */
        $result = $this->evaluator->process($expression);

        $this->assertEquals("<p><strong>test</strong></p>\n", $result);
    }

    public function testLastExpressionEvaluation1(): void
    {
        $expression = "\$a = 1; \$b = \$a + 1; \$b;";

        /** @noinspection PhpUnhandledExceptionInspection */
        $result = $this->evaluator->process($expression);

        $this->assertEquals(2, $result);
    }

    public function testLastExpressionEvaluation2(): void
    {
        $expression = "\$a = 1; \$b = \$a + 1; \$b";

        /** @noinspection PhpUnhandledExceptionInspection */
        $result = $this->evaluator->process($expression);

        $this->assertEquals(2, $result);
    }

    public function testLastExpressionEvaluation3(): void
    {
        $expression = "\$a = 1; \$a + 1;";

        /** @noinspection PhpUnhandledExceptionInspection */
        $result = $this->evaluator->process($expression);

        $this->assertEquals(2, $result);
    }

    public function testLastExpressionEvaluation4(): void
    {
        $expression = "\$a = 1; \$b = \$a + 1;";

        /** @noinspection PhpUnhandledExceptionInspection */
        $result = $this->evaluator->process($expression);

        $this->assertEquals(2, $result);
    }

    public function testLastExpressionEvaluation5(): void
    {
        $expression = "\$c = (\$a = 1; \$b = \$a + 1;); \$c;";

        /** @noinspection PhpUnhandledExceptionInspection */
        $result = $this->evaluator->process($expression);

        $this->assertEquals(2, $result);
    }

    public function testLastExpressionEvaluationNull1(): void
    {
        $expression = "null";

        /** @noinspection PhpUnhandledExceptionInspection */
        $result = $this->evaluator->process($expression);

        $this->assertEquals(null, $result);
    }

    public function testLastExpressionEvaluationNull2(): void
    {
        $expression = "";

        /** @noinspection PhpUnhandledExceptionInspection */
        $result = $this->evaluator->process($expression);

        $this->assertEquals(null, $result);
    }
}
