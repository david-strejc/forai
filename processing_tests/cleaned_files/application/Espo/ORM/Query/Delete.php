<?php
//FORAI:F371;DEF[C215:Delete,F1467:getFrom,F1468:getFromAlias,F1469:getLimit,F1470:validateRawParams];IMP[];EXP[C215,F1467,F1468,F1469,F1470];LANG[php]//

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

namespace Espo\ORM\Query;

use RuntimeException;

/**
 * Delete parameters.
 *
 * @immutable
 */
class Delete implements Query
{
    use SelectingTrait;
    use BaseTrait;

    /**
     * Get an entity type.
     */
    public function getFrom(): string
    {
        return $this->params['from'];
    }

    /**
     * Get a from-alias
     */
    public function getFromAlias(): ?string
    {
        return $this->params['fromAlias'] ?? null;
    }

    /**
     * Get a LIMIT.
     */
    public function getLimit(): ?int
    {
        return $this->params['limit'] ?? null;
    }

    /**
     * @param array<string, mixed> $params
     */
    private function validateRawParams(array $params): void
    {
        $this->validateRawParamsSelecting($params);

        $from = $params['from'] ?? null;

        if (!$from || !is_string($from)) {
            throw new RuntimeException("Select params: Missing 'from'.");
        }
    }
}
