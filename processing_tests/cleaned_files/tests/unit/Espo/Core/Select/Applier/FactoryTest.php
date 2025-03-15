<?php
//FORAI:F2730;DEF[C2328:FactoryTest,F11734:setUp,F11735:testCreate1,F11736:testCreate2,F11737:testCreate3,F11738:testCreate4,F11739:testCreate5,F11740:testCreate6,F11741:testCreate7,F11742:testCreate8,F11743:testCreate9,F11744:prepareFactoryTest];IMP[F1991:C1617,F1994:C1622,F1993:C1623,F846:C649,F1360:C1113,F1343:C1098,F1344:C1099,F1341:C1097,F1314:C1073,F1387:C1137,F1381:C1132,F1312:C1071,F1306:C1064,F1347:C1100,F1321:C1078];EXP[C2328,F11734,F11735,F11736,F11737,F11738,F11739,F11740,F11741,F11742,F11743,F11744];LANG[php]//

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

namespace tests\unit\Espo\Core\Select\Applier;

use Espo\Core\Binding\Binder;
use Espo\Core\Binding\BindingContainer;
use Espo\Core\Binding\BindingData;
use Espo\Core\InjectableFactory;
use Espo\Core\Select\AccessControl\Applier as AccessControlFilterApplier;
use Espo\Core\Select\Applier\Appliers\Additional as AdditionalApplier;
use Espo\Core\Select\Applier\Appliers\Limit as LimitApplier;
use Espo\Core\Select\Applier\Factory as ApplierFactory;
use Espo\Core\Select\Bool\Applier as BoolFilterListApplier;
use Espo\Core\Select\Order\Applier as OrderApplier;
use Espo\Core\Select\Primary\Applier as PrimaryFilterApplier;
use Espo\Core\Select\Select\Applier as SelectApplier;
use Espo\Core\Select\SelectManager;
use Espo\Core\Select\SelectManagerFactory;
use Espo\Core\Select\Text\Applier as TextFilterApplier;
use Espo\Core\Select\Where\Applier as WhereApplier;

use Espo\Entities\User;

class FactoryTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $this->injectableFactory = $this->createMock(InjectableFactory::class);
        $this->selectManagerFactory = $this->createMock(SelectManagerFactory::class);
        $this->selectManagerFactory = $this->createMock(SelectManagerFactory::class);
        $this->user = $this->createMock(User::class);

        $this->selectManager = $this->createMock(SelectManager::class);

        $this->factory = new ApplierFactory(
            $this->injectableFactory,
            $this->selectManagerFactory
        );
    }

    public function testCreate1()
    {
        $this->prepareFactoryTest(SelectApplier::class, ApplierFactory::SELECT, 'createSelect');
    }

    public function testCreate2()
    {
        $this->prepareFactoryTest(
            BoolFilterListApplier::class, ApplierFactory::BOOL_FILTER_LIST, 'createBoolFilterList');
    }

    public function testCreate3()
    {
        $this->prepareFactoryTest(TextFilterApplier::class, ApplierFactory::TEXT_FILTER, 'createTextFilter');
    }

    public function testCreate4()
    {
        $this->prepareFactoryTest(WhereApplier::class, ApplierFactory::WHERE, 'createWhere');
    }

    public function testCreate5()
    {
        $this->prepareFactoryTest(OrderApplier::class, ApplierFactory::ORDER, 'createOrder');
    }

    public function testCreate6()
    {
        $this->prepareFactoryTest(LimitApplier::class, ApplierFactory::LIMIT, 'createLimit');
    }

    public function testCreate7()
    {
        $this->prepareFactoryTest(AdditionalApplier::class, ApplierFactory::ADDITIONAL, 'createAdditional');
    }

    public function testCreate8()
    {
        $this->prepareFactoryTest(
            PrimaryFilterApplier::class, ApplierFactory::PRIMARY_FILTER, 'createPrimaryFilter');
    }

    public function testCreate9()
    {
        $this->prepareFactoryTest(
            AccessControlFilterApplier::class, ApplierFactory::ACCESS_CONTROL_FILTER, 'createAccessControlFilter');
    }

    protected function prepareFactoryTest(string $defaultClassName, string $type, string $method)
    {
        $entityType = 'Test';

        $this->selectManagerFactory
            ->expects($this->once())
            ->method('create')
            ->with('Test', $this->user)
            ->willReturn($this->selectManager);

        $applierClassName = $className ?? $defaultClassName;

        $applier = $this->createMock($defaultClassName);

        $bindingData = new BindingData();

        $binder = new Binder($bindingData);

        $binder
            ->bindInstance(User::class, $this->user)
            ->bindInstance(SelectManager::class, $this->selectManager)
            ->for($applierClassName)
            ->bindValue('$entityType', $entityType)
            ->bindValue('$selectManager', $this->selectManager);

        $bindingContainer = new BindingContainer($bindingData);

        $this->injectableFactory
            ->expects($this->once())
            ->method('createWithBinding')
            ->with($applierClassName, $bindingContainer)
            ->willReturn($applier);

        $resultApplier = $this->factory->$method(
            $entityType,
            $this->user,
            $type
        );

        $this->assertEquals($applier, $resultApplier);
    }
}
