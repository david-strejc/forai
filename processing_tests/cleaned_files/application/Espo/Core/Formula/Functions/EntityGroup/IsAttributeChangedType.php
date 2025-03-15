<?php
//FORAI:F1292;DEF[C1050:IsAttributeChangedType,F5406:process,F5407:check];IMP[F931:C709];EXP[C1050,F5406,F5407];LANG[php]//

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

namespace Espo\Core\Formula\Functions\EntityGroup;

use Espo\Core\Exceptions\Error;

class IsAttributeChangedType extends \Espo\Core\Formula\Functions\Base
{
    /**
     * @return bool
     * @throws Error
     * @throws \Espo\Core\Formula\Exceptions\Error
     */
    public function process(\stdClass $item)
    {
        if (count($item->value) < 1) {
            throw new Error("isAttributeChanged: too few arguments.");
        }

        $attribute = $this->evaluate($item->value[0]);

        return $this->check($attribute);
    }

    /**
     * @param string $attribute
     * @return bool
     * @throws Error
     */
    protected function check($attribute)
    {
        return $this->getEntity()->isAttributeChanged($attribute);
    }
}
