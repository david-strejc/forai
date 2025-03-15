<?php
//FORAI:F940;DEF[C716:AuthenticationData,F4290:__construct,F4291:create,F4292:getUsername,F4293:getPassword,F4294:getMethod,F4295:byTokenOnly,F4296:withUsername,F4297:withPassword,F4298:withMethod,F4299:withByTokenOnly];IMP[];EXP[C716,F4291,F4292,F4293,F4294,F4295,F4296,F4297,F4298,F4299];LANG[php]//

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

namespace Espo\Core\Authentication;

/**
 * @immutable
 */
class AuthenticationData
{
    private bool $byTokenOnly = false;

    public function __construct(
        private ?string $username = null,
        private ?string $password = null,
        private ?string $method = null
    ) {}

    public static function create(): self
    {
        return new self();
    }

    /**
     * A username.
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * A password or auth-token.
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * A method.
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }

    /**
     * Authenticate by auth-token only. No username check.
     */
    public function byTokenOnly(): bool
    {
        return $this->byTokenOnly;
    }

    public function withUsername(?string $username): self
    {
        $obj = clone $this;
        $obj->username = $username;

        return $obj;
    }

    public function withPassword(?string $password): self
    {
        $obj = clone $this;
        $obj->password = $password;

        return $obj;
    }

    public function withMethod(?string $method): self
    {
        $obj = clone $this;
        $obj->method = $method;

        return $obj;
    }

    public function withByTokenOnly(bool $byTokenOnly): self
    {
        $obj = clone $this;
        $obj->byTokenOnly = $byTokenOnly;

        return $obj;
    }
}
