<?php
//FORAI:F524;DEF[C346:Post,F2452:__construct,F2453:process];IMP[F2011:C1636,F927:C708,F926:C705,F298:C159,F516:C338];EXP[C346,F2453];LANG[php]//

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

namespace Espo\Tools\Import\Api;

use Espo\Core\Acl;
use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Forbidden;
use Espo\Entities\Import;
use Espo\Tools\Import\Params as ImportParams;
use Espo\Tools\Import\Service;

/**
 * Creates imports.
 */
class Post implements Action
{
    public function __construct(private Service $service, private Acl $acl) {}

    public function process(Request $request): Response
    {
        if (!$this->acl->checkScope(Import::ENTITY_TYPE)) {
            throw new Forbidden();
        }

        $data = $request->getParsedBody();

        $entityType = $data->entityType ?? null;
        $attributeList = $data->attributeList ?? null;
        $attachmentId = $data->attachmentId ?? null;

        if (!is_array($attributeList)) {
            throw new BadRequest("No `attributeList`.");
        }

        if (!$attachmentId) {
            throw new BadRequest("No `attachmentId`.");
        }

        if (!$entityType) {
            throw new BadRequest("No `entityType`.");
        }

        $params = ImportParams::fromRaw($data);

        $result = $this->service->import($entityType, $attributeList, $attachmentId, $params);

        return ResponseComposer::json($result->getValueMap());
    }
}
