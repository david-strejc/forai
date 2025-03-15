<?php
//FORAI:F898;DEF[C682:DefaultTableFactory,F4116:__construct,F4117:create,F4118:createBindingContainer];IMP[F1991:C1617,F1994:C1622,F1993:C1623,F846:C649];EXP[C682,F4117,F4118];LANG[php]//

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

namespace Espo\Core\Acl\Table;

use Espo\Entities\User;
use Espo\Core\Acl\Table;
use Espo\Core\Binding\Binder;
use Espo\Core\Binding\BindingContainer;
use Espo\Core\Binding\BindingData;
use Espo\Core\InjectableFactory;

class DefaultTableFactory implements TableFactory
{
    public function __construct(private InjectableFactory $injectableFactory)
    {}

    /**
     * Create a table.
     */
    public function create(User $user): Table
    {
        $bindingContainer = $this->createBindingContainer($user);

        return $this->injectableFactory->createWithBinding(DefaultTable::class, $bindingContainer);
    }

    private function createBindingContainer(User $user): BindingContainer
    {
        $bindingData = new BindingData();

        $binder = new Binder($bindingData);

        $binder
            ->bindInstance(User::class, $user)
            ->bindImplementation(RoleListProvider::class, DefaultRoleListProvider::class)
            ->bindImplementation(CacheKeyProvider::class, DefaultCacheKeyProvider::class);

        return new BindingContainer($bindingData);
    }
}
