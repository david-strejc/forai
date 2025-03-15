<?php
//FORAI:F334;DEF[C190:EmptyHookMediator,F1194:beforeSave,F1195:afterSave,F1196:beforeRemove,F1197:afterRemove,F1198:beforeRelate,F1199:afterRelate,F1200:beforeUnrelate,F1201:afterUnrelate,F1202:beforeMassRelate,F1203:afterMassRelate];IMP[F377:C220];EXP[C190,F1194,F1195,F1196,F1197,F1198,F1199,F1200,F1201,F1202,F1203];LANG[php]//

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

class EmptyHookMediator implements HookMediator
{
    public function beforeSave(Entity $entity, array $options): void
    {}

    public function afterSave(Entity $entity, array $options): void
    {}

    public function beforeRemove(Entity $entity, array $options): void
    {}

    public function afterRemove(Entity $entity, array $options): void
    {}

    public function beforeRelate(Entity $entity, string $relationName, Entity $foreignEntity, ?array $columnData, array $options): void
    {}

    public function afterRelate(Entity $entity, string $relationName, Entity $foreignEntity, ?array $columnData, array $options): void
    {}

    public function beforeUnrelate(Entity $entity, string $relationName, Entity $foreignEntity, array $options): void
    {}

    public function afterUnrelate(Entity $entity, string $relationName, Entity $foreignEntity, array $options): void
    {}

    public function beforeMassRelate(Entity $entity, string $relationName, Select $query, array $options): void
    {}

    public function afterMassRelate(Entity $entity, string $relationName, Select $query, array $options): void
    {}
}
