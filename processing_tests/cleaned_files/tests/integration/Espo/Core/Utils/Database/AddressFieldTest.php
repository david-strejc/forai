<?php
//FORAI:F2916;DEF[C2506:AddressFieldTest<Base>,F12678:fieldlist,F12679:testColumn];IMP[];EXP[C2506,F12678,F12679];LANG[php]//

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

namespace tests\integration\Espo\Core\Utils\Database;

class AddressFieldTest extends Base
{
    public function fieldlist()
    {
        return [
            ['testAddressStreet', 255],
            ['testAddressCity', 100],
            ['testAddressState', 100],
            ['testAddressCountry', 100],
            ['testAddressPostalCode', 40],
        ];
    }

    /**
     * @dataProvider fieldlist
     */
    public function testColumn($fieldName, $length)
    {
        $column = $this->getColumnInfo('Test', $fieldName);

        $this->assertNotEmpty($column);
        $this->assertEquals('varchar', $column['DATA_TYPE']);
        $this->assertEquals($length, $column['CHARACTER_MAXIMUM_LENGTH']);
        $this->assertEquals('YES', $column['IS_NULLABLE']);
        $this->assertEquals('utf8mb4_unicode_ci', $column['COLLATION_NAME']);
    }
}
