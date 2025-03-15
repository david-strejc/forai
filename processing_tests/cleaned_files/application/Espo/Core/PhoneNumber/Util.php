<?php
//FORAI:F855;DEF[C663:Util,F4012:splitExtension];IMP[];EXP[C663,F4012];LANG[php]//

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

namespace Espo\Core\PhoneNumber;

class Util
{
    /**
     * @internal Do not use in custom code.
     * @return array{string, ?string}
     */
    public static function splitExtension(string $value): array
    {
        $ext = null;

        $delimiters = [
            'ext.',
            'x.',
            'x',
            '#',
        ];

        foreach ($delimiters as $delimiter) {
            $index = strrpos($value, $delimiter);

            if ($index === false || $index < 2) {
                continue;
            }

            $ext = trim(substr($value, $index + strlen($delimiter)));
            $value = trim(substr($value, 0, $index));

            break;
        }

        return [$value, $ext];
    }
}
