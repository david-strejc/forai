<?php
//FORAI:F2326;DEF[C1938:BeforeCreate,F9683:__construct,F9684:process];IMP[F926:C705,F1662:C1385];EXP[C1938,F9684];LANG[php]//

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

namespace Espo\Classes\RecordHooks\EmailAccount;

use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Record\Hook\SaveHook;
use Espo\Core\Utils\Config;
use Espo\Entities\EmailAccount;
use Espo\Entities\User;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use const PHP_INT_MAX;

/**
 * @implements SaveHook<EmailAccount>
 */
class BeforeCreate implements SaveHook
{
    public function __construct(
        private User $user,
        private Config $config,
        private EntityManager $entityManager
    ) {}

    public function process(Entity $entity): void
    {
        if ($this->user->isAdmin()) {
            return;
        }

        $entity->set('assignedUserId', $this->user->getId());

        $count = $this->entityManager
            ->getRDBRepository(EmailAccount::ENTITY_TYPE)
            ->where(['assignedUserId' => $this->user->getId()])
            ->count();

        if ($count >= $this->config->get('maxEmailAccountCount', PHP_INT_MAX)) {
            throw new Forbidden("Email Account number for user limit exceeded.");
        }
    }
}
