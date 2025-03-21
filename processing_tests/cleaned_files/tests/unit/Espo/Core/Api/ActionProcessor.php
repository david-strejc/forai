<?php
//FORAI:F2830;DEF[C2426:ActionProcessor,F12394:setUp,F12395:testAction1];IMP[F2601:C2209];EXP[C2426,F12394,F12395];LANG[php]//

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

namespace tests\unit\Espo\Core\Api;

use Espo\Core\{
    Utils\ClassFinder,
    InjectableFactory,
    Api\ControllerActionProcessor,
    Api\RequestWrapper,
    Api\ResponseWrapper,
};

use tests\unit\testClasses\Controllers\TestController;

class ActionProcessor extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $this->classFinder = $this->createMock(ClassFinder::class);
        $this->injectableFactory = $this->createMock(InjectableFactory::class);
        $this->request = $this->createMock(RequestWrapper::class);
        $this->response = $this->createMock(ResponseWrapper::class);

        $this->actionProcessor = new ActionProcessor($this->injectableFactory, $this->classFinder);
    }

    public function testAction1()
    {
        $controller = $this->getMockBuilder(TestController::class)->disableOriginalConstructor()->getMock();

        $this->classFinder
            ->expects($this->once())
            ->method('find')
            ->with('Controllers', 'Test')
            ->willReturn(TestController::class);

        $this->request
            ->expects($this->once())
            ->method('getMethod')
            ->willReturn('POST');

        $this->injectableFactory
            ->expects($this->once())
            ->method('createWith')
            ->with(TestController::class, ['name' => 'Test'])
            ->willReturn($controller);

        $controller
            ->expects($this->once())
            ->method('postActionHello')
            ->with($this->request, $this->response);

        $this->actionProcessor->process('Test', 'hello', $this->request, $this->response);
    }
}
