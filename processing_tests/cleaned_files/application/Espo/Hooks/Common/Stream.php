<?php
//FORAI:F791;DEF[C598:Stream,F3661:__construct,F3662:beforeSave,F3663:afterSave,F3664:afterRemove,F3665:afterRelate,F3666:afterUnrelate];IMP[F1415:C1167,F342:C197,F341:C196,F340:C195,F343:C200];EXP[C598,F3662,F3663,F3664,F3665,F3666];LANG[php]//

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

namespace Espo\Hooks\Common;

use Espo\Core\Hook\Hook\AfterRelate;
use Espo\Core\Hook\Hook\AfterRemove;
use Espo\Core\Hook\Hook\AfterSave;
use Espo\Core\Hook\Hook\AfterUnrelate;
use Espo\Core\Hook\Hook\BeforeSave;
use Espo\Core\ORM\Repository\Option\SaveOption;
use Espo\ORM\Entity;
use Espo\ORM\Repository\Option\RelateOptions;
use Espo\ORM\Repository\Option\RemoveOptions;
use Espo\ORM\Repository\Option\SaveOptions;
use Espo\ORM\Repository\Option\UnrelateOptions;
use Espo\Tools\Stream\HookProcessor;

/**
 * @implements BeforeSave<Entity>
 * @implements AfterSave<Entity>
 * @implements AfterRemove<Entity>
 * @implements AfterRelate<Entity>
 * @implements AfterUnrelate<Entity>
 */
class Stream implements BeforeSave, AfterSave, AfterRemove, AfterRelate, AfterUnrelate
{
    public static int $order = 9;

    public function __construct(private HookProcessor $processor)
    {}

    public function beforeSave(Entity $entity, SaveOptions $options): void
    {
        if ($options->get(SaveOption::SILENT)) {
            return;
        }

        $this->processor->beforeSave($entity, $options);
    }

    public function afterSave(Entity $entity, SaveOptions $options): void
    {
        if ($options->get(SaveOption::SILENT)) {
            return;
        }

        $this->processor->afterSave($entity, $options->toAssoc());
    }

    public function afterRemove(Entity $entity, RemoveOptions $options): void
    {
        if ($options->get(SaveOption::SILENT)) {
            return;
        }

        $this->processor->afterRemove($entity, $options);
    }

    public function afterRelate(
        Entity $entity,
        string $relationName,
        Entity $relatedEntity,
        array $columnData,
        RelateOptions $options
    ): void {

        if ($options->get(SaveOption::SILENT)) {
            return;
        }

        $this->processor->afterRelate($entity, $relatedEntity, $relationName, $options->toAssoc());
    }

    public function afterUnrelate(
        Entity $entity,
        string $relationName,
        Entity $relatedEntity,
        UnrelateOptions $options
    ): void {

        if ($options->get(SaveOption::SILENT)) {
            return;
        }

        $this->processor->afterUnrelate($entity, $relatedEntity, $relationName, $options->toAssoc());
    }
}
