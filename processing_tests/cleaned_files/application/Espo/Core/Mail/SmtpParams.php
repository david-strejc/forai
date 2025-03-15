<?php
//FORAI:F1431;DEF[C1181:SmtpParams,F6103:__construct,F6104:create,F6105:toArray,F6106:fromArray,F6107:getServer,F6108:getPort,F6109:getFromAddress,F6110:getFromName,F6111:getConnectionOptions,F6112:useAuth,F6113:getAuthMechanism,F6114:getAuthClassName,F6115:getUsername,F6116:getPassword,F6117:getSecurity,F6118:withFromAddress,F6119:withFromName,F6120:withConnectionOptions,F6121:withAuth,F6122:withAuthMechanism,F6123:withAuthClassName,F6124:withUsername,F6125:withPassword,F6126:withSecurity];IMP[];EXP[C1181,F6104,F6105,F6106,F6107,F6108,F6109,F6110,F6111,F6112,F6113,F6114,F6115,F6116,F6117,F6118,F6119,F6120,F6121,F6122,F6123,F6124,F6125,F6126];LANG[php]//

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

namespace Espo\Core\Mail;

use RuntimeException;

/**
 * SMTP parameters.
 *
 * @immutable
 */
class SmtpParams
{
    private ?string $fromAddress = null;
    private ?string $fromName = null;
    /** @var ?array<string, mixed> */
    private ?array $connectionOptions = null;
    private bool $auth = false;
    private ?string $authMechanism = null;
    /** @var ?class-string */
    private ?string $authClassName = null;
    private ?string $username = null;
    private ?string $password = null;
    private ?string $security = null;

    /** @var string[] */
    private array $paramList = [
        'server',
        'port',
        'fromAddress',
        'fromName',
        'connectionOptions',
        'auth',
        'authMechanism',
        'authClassName',
        'username',
        'password',
        'security',
    ];

    public function __construct(
        private string $server,
        private int $port
    ) {}

    public static function create(string $server, int $port): self
    {
        return new self($server, $port);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $params = [];

        foreach ($this->paramList as $name) {
            if ($this->$name !== null) {
                $params[$name] = $this->$name;
            }
        }

        return $params;
    }

    /**
     * @param array<string, mixed> $params
     */
    public static function fromArray(array $params): self
    {
        $server = $params['server'] ?? null;
        $port = $params['port'] ?? null;
        $auth = $params['auth'] ?? false;

        if ($server === null) {
            throw new RuntimeException("Empty server.");
        }

        if ($port === null) {
            throw new RuntimeException("Empty port.");
        }

        $obj = new self($server, $port);

        $obj->auth = $auth;

        foreach ($obj->paramList as $name) {
            if ($obj->$name !== null) {
                continue;
            }

            if (array_key_exists($name, $params)) {
               $obj->$name = $params[$name];
            }
        }

        if (isset($params['smtpAuthClassName'])) {
            // For bc.
            $obj->authClassName = $params['smtpAuthClassName'];
        }

        return $obj;
    }

    public function getServer(): string
    {
        return $this->server;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getFromAddress(): ?string
    {
        return $this->fromAddress;
    }

    public function getFromName(): ?string
    {
        return $this->fromName;
    }

    /**
     * @return ?array<string, mixed>
     */
    public function getConnectionOptions(): ?array
    {
        return $this->connectionOptions;
    }

    public function useAuth(): bool
    {
        return $this->auth;
    }

    public function getAuthMechanism(): ?string
    {
        return $this->authMechanism;
    }

    /**
     * @return ?class-string
     */
    public function getAuthClassName(): ?string
    {
        return $this->authClassName;
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

    public function withFromAddress(?string $fromAddress): self
    {
        $obj = clone $this;
        $obj->fromAddress = $fromAddress;

        return $obj;
    }

    public function withFromName(?string $fromName): self
    {
        $obj = clone $this;
        $obj->fromName = $fromName;

        return $obj;
    }

    /**
     * @param ?array<string, mixed> $connectionOptions
     */
    public function withConnectionOptions(?array $connectionOptions): self
    {
        $obj = clone $this;
        $obj->connectionOptions = $connectionOptions;

        return $obj;
    }

    public function withAuth(bool $auth = true): self
    {
        $obj = clone $this;
        $obj->auth = $auth;

        return $obj;
    }

    public function withAuthMechanism(?string $authMechanism): self
    {
        $obj = clone $this;
        $obj->authMechanism = $authMechanism;

        return $obj;
    }

    /**
     * @param ?class-string $authClassName
     */
    public function withAuthClassName(?string $authClassName): self
    {
        $obj = clone $this;
        $obj->authClassName = $authClassName;

        return $obj;
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

    public function withSecurity(?string $security): self
    {
        $obj = clone $this;
        $obj->security = $security;

        return $obj;
    }
}
