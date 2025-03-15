<?php
//FORAI:F807;DEF[C613:ScheduledJob<Database>,F3713:afterSave];IMP[F268:C128,F1560:C1289,F1528:C1262];EXP[C613,F3713];LANG[php]//

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

namespace Espo\Repositories;

use Espo\Entities\Job as JobEntity;
use Espo\ORM\Entity;
use Espo\Core\Job\Job\Status;
use Espo\Core\Repositories\Database;

/**
 * @extends Database<\Espo\Entities\ScheduledJob>
 */
class ScheduledJob extends Database
{
    protected function afterSave(Entity $entity, array $options = [])
    {
        parent::afterSave($entity, $options);

        if ($entity->isAttributeChanged('scheduling')) {
            $jobList = $this->entityManager
                ->getRDBRepository(JobEntity::ENTITY_TYPE)
                ->where([
                    'scheduledJobId' => $entity->getId(),
                    'status' => Status::PENDING,
                ])
                ->find();

            foreach ($jobList as $job) {
                $this->entityManager->removeEntity($job);
            }
        }
    }
}
