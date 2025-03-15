<?php
//FORAI:F603;DEF[C418:PostDestroyAuthToken,F2812:__construct,F2813:process];IMP[F2011:C1636,F935:C713,F927:C708,F925:C706,F1643:C1365];EXP[C418,F2813];LANG[php]//

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

namespace Espo\Tools\App\Api;

use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Authentication\AuthenticationFactory;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\Utils\Json;

class PostDestroyAuthToken implements Action
{
    public function __construct(private AuthenticationFactory $authenticationFactory) {}

    public function process(Request $request): Response
    {
        $data = $request->getParsedBody();

        $token = $data->token ?? null;

        if (!$token || !is_string($token)) {
            throw new BadRequest("No `token`.");
        }

        $authentication = $this->authenticationFactory->create();

        $response = ResponseComposer::empty();

        try {
            $authentication->destroyAuthToken($token, $request, $response);
        } catch (NotFound) {
            return $response->writeBody(Json::encode(false));
        }

        return $response->writeBody(Json::encode(true));
    }
}
