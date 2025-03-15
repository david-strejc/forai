<?php
//FORAI:F1469;DEF[C1212:Params,F6377:__construct,F6378:createBuilder,F6379:getHost,F6380:getPort,F6381:getUsername,F6382:getPassword,F6383:getSecurity,F6384:getImapHandlerClassName,F6385:getId,F6386:getUserId,F6387:getEmailAddress,F6388:withPassword,F6389:withImapHandlerClassName];IMP[];EXP[C1212,F6378,F6379,F6380,F6381,F6382,F6383,F6384,F6385,F6386,F6387,F6388,F6389];LANG[php]//

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

namespace Espo\Core\Mail\Account\Storage;

/**
 * @immutable
 */
class Params
{
    /** @var ?class-string<object> */
    private ?string $imapHandlerClassName;

    /**
     * @param ?class-string<object> $imapHandlerClassName
     */
    public function __construct(
        private ?string $host,
        private ?int $port,
        private ?string $username,
        private ?string $password,
        private ?string $security,
        ?string $imapHandlerClassName,
        private ?string $id,
        private ?string $userId,
        private ?string $emailAddress
    ) {
        $this->imapHandlerClassName = $imapHandlerClassName;
    }

    public static function createBuilder(): ParamsBuilder
    {
        return new ParamsBuilder();
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getSecurity(): ?string
    {
        return $this->security;
    }

    /**
     * @return ?class-string
     */
    public function getImapHandlerClassName(): ?string
    {
        return $this->imapHandlerClassName;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }

    public function withPassword(?string $password): self
    {
        $obj = clone $this;
        $obj->password = $password;

        return $obj;
    }

    /**
     * @param ?class-string $imapHandlerClassName
     */
    public function withImapHandlerClassName(?string $imapHandlerClassName): self
    {
        $obj = clone $this;
        $obj->imapHandlerClassName = $imapHandlerClassName;

        return $obj;
    }
}
