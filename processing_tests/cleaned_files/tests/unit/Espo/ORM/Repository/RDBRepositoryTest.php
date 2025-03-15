<?php
//FORAI:F2677;DEF[C2274:RDBRepositoryTest<TestCase>,F11021:setUp,F11022:createCollectionMock,F11023:createRepository,F11024:createEntity,F11025:testFind,F11026:testFindOne1,F11027:testFindOne2,F11028:testFindOne3,F11029:testFindOneWithDeletedLegacy,F11030:testCount1,F11031:testCount2,F11032:testCount3,F11033:testMax1,F11034:testWhere1,F11035:testWhere2,F11036:testWhere3,F11037:testWhereFineOne,F11038:testJoin1,F11039:testJoin2,F11040:testJoin3,F11041:testJoin4,F11042:testJoin5,F11043:testLeftJoin1,F11044:testLeftJoin2,F11045:testMultipleLeftJoins,F11046:testDistinct,F11047:testForUpdate,F11048:testSth,F11049:testOrder1,F11050:testOrder2,F11051:testOrder3,F11052:testOrder4,F11053:testOrder5,F11054:testGroupBy1,F11055:testGroupBy2,F11056:testGroupBy3,F11057:testGroupBy4,F11058:testGroupBy5,F11059:testSelect1,F11060:testSelect2,F11061:testSelect3,F11062:testSelect4,F11063:testClone1,F11064:testGetById1,F11065:testRelationInstance,F11066:testRelationCloneInstance,F11067:testRelationCloneBelongsToParentException,F11068:testRelationCount,F11069:testRelationFindHasMany,F11070:testRelationFindBelongsTo,F11071:testRelationFindBelongsToParent,F11072:testRelationFindOneBelongsToParent,F11073:testRelationIsRelated1,F11074:testRelationIsRelated2,F11075:testRelationIsRelated3,F11076:testRelate1,F11077:testUnrelate1,F11078:testRelateById1,F11079:testUnrelateById1,F11080:testMassRelate,F11081:testGetColumn,F11082:testUpdateColumns,F11083:testRelationSelectBuilderFind1,F11084:testRelationSelectBuilderFind2,F11085:testRelationSelectBuilderFindOne,F11086:testRelationSelectBuilderColumnsWhere1,F11087:testRelationSelectBuilderColumnsWhere2,F11088:testRelationSelectBuilderRelationWhere1,F11089:testRelationSelectBuilderRelationWhere2,F11090:testRelationSelectBuilderRelationWhere3,F11091:testRelationSelectBuilderRelationWhere4];IMP[unknown:*,unknown:*];EXP[C2274,F11021,F11022,F11023,F11024,F11025,F11026,F11027,F11028,F11029,F11030,F11031,F11032,F11033,F11034,F11035,F11036,F11037,F11038,F11039,F11040,F11041,F11042,F11043,F11044,F11045,F11046,F11047,F11048,F11049,F11050,F11051,F11052,F11053,F11054,F11055,F11056,F11057,F11058,F11059,F11060,F11061,F11062,F11063,F11064,F11065,F11066,F11067,F11068,F11069,F11070,F11071,F11072,F11073,F11074,F11075,F11076,F11077,F11078,F11079,F11080,F11081,F11082,F11083,F11084,F11085,F11086,F11087,F11088,F11089,F11090,F11091];LANG[php]//

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

namespace tests\unit\Espo\ORM\Repository;

require_once 'tests/unit/testData/DB/Entities.php';

use Espo\ORM\CollectionFactory;
use Espo\ORM\Entity;
use Espo\ORM\EntityFactory;
use Espo\ORM\EntityManager;
use Espo\ORM\Mapper\BaseMapper;
use Espo\ORM\Metadata;
use Espo\ORM\MetadataDataProvider;
use Espo\ORM\Query\Part\Condition as Cond;
use Espo\ORM\Query\Part\Expression as Expr;
use Espo\ORM\Query\Part\Order as OrderExpr;
use Espo\ORM\Query\Select;
use Espo\ORM\QueryBuilder;
use Espo\ORM\Repository\RDBRelation;
use Espo\ORM\Repository\RDBRelationSelectBuilder;
use Espo\ORM\Repository\RDBRepository as Repository;
use Espo\ORM\SthCollection;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use tests\unit\testData\Entities\Test;
use tests\unit\testData\DB as Entities;

class RDBRepositoryTest extends TestCase
{
    /**
     * @var Repository
     */
    private $repository;

    protected function setUp(): void
    {
        $entityManager = $this->entityManager =
            $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();

        $entityFactory = $this->entityFactory =

        $this->getMockBuilder(EntityFactory::class)->disableOriginalConstructor()->getMock();

        $this->collectionFactory = new CollectionFactory($this->entityManager);

        $this->mapper = $this->getMockBuilder(BaseMapper::class)->disableOriginalConstructor()->getMock();

        $entityManager
            ->method('getMapper')
            ->will($this->returnValue($this->mapper));

        $this->queryBuilder = new QueryBuilder();

        $entityManager
            ->method('getQueryBuilder')
            ->will($this->returnValue($this->queryBuilder));

        $entityManager
            ->method('getQueryBuilder')
            ->will($this->returnValue($this->queryBuilder));

        $entityManager
            ->method('getCollectionFactory')
            ->will($this->returnValue($this->collectionFactory));

        $entityManager
            ->method('getEntityFactory')
            ->will($this->returnValue($this->entityFactory));

        $ormMetadata = include('tests/unit/testData/DB/ormMetadata.php');

        $metadataDataProvider = $this->createMock(MetadataDataProvider::class);

        $metadataDataProvider
            ->expects($this->any())
            ->method('get')
            ->willReturn($ormMetadata);

        $this->metadata = new Metadata($metadataDataProvider);

        $this->seed = $this->createEntity('Test', Test::class);

        $this->account = $this->createEntity('Account', Entities\Account::class);

        $this->team = $this->createEntity('Team', Entities\Team::class);

        $this->collection = $this->createCollectionMock();

        $entityFactory
            ->method('create')
            ->will(
                $this->returnCallback(
                    function (string $entityType) {
                        $className = 'tests\\unit\\testData\\DB\\' . ucfirst($entityType);

                        return $this->createEntity($entityType, $className);
                    }
                )
            );

        $this->repository = $this->createRepository('Test');
    }

    protected function createCollectionMock(?array $itemList = null) : SthCollection
    {
        $collection = $this->getMockBuilder(SthCollection::class)->disableOriginalConstructor()->getMock();

        $itemList = $itemList ?? [];

        $generator = (function () use ($itemList) {
            foreach ($itemList as $item) {
                yield $item;
            }
        })();

        $collection
            ->expects($this->any())
            ->method('getIterator')
            ->will(
                $this->returnValue($generator)
            );

        return $collection;
    }

    protected function createRepository(string $entityType)
    {
        $repository = new Repository($entityType, $this->entityManager, $this->entityFactory);

        $this->entityManager
            ->method('getRepository')
            ->will($this->returnValue($repository));

        return $repository;
    }

    protected function createEntity(string $entityType, string $className)
    {
        $defs = $this->metadata->get($entityType);

        return new $className($entityType, $defs, $this->entityManager);
    }

    /**
     * @deprecated
     */
    public function testFind(): void
    {
        $paramsExpected = Select::fromRaw([
            'from' => 'Test',
            'whereClause' => [
                'name' => 'test',
            ],
        ]);

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($paramsExpected);

        $this->repository
            ->where([
                'name' => 'test',
            ])
            ->find();
    }

    public function testFindOne1(): void
    {
        $paramsExpected = Select::fromRaw([
            'from' => 'Test',
            'whereClause' => [
                'name' => 'test',
            ],
            'offset' => 0,
            'limit' => 1,
        ]);

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($paramsExpected);

        $this->repository
            ->where([
                'name' => 'test',
            ])
            ->findOne();
    }

    public function testFindOne2()
    {
        $select = $this->queryBuilder
            ->select()
            ->from('Test')
            ->where(['name' => 'test'])
            ->limit(0, 1)
            ->build();

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($select);

        $this->repository->where(['name' => 'test'])->findOne();
    }

    public function testFindOne3()
    {
        $select = $this->queryBuilder
            ->select()
            ->distinct()
            ->from('Test')
            ->where(['name' => 'test'])
            ->limit(0, 1)
            ->build();

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($select);

        $this->repository
            ->distinct()
            ->where(['name' => 'test'])
            ->findOne();
    }

    public function testFindOneWithDeletedLegacy(): void
    {
        $paramsExpected = Select::fromRaw([
            'from' => 'Test',
            'whereClause' => [
                'name' => 'test',
            ],
            'withDeleted' => true,
            'offset' => 0,
            'limit' => 1,
        ]);

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($paramsExpected);

        $this->repository
            ->where(['name' => 'test'])
            ->findOne(['withDeleted' => true]);
    }

    /**
     * @deprecated
     */
    public function testCount1()
    {
        $select = $this->queryBuilder
            ->select()
            ->from('Test')
            ->where(['name' => 'test'])
            ->build();

        $this->mapper
            ->expects($this->once())
            ->method('count')
            ->will($this->returnValue(1))
            ->with($select);

        $this->repository
            ->where(['name' => 'test'])
            ->count();
    }

    public function testCount2()
    {
        $select = $this->queryBuilder
            ->select()
            ->from('Test')
            ->where(['name' => 'test'])
            ->build();

        $this->mapper
            ->expects($this->once())
            ->method('count')
            ->will($this->returnValue(1))
            ->with($select);

        $this->repository->where(['name' => 'test'])->count();
    }

    public function testCount3()
    {
        $select = $this->queryBuilder
            ->select()
            ->from('Test')
            ->build();

        $this->mapper
            ->expects($this->once())
            ->method('count')
            ->will($this->returnValue(1))
            ->with($select);

        $this->repository->count();
    }

    public function testMax1()
    {
        $select = $this->queryBuilder
            ->select()
            ->from('Test')
            ->build();

        $this->mapper
            ->expects($this->once())
            ->method('max')
            ->will($this->returnValue(1))
            ->with($select, 'test');

        $this->repository->max('test');
    }

    public function testWhere1()
    {
        $paramsExpected = Select::fromRaw([
            'from' => 'Test',
            'whereClause' => [
                'name' => 'test',
            ],
        ]);

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($paramsExpected);

        $this->repository->where(['name' => 'test'])->find();
    }

    public function testWhere2()
    {
        $paramsExpected = Select::fromRaw([
            'from' => 'Test',
            'whereClause' => [
                'name' => 'test',
            ],
        ]);

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($paramsExpected);

        $this->repository->where('name', 'test')->find();
    }

    public function testWhere3()
    {
        $paramsExpected = Select::fromRaw([
            'from' => 'Test',
            'whereClause' => [
                'name=' => 'test',
            ],
        ]);

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($paramsExpected);

        $this->repository
            ->where(Cond::equal(Expr::column('name'), 'test'))
            ->find();
    }

    public function testWhereFineOne()
    {
        $paramsExpected = Select::fromRaw([
            'from' => 'Test',
            'whereClause' => [
                'name' => 'test',
            ],
            'offset' => 0,
            'limit' => 1,
        ]);

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($paramsExpected);

        $this->repository->where('name', 'test')->findOne();
    }

    public function testJoin1()
    {
        $paramsExpected = Select::fromRaw([
            'from' => 'Test',
            'joins' => [
                'Test',
            ],
        ]);

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($paramsExpected);

        $this->repository->join('Test')->find();
    }

    public function testJoin2()
    {
        $paramsExpected = Select::fromRaw([
            'from' => 'Test',
            'joins' => [
                'Test1',
                'Test2',
            ],
        ]);

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($paramsExpected);

        $this->repository->join(['Test1', 'Test2'])->find();
    }

    public function testJoin3()
    {
        $paramsExpected = Select::fromRaw([
            'from' => 'Test',
            'joins' => [
                ['Test1', 'test1'],
                ['Test2', 'test2'],
            ],
        ]);

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($paramsExpected);

        $this->repository->join([['Test1', 'test1'], ['Test2', 'test2']])->find();
    }

    public function testJoin4()
    {
        $paramsExpected = Select::fromRaw([
            'from' => 'Test',
            'joins' => [
                ['Test1', 'test1', ['k' => 'v']],
            ],
        ]);

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($paramsExpected);

        $this->repository->join('Test1', 'test1', ['k' => 'v'])->find();
    }

    public function testJoin5()
    {
        $paramsExpected = $this->queryBuilder
            ->select()
            ->from('Test')
            ->join('Test1', 'test1', Cond::equal(Expr::column('k'), 'v'))
            ->build();

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($paramsExpected);

        $this->repository
            ->join('Test1', 'test1', Cond::equal(Expr::column('k'), 'v'))
            ->find();
    }

    public function testLeftJoin1()
    {
        $paramsExpected = Select::fromRaw([
            'from' => 'Test',
            'leftJoins' => [
                'Test',
            ],
        ]);

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($paramsExpected);

        $this->repository->leftJoin('Test')->find();
    }

    public function testLeftJoin2()
    {
        $paramsExpected = $this->queryBuilder
            ->select()
            ->from('Test')
            ->leftJoin('Test1', 'test1', Cond::equal(Expr::column('k'), 'v'))
            ->build();

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($paramsExpected);

        $this->repository
            ->leftJoin('Test1', 'test1', Cond::equal(Expr::column('k'), 'v'))
            ->find();
    }

    public function testMultipleLeftJoins()
    {
        $paramsExpected = Select::fromRaw([
            'from' => 'Test',
            'leftJoins' => [
                'Test1',
                ['Test2', 'test2'],
            ],
        ]);

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($paramsExpected);

        $this->repository->leftJoin('Test1')->leftJoin('Test2', 'test2')->find();
    }

    public function testDistinct()
    {
        $paramsExpected = Select::fromRaw([
            'from' => 'Test',
            'distinct' => true,
        ]);

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($paramsExpected);

        $this->repository->distinct()->find();
    }

    public function testForUpdate()
    {
        $paramsExpected = Select::fromRaw([
            'from' => 'Test',
            'forUpdate' => true,
        ]);

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($paramsExpected);

        $this->repository->forUpdate()->find();
    }

    public function testSth()
    {
        $paramsExpected = Select::fromRaw([
            'from' => 'Test',
            //'returnSthCollection' => true,
        ]);

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($paramsExpected);

        $this->repository->sth()->find();
    }

    public function testOrder1()
    {
        $paramsExpected = Select::fromRaw([
            'from' => 'Test',
            'orderBy' => [['name', 'ASC']],
        ]);

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($paramsExpected);

        $this->repository
            ->order('name')
            ->find();
    }

    public function testOrder2()
    {
        $paramsExpected = Select::fromRaw([
            'from' => 'Test',
            'orderBy' => [['name', 'ASC']],
        ]);

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($paramsExpected);

        $this->repository
            ->order(Expr::create('name'))
            ->find();
    }

    public function testOrder3()
    {
        $paramsExpected = Select::fromRaw([
            'from' => 'Test',
            'orderBy' => [['name', 'ASC']],
        ]);

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($paramsExpected);

        $this->repository
            ->order([
                [Expr::create('name'), 'ASC']
            ])
            ->find();
    }

    public function testOrder4()
    {
        $paramsExpected = Select::fromRaw([
            'from' => 'Test',
            'orderBy' => [
                ['name', 'ASC'],
                ['hello', 'DESC'],
            ],
        ]);

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($paramsExpected);

        $this->repository
            ->order([
                OrderExpr::fromString('name'),
                OrderExpr::fromString('hello')->withDesc(),
            ])
            ->find();
    }

    public function testOrder5()
    {
        $paramsExpected = Select::fromRaw([
            'from' => 'Test',
            'orderBy' => [
                ['name', 'ASC'],
                ['hello', 'DESC'],
            ],
        ]);

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($paramsExpected);

        $this->repository
            ->order(OrderExpr::fromString('name'))
            ->order(OrderExpr::fromString('hello')->withDesc())
            ->find();
    }

    public function testGroupBy1()
    {
        $paramsExpected = Select::fromRaw([
            'from' => 'Test',
            'groupBy' => ['id'],
        ]);

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($paramsExpected);

        $this->repository->group('id')->find();
    }

    public function testGroupBy2()
    {
        $paramsExpected = Select::fromRaw([
            'from' => 'Test',
            'groupBy' => ['id', 'name'],
        ]);

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($paramsExpected);

        $this->repository
            ->group('id')
            ->group('name')
            ->find();
    }

    public function testGroupBy3()
    {
        $paramsExpected = Select::fromRaw([
            'from' => 'Test',
            'groupBy' => ['id', 'name'],
        ]);

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($paramsExpected);

        $this->repository
            ->group('id')
            ->group(['id', 'name'])
            ->find();
    }

    public function testGroupBy4()
    {
        $paramsExpected = Select::fromRaw([
            'from' => 'Test',
            'groupBy' => ['id', 'name'],
        ]);

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($paramsExpected);

        $this->repository
            ->group(Expr::create('id'))
            ->group(Expr::create('name'))
            ->find();
    }

    public function testGroupBy5()
    {
        $paramsExpected = Select::fromRaw([
            'from' => 'Test',
            'groupBy' => ['id', 'name'],
        ]);

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($paramsExpected);

        $this->repository
            ->group([Expr::create('id'), Expr::create('name')])
            ->find();
    }

    public function testSelect1()
    {
        $paramsExpected = Select::fromRaw([
            'from' => 'Test',
            'select' => ['name', 'date'],
        ]);

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($paramsExpected);

        $this->repository->select(['name', 'date'])->find();
    }

    public function testSelect2()
    {
        $select = $this->queryBuilder
            ->select()
            ->from('Test')
            ->select(['name'])
            ->select('date')
            ->build();

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($select);

        $this->repository
            ->select(['name'])
            ->select('date')
            ->find();
    }

    public function testSelect3()
    {
        $select = $this->queryBuilder
            ->select(Expr::create('name'))
            ->from('Test')
            ->build();

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($select);

        $this->repository
            ->select(['name'])
            ->find();
    }

    public function testSelect4()
    {
        $select = $this->queryBuilder
            ->select([
                'name1',
                ['name2', 'alias'],
                ['name3'],
            ])
            ->from('Test')
            ->build();

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->collection))
            ->with($select);

        $this->repository
            ->select([
                Expr::create('name1'),
                [Expr::create('name2'), 'alias'],
                [Expr::create('name3')],
            ])
            ->find();
    }

    public function testClone1()
    {
        $select = $this->queryBuilder
            ->select()
            ->from('Test')
            ->build();

        $selectExpected = $this->queryBuilder
            ->select()
            ->from('Test')
            ->select('id')
            ->build();

        $collection = $this->createCollectionMock();

        $this->mapper
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($collection))
            ->with($selectExpected);

        $this->repository
            ->clone($select)
            ->select('id')
            ->find();
    }

    public function testGetById1()
    {
        $select = $this->queryBuilder
            ->select()
            ->from('Test')
            ->where(['id' => '1'])
            ->build();

        $entity = $this->getMockBuilder(Entity::class)->getMock();

        $this->mapper
            ->expects($this->once())
            ->method('selectOne')
            ->will($this->returnValue($entity))
            ->with($select);

        $this->repository->getById('1');
    }

    public function testRelationInstance()
    {
        $repository = $this->createRepository('Account');

        $account = $this->entityFactory->create('Account');
        $account->id = 'accountId';

        $relation = $repository->getRelation($account, 'teams');

        $this->assertInstanceOf(RDBRelation::class, $relation);
    }

    public function testRelationCloneInstance()
    {
        $repository = $this->createRepository('Account');

        $account = $this->entityFactory->create('Account');
        $account->id = 'accountId';

        $select = $this->queryBuilder
            ->select()
            ->from('Team')
            ->build();

        $relationSelectBuilder = $repository->getRelation($account, 'teams')->clone($select);

        $this->assertInstanceOf(RDBRelationSelectBuilder::class, $relationSelectBuilder);
    }

    public function testRelationCloneBelongsToParentException()
    {
        $repository = $this->createRepository('Note');

        $note = $this->entityFactory->create('Note');
        $note->id = 'noteId';

        $select = $this->queryBuilder
            ->select()
            ->from('Post')
            ->build();

        $this->expectException(RuntimeException::class);

        $repository->getRelation($note, 'parent')->clone($select);
    }

    public function testRelationCount()
    {
        $post = $this->entityFactory->create('Post');
        $post->id = 'postId';

        $select = $this->queryBuilder
            ->select()
            ->from('Comment')
            ->build();

        $this->mapper
            ->expects($this->once())
            ->method('countRelated')
            ->will($this->returnValue(1))
            ->with($post, 'comments', $select);

        $this->createRepository('Post')->getRelation($post, 'comments')->count();
    }

    public function testRelationFindHasMany()
    {
        $repository = $this->createRepository('Post');

        $post = $this->entityFactory->create('Post');
        $post->id = 'postId';

        $collection = $this->createCollectionMock();

        $select = $this->queryBuilder
            ->select()
            ->from('Comment')
            ->build();

        $this->mapper
            ->expects($this->once())
            ->method('selectRelated')
            ->will($this->returnValue($collection))
            ->with($post, 'comments', $select);

        $repository->getRelation($post, 'comments')->find();
    }

    public function testRelationFindBelongsTo()
    {
        $comment = $this->entityFactory->create('Comment');
        $comment->id = 'commentId';

        $post = $this->entityFactory->create('Post');
        $post->id = 'postId';

        $select = $this->queryBuilder
            ->select()
            ->from('Post')
            ->build();

        $this->mapper
            ->expects($this->once())
            ->method('selectRelated')
            ->will($this->returnValue($post))
            ->with($comment, 'post', $select);

        $result = $this->createRepository('Comment')
            ->getRelation($comment, 'post')
            ->find();

        $this->assertEquals(1, count($result));

        $this->assertEquals($post, $result[0]);
    }

    public function testRelationFindBelongsToParent()
    {
        $note = $this->entityFactory->create('Note');
        $note->id = 'noteId';

        $post = $this->entityFactory->create('Post');
        $post->id = 'noteId';

        $this->mapper
            ->expects($this->once())
            ->method('selectRelated')
            ->will($this->returnValue($post))
            ->with($note, 'parent');

        $result = $this->createRepository('Note')->getRelation($note, 'parent')->find();

        $this->assertEquals(1, count($result));

        $this->assertEquals($post, $result[0]);
    }

    public function testRelationFindOneBelongsToParent()
    {
        $note = $this->entityFactory->create('Note');
        $note->id = 'noteId';

        $post = $this->entityFactory->create('Post');
        $post->id = 'postId';

        $this->mapper
            ->expects($this->once())
            ->method('selectRelated')
            ->will($this->returnValue($post))
            ->with($note, 'parent');

        $result = $this->createRepository('Note')->getRelation($note, 'parent')->findOne();

        $this->assertEquals($post, $result);
    }

    public function testRelationIsRelated1()
    {
        $note = $this->entityFactory->create('Note');
        $note->set('id', 'noteId');

        $post = $this->entityFactory->create('Post');
        $post->set('id', 'postId');

        $note->set('parentId', $post->id);
        $note->set('parentType', 'Post');

        $result = $this->createRepository('Note')->getRelation($note, 'parent')->isRelated($post);

        $this->assertTrue($result);

        $note->set('parentId', 'anotherId');
        $note->set('parentType', 'Post');

        $result = $this->createRepository('Note')->getRelation($note, 'parent')->isRelated($post);

        $this->assertFalse($result);
    }

    public function testRelationIsRelated2()
    {
        $post = $this->entityFactory->create('Post');
        $post->set('id', 'postId');

        $note = $this->entityFactory->create('Note');
        $note->set('id', 'noteId');

        $collection = $this->createCollectionMock([$note]);

        $select = $this->queryBuilder
            ->select()
            ->from('Note')
            ->select(['id'])
            ->where(['id' => $note->id])
            ->limit(0, 1)
            ->build();

        $this->mapper
            ->expects($this->once())
            ->method('selectRelated')
            ->will($this->returnValue($collection))
            ->with($post, 'notes', $select);

        $result = $this->createRepository('Post')->getRelation($post, 'notes')->isRelated($note);

        $this->assertTrue($result);
    }

    public function testRelationIsRelated3()
    {
        $post = $this->entityFactory->create('Post');
        $post->set('id', 'postId');

        $note = $this->entityFactory->create('Note');
        $note->set('id', 'noteId');

        $collection = $this->createCollectionMock();

        $select = $this->queryBuilder
            ->select()
            ->from('Note')
            ->select(['id'])
            ->where(['id' => $note->id])
            ->limit(0, 1)
            ->build();

        $this->mapper
            ->expects($this->once())
            ->method('selectRelated')
            ->will($this->returnValue($collection))
            ->with($post, 'notes', $select);

        $result = $this->createRepository('Post')->getRelation($post, 'notes')->isRelated($note);

        $this->assertFalse($result);
    }

    public function testRelate1()
    {
        $post = $this->entityFactory->create('Post');
        $post->set('id', 'postId');

        $note = $this->entityFactory->create('Note');
        $note->set('id', 'noteId');

        $this->mapper
            ->expects($this->once())
            ->method('relate')
            ->with($post, 'notes', $note);

        $this->createRepository('Post')->getRelation($post, 'notes')->relate($note);
    }

    public function testUnrelate1()
    {
        $post = $this->entityFactory->create('Post');
        $post->set('id', 'postId');

        $note = $this->entityFactory->create('Note');
        $note->set('id', 'noteId');

        $this->mapper
            ->expects($this->once())
            ->method('unrelate')
            ->with($post, 'notes', $note);

        $this->createRepository('Post')->getRelation($post, 'notes')->unrelate($note);
    }

    public function testRelateById1()
    {
        $post = $this->entityFactory->create('Post');
        $post->set('id', 'postId');

        $this->mapper
            ->expects($this->once())
            ->method('relate')
            ->with($post, 'notes', $this->isInstanceOf(Entities\Note::class));

        $this->createRepository('Post')->getRelation($post, 'notes')->relateById('noteId');
    }

    public function testUnrelateById1()
    {
        $post = $this->entityFactory->create('Post');
        $post->set('id', 'postId');

        $this->mapper
            ->expects($this->once())
            ->method('unrelate')
            ->with($post, 'notes', $this->isInstanceOf(Entities\Note::class));

        $this->createRepository('Post')->getRelation($post, 'notes')->unrelateById('noteId');
    }

    public function testMassRelate()
    {
        $post = $this->entityFactory->create('Post');
        $post->set('id', 'postId');

        $select = $this->queryBuilder
            ->select()
            ->from('Note')
            ->build();

        $this->mapper
            ->expects($this->once())
            ->method('massRelate')
            ->with($post, 'notes', $select);

        $this->createRepository('Post')->getRelation($post, 'notes')->massRelate($select);
    }

    public function testGetColumn()
    {
        $account = $this->entityFactory->create('Account');
        $account->set('id', 'accountId');

        $team = $this->entityFactory->create('Team');
        $team->set('id', 'teamId');

        $this->mapper
            ->expects($this->once())
            ->method('getRelationColumn')
            ->with($account, 'teams', $team->id, 'test');

        $this->createRepository('Post')->getRelation($account, 'teams')->getColumn($team, 'test');
    }

    public function testUpdateColumns()
    {
        $account = $this->entityFactory->create('Account');
        $account->set('id', 'accountId');

        $team = $this->entityFactory->create('Team');
        $team->set('id', 'teamId');

        $columns = [
            'column' => 'test',
        ];

        $this->mapper
            ->expects($this->once())
            ->method('updateRelationColumns')
            ->with($account, 'teams', $team->id, $columns);

        $this->createRepository('Post')->getRelation($account, 'teams')->updateColumns($team, $columns);
    }

    public function testRelationSelectBuilderFind1()
    {
        $repository = $this->createRepository('Post');

        $post = $this->entityFactory->create('Post');
        $post->id = 'postId';

        $collection = $this->createCollectionMock();

        $select = $this->queryBuilder
            ->select()
            ->from('Comment')
            ->select(['id'])
            ->distinct()
            ->where(['name' => 'test'])
            ->join('Test', 'test', ['id:' => 'id'])
            ->order('id', 'DESC')
            ->build();

        $this->mapper
            ->expects($this->once())
            ->method('selectRelated')
            ->will($this->returnValue($collection))
            ->with($post, 'comments', $select);

        $repository->getRelation($post, 'comments')
            ->select(['id'])
            ->distinct()
            ->where(['name' => 'test'])
            ->join('Test', 'test', ['id:' => 'id'])
            ->order('id', 'DESC')
            ->find();
    }

    public function testRelationSelectBuilderFind2()
    {
        $repository = $this->createRepository('Post');

        $post = $this->entityFactory->create('Post');
        $post->id = 'postId';

        $collection = $this->createCollectionMock();

        $select = $this->queryBuilder
            ->select()
            ->from('Comment')
            ->select(['id'])
            ->distinct()
            ->where(Cond::equal(Expr::column('name'), 'test'))
            ->join('Test', 'test', Cond::equal(Expr::column('test.id'), Expr::column('id')))
            ->order('id', 'DESC')
            ->build();

        $this->mapper
            ->expects($this->once())
            ->method('selectRelated')
            ->will($this->returnValue($collection))
            ->with($post, 'comments', $select);

        $repository->getRelation($post, 'comments')
            ->select(['id'])
            ->distinct()
            ->where(Cond::equal(Expr::column('name'), 'test'))
            ->join(
                'Test',
                'test',
                Cond::equal(Expr::column('test.id'), Expr::column('id'))
            )
            ->order('id', 'DESC')
            ->find();
    }

    public function testRelationSelectBuilderFindOne()
    {
        $repository = $this->createRepository('Post');

        $post = $this->entityFactory->create('Post');
        $post->id = 'postId';



        $comment = $this->entityFactory->create('Comment');
        $comment->set('id', 'commentId');

        $collection = $this->createCollectionMock([$comment]);

        $select = $this->queryBuilder
            ->select()
            ->from('Comment')
            ->select(['id'])
            ->distinct()
            ->where(['name' => 'test'])
            ->join('Test', 'test', ['id:' => 'id'])
            ->order('id', 'DESC')
            ->limit(0, 1)
            ->build();

        $this->mapper
            ->expects($this->once())
            ->method('selectRelated')
            ->will($this->returnValue($collection))
            ->with($post, 'comments', $select);

        $result = $repository->getRelation($post, 'comments')
            ->select(['id'])
            ->distinct()
            ->where(['name' => 'test'])
            ->join('Test', 'test', ['id:' => 'id'])
            ->order('id', 'DESC')
            ->findOne();

        $this->assertEquals($comment, $result);
    }

    public function testRelationSelectBuilderColumnsWhere1()
    {
        $account = $this->entityFactory->create('Account');
        $account->set('id', 'accountId');

        $collection = $this->createCollectionMock();

        $select = $this->queryBuilder
            ->select()
            ->from('Team')
            ->where(['entityTeam.deleted' => false])
            ->build();

        $this->mapper
            ->expects($this->once())
            ->method('selectRelated')
            ->will($this->returnValue($collection))
            ->with($account, 'teams', $select);


        $this->createRepository('Account')->getRelation($account, 'teams')
            ->columnsWhere(['deleted' => false])
            ->find();
    }

    public function testRelationSelectBuilderColumnsWhere2()
    {
        $account = $this->entityFactory->create('Account');
        $account->set('id', 'accountId');

        $collection = $this->createCollectionMock();

        $select = $this->queryBuilder
            ->select()
            ->from('Team')
            ->where([
                'OR' => [
                    ['entityTeam.deleted' => false],
                    ['entityTeam.deleted' => null],
                ]
            ])
            ->build();

        $this->mapper
            ->expects($this->once())
            ->method('selectRelated')
            ->will($this->returnValue($collection))
            ->with($account, 'teams', $select);


        $this->createRepository('Account')->getRelation($account, 'teams')
            ->columnsWhere([
                'OR' => [
                    ['deleted' => false],
                    ['deleted' => null],
                ]
            ])
            ->find();
    }

    public function testRelationSelectBuilderRelationWhere1()
    {
        $account = $this->entityFactory->create('Account');
        $account->set('id', 'accountId');

        $collection = $this->createCollectionMock();

        $select = $this->queryBuilder
            ->select()
            ->from('Team')
            ->where(['entityTeam.deleted' => false])
            ->build();

        $this->mapper
            ->expects($this->once())
            ->method('selectRelated')
            ->will($this->returnValue($collection))
            ->with($account, 'teams', $select);

        $this->createRepository('Account')
            ->getRelation($account, 'teams')
            ->where(['@relation.deleted' => false])
            ->find();
    }

    public function testRelationSelectBuilderRelationWhere2()
    {
        $account = $this->entityFactory->create('Account');
        $account->set('id', 'accountId');

        $collection = $this->createCollectionMock();

        $select = $this->queryBuilder
            ->select()
            ->from('Team')
            ->where([
                'OR' => [
                    ['entityTeam.deleted' => false],
                    ['entityTeam.deleted' => null],
                ],
                'deleted' => false,
            ])
            ->build();

        $this->mapper
            ->expects($this->once())
            ->method('selectRelated')
            ->will($this->returnValue($collection))
            ->with($account, 'teams', $select);

        $this->createRepository('Account')
            ->getRelation($account, 'teams')
            ->where([
                'OR' => [
                    ['@relation.deleted' => false],
                    ['@relation.deleted' => null],
                ],
                'deleted' => false,
            ])
            ->find();
    }

    public function testRelationSelectBuilderRelationWhere3()
    {
        $account = $this->entityFactory->create('Account');
        $account->set('id', 'accountId');

        $collection = $this->createCollectionMock();

        $select = $this->queryBuilder
            ->select()
            ->from('Team')
            ->where(['entityTeam.deleted' => false])
            ->build();

        $this->mapper
            ->expects($this->once())
            ->method('selectRelated')
            ->will($this->returnValue($collection))
            ->with($account, 'teams', $select);

        $this->createRepository('Account')
            ->getRelation($account, 'teams')
            ->where('@relation.deleted', false)
            ->find();
    }

    public function testRelationSelectBuilderRelationWhere4()
    {
        $account = $this->entityFactory->create('Account');
        $account->set('id', 'accountId');

        $collection = $this->createCollectionMock();

        $select = $this->queryBuilder
            ->select()
            ->from('Team')
            ->where(['entityTeam.deleted=' => false])
            ->build();

        $this->mapper
            ->expects($this->once())
            ->method('selectRelated')
            ->will($this->returnValue($collection))
            ->with($account, 'teams', $select);

        $this->createRepository('Account')
            ->getRelation($account, 'teams')
            ->where(
                Cond::equal(Expr::column('@relation.deleted'), false)
            )
            ->find();
    }
}
