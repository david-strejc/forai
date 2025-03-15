<?php
//FORAI:F1443;DEF[C1191:SystemSettingsAccount,F6174:__construct,F6175:updateFetchData,F6176:getConnectedAt,F6177:updateConnectedAt,F6178:relateEmail,F6179:getPortionLimit,F6180:isAvailableForFetching,F6181:getEmailAddress,F6182:getAssignedUser,F6183:getUser,F6184:getUsers,F6185:getTeams,F6186:keepFetchedEmailsUnread,F6187:getFetchData,F6188:getFetchSince,F6189:getEmailFolder,F6190:getGroupEmailFolder,F6191:getMonitoredFolderList,F6192:getId,F6193:getEntityType,F6194:getHost,F6195:getPort,F6196:getUsername,F6197:getPassword,F6198:getSecurity,F6199:getImapHandlerClassName,F6200:getSentFolder,F6201:isAvailableForSending,F6202:storeSentEmails,F6203:getSmtpParams,F6204:getImapParams];IMP[F1845:C1547,F1849:C1551,F1427:C1178,F1435:C1184,F1662:C1385,F302:C162];EXP[C1191,F6175,F6176,F6177,F6178,F6179,F6180,F6181,F6182,F6183,F6184,F6185,F6186,F6187,F6188,F6189,F6190,F6191,F6192,F6193,F6194,F6195,F6196,F6197,F6198,F6199,F6200,F6201,F6202,F6203,F6204];LANG[php]//

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

namespace Espo\Core\Mail\Account;

use Espo\Core\Field\Date;
use Espo\Core\Field\DateTime;
use Espo\Core\Field\Link;
use Espo\Core\Field\LinkMultiple;
use Espo\Core\Mail\ConfigDataProvider;
use Espo\Core\Mail\Exceptions\NoSmtp;
use Espo\Core\Mail\SmtpParams;
use Espo\Core\Utils\Config;
use Espo\Entities\Email;
use Espo\Entities\Settings;

class SystemSettingsAccount implements Account
{
    public function __construct(
        private Config $config,
        private ConfigDataProvider $configDataProvider,
    ) {}

    public function updateFetchData(FetchData $fetchData): void {}

    public function getConnectedAt(): ?DateTime
    {
        return null;
    }

    public function updateConnectedAt(): void
    {}

    public function relateEmail(Email $email): void {}

    public function getPortionLimit(): int
    {
        return 0;
    }

    public function isAvailableForFetching(): bool
    {
        return false;
    }

    public function getEmailAddress(): ?string
    {
        return $this->configDataProvider->getSystemOutboundAddress();
    }

    public function getAssignedUser(): ?Link
    {
        return null;
    }

    public function getUser(): ?Link
    {
        return null;
    }

    public function getUsers(): LinkMultiple
    {
        return LinkMultiple::create();
    }

    public function getTeams(): LinkMultiple
    {
        return LinkMultiple::create();
    }

    public function keepFetchedEmailsUnread(): bool
    {
        return false;
    }

    public function getFetchData(): FetchData
    {
        return FetchData::fromRaw((object) []);
    }

    public function getFetchSince(): ?Date
    {
        return null;
    }

    public function getEmailFolder(): ?Link
    {
        return null;
    }

    public function getGroupEmailFolder(): ?Link
    {
        return null;
    }

    public function getMonitoredFolderList(): array
    {
        return [];
    }

    public function getId(): ?string
    {
        return null;
    }

    public function getEntityType(): string
    {
        return Settings::ENTITY_TYPE;
    }

    public function getHost(): ?string
    {
        return null;
    }

    public function getPort(): ?int
    {
        return null;
    }

    public function getUsername(): ?string
    {
        return null;
    }

    public function getPassword(): ?string
    {
        return null;
    }

    public function getSecurity(): ?string
    {
        return null;
    }

    /**
     * @return ?class-string<object>
     */
    public function getImapHandlerClassName(): ?string
    {
        return null;
    }

    public function getSentFolder(): ?string
    {
        return null;
    }

    public function isAvailableForSending(): bool
    {
        return (bool) $this->config->get('smtpServer');
    }

    public function storeSentEmails(): bool
    {
        return false;
    }

    /**
     * @throws NoSmtp
     */
    public function getSmtpParams(): ?SmtpParams
    {
        $host = $this->config->get('smtpServer');
        $port = $this->config->get('smtpPort');

        if (!$host) {
            throw new NoSmtp("No system SMTP settings.");
        }

        if (!$port) {
            throw new NoSmtp("No system SMTP port.");
        }

        $params = SmtpParams::create($host, $port)
            ->withSecurity($this->config->get('smtpSecurity'))
            ->withAuth($this->config->get('smtpAuth'));

        if ($params->useAuth()) {
            $password = $this->config->get('smtpPassword');

            $params = $params
                ->withUsername($this->config->get('smtpUsername'))
                ->withPassword($password)
                ->withAuthMechanism($this->config->get('smtpAuthMechanism') ?? 'login');
        }

        return $params;
    }

    public function getImapParams(): ?ImapParams
    {
        return null;
    }
}
