<?php
//FORAI:F2114;DEF[C1725:AccessChecker,F9203:__construct,F9204:checkEntityCreate,F9205:checkEntityRead,F9206:checkEntityEdit,F9207:checkEntityDelete,F9208:checkEntityStream];IMP[F858:C666,F875:C671,F861:C667];EXP[C1725,F9204,F9205,F9206,F9207,F9208];LANG[php]//

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

namespace Espo\Classes\Acl\User;

use Espo\Core\Acl\Permission;
use Espo\Entities\User;
use Espo\ORM\Entity;
use Espo\Core\Acl\AccessEntityCREDSChecker;
use Espo\Core\Acl\DefaultAccessChecker;
use Espo\Core\Acl\ScopeData;
use Espo\Core\Acl\Table;
use Espo\Core\Acl\Traits\DefaultAccessCheckerDependency;
use Espo\Core\AclManager;

/**
 * @implements AccessEntityCREDSChecker<User>
 */
class AccessChecker implements AccessEntityCREDSChecker
{
    use DefaultAccessCheckerDependency;

    private DefaultAccessChecker $defaultAccessChecker;
    private AclManager $aclManager;

    public function __construct(DefaultAccessChecker $defaultAccessChecker, AclManager $aclManager)
    {
        $this->defaultAccessChecker = $defaultAccessChecker;
        $this->aclManager = $aclManager;
    }

    public function checkEntityCreate(User $user, Entity $entity, ScopeData $data): bool
    {
        if (!$user->isAdmin()) {
            return false;
        }

        if ($entity->isSuperAdmin() && !$user->isSuperAdmin()) {
            return false;
        }

        return $this->defaultAccessChecker->checkEntityCreate($user, $entity, $data);
    }

    public function checkEntityRead(User $user, Entity $entity, ScopeData $data): bool
    {
        if ($entity->isPortal()) {
            if ($this->aclManager->getPermissionLevel($user, Permission::PORTAL) === Table::LEVEL_YES) {
                return true;
            }

            return false;
        }

        if ($entity->isSuperAdmin() && !$user->isSuperAdmin()) {
            return false;
        }

        return $this->defaultAccessChecker->checkEntityRead($user, $entity, $data);
    }

    public function checkEntityEdit(User $user, Entity $entity, ScopeData $data): bool
    {
        if ($entity->isSystem()) {
            return false;
        }

        if (!$user->isAdmin()) {
            if ($user->getId() !== $entity->getId()) {
                return false;
            }
        }

        if ($entity->isSuperAdmin() && !$user->isSuperAdmin()) {
            return false;
        }

        return $this->defaultAccessChecker->checkEntityEdit($user, $entity, $data);
    }

    public function checkEntityDelete(User $user, Entity $entity, ScopeData $data): bool
    {
        if (!$user->isAdmin()) {
            return false;
        }

        if ($entity->isSystem()) {
            return false;
        }

        if ($entity->isSuperAdmin() && !$user->isSuperAdmin()) {
            return false;
        }

        return $this->defaultAccessChecker->checkEntityDelete($user, $entity, $data);
    }

    public function checkEntityStream(User $user, Entity $entity, ScopeData $data): bool
    {
        /** @noinspection PhpRedundantOptionalArgumentInspection */
        return $this->aclManager->checkUserPermission($user, $entity, Permission::USER);
    }
}
