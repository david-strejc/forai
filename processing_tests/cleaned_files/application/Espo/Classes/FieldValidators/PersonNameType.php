<?php
//FORAI:F2262;DEF[C1875:PersonNameType,F9523:__construct,F9524:checkRequired];IMP[F1642:C1364];EXP[C1875,F9524];LANG[php]//

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

namespace Espo\Classes\FieldValidators;

use Espo\ORM\Entity;
use Espo\Core\Utils\FieldUtil;

class PersonNameType
{
    public function __construct(private FieldUtil $fieldUtil)
    {}

    public function checkRequired(Entity $entity, string $field): bool
    {
        $isEmpty = true;

        $attributeList = $this->fieldUtil->getActualAttributeList($entity->getEntityType(), $field);

        foreach ($attributeList as $attribute) {
            if ($attribute === 'salutation' . ucfirst($field)) {
                continue;
            }

            if ($entity->has($attribute) && $entity->get($attribute) !== '') {
                $isEmpty = false;

                break;
            }
        }

        if ($isEmpty) {
            return false;
        }

        return true;
    }
}
