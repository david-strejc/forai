<?php
//FORAI:F2342;DEF[C1953:UnsetUserDefaultTeam,F9723:__construct,F9724:process];IMP[F292:C153];EXP[C1953,F9724];LANG[php]//

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

namespace Espo\Classes\RecordHooks\Team;

use Espo\Core\Record\Hook\UnlinkHook;
use Espo\Entities\Team;
use Espo\Entities\User;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;

/**
 * @implements UnlinkHook<Team>
 */
class UnsetUserDefaultTeam implements UnlinkHook
{
    public function __construct(private EntityManager $entityManager)
    {}

    public function process(Entity $entity, string $link, Entity $foreignEntity): void
    {
        if (!$foreignEntity instanceof User || $link !== 'users') {
            return;
        }

        if ($foreignEntity->getDefaultTeam()?->getId() !== $entity->getId()) {
            return;
        }

        $foreignEntity->setDefaultTeam(null);

        $this->entityManager->saveEntity($foreignEntity);
    }
}
