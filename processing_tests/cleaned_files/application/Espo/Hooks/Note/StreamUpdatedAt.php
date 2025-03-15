<?php
//FORAI:F779;DEF[C585:StreamUpdatedAt,F3626:__construct,F3627:afterSave];IMP[F340:C195];EXP[C585,F3627];LANG[php]//

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

namespace Espo\Hooks\Note;

use Espo\Core\Hook\Hook\AfterSave;
use Espo\ORM\Entity;
use Espo\ORM\Repository\Option\SaveOptions;
use Espo\Entities\Note;
use Espo\Tools\Stream\Service;

/**
 * @implements AfterSave<Note>
 */
class StreamUpdatedAt implements AfterSave
{
    public function __construct(private Service $service)
    {}

    public function afterSave(Entity $entity, SaveOptions $options): void
    {
        if (!$entity->isNew()) {
            return;
        }

        if (
            $entity->getType() !== Note::TYPE_POST ||
            !$entity->getParentType() ||
            !$this->service->checkIsEnabled($entity->getParentType())
        ) {
            return;
        }

        if (!$entity->getParent()) {
            return;
        }

        $this->service->updateStreamUpdatedAt($entity->getParent());
    }
}
