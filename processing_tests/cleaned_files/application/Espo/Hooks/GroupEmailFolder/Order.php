<?php
//FORAI:F782;DEF[C589:Order,F3635:__construct,F3636:beforeSave];IMP[F259:C119];EXP[C589,F3636];LANG[php]//

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

namespace Espo\Hooks\GroupEmailFolder;

use Espo\Entities\GroupEmailFolder;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;

class Order
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param GroupEmailFolder $entity
     */
    public function beforeSave(Entity $entity): void
    {
        $order = $entity->getOrder();

        if ($order !== null) {
            return;
        }

        $order = $this->entityManager
            ->getRDBRepositoryByClass(GroupEmailFolder::class)
            ->max('order');

        if (!$order) {
            $order = 0;
        }

        $order++;

        $entity->set('order', $order);
    }
}
