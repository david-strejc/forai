<?php
//FORAI:F2519;DEF[C2128:Actual,F10514:__construct,F10515:apply];IMP[F369:C213,F380:C223,F1665:C1390];EXP[C2128,F10515];LANG[php]//

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

namespace Espo\Modules\Crm\Classes\Select\Task\PrimaryFilters;

use Espo\ORM\Query\SelectBuilder;
use Espo\ORM\Query\Part\Condition as Cond;

use Espo\Core\Select\Primary\Filter;
use Espo\Core\Utils\Metadata;

class Actual implements Filter
{
    public const NAME = 'actual';

    public function __construct(private Metadata $metadata)
    {}

    public function apply(SelectBuilder $queryBuilder): void
    {
        $notActualStatusList = $this->metadata
            ->get(['entityDefs', 'Task', 'fields', 'status', 'notActualOptions']) ?? [];

        $queryBuilder->where(
            Cond::notIn(
                Cond::column('status'),
                $notActualStatusList
            )
        );
    }
}
