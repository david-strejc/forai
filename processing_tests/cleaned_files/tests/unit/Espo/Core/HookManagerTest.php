<?php
//FORAI:F2694;DEF[C2291:HookManagerTest<TestCase>,F11235:setUp,F11236:initPathProvider,F11237:testHookExists,F11238:testSortHooks,F11239:testCase1CustomHook,F11240:testCase2ModuleHook1,F11241:testCase2ModuleHookReverseModuleOrder,F11242:testCase3CoreHook];IMP[F2600:C2210,F1485:C1225,F847:C652,F846:C649,F1662:C1385,F1641:C1363,F1656:C1380,F1665:C1390,F1673:C1395];EXP[C2291,F11235,F11236,F11237,F11238,F11239,F11240,F11241,F11242];LANG[php]//

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

namespace tests\unit\Espo\Core;

use PHPUnit\Framework\TestCase;
use tests\unit\ReflectionHelper;

use Espo\Core\Hook\GeneralInvoker;
use Espo\Core\HookManager;
use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\DataCache;
use Espo\Core\Utils\File\Manager as FileManager;
use Espo\Core\Utils\Log;
use Espo\Core\Utils\Metadata;
use Espo\Core\Utils\Module\PathProvider;

class HookManagerTest extends TestCase
{
    private $hookManager;

    private $filesPath = 'tests/unit/testData/Hooks';

    private ?Config\SystemConfig $systemConfig = null;

    protected function setUp(): void
    {
        $this->metadata = $this->createMock(Metadata::class);
            $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();

        $this->systemConfig = $this->createMock(Config\SystemConfig::class);

        $this->injectableFactory = $this->createMock(InjectableFactory::class);
        $this->dataCache = $this->createMock(DataCache::class);
        $this->fileManager = new FileManager();
        $this->pathProvider = $this->createMock(PathProvider::class);
        $this->generalInvoker = $this->createMock(GeneralInvoker::class);

        $this->hookManager = new HookManager(
            $this->injectableFactory,
            $this->fileManager,
            $this->metadata,
            $this->dataCache,
            $this->createMock(Log::class),
            $this->pathProvider,
            $this->generalInvoker,
            $this->systemConfig,
        );

        $this->reflection = new ReflectionHelper($this->hookManager);
    }

    private function initPathProvider(string $folder): void
    {
        $this->pathProvider
            ->method('getCustom')
            ->willReturn($this->filesPath . '/' . $folder . '/custom/Espo/Custom/');

        $this->pathProvider
            ->method('getCore')
            ->willReturn($this->filesPath . '/' . $folder . '/application/Espo/');

        $this->pathProvider
            ->method('getModule')
            ->willReturnCallback(
                function (?string $moduleName) use ($folder): string {
                    $path = $this->filesPath . '/' . $folder . '/application/Espo/Modules/{*}/';

                    if ($moduleName === null) {
                        return $path;
                    }

                    return str_replace('{*}', $moduleName, $path);
                }
            );
    }

    public function testHookExists(): void
    {
        $data = array (
            'Espo\\Hooks\\Note\\Stream' => 8,
            'Espo\\Hooks\\Note\\Mentions' => 9,
            'Espo\\Hooks\\Note\\Notifications' => 14,
        );

        $data = array (
          array (
            'className' => 'Espo\\Hooks\\Note\\Stream',
            'order' => 8,
          ),
          array (
            'className' => 'Espo\\Hooks\\Note\\Mentions',
            'order' => 9,
          ),
          array (
            'className' => 'Espo\\Hooks\\Note\\Notifications',
            'order' => 14,
          ),
        );

        $this->assertTrue(
            $this->reflection->invokeMethod('hookExists', array('Espo\\Hooks\\Note\\Mentions', $data))
        );
        $this->assertTrue(
            $this->reflection->invokeMethod('hookExists', array('Espo\\Modules\\Crm\\Hooks\\Note\\Mentions', $data))
        );
        $this->assertTrue(
            $this->reflection->invokeMethod('hookExists', array('Espo\\Modules\\Test\\Hooks\\Note\\Mentions', $data))
        );
        $this->assertTrue(
            $this->reflection->invokeMethod('hookExists', array('Espo\\Modules\\Test\\Hooks\\Common\\Stream', $data))
        );
        $this->assertFalse(
            $this->reflection->invokeMethod('hookExists', array('Espo\\Hooks\\Note\\TestHook', $data))
        );
    }

    public function testSortHooks()
    {
        $data = array (
            'Common' =>
            array (
              'afterSave' =>
              array (
                array (
                    'className' => 'Espo\\Hooks\\Common\\AssignmentEmailNotification',
                    'order' => 9,
                ),
                array (
                    'className' => 'Espo\\Hooks\\Common\\Notifications',
                    'order' => 10,
                ),
                array (
                    'className' => 'Espo\\Hooks\\Common\\Stream',
                    'order' => 9,
                ),
              ),
              'beforeSave' =>
              array (
                array (
                    'className' => 'Espo\\Hooks\\Common\\Formula',
                    'order' => 5,
                ),
                array (
                    'className' => 'Espo\\Hooks\\Common\\NextNumber',
                    'order' => 10,
                ),
                array (
                    'className' => 'Espo\\Hooks\\Common\\CurrencyConverted',
                    'order' => 1,
                ),
              ),
            ),
            'Note' =>
            array (
              'beforeSave' =>
              array (
                array (
                    'className' => 'Espo\\Hooks\\Note\\Mentions',
                    'order' => 9,
                ),
              ),
              'afterSave' =>
              array (
                array (
                    'className' => 'Espo\\Hooks\\Note\\Notifications',
                    'order' => 14,
                ),
              ),
            ),
        );

        $result = array (
          'Common' =>
          array (
            'afterSave' =>
            array (
                array (
                    'className' => 'Espo\\Hooks\\Common\\AssignmentEmailNotification',
                    'order' => 9,
                ),
                array (
                    'className' => 'Espo\\Hooks\\Common\\Stream',
                    'order' => 9,
                ),
                array (
                    'className' => 'Espo\\Hooks\\Common\\Notifications',
                    'order' => 10,
                ),
            ),
            'beforeSave' =>
            array (
                array (
                    'className' => 'Espo\\Hooks\\Common\\CurrencyConverted',
                    'order' => 1,
                ),
                array (
                    'className' => 'Espo\\Hooks\\Common\\Formula',
                    'order' => 5,
                ),
                array (
                    'className' => 'Espo\\Hooks\\Common\\NextNumber',
                    'order' => 10,
                ),
            ),
          ),
          'Note' =>
          array (
            'beforeSave' =>
            array (
                array (
                    'className' => 'Espo\\Hooks\\Note\\Mentions',
                    'order' => 9,
                ),
            ),
            'afterSave' =>
            array (
                array (
                    'className' => 'Espo\\Hooks\\Note\\Notifications',
                    'order' => 14,
                ),
            ),
          ),
        );

        $this->assertEquals($result, $this->reflection->invokeMethod('sortHooks', array($data)) );
    }

    public function testCase1CustomHook()
    {
        $this->initPathProvider('testCase1');

        $this->systemConfig
            ->expects($this->exactly(2))
            ->method('useCache')
            ->will($this->returnValue(false));

        $this->metadata
            ->expects($this->once())
            ->method('getModuleList')
            ->will($this->returnValue(array(
                'Crm',
                'Test',
            )));

        $this->reflection->invokeMethod('loadHooks');

        $result = array (
          'Note' =>
          array (
            'beforeSave' =>
            [
                [
                    'className' => 'tests\\unit\\testData\\Hooks\\testCase1\\custom\\Espo\\Custom\\Hooks\\Note\\Mentions',
                    'order' => 7,
                ],
            ],
          ),
        );

        $this->assertEquals($result, $this->reflection->getProperty('data'));
    }

    public function testCase2ModuleHook1()
    {
        $this->initPathProvider('testCase2');

        $this->systemConfig
            ->expects($this->exactly(2))
            ->method('useCache')
            ->will($this->returnValue(false));

        $this->metadata
            ->expects($this->once())
            ->method('getModuleList')
            ->will($this->returnValue(array(
                'Crm',
                'Test',
            )));

        $this->reflection->invokeMethod('loadHooks');

        $result = array (
          'Note' =>
          array (
            'beforeSave' =>
            array (
                array (
                    'className' =>
                    'tests\\unit\\testData\\Hooks\\testCase2\\application\\Espo\\Modules\\Crm\\Hooks\\Note\\Mentions',
                    'order' => 9,
                ),
            ),
          ),
        );

        $this->assertEquals($result, $this->reflection->getProperty('data'));
    }

    public function testCase2ModuleHookReverseModuleOrder()
    {
        $this->initPathProvider('testCase2');

        $this->systemConfig
            ->expects($this->exactly(2))
            ->method('useCache')
            ->will($this->returnValue(false));

        $this->metadata
            ->expects($this->once())
            ->method('getModuleList')
            ->will($this->returnValue(array(
                'Test',
                'Crm',
            )));

        $this->reflection->invokeMethod('loadHooks');

        $result = array (
          'Note' =>
          array (
            'beforeSave' =>
            array (
                array (
                    'className' =>
                        'tests\\unit\\testData\\Hooks\\testCase2\\application\\Espo\\Modules\\Test\\Hooks\\Note\\Mentions',
                    'order' => 9,
                ),
            ),
          ),
        );

        $this->assertEquals($result, $this->reflection->getProperty('data'));
    }

    public function testCase3CoreHook()
    {
        $this->initPathProvider('testCase3');

        $this->systemConfig
            ->expects($this->exactly(2))
            ->method('useCache')
            ->will($this->returnValue(false));

        $this->metadata
            ->expects($this->once())
            ->method('getModuleList')
            ->will($this->returnValue(array(
            )));

        $this->reflection->invokeMethod('loadHooks');

        $result = array (
          'Note' =>
          array (
            'beforeSave' =>
            array (
                array (
                    'className' => 'tests\\unit\\testData\\Hooks\\testCase3\\application\\Espo\\Hooks\\Note\\Mentions',
                    'order' => 9,
                ),
            ),
          ),
        );

        $this->assertEquals($result, $this->reflection->getProperty('data'));
    }
}
