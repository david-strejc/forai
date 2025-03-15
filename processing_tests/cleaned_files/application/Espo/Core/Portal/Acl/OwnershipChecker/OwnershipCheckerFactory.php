<?php
//FORAI:F1051;DEF[C814:OwnershipCheckerFactory,F4829:__construct,F4830:create,F4831:getClassName,F4832:createBindingContainer];IMP[F887:C675,F1991:C1617,F1994:C1622,F1993:C1623,F846:C649,F1048:C813,F1665:C1390];EXP[C814,F4830,F4831,F4832];LANG[php]//

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

namespace Espo\Core\Portal\Acl\OwnershipChecker;

use Espo\Core\Acl\Exceptions\NotImplemented;
use Espo\Core\Acl\OwnershipChecker;
use Espo\Core\Binding\Binder;
use Espo\Core\Binding\BindingContainer;
use Espo\Core\Binding\BindingData;
use Espo\Core\InjectableFactory;
use Espo\Core\Portal\Acl\DefaultOwnershipChecker;
use Espo\Core\Portal\AclManager as PortalAclManager;
use Espo\Core\Utils\Metadata;

class OwnershipCheckerFactory
{
    /** @var class-string<OwnershipChecker> */
    private $defaultClassName = DefaultOwnershipChecker::class;

    public function __construct(
        private Metadata $metadata,
        private InjectableFactory $injectableFactory
    ) {}

    /**
     * Create an ownership checker.
     *
     * @throws NotImplemented
     */
    public function create(string $scope, PortalAclManager $aclManager): OwnershipChecker
    {
        $className = $this->getClassName($scope);

        $bindingContainer = $this->createBindingContainer($aclManager);

        return $this->injectableFactory->createWithBinding($className, $bindingContainer);
    }

    /**
     * @return class-string<OwnershipChecker>
     */
    private function getClassName(string $scope): string
    {
        $className = $this->metadata->get(['aclDefs', $scope, 'portalOwnershipCheckerClassName']);

        if ($className) {
            /** @var class-string<OwnershipChecker> */
            return $className;
        }

        if (!$this->metadata->get(['scopes', $scope])) {
            throw new NotImplemented();
        }

        return $this->defaultClassName;
    }

    private function createBindingContainer(PortalAclManager $aclManager): BindingContainer
    {
        $bindingData = new BindingData();

        $binder = new Binder($bindingData);

        $binder->bindInstance(PortalAclManager::class, $aclManager);

        return new BindingContainer($bindingData);
    }
}
