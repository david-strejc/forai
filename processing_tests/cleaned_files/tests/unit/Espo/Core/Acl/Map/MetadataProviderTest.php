<?php
//FORAI:F2702;DEF[C2299:MetadataProviderTest<TestCase>,F11300:setUp,F11301:testGetPermissionList];IMP[F890:C677,F858:C666,F1665:C1390];EXP[C2299,F11300,F11301];LANG[php]//

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

namespace tests\unit\Espo\Core\Acl\Map;

use Espo\Core\Acl\Map\MetadataProvider;
use Espo\Core\Acl\Permission;
use Espo\Core\Utils\Metadata;
use PHPUnit\Framework\TestCase;

class MetadataProviderTest extends TestCase
{
    private $metadata;

    protected function setUp(): void
    {
        $this->metadata = $this->createMock(Metadata::class);
    }

    public function testGetPermissionList(): void
    {
        $provider = new MetadataProvider($this->metadata);

        $this->metadata
            ->expects($this->once())
            ->method('get')
            ->with(['app', 'acl', 'valuePermissionList'])
            ->willReturn(['assignmentPermission', 'portalPermission']);

        $this->assertEquals([Permission::ASSIGNMENT, 'portal'], $provider->getPermissionList());
    }
}
