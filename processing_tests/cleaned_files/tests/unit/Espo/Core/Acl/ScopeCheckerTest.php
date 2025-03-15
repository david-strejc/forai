<?php
//FORAI:F2696;DEF[C2293:ScopeCheckerTest,F11247:setUp,F11248:testCheckerNoData1,F11249:testCheckerNoData2,F11250:testCheckerNoData3,F11251:testCheckerNoData4,F11252:testCheckerNoData5,F11253:testCheckerActionNoData1,F11254:testCheckerActionNoData2,F11255:testCheckerData1,F11256:testCheckerData2,F11257:testCheckerData3,F11258:testCheckerData4,F11259:testCheckerData5,F11260:testCheckerData6,F11261:testCheckerData7,F11262:testCheckerData8,F11263:testCheckerData9,F11264:testCheckerData10,F11265:testCheckerData11,F11266:testCheckerData12,F11267:testCheckerData13,F11268:testCheckerDataNoAction1,F11269:testCheckerDataNoAction2,F11270:testCheckerDataNoAction3,F11271:testCheckerDataNoAction4];IMP[];EXP[C2293,F11247,F11248,F11249,F11250,F11251,F11252,F11253,F11254,F11255,F11256,F11257,F11258,F11259,F11260,F11261,F11262,F11263,F11264,F11265,F11266,F11267,F11268,F11269,F11270,F11271];LANG[php]//

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

use Espo\Core\{
    Acl\AccessChecker\ScopeChecker,
    Acl\AccessChecker\ScopeCheckerData,
    Acl\ScopeData,
    Acl\Table,
};

class ScopeCheckerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ScopeChecker
     */
    private $scopeChecker;

    protected function setUp() : void
    {
        $this->scopeChecker = new ScopeChecker();
    }

    public function testCheckerNoData1()
    {
        $data = ScopeData::fromRaw(false);

        $result = $this->scopeChecker->check($data);

        $this->assertEquals(false, $result);
    }

    public function testCheckerNoData2()
    {
        $data = ScopeData::fromRaw(true);

        $result = $this->scopeChecker->check($data);

        $this->assertEquals(true, $result);
    }

    public function testCheckerNoData3()
    {
        $data = ScopeData::fromRaw(
            (object) [
                Table::ACTION_READ => Table::LEVEL_NO,
            ],
        );

        $result = $this->scopeChecker->check($data);

        $this->assertEquals(true, $result);
    }

    public function testCheckerNoData4()
    {
        $data = ScopeData::fromRaw(
            (object) [
                Table::ACTION_READ => Table::LEVEL_ALL,
            ],
        );

        $result = $this->scopeChecker->check($data);

        $this->assertEquals(true, $result);
    }

    public function testCheckerNoData5()
    {
        $data = ScopeData::fromRaw(
            (object) [
                Table::ACTION_READ => Table::LEVEL_TEAM,
            ],
        );

        $result = $this->scopeChecker->check($data);

        $this->assertEquals(true, $result);
    }

    public function testCheckerActionNoData1()
    {
        $data = ScopeData::fromRaw(false);

        $result = $this->scopeChecker->check($data, Table::ACTION_CREATE);

        $this->assertEquals(false, $result);
    }

    public function testCheckerActionNoData2()
    {
        $data = ScopeData::fromRaw(true);

        $result = $this->scopeChecker->check($data, Table::ACTION_CREATE);

        $this->assertEquals(true, $result);
    }

    public function testCheckerData1()
    {
        $checkerData = ScopeCheckerData
            ::createBuilder()
            ->setIsOwn(false)
            ->setInTeam(false)
            ->build();

        $data = ScopeData::fromRaw(
            (object) [
                Table::ACTION_READ => Table::LEVEL_ALL,
            ],
        );

        $result = $this->scopeChecker->check($data, Table::ACTION_READ, $checkerData);

        $this->assertEquals(true, $result);
    }

    public function testCheckerData2()
    {
        $checkerData = ScopeCheckerData
            ::createBuilder()
            ->setIsOwn(false)
            ->setInTeam(false)
            ->build();

        $data = ScopeData::fromRaw(
            (object) [
                Table::ACTION_READ => Table::LEVEL_TEAM,
            ],
        );

        $result = $this->scopeChecker->check($data, Table::ACTION_READ, $checkerData);

        $this->assertEquals(false, $result);
    }

    public function testCheckerData3()
    {
        $checkerData = ScopeCheckerData
            ::createBuilder()
            ->setIsOwn(false)
            ->setInTeam(false)
            ->build();

        $data = ScopeData::fromRaw(
            (object) [
                Table::ACTION_READ => Table::LEVEL_OWN,
            ],
        );

        $result = $this->scopeChecker->check($data, Table::ACTION_READ, $checkerData);

        $this->assertEquals(false, $result);
    }

    public function testCheckerData4()
    {
        $checkerData = ScopeCheckerData
            ::createBuilder()
            ->setIsOwn(false)
            ->setInTeam(false)
            ->build();

        $data = ScopeData::fromRaw(
            (object) [
                Table::ACTION_READ => Table::LEVEL_NO,
            ],
        );

        $result = $this->scopeChecker->check($data, Table::ACTION_READ, $checkerData);

        $this->assertEquals(false, $result);
    }

    public function testCheckerData5()
    {
        $checkerData = ScopeCheckerData
            ::createBuilder()
            ->setIsOwn(false)
            ->setInTeam(true)
            ->build();

        $data = ScopeData::fromRaw(
            (object) [
                Table::ACTION_READ => Table::LEVEL_OWN,
            ],
        );

        $result = $this->scopeChecker->check($data, Table::ACTION_READ, $checkerData);

        $this->assertEquals(false, $result);
    }

    public function testCheckerData6()
    {
        $checkerData = ScopeCheckerData
            ::createBuilder()
            ->setIsOwn(false)
            ->setInTeam(true)
            ->build();

        $data = ScopeData::fromRaw(
            (object) [
                Table::ACTION_READ => Table::LEVEL_TEAM,
            ],
        );

        $result = $this->scopeChecker->check($data, Table::ACTION_READ, $checkerData);

        $this->assertEquals(true, $result);
    }

    public function testCheckerData7()
    {
        $checkerData = ScopeCheckerData
            ::createBuilder()
            ->setIsOwn(true)
            ->setInTeam(true)
            ->build();

        $data = ScopeData::fromRaw(
            (object) [
                Table::ACTION_READ => Table::LEVEL_TEAM,
            ],
        );

        $result = $this->scopeChecker->check($data, Table::ACTION_READ, $checkerData);

        $this->assertEquals(true, $result);
    }

    public function testCheckerData8()
    {
        $checkerData = ScopeCheckerData
            ::createBuilder()
            ->setIsOwn(false)
            ->setInTeam(false)
            ->build();

        $data = ScopeData::fromRaw(
            (object) [
                Table::ACTION_CREATE => Table::LEVEL_YES,
            ],
        );

        $result = $this->scopeChecker->check($data, Table::ACTION_CREATE, $checkerData);

        $this->assertEquals(true, $result);
    }

    public function testCheckerData9()
    {
        $checkerData = ScopeCheckerData
            ::createBuilder()
            ->setIsOwn(true)
            ->setInTeam(false)
            ->build();

        $data = ScopeData::fromRaw(
            (object) [
                Table::ACTION_READ => Table::LEVEL_OWN,
            ],
        );

        $result = $this->scopeChecker->check($data, Table::ACTION_READ, $checkerData);

        $this->assertEquals(true, $result);
    }

    public function testCheckerData10()
    {
        $checkerData = ScopeCheckerData
            ::createBuilder()
            ->setIsOwn(true)
            ->setInTeam(true)
            ->build();

        $data = ScopeData::fromRaw(
            (object) [
                Table::ACTION_READ => Table::LEVEL_OWN,
            ],
        );

        $result = $this->scopeChecker->check($data, Table::ACTION_READ, $checkerData);

        $this->assertEquals(true, $result);
    }

    public function testCheckerData11()
    {
        $checkerData = ScopeCheckerData
            ::createBuilder()
            ->setIsOwn(true)
            ->setInTeam(false)
            ->build();

        $data = ScopeData::fromRaw(
            (object) [
                Table::ACTION_READ => Table::LEVEL_TEAM,
            ],
        );

        $result = $this->scopeChecker->check($data, Table::ACTION_READ, $checkerData);

        $this->assertEquals(true, $result);
    }

    public function testCheckerData12()
    {
        $checkerData = ScopeCheckerData
            ::createBuilder()
            ->setIsOwn(true)
            ->setInTeam(true)
            ->build();

        $data = ScopeData::fromRaw(
            (object) [],
        );

        $result = $this->scopeChecker->check($data, Table::ACTION_READ, $checkerData);

        $this->assertEquals(false, $result);
    }

    public function testCheckerData13()
    {
        $checkerData = ScopeCheckerData
            ::createBuilder()
            ->setIsOwn(false)
            ->setInTeam(false)
            ->build();

        $data = ScopeData::fromRaw(false);

        $result = $this->scopeChecker->check($data, Table::ACTION_READ, $checkerData);

        $this->assertEquals(false, $result);
    }

    public function testCheckerDataNoAction1()
    {
        $checkerData = ScopeCheckerData
            ::createBuilder()
            ->setIsOwn(true)
            ->setInTeam(true)
            ->build();

        $data = ScopeData::fromRaw(false);

        $result = $this->scopeChecker->check($data, null, $checkerData);

        $this->assertEquals(false, $result);
    }

    public function testCheckerDataNoAction2()
    {
        $checkerData = ScopeCheckerData
            ::createBuilder()
            ->setIsOwn(false)
            ->setInTeam(false)
            ->build();

        $data = ScopeData::fromRaw(true);

        $result = $this->scopeChecker->check($data, null, $checkerData);

        $this->assertEquals(true, $result);
    }

    public function testCheckerDataNoAction3()
    {
        $checkerData = ScopeCheckerData
            ::createBuilder()
            ->setIsOwn(true)
            ->setInTeam(true)
            ->build();

        $data = ScopeData::fromRaw(
            (object) [
                Table::ACTION_READ => Table::LEVEL_TEAM,
            ],
        );

        $result = $this->scopeChecker->check($data, null, $checkerData);

        $this->assertEquals(true, $result);
    }

    public function testCheckerDataNoAction4()
    {
        $checkerData = ScopeCheckerData
            ::createBuilder()
            ->setIsOwn(true)
            ->setInTeam(true)
            ->build();

        $data = ScopeData::fromRaw(
            (object) [
                Table::ACTION_CREATE => Table::LEVEL_NO,
                Table::ACTION_READ => Table::LEVEL_NO,
            ],
        );

        $result = $this->scopeChecker->check($data, null, $checkerData);

        $this->assertEquals(true, $result);
    }
}
