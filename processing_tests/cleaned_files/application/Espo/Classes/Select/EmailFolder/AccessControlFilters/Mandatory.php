<?php
//FORAI:F2159;DEF[C1775:Mandatory,F9324:__construct,F9325:apply];IMP[F369:C213];EXP[C1775,F9325];LANG[php]//

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

namespace Espo\Classes\Select\EmailFolder\AccessControlFilters;

use Espo\ORM\Query\SelectBuilder;
use Espo\Core\Select\AccessControl\Filter;
use Espo\Entities\User;

class Mandatory implements Filter
{
    public function __construct(private User $user)
    {}

    public function apply(SelectBuilder $queryBuilder): void
    {
        if ($this->user->isAdmin()) {
            return;
        }

        $queryBuilder->where([
            'assignedUserId' => $this->user->getId(),
        ]);
    }
}
