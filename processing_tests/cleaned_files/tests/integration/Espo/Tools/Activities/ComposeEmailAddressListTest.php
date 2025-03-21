<?php
//FORAI:F2863;DEF[C2454:ComposeEmailAddressListTest<BaseTestCase>,F12465:testGetGetAddressList];IMP[F2374:C1986,F2435:C2044];EXP[C2454,F12465];LANG[php]//

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

namespace tests\integration\Espo\Tools\Activities;

use Espo\Core\Api\Request;
use Espo\Modules\Crm\Entities\Account;
use Espo\Modules\Crm\Entities\CaseObj;
use Espo\Modules\Crm\Tools\Activities\Api\GetComposeAddressList;
use tests\integration\Core\BaseTestCase;

class ComposeEmailAddressListTest extends BaseTestCase
{
    public function testGetGetAddressList(): void
    {
        $em = $this->getEntityManager();

        $account = $em->createEntity(Account::ENTITY_TYPE, [
            'name' => 'Test',
            'emailAddress' => 'test@test.com',
        ]);

        $case = $em->createEntity(CaseObj::ENTITY_TYPE, [
            'accountId' => $account->getId(),
        ]);

        $action = $this->getInjectableFactory()
            ->create(GetComposeAddressList::class);

        $request = $this->createMock(Request::class);

        $request
            ->expects($this->any())
            ->method('getRouteParam')
            ->willReturnMap([
                ['parentType', CaseObj::ENTITY_TYPE],
                ['id', $case->getId()]
            ]);

        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $action->process($request);

        $list = json_decode($response->getBody());

        $this->assertEquals([
            (object) [
                'emailAddress' => 'test@test.com',
                'name' => $account->get('name'),
                'entityId' => $account->getId(),
                'entityType' => Account::ENTITY_TYPE,
            ]
        ], $list);
    }
}
