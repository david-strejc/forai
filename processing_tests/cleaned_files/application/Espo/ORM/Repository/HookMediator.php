<?php
//FORAI:F339;DEF[F1307:beforeSave,F1308:afterSave,F1309:beforeRemove,F1310:afterRemove,F1311:beforeRelate,F1312:afterRelate,F1313:beforeUnrelate,F1314:afterUnrelate,F1315:beforeMassRelate,F1316:afterMassRelate];IMP[F377:C220];EXP[F1307,F1308,F1309,F1310,F1311,F1312,F1313,F1314,F1315,F1316];LANG[php]//

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

namespace Espo\ORM\Repository;

use Espo\ORM\Entity;
use Espo\ORM\Query\Select;

interface HookMediator
{
    /**
     * @param array<string, mixed> $options
     */
    public function beforeSave(Entity $entity, array $options): void;

    /**
     * @param array<string, mixed> $options
     */
    public function afterSave(Entity $entity, array $options): void;

    /**
     * @param array<string, mixed> $options
     */
    public function beforeRemove(Entity $entity, array $options): void;

    /**
     * @param array<string, mixed> $options
     */
    public function afterRemove(Entity $entity, array $options): void;

    /**
     * @param array<string, mixed>|null $columnData Role values.
     * @param array<string, mixed> $options
     */
    public function beforeRelate(
        Entity $entity,
        string $relationName,
        Entity $foreignEntity,
        ?array $columnData,
        array $options
    ): void;

    /**
     * @param array<string, mixed>|null $columnData Role values.
     * @param array<string, mixed> $options
     */
    public function afterRelate(
        Entity $entity,
        string $relationName,
        Entity $foreignEntity,
        ?array $columnData,
        array $options
    ): void;

    /**
     * @param array<string, mixed> $options
     */
    public function beforeUnrelate(Entity $entity, string $relationName, Entity $foreignEntity, array $options): void;

    /**
     * @param array<string, mixed> $options
     */
    public function afterUnrelate(Entity $entity, string $relationName, Entity $foreignEntity, array $options): void;

    /**
     * @param array<string, mixed> $options
     */
    public function beforeMassRelate(Entity $entity, string $relationName, Select $query, array $options): void;

    /**
     * @param array<string, mixed> $options
     */
    public function afterMassRelate(Entity $entity, string $relationName, Select $query, array $options): void;
}
