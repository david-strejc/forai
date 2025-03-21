<?php
//FORAI:F2895;DEF[C2487:ReminderTest<BaseTestCase>,F12572:testOne];IMP[F1413:C1164,F2387:C1999];EXP[C2487,F12572];LANG[php]//

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

namespace tests\integration\Espo\Core\FieldProcessing;

use Espo\Core\Field\DateTime;
use Espo\Core\ORM\EntityManager;
use Espo\Entities\User;
use Espo\Modules\Crm\Entities\Reminder;
use tests\integration\Core\BaseTestCase;

class ReminderTest extends BaseTestCase
{
    public function testOne(): void
    {
        /* @var $entityManager EntityManager */
        $entityManager = $this->getContainer()->getByClass(EntityManager::class);

        $user = $this->getContainer()->getByClass(User::class);

        $meeting = $entityManager->createEntity('Meeting', [
            'dateStart' => DateTime::createNow()->modify('+1 day')->toString(),
            'usersIds' => [$user->getId()],
            'reminders' => [
                (object) [
                    'type' => 'Popup',
                    'seconds' => 0,
                ],
                 (object) [
                    'type' => 'Popup',
                    'seconds' => 60,
                ]
            ]
        ]);

        $reminderList = $entityManager
            ->getRDBRepository(Reminder::ENTITY_TYPE)
            ->where([
                'entityId' => $meeting->getId(),
                'entityType' => $meeting->getEntityType(),
            ])
            ->order('remindAt')
            ->find();

        $this->assertEquals(2, count($reminderList));
    }
}
