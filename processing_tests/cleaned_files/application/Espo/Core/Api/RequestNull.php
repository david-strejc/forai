<?php
//FORAI:F2016;DEF[C1642:RequestNull,F8922:hasQueryParam,F8923:getQueryParam,F8924:getQueryParams,F8925:hasRouteParam,F8926:getRouteParam,F8927:getRouteParams,F8928:getHeader,F8929:hasHeader,F8930:getHeaderAsArray,F8931:getMethod,F8932:getUri,F8933:getResourcePath,F8934:getBodyContents,F8935:getParsedBody,F8936:getCookieParam,F8937:getServerParam];IMP[];EXP[C1642,F8922,F8923,F8924,F8925,F8926,F8927,F8928,F8929,F8930,F8931,F8932,F8933,F8934,F8935,F8936,F8937];LANG[php]//

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

namespace Espo\Core\Api;

use Espo\Core\Api\Request as ApiRequest;

use Psr\Http\Message\UriInterface;
use Slim\Psr7\Factory\UriFactory;

use stdClass;

/**
 * An empty stub for Request.
 */
class RequestNull implements ApiRequest
{
    public function hasQueryParam(string $name): bool
    {
        return false;
    }

    /**
     * @return null
     * @noinspection PhpDocSignatureInspection
     */
    public function getQueryParam(string $name): ?string
    {
        return null;
    }

    public function getQueryParams(): array
    {
        return [];
    }

    public function hasRouteParam(string $name): bool
    {
        return false;
    }

    public function getRouteParam(string $name): ?string
    {
        return null;
    }

    public function getRouteParams(): array
    {
        return [];
    }

    public function getHeader(string $name): ?string
    {
        return null;
    }

    public function hasHeader(string $name): bool
    {
        return false;
    }

    /**
     * @return string[]
     */
    public function getHeaderAsArray(string $name): array
    {
        return [];
    }

    public function getMethod(): string
    {
        return '';
    }

    public function getUri(): UriInterface
    {
        return (new UriFactory())->createUri();
    }

    public function getResourcePath(): string
    {
        return '';
    }

    public function getBodyContents(): ?string
    {
        return null;
    }

    public function getParsedBody(): stdClass
    {
        return (object) [];
    }

    public function getCookieParam(string $name): ?string
    {
        return null;
    }

    /**
     * @return mixed
     */
    public function getServerParam(string $name)
    {
        return null;
    }
}
