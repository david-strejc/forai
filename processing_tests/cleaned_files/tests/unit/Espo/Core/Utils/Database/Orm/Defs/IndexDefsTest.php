<?php
//FORAI:F2797;DEF[C2396:IndexDefsTest<TestCase>,F12112:testWithParamsMerged,F12113:testToAssoc,F12114:testUnique,F12115:testWithFlag];IMP[F1773:C1487];EXP[C2396,F12112,F12113,F12114,F12115];LANG[php]//

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

namespace tests\unit\Espo\Core\Utils\Database\Orm\Defs;

use Espo\Core\Utils\Database\Orm\Defs\IndexDefs;
use PHPUnit\Framework\TestCase;

class IndexDefsTest extends TestCase
{
    public function testWithParamsMerged(): void
    {
        $defs = IndexDefs::create('test')
            ->withParamsMerged([
                'a' => 'a',
                'b' => 'b',
            ])
            ->withParamsMerged([
                'b' => 'mb',
                'c' => 'mc',
            ])
            ->withParam('e', 'e');

        $this->assertEquals('a', $defs->getParam('a'));
        $this->assertEquals('mb', $defs->getParam('b'));
        $this->assertEquals('mc', $defs->getParam('c'));
        $this->assertEquals('e', $defs->getParam('e'));

        $this->assertTrue($defs->hasParam('c'));
        $this->assertFalse($defs->hasParam('d'));
    }

    public function testToAssoc(): void
    {
        $params = [
            'a' => 'a',
            'b' => 'b',
        ];

        $defs = IndexDefs::create('test')
            ->withParamsMerged($params);

        $this->assertEquals($params, $defs->toAssoc());
    }

    public function testUnique(): void
    {
        $defs = IndexDefs::create('test')
            ->withUnique();

        $this->assertEquals(['type' => 'unique'], $defs->toAssoc());

        $defs = IndexDefs::create('test')
            ->withoutUnique();

        $this->assertEquals([], $defs->toAssoc());
    }

    public function testWithFlag(): void
    {
        $defs = IndexDefs::create('test')
            ->withFlag('fulltext');

        $this->assertEquals(['flags' => ['fulltext']], $defs->toAssoc());

        $defs = IndexDefs::create('test')
            ->withoutFlag('fulltext');

        $this->assertEquals([], $defs->toAssoc());
    }
}
