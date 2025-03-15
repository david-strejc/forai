<?php
//FORAI:F2022;DEF[F8975:getStatusCode,F8976:getReasonPhrase,F8977:setStatus,F8978:setHeader,F8979:addHeader,F8980:getHeader,F8981:hasHeader,F8982:getHeaderNames,F8983:getHeaderAsArray,F8984:writeBody,F8985:setBody,F8986:getBody];IMP[];EXP[F8975,F8976,F8977,F8978,F8979,F8980,F8981,F8982,F8983,F8984,F8985,F8986];LANG[php]//

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

use Psr\Http\Message\StreamInterface;

/**
 * Representation of an HTTP response. An instance is mutable.
 */
interface Response
{
    /**
     * Get a status code.
     */
    public function getStatusCode(): int;

    /**
     * Get a status reason phrase.
     */
    public function getReasonPhrase(): string;

    /**
     * Set a status code.
     */
    public function setStatus(int $code, ?string $reason = null): self;

    /**
     * Set a specific header.
     */
    public function setHeader(string $name, string $value): self;

    /**
     * Add a specific header.
     */
    public function addHeader(string $name, string $value): self;

    /**
     * Get a header value.
     */
    public function getHeader(string $name): ?string;

    /**
     * Whether a header is set.
     */
    public function hasHeader(string $name): bool;

    /**
     * Get all set header names.
     *
     * @return string[]
     */
    public function getHeaderNames(): array;

    /**
     * Get a header values as an array.
     *
     * @return string[]
     */
    public function getHeaderAsArray(string $name): array;

    /**
     * Write a body.
     */
    public function writeBody(string $string): self;

    /**
     * Set a body.
     */
    public function setBody(StreamInterface $body): self;

    /**
     * Get a body.
     */
    public function getBody(): StreamInterface;
}
