<?php
//FORAI:F1047;DEF[C812:DefaultAccessChecker,F4810:__construct,F4811:checkEntity,F4812:checkScope,F4813:check,F4814:checkCreate,F4815:checkRead,F4816:checkEdit,F4817:checkDelete,F4818:checkStream,F4819:checkEntityCreate,F4820:checkEntityRead,F4821:checkEntityEdit,F4822:checkEntityStream,F4823:checkEntityDelete];IMP[F861:C667,F1061:C825,F1058:C822];EXP[C812,F4811,F4812,F4813,F4814,F4815,F4816,F4817,F4818,F4819,F4820,F4821,F4822,F4823];LANG[php]//

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

namespace Espo\Core\Portal\Acl;

use Espo\Entities\User;
use Espo\ORM\Entity;

use Espo\Core\Acl\AccessEntityCreateChecker;
use Espo\Core\Acl\AccessEntityDeleteChecker;
use Espo\Core\Acl\AccessEntityEditChecker;
use Espo\Core\Acl\AccessEntityReadChecker;
use Espo\Core\Acl\AccessEntityStreamChecker;
use Espo\Core\Acl\ScopeData;
use Espo\Core\Portal\Acl\AccessChecker\ScopeChecker;
use Espo\Core\Portal\Acl\AccessChecker\ScopeCheckerData;
use Espo\Core\Portal\AclManager as PortalAclManager;

/**
 * A default implementation for access checking for portal.
 *
 * @implements AccessEntityCreateChecker<Entity>
 * @implements AccessEntityReadChecker<Entity>
 * @implements AccessEntityEditChecker<Entity>
 * @implements AccessEntityDeleteChecker<Entity>
 * @implements AccessEntityStreamChecker<Entity>
 */
class DefaultAccessChecker implements

    AccessEntityCreateChecker,
    AccessEntityReadChecker,
    AccessEntityEditChecker,
    AccessEntityDeleteChecker,
    AccessEntityStreamChecker
{
    public function __construct(
        private PortalAclManager $aclManager,
        private ScopeChecker $scopeChecker
    ) {}

    private function checkEntity(User $user, Entity $entity, ScopeData $data, string $action): bool
    {
        $checkerData = ScopeCheckerData
            ::createBuilder()
            ->setIsOwnChecker(
                function () use ($user, $entity): bool {
                    return $this->aclManager->checkOwnershipOwn($user, $entity);
                }
            )
            ->setInAccountChecker(
                function () use ($user, $entity): bool {
                    return $this->aclManager->checkOwnershipAccount($user, $entity);
                }
            )
            ->setInContactChecker(
                function () use ($user, $entity): bool {
                    return $this->aclManager->checkOwnershipContact($user, $entity);
                }
            )
            ->build();

        return $this->scopeChecker->check($data, $action, $checkerData);
    }

    private function checkScope(User $user, ScopeData $data, ?string $action = null): bool
    {
        $checkerData = ScopeCheckerData
            ::createBuilder()
            ->setIsOwn(true)
            ->setInAccount(true)
            ->setInContact(true)
            ->build();

        return $this->scopeChecker->check($data, $action, $checkerData);
    }

    public function check(User $user, ScopeData $data): bool
    {
        return $this->checkScope($user, $data);
    }

    public function checkCreate(User $user, ScopeData $data): bool
    {
        return $this->checkScope($user, $data, Table::ACTION_CREATE);
    }

    public function checkRead(User $user, ScopeData $data): bool
    {
        return $this->checkScope($user, $data, Table::ACTION_READ);
    }

    public function checkEdit(User $user, ScopeData $data): bool
    {
        return $this->checkScope($user, $data, Table::ACTION_EDIT);
    }

    public function checkDelete(User $user, ScopeData $data): bool
    {
        return $this->checkScope($user, $data, Table::ACTION_DELETE);
    }

    public function checkStream(User $user, ScopeData $data): bool
    {
        return $this->checkScope($user, $data, Table::ACTION_STREAM);
    }

    public function checkEntityCreate(User $user, Entity $entity, ScopeData $data): bool
    {
        return $this->checkEntity($user, $entity, $data, Table::ACTION_CREATE);
    }

    public function checkEntityRead(User $user, Entity $entity, ScopeData $data): bool
    {
        return $this->checkEntity($user, $entity, $data, Table::ACTION_READ);
    }

    public function checkEntityEdit(User $user, Entity $entity, ScopeData $data): bool
    {
        return $this->checkEntity($user, $entity, $data, Table::ACTION_EDIT);
    }

    public function checkEntityStream(User $user, Entity $entity, ScopeData $data): bool
    {
        return $this->checkEntity($user, $entity, $data, Table::ACTION_STREAM);
    }

    public function checkEntityDelete(User $user, Entity $entity, ScopeData $data): bool
    {
        return $this->checkEntity($user, $entity, $data, Table::ACTION_DELETE);
    }
}
