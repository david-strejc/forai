<?php
//FORAI:F2678;DEF[C2275:SelectBuilderTest,F11092:setUp,F11093:testFrom,F11094:testSelect1,F11095:testSelect2,F11096:testSelect3,F11097:testSelect4,F11098:testSelect5,F11099:testSelect6,F11100:testSelect7,F11101:testCloneNotSame,F11102:testWhereNull1,F11103:testWhereNull2,F11104:testGroupBy1,F11105:testGroupBy2,F11106:testGroupBy3,F11107:testHaving1,F11108:testOrder1,F11109:testOrder2,F11110:testOrder3,F11111:testOrder4,F11112:testOrder5,F11113:testOrder6,F11114:testClone,F11115:testCloneException,F11116:testWhereSameKeys1,F11117:testWhereSameKeys2,F11118:testLeftJoin1,F11119:testLeftJoin2,F11120:testLeftJoin3,F11121:testJoin1,F11122:testJoin2,F11123:testJoin3,F11124:testWhereItemUsage1,F11125:testExists1];IMP[];EXP[C2275,F11092,F11093,F11094,F11095,F11096,F11097,F11098,F11099,F11100,F11101,F11102,F11103,F11104,F11105,F11106,F11107,F11108,F11109,F11110,F11111,F11112,F11113,F11114,F11115,F11116,F11117,F11118,F11119,F11120,F11121,F11122,F11123,F11124,F11125];LANG[php]//

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

namespace tests\unit\Espo\ORM\Query;

use Espo\ORM\{
    Query\SelectBuilder,
    Query\Part\Condition as Cond,
    Query\Part\Expression as Expr,
    Query\Part\Selection,
    Query\Part\Order,
    Query\Part\Join,
    Query\Part\WhereClause,
};

class SelectBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var SelectBuilder
     */
    private $builder;

    protected function setUp(): void
    {
        $this->builder = new SelectBuilder();
    }

    public function testFrom()
    {
        $params = $this->builder
            ->from('Test')
            ->build()
            ->getRaw();

        $this->assertEquals('Test', $params['from']);
    }

    public function testSelect1()
    {
        $select = $this->builder
            ->from('Test')
            ->select(['id', 'name'])
            ->select('test')
            ->build();

        $this->assertEquals(
            [
                Selection::fromString('id'),
                Selection::fromString('name'),
                Selection::fromString('test'),
            ],
            $select->getSelect()
        );
    }

    public function testSelect2()
    {
        $select = $this->builder
            ->from('Test')
            ->select('test')
            ->select(['id', 'name'])
            ->build();

        $this->assertEquals(
            [
                Selection::fromString('id'),
                Selection::fromString('name'),
            ],
            $select->getSelect()
        );
    }

    public function testSelect3()
    {
        $select = $this->builder
            ->from('Test')
            ->select('test', 'hello')
            ->build();

        $this->assertEquals(
            [
                Selection::fromString('test')->withAlias('hello'),
            ],
            $select->getSelect()
        );
    }

    public function testSelect4()
    {
        $select = $this->builder
            ->from('Test')
            ->select(Expr::create('test'), 'hello')
            ->build();

        $this->assertEquals(
            [
                Selection::fromString('test')->withAlias('hello'),
            ],
            $select->getSelect()
        );
    }

    public function testSelect5()
    {
        $select = $this->builder
            ->from('Test')
            ->select('test')
            ->select([Expr::create('id'), Expr::create('name')])
            ->build();

        $this->assertEquals(
            [
                Selection::fromString('id'),
                Selection::fromString('name'),
            ],
            $select->getSelect()
        );
    }

    public function testSelect6()
    {
        $select = $this->builder
            ->from('Test')
            ->select('test')
            ->select([
                [Expr::create('id'), 'id'],
                [Expr::create('name'), 'name'],
            ])
            ->build();

        $this->assertEquals(
            [
                Selection::fromString('id')->withAlias('id'),
                Selection::fromString('name')->withAlias('name'),
            ],
            $select->getSelect()
        );
    }

    public function testSelect7()
    {
        $select = $this->builder
            ->from('Test')
            ->select([
                'id',
                Selection::create(Expr::create('name'))
            ])
            ->select(Selection::fromString('test')->withAlias('testAlias'))
            ->build();

        $this->assertEquals(
            [
                Selection::fromString('id'),
                Selection::fromString('name'),
                Selection::fromString('test')->withAlias('testAlias'),
            ],
            $select->getSelect()
        );
    }

    public function testCloneNotSame()
    {
        $builder = new SelectBuilder();

        $select = $builder
            ->from('Test')
            ->build();

        $builder = new SelectBuilder();

        $selectCloned = $builder
            ->clone($select)
            ->build();

        $this->assertNotSame($selectCloned, $select);
    }

    public function testWhereNull1()
    {
        $select = $this->builder
            ->from('Test')
            ->where(['test' => null])
            ->build();

        $raw = $select->getRaw();

        $this->assertEquals(['test' => null], $raw['whereClause']);
    }

    public function testWhereNull2()
    {
        $select = $this->builder
            ->from('Test')
            ->where('test', null)
            ->build();

        $raw = $select->getRaw();

        $this->assertEquals(['test' => null], $raw['whereClause']);
    }

    public function testGroupBy1()
    {
        $select = $this->builder
            ->from('Test')
            ->having(['test' => null])
            ->group(['test'])
            ->build();

        $raw = $select->getRaw();

        $this->assertEquals(['test' => null], $raw['havingClause']);

        $this->assertEquals(['test'], $raw['groupBy']);
    }

    public function testGroupBy2()
    {
        $select = $this->builder
            ->from('Test')
            ->having(Cond::equal(Expr::column('test'), null))
            ->group(Expr::create('test'))
            ->build();

        $raw = $select->getRaw();

        $this->assertEquals(['test'], $raw['groupBy']);
    }

    public function testGroupBy3()
    {
        $select = $this->builder
            ->from('Test')
            ->group([
                Expr::create('test1'),
                Expr::create('test2'),
            ])
            ->build();


        $this->assertEquals(
            [
                Expr::create('test1'),
                Expr::create('test2'),
            ],
            $select->getGroup()
        );
    }

    public function testHaving1()
    {
        $select = $this->builder
            ->from('Test')
            ->having(Cond::equal(Expr::column('test'), null))
            ->group(Expr::create('test'))
            ->build();


        $this->assertEquals(
            Cond::equal(Expr::column('test'), null)->getRaw(),
            $select->getHaving()->getRaw()
        );
    }

    public function testOrder1()
    {
        $select = $this->builder
            ->from('Test')
            ->order(Expr::create('test'))
            ->build();

        $raw = $select->getRaw();

        $this->assertEquals([['test', 'ASC']], $raw['orderBy']);
    }

    public function testOrder2()
    {
        $select = $this->builder
            ->from('Test')
            ->order(Expr::create('test'))
            ->build();

        $raw = $select->getRaw();

        $this->assertEquals([['test', 'ASC']], $raw['orderBy']);
    }

    public function testOrder3()
    {
        $select = $this->builder
            ->from('Test')
            ->order([Expr::create('test')], 'DESC')
            ->build();

        $raw = $select->getRaw();

        $this->assertEquals([['test', 'DESC']], $raw['orderBy']);
    }

    public function testOrder4()
    {
        $select = $this->builder
            ->from('Test')
            ->order([[Expr::create('test'), 'DESC']], 'ASC')
            ->build();

        $raw = $select->getRaw();

        $this->assertEquals([['test', 'DESC']], $raw['orderBy']);
    }

    public function testOrder5()
    {
        $select = $this->builder
            ->from('Test')
            ->order(Order::fromString('test')->withDesc())
            ->order('hello', true)
            ->build();

        $this->assertEquals(
            [
                Order::fromString('test')->withDesc(),
                Order::fromString('hello')->withDesc(),
            ],
            $select->getOrder()
        );
    }

    public function testOrder6()
    {
        $select = $this->builder
            ->from('Test')
            ->order([
                Order::fromString('test')->withDesc(),
                ['hello', true],
            ])
            ->build();

        $this->assertEquals(
            [
                Order::fromString('test')->withDesc(),
                Order::fromString('hello')->withDesc(),
            ],
            $select->getOrder()
        );
    }

    public function testClone()
    {
        $builder = new SelectBuilder();

        $select = $builder
            ->from('Test')
            ->where('test1', '1')
            ->build();

        $builder = new SelectBuilder();

        $selectCloned = $builder
            ->clone($select)
            ->distinct()
            ->where('test2', '2')
            ->build();

        $params = $select->getRaw();
        $paramsCloned = $selectCloned->getRaw();

        $this->assertTrue($paramsCloned['distinct']);
        $this->assertFalse($params['distinct'] ?? false);

        $this->assertEquals(['test1' =>'1'], $params['whereClause']);
        $this->assertEquals(['test1' => '1', 'test2' => '2'], $paramsCloned['whereClause']);
    }

    public function testCloneException()
    {
        $builder = new SelectBuilder();

        $select = $builder
            ->from('Test')
            ->where('test1', '1')
            ->build();

        $builder = new SelectBuilder();

        $this->expectException(\RuntimeException::class);

        $builder
            ->from('Test')
            ->clone($select);
    }

    public function testWhereSameKeys1()
    {
        $builder = new SelectBuilder();

        $select = $builder
            ->from('Test')
            ->where(['test' => '1'])
            ->where(['test' => '2'])
            ->build();

        $raw = $select->getRaw();

        $expected = [
            'test' => '1',
            ['test' => '2'],
        ];

        $this->assertEquals($expected, $raw['whereClause']);
    }

    public function testWhereSameKeys2()
    {
        $builder = new SelectBuilder();

        $select = $builder
            ->from('Test')
            ->where([
                'OR' => [
                    'test' => '1'
                ],
            ])
            ->where([
                'OR' => [
                    'test' => '2'
                ],
            ])
            ->build();

        $raw = $select->getRaw();

        $expected = [
            'OR' => [
                'test' => '1'
            ],
            [
                'OR' => [
                    'test' => '2'
                ],
            ]
        ];

        $this->assertEquals($expected, $raw['whereClause']);
    }

    public function testLeftJoin1()
    {
        $params = $this->builder
            ->from('Test')
            ->leftJoin('link1')
            ->leftJoin('link1')
            ->leftJoin('link2')
            ->build()
            ->getRaw();

        $this->assertEquals(['link1', 'link2'], $params['leftJoins']);
    }

    public function testLeftJoin2()
    {
        $query = $this->builder
            ->from('Test')
            ->leftJoin('link1', 'alias1', ['name' => 'test'])
            ->leftJoin('link2', 'alias2')
            ->build();

        $this->assertEquals(
            [
                Join::create('link1', 'alias1')
                    ->withConditions(WhereClause::fromRaw(['name' => 'test'])),
                Join::create('link2', 'alias2'),
            ],
            $query->getLeftJoins()
        );
    }

    public function testLeftJoin3()
    {
        $query = $this->builder
            ->from('Test')
            ->leftJoin(
                Join::create('link1', 'alias1')
                    ->withConditions(
                        WhereClause::fromRaw(['name' => 'test'])
                    )
            )
            ->leftJoin(
                Join::create('link2', 'alias2')
            )
            ->build();

        $this->assertEquals(
            [
                Join::create('link1', 'alias1')
                    ->withConditions(WhereClause::fromRaw(['name' => 'test'])),
                Join::create('link2', 'alias2'),
            ],
            $query->getLeftJoins()
        );
    }

    public function testJoin1()
    {
        $params = $this->builder
            ->from('Test')
            ->join('link1')
            ->join('link1')
            ->join('link2')
            ->build()
            ->getRaw();

        $this->assertEquals(['link1', 'link2'], $params['joins']);
    }

    public function testJoin2()
    {
        $query = $this->builder
            ->from('Test')
            ->join('link1', 'alias1', ['name' => 'test'])
            ->join('link2', 'alias2')
            ->build();

        $this->assertEquals(
            [
                Join::create('link1', 'alias1')
                    ->withConditions(WhereClause::fromRaw(['name' => 'test'])),
                Join::create('link2', 'alias2'),
            ],
            $query->getJoins()
        );
    }

    public function testJoin3()
    {
        $query = $this->builder
            ->from('Test')
            ->join(
                Join::create('link1', 'alias1')
                    ->withConditions(WhereClause::fromRaw(['name' => 'test']))
            )
            ->join(Join::create('link2', 'alias2'))
            ->build();

        $this->assertEquals(
            [
                Join::create('link1', 'alias1')
                    ->withConditions(WhereClause::fromRaw(['name' => 'test'])),
                Join::create('link2', 'alias2'),
            ],
            $query->getJoins()
        );
    }

    public function testWhereItemUsage1()
    {
        $query = $this->builder
            ->from('Test')
            ->where(
                Cond::or(
                    Cond::equal(Expr::column('test'), '1'),
                    Cond::equal(Expr::column('test'), '2')
                )
            )
            ->join(
                'Table1',
                'table1',
                Cond::equal(Expr::column('table1.testId'), Expr::column('id'))
            )
            ->leftJoin(
                'Table2',
                'table2',
                Cond::equal(Expr::column('table2.testId'), Expr::column('id'))
            )
            ->build();

        $raw = $query->getRaw();

        $expectedWhere = [
            'OR' => [
                ['test=' => '1'],
                ['test=' => '2'],
            ]
        ];

        $expectedJoins = [
            [
                'Table1',
                'table1',
                [
                    'table1.testId=:' => 'id'
                ],
                ['noLeftAlias' => true]
            ]
        ];

        $expectedLeftJoins = [
            [
                'Table2',
                'table2',
                [
                    'table2.testId=:' => 'id'
                ],
                ['noLeftAlias' => true]
            ]
        ];

        $this->assertEquals($expectedWhere, $raw['whereClause']);
        $this->assertEquals($expectedJoins, $raw['joins']);
        $this->assertEquals($expectedLeftJoins, $raw['leftJoins']);
    }

    public function testExists1(): void
    {
        $query = (new SelectBuilder())
            ->select('id')
            ->from('Test', 'test')
            ->where(
                Cond::exists(
                    (new SelectBuilder())
                        ->select('id')
                        ->from('Test', 'sq')
                        ->where(
                            Cond::equal(
                                Cond::column('test.id'),
                                Cond::column('sq.id')
                            )
                        )
                        ->build()
                )
            )
            ->build();

        $expected = [
            'select' => ['id'],
            'from' => 'Test',
            'fromAlias' => 'test',
            'whereClause' => [
                'EXISTS' => (new SelectBuilder())
                    ->select('id')
                    ->from('Test', 'sq')
                    ->where(
                        Cond::equal(
                            Cond::column('test.id'),
                            Cond::column('sq.id')
                        )
                    )
                    ->build()
            ],
        ];

        $this->assertEquals($expected, $query->getRaw());
    }
}
