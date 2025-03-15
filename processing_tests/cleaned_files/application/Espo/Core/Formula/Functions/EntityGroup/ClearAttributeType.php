<?php
//FORAI:F1285;DEF[C1042:ClearAttributeType<BaseFunction>,F5399:process];IMP[F1122:C881,F1133:C891,F1130:C888,F1155:C913];EXP[C1042,F5399];LANG[php]//

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

use Espo\Core\Formula\ArgumentList;
use Espo\Core\Formula\Exceptions\BadArgumentType;
use Espo\Core\Formula\Exceptions\TooFewArguments;
use Espo\Core\Formula\Functions\BaseFunction;

/**
 * @noinspection PhpUnused
 */
class ClearAttributeType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        if (count($args) < 1) {
            throw TooFewArguments::create(1);
        }

        $args = $this->evaluate($args);

        $attribute = $args[0];

        if (!is_string($attribute)) {
            throw BadArgumentType::create(1, 'string');
        }

        $this->getEntity()->clear($attribute);

        return null;
    }
}
