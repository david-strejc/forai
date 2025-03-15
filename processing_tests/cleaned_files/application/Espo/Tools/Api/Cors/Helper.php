<?php
//FORAI:F738;DEF[F3430:isCredentialsAllowed,F3431:getAllowedOrigin,F3432:getAllowedMethods,F3433:getAllowedHeaders,F3434:getSuccessStatus,F3435:getMaxAge];IMP[];EXP[F3430,F3431,F3432,F3433,F3434,F3435];LANG[php]//

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

namespace Espo\Tools\Api\Cors;

use Psr\Http\Message\RequestInterface as Request;

interface Helper
{
    public function isCredentialsAllowed(Request $request): bool;

    public function getAllowedOrigin(Request $request): ?string;

    /**
     * @return string[]
     */
    public function getAllowedMethods(Request $request): array;

    /**
     * @return string[]
     */
    public function getAllowedHeaders(Request $request): array;

    public function getSuccessStatus(): ?int;

    public function getMaxAge(): ?int;
}
