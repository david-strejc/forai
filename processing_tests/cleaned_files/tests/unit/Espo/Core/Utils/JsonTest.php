<?php
//FORAI:F2781;DEF[C2379:JsonTest,F12021:setUp,F12022:tearDown,F12023:testDecodeBad1,F12024:testEncode,F12025:testDecode];IMP[F1643:C1365];EXP[C2379,F12021,F12022,F12023,F12024,F12025];LANG[php]//

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

namespace tests\unit\Espo\Core\Utils;

use Espo\Core\Utils\Json;

use JsonException;

class JsonTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
    }

    protected function tearDown() : void
    {
    }

    public function testDecodeBad1()
    {
        $value = '{';

        $this->expectException(JsonException::class);

        Json::decode($value);
    }

    public function testEncode()
    {
        $testVal = ['testOption' => 'Test'];

        $this->assertEquals(json_encode($testVal), Json::encode($testVal));
    }

    public function testDecode()
    {
        $value = ['testOption' => 'Test'];

        $this->assertEquals($value, Json::decode(json_encode($value), true));

        $test = '{"folder":"data\/logs"}';

        $this->assertEquals('data/logs', Json::decode($test)->folder);
    }
}
