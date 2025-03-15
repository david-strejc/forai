<?php
//FORAI:F239;DEF[C99:Notification<RecordBase>,F328:getActionList,F329:getActionNotReadCount,F330:postActionMarkAllRead,F331:getNotificationService];IMP[F1909:C1608,F503:C326,F1017:C785,F927:C708,F931:C709,F926:C705,F1322:C1080];EXP[C99,F328,F329,F330,F331];LANG[php]//

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

use Espo\Core\Name\Field;
use Espo\Tools\Notification\RecordService as Service;

use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Controllers\RecordBase;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Select\SearchParams;
use Espo\Core\Select\Where\Item as WhereItem;

use stdClass;

class Notification extends RecordBase
{
    public static $defaultAction = 'list';

    /**
     * @throws BadRequest
     * @throws Forbidden
     * @throws Error
     */
    public function getActionList(Request $request, Response $response): stdClass
    {
        $searchParamsAux = $this->searchParamsFetcher->fetch($request);

        $offset = $searchParamsAux->getOffset();
        $maxSize = $searchParamsAux->getMaxSize();

        $after = $request->getQueryParam('after');

        $searchParams = SearchParams
            ::create()
            ->withOffset($offset)
            ->withMaxSize($maxSize);

        if ($after) {
            $searchParams = $searchParams
                ->withWhereAdded(
                    WhereItem
                        ::createBuilder()
                        ->setAttribute(Field::CREATED_AT)
                        ->setType(WhereItem\Type::AFTER)
                        ->setValue($after)
                        ->build()
                );

        }

        $userId = $this->user->getId();

        $recordCollection = $this->getNotificationService()->get($userId, $searchParams);

        return (object) [
            'total' => $recordCollection->getTotal(),
            'list' => $recordCollection->getValueMapList(),
        ];
    }

    public function getActionNotReadCount(): int
    {
        $userId = $this->user->getId();

        return $this->getNotificationService()->getNotReadCount($userId);
    }

    public function postActionMarkAllRead(Request $request): bool
    {
        $userId = $this->user->getId();

        $this->getNotificationService()->markAllRead($userId);

        return true;
    }

    private function getNotificationService(): Service
    {
        return $this->injectableFactory->create(Service::class);
    }
}
