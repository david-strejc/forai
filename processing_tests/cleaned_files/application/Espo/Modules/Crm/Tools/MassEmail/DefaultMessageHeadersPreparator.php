<?php
//FORAI:F2413;DEF[C2024:DefaultMessageHeadersPreparator,F10181:__construct,F10182:prepare,F10183:getSiteUrl,F10184:addMandatoryOptOut];IMP[F1440:C1189,F1662:C1385,F2390:C2002,F2417:C2027];EXP[C2024,F10182,F10183,F10184];LANG[php]//

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

namespace Espo\Modules\Crm\Tools\MassEmail;

use Espo\Core\Mail\Mail\Header\XQueueItemId;
use Espo\Core\Utils\Config;
use Espo\Modules\Crm\Entities\Campaign;
use Espo\Modules\Crm\Tools\MassEmail\MessagePreparator\Data;
use Laminas\Mail\Headers;

class DefaultMessageHeadersPreparator implements MessageHeadersPreparator
{
    public function __construct(private Config $config)
    {}

    public function prepare(Headers $headers, Data $data): void
    {
        $id = $data->getId();

        $header = new XQueueItemId();
        $header->setId($id);

        $headers->addHeader($header);
        $headers->addHeaderLine('Precedence', 'bulk');

        $this->addMandatoryOptOut($headers, $data);
    }

    private function getSiteUrl(): string
    {
        $url = $this->config->get('massEmailSiteUrl') ?? $this->config->get('siteUrl');

        return rtrim($url, '/');
    }

    private function addMandatoryOptOut(Headers $headers, Data $data): void
    {
        $campaignType = $data->getQueueItem()->getMassEmail()?->getCampaign()?->getType();

        if ($campaignType === Campaign::TYPE_INFORMATIONAL_EMAIL) {
            return;
        }

        if ($this->config->get('massEmailDisableMandatoryOptOutLink')) {
            return;
        }

        $id = $data->getId();

        $url = "{$this->getSiteUrl()}/api/v1/Campaign/unsubscribe/$id";

        $headers->addHeaderLine('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
        $headers->addHeaderLine('List-Unsubscribe', "<$url>");
    }
}
