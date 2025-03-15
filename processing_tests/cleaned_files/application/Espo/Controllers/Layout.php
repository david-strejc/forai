<?php
//FORAI:F189;DEF[C49:Layout,F193:__construct,F194:getActionRead,F195:putActionUpdate,F196:postActionResetToDefault,F197:getActionGetOriginal,F198:postActionCreate,F199:postActionDelete];IMP[F918:C699,F931:C709,F926:C705,F927:C708,F925:C706,F846:C649,F513:C335,F511:C334,F512:C333];EXP[C49,F194,F195,F196,F197,F198,F199];LANG[php]//

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

namespace Espo\Controllers;

use Espo\Core\Exceptions\Conflict;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Api\Request;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\InjectableFactory;
use Espo\Tools\Layout\CustomLayoutService;
use Espo\Tools\Layout\LayoutDefs;
use Espo\Tools\Layout\Service as Service;
use Espo\Entities\User;
use stdClass;

class Layout
{
    public function __construct(
        private User $user,
        private Service $service,
        private InjectableFactory $injectableFactory
    ) {}

    /**
     * @return mixed
     * @throws Forbidden
     * @throws NotFound
     * @throws Error
     * @throws BadRequest
     */
    public function getActionRead(Request $request)
    {
        $params = $request->getRouteParams();

        $scope = $params['scope'] ?? null;
        $name = $params['name'] ?? null;

        if (!$scope || !$name) {
            throw new BadRequest();
        }

        return $this->service->getForFrontend($scope, $name);
    }

    /**
     * @return mixed
     * @throws Forbidden
     * @throws BadRequest
     * @throws NotFound
     * @throws Error
     */
    public function putActionUpdate(Request $request)
    {
        $params = $request->getRouteParams();

        $data = json_decode($request->getBodyContents() ?? 'null');

        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }

        $scope = $params['scope'] ?? null;
        $name = $params['name'] ?? null;
        $setId = $params['setId'] ?? null;

        if (!$scope || !$name) {
            throw new BadRequest();
        }

        return $this->service->update($scope, $name, $setId, $data);
    }

    /**
     * @return array<int, mixed>|stdClass|null
     * @throws Forbidden
     * @throws BadRequest
     * @throws NotFound
     * @throws Error
     */
    public function postActionResetToDefault(Request $request)
    {
        $data = $request->getParsedBody();

        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }

        if (empty($data->scope) || empty($data->name)) {
            throw new BadRequest();
        }

        return $this->service->resetToDefault($data->scope, $data->name, $data->setId ?? null);
    }

    /**
     * @return array<int, mixed>|stdClass|null
     * @throws BadRequest
     * @throws Forbidden
     * @throws NotFound
     * @throws Error
     */
    public function getActionGetOriginal(Request $request)
    {
        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }

        $scope = $request->getQueryParam('scope');
        $name = $request->getQueryParam('name');
        $setId = $request->getQueryParam('setId');

        if (!$scope || !$name) {
            throw new BadRequest("No `scope` or `name` parameter.");
        }

        return $this->service->getOriginal($scope, $name, $setId);
    }

    /**
     * @throws Forbidden
     * @throws BadRequest
     * @throws Conflict
     */
    public function postActionCreate(Request $request): bool
    {
        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }

        $body = $request->getParsedBody();

        $scope = $body->scope ?? null;
        $name = $body->name ?? null;
        $type = $body->type ?? null;
        $label = $body->label ?? null;

        if (
            !is_string($scope) ||
            !is_string($name) ||
            !is_string($type) ||
            !is_string($label) ||
            !$scope ||
            !$name ||
            !$type ||
            !$label
        ) {
            throw new BadRequest();
        }

        $defs = new LayoutDefs($scope, $name, $type, $label);

        $service = $this->injectableFactory->create(CustomLayoutService::class);

        $service->create($defs);

        return true;
    }

    /**
     * @throws Forbidden
     * @throws BadRequest
     */
    public function postActionDelete(Request $request): bool
    {
        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }

        $body = $request->getParsedBody();

        $scope = $body->scope ?? null;
        $name = $body->name ?? null;

        if (
            !is_string($scope) ||
            !is_string($name) ||
            !$scope ||
            !$name
        ) {
            throw new BadRequest();
        }

        $service = $this->injectableFactory->create(CustomLayoutService::class);

        $service->delete($scope, $name);

        return true;
    }
}
