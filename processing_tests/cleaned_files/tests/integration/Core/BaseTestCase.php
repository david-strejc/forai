<?php
//FORAI:F2932;DEF[C2524:BaseTestCase<TestCase>,F12756:createApplication,F12757:setApplication,F12758:auth,F12759:getApplication,F12760:getContainer,F12761:getFileManager,F12762:getDataManager,F12763:getInjectableFactory,F12764:getMetadata,F12765:getEntityManager,F12766:getConfig,F12767:normalizePath,F12768:sendRequest,F12769:setUp,F12770:reCreateApplication,F12771:tearDown,F12772:createUser,F12773:beforeSetUp,F12774:beforeStartApplication,F12775:afterStartApplication,F12776:createRequest,F12777:createResponse,F12778:setData,F12779:fullReset];IMP[F2013:C1638,F851:C656,F853:C659,F854:C662,F846:C649,F1662:C1385,F1665:C1390];EXP[C2524,F12756,F12757,F12758,F12759,F12760,F12761,F12762,F12763,F12764,F12765,F12766,F12767,F12768,F12769,F12770,F12771,F12772,F12773,F12774,F12775,F12776,F12777,F12778,F12779];LANG[php]//

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

namespace tests\integration\Core;

use Espo\Core\Api\RequestWrapper;
use Espo\Core\Api\ResponseWrapper;
use Espo\Core\Application;
use Espo\Core\Container;
use Espo\Core\DataManager;
use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\File\Manager as FileManager;
use Espo\Core\Utils\Metadata;
use Espo\Entities\User;
use Espo\ORM\EntityManager;

use PHPUnit\Framework\TestCase;

use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;

abstract class BaseTestCase extends TestCase
{
    private ?Tester $espoTester = null;
    private ?Application $espoApplication = null;
    private ?string $authenticationMethod = null;

    /** Path to file with data. */
    protected ?string $dataFile = null;
    /** Path to files which needs to be copied. */
    protected ?string $pathToFiles = null;
    /** Username used for authentication. */
    protected ?string $userName = null;
    /** Password used for authentication. */
    protected ?string $password = null;
    /**
     * @var ?array{
     *     entities?: array<string, array<string, mixed>>,
     *     files?: string,
     *     config?: array<string, mixed>,
     *     preferences?: array<string, mixed>,
     * }
     */
    protected $initData = null;

    protected function createApplication(bool $clearCache = true, ?string $portalId = null): Application
    {
        return $this->espoTester->getApplication(true, $clearCache, $portalId);
    }

    protected function setApplication(Application $application): void
    {
        $this->espoApplication = $application;
    }

    protected function auth(
        ?string $userName = null,
        ?string $password = null,
        ?string $portalId = null,
        ?string $authenticationMethod = null,
        ?RequestWrapper $request = null
    ): void {

        $this->userName = $userName;
        $this->password = $password;
        $this->authenticationMethod = $authenticationMethod;

        if (isset($this->espoTester)) {
            $this->espoTester->auth($userName, $password, $portalId, $authenticationMethod, $request);
        }
    }

    /**
     * Get the application.
     */
    protected function getApplication(): Application
    {
        return $this->espoApplication;
    }

    /**
     * Get container.
     */
    protected function getContainer(): Container
    {
        return $this->getApplication()->getContainer();
    }

    protected function getFileManager(): FileManager
    {
        return $this->getApplication()->getContainer()->get('fileManager');
    }

    protected function getDataManager(): DataManager
    {
        return $this->getApplication()->getContainer()->get('dataManager');
    }

    protected function getInjectableFactory(): InjectableFactory
    {
        return $this->getApplication()->getContainer()->get('injectableFactory');
    }

    protected function getMetadata(): Metadata
    {
        return $this->getApplication()->getContainer()->get('metadata');
    }

    protected function getEntityManager(): EntityManager
    {
        return $this->getApplication()->getContainer()->get('entityManager');
    }

    protected function getConfig(): Config
    {
        return $this->getApplication()->getContainer()->get('config');
    }

    protected function normalizePath(string $path): string
    {
        return $this->espoTester->normalizePath($path);
    }

    /*protected function sendRequest($method, $action, $data = null)
    {
        return $this->espoTester->sendRequest($method, $action, $data);
    }*/

    protected function setUp(): void
    {
        $params = [
            'className' => get_class($this),
            'dataFile' => $this->dataFile,
            'pathToFiles' => $this->pathToFiles,
            'initData' => $this->initData,
        ];

        $this->espoTester = new Tester($params);

        $this->beforeSetUp();
        $this->espoTester->initialize();
        $this->auth($this->userName, $this->password, null, $this->authenticationMethod);
        $this->beforeStartApplication();
        $this->espoApplication = $this->createApplication();
        $this->afterStartApplication();
    }

    /**
     * Re-create an application.
     */
    protected function reCreateApplication(): void
    {
        $this->espoApplication = $this->createApplication();
    }

    protected function tearDown(): void
    {
        $this->espoTester->terminate();
        $this->espoTester = null;
        $this->espoApplication = null;
    }

    /**
     * @param array<string, mixed>|string $userData Data or a user name.
     * @param ?array<string, mixed> $role
     */
    protected function createUser($userData, ?array $role = null, bool $isPortal = false): User
    {
        return $this->espoTester->createUser($userData, $role, $isPortal);
    }

    protected function beforeSetUp(): void
    {}

    protected function beforeStartApplication(): void
    {}

    protected function afterStartApplication(): void
    {}

    protected function createRequest(
        string $method,
        array $queryParams = [],
        array $headers = [],
        ?string $body = null,
        array $routeParams = []
    ): RequestWrapper {

        $request = (new RequestFactory())
            ->createRequest($method, 'http://localhost/?' . http_build_query($queryParams));

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        if ($body) {
            $request = $request->withBody(
                (new StreamFactory)->createStream($body)
            );
        }

        return new RequestWrapper($request, '', $routeParams);
    }

    protected function createResponse(): ResponseWrapper
    {
        return new ResponseWrapper(
            (new ResponseFactory())->createResponse()
        );
    }

    protected function setData(array $data): void
    {
        $this->espoTester->setData($data);
    }

    /**
     * @todo Revise whether needed.
     */
    protected function fullReset(): void
    {
        $this->espoTester->setParam('fullReset', true);
    }
}
