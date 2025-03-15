<?php
//FORAI:F2700;DEF[C2296:ScopeCheckerDataTest<TestCase>,F11281:setUp,F11282:testCheckerData0,F11283:testCheckerData1,F11284:testCheckerData2,F11285:testCheckerData3];IMP[F910:C693];EXP[C2296,F11281,F11282,F11283,F11284,F11285];LANG[php]//

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

namespace tests\unit\Espo\Core\Acl;

use Espo\Core\Acl\AccessChecker\ScopeCheckerData;
use PHPUnit\Framework\TestCase;

class ScopeCheckerDataTest extends TestCase
{
    protected function setUp() : void
    {
    }

    public function testCheckerData0()
    {
        $checkerData = ScopeCheckerData
            ::createBuilder()
            ->build();

        $this->assertFalse($checkerData->isOwn());
        $this->assertFalse($checkerData->inTeam());
        $this->assertFalse($checkerData->isShared());
    }

    public function testCheckerData1()
    {
        $checkerData = ScopeCheckerData
            ::createBuilder()
            ->setIsOwn(true)
            ->setInTeam(true)
            ->setIsShared(true)
            ->build();

        $this->assertTrue($checkerData->isOwn());
        $this->assertTrue($checkerData->inTeam());
        $this->assertTrue($checkerData->isShared());
    }

    public function testCheckerData2()
    {
        $checkerData = ScopeCheckerData
            ::createBuilder()
            ->setIsOwn(false)
            ->setInTeamChecker(
                function (): bool {
                    return true;
                }
            )
            ->setIsSharedChecker(
                function (): bool {
                    return true;
                }
            )
            ->build();

        $this->assertFalse($checkerData->isOwn());
        $this->assertTrue($checkerData->inTeam());
        $this->assertTrue($checkerData->isShared());
    }

    public function testCheckerData3()
    {
        $checkerData = ScopeCheckerData
            ::createBuilder()
            ->setIsOwnChecker(
                function (): bool {
                    return true;
                }
            )
            ->setInTeamChecker(
                function (): bool {
                    return false;
                }
            )
            ->setIsSharedChecker(
                function (): bool {
                    return false;
                }
            )
            ->build();

        $this->assertTrue($checkerData->isOwn());
        $this->assertFalse($checkerData->inTeam());
        $this->assertFalse($checkerData->isShared());
    }
}
