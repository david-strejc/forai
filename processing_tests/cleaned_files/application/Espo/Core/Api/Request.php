<?php
//FORAI:F2021;DEF[F8959:hasQueryParam,F8960:getQueryParam,F8961:getQueryParams,F8962:hasRouteParam,F8963:getRouteParam,F8964:getRouteParams,F8965:getHeader,F8966:hasHeader,F8967:getHeaderAsArray,F8968:getMethod,F8969:getUri,F8970:getResourcePath,F8971:getBodyContents,F8972:getParsedBody,F8973:getCookieParam,F8974:getServerParam];IMP[];EXP[F8959,F8960,F8961,F8962,F8963,F8964,F8965,F8966,F8967,F8968,F8969,F8970,F8971,F8972,F8973,F8974];LANG[php]//

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

use Psr\Http\Message\UriInterface;

use stdClass;

/**
 * Representation of an HTTP request.
 */
interface Request
{
    /**
     * Whether a query parameter is set.
     */
    public function hasQueryParam(string $name): bool;

    /**
     * Get a query parameter.
     */
    public function getQueryParam(string $name): ?string;

    /**
     * Get all query parameters.
     *
     * @return array<string, string|array<scalar, mixed>>
     */
    public function getQueryParams(): array;

    /**
     * Whether a route parameter is set.
     */
    public function hasRouteParam(string $name): bool;

    /**
     * Get a route parameter.
     */
    public function getRouteParam(string $name): ?string;

    /**
     * Get all route parameters.
     *
     * @return array<string, string>
     */
    public function getRouteParams(): array;

    /**
     * Get a header value. Multiple values will be concatenated with a comma.
     */
    public function getHeader(string $name): ?string;

    /**
     * Whether a header is set.
     */
    public function hasHeader(string $name): bool;

    /**
     * Get a header values as an array.
     *
     * @return string[]
     */
    public function getHeaderAsArray(string $name): array;

    /**
     * Get a request method.
     */
    public function getMethod(): string;

    /**
     * Get Uri.
     */
    public function getUri(): UriInterface;

    /**
     * Get a relative path of a request (w/o base path).
     */
    public function getResourcePath(): string;

    /**
     * Get body contents.
     */
    public function getBodyContents(): ?string;

    /**
     * Get a parsed body. If JSON array is passed, then will be converted to `{"list": ARRAY}`.
     */
    public function getParsedBody(): stdClass;

    /**
     * Get a cookie param value.
     */
    public function getCookieParam(string $name): ?string;

    /**
     * Get a server param value.
     *
     * @return mixed
     */
    public function getServerParam(string $name);
}
