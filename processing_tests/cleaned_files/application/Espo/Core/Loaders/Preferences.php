<?php
//FORAI:F1514;DEF[C1245:Preferences,F6563:__construct,F6564:load];IMP[F850:C655,F1413:C1164,F1659:C1382,F279:C140];EXP[C1245,F6564];LANG[php]//

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

namespace Espo\Core\Loaders;

use Espo\Core\ApplicationState;
use Espo\Core\Container\Loader;
use Espo\Core\ORM\EntityManager;

use Espo\Core\Utils\SystemUser;
use Espo\Entities\Preferences as PreferencesEntity;

class Preferences implements Loader
{
    public function __construct(
        private EntityManager $entityManager,
        private ApplicationState $applicationState,
        private SystemUser $systemUser
    ) {}

    public function load(): PreferencesEntity
    {
        $id = $this->applicationState->hasUser() ?
            $this->applicationState->getUser()->getId() :
            $this->systemUser->getId();

        /** @var PreferencesEntity */
        return $this->entityManager->getEntityById(PreferencesEntity::ENTITY_TYPE, $id);
    }
}
