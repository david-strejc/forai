<?php
//FORAI:F299;DEF[C160:InboundEmail<Entity>,F808:isAvailableForFetching,F809:isAvailableForSending,F810:isActive,F811:smtpIsForMassEmail,F812:storeSentEmails,F813:getReplyToAddress,F814:getReplyFromAddress,F815:getReplyFromName,F816:getName,F817:getFromName,F818:getEmailAddress,F819:getAssignToUser,F820:getTargetUserPosition,F821:getTeam,F822:getTeams,F823:addAllTeamUsers,F824:keepFetchedEmailsUnread,F825:getFetchData,F826:getFetchSince,F827:getEmailFolder,F828:getMonitoredFolderList,F829:getHost,F830:getPort,F831:getUsername,F832:getPassword,F833:getSecurity,F834:getImapHandlerClassName,F835:createCase,F836:autoReply,F837:getSentFolder,F838:getGroupEmailFolder,F839:getCaseDistribution,F840:getSmtpHost,F841:getSmtpPort,F842:getSmtpAuth,F843:getSmtpSecurity,F844:getSmtpUsername,F845:getSmtpPassword,F846:getSmtpAuthMechanism,F847:getSmtpHandlerClassName];IMP[F1909:C1608,F1845:C1547,F1849:C1551];EXP[C160,F808,F809,F810,F811,F812,F813,F814,F815,F816,F817,F818,F819,F820,F821,F822,F823,F824,F825,F826,F827,F828,F829,F830,F831,F832,F833,F834,F835,F836,F837,F838,F839,F840,F841,F842,F843,F844,F845,F846,F847];LANG[php]//

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

namespace Espo\Entities;

use Espo\Core\Name\Field;
use Espo\Core\ORM\Entity;

use Espo\Core\Field\Date;
use Espo\Core\Field\Link;
use Espo\Core\Field\LinkMultiple;

use stdClass;

class InboundEmail extends Entity
{
    public const ENTITY_TYPE = 'InboundEmail';

    public const STATUS_ACTIVE = 'Active';

    public const CASE_DISTRIBUTION_DIRECT_ASSIGNMENT = 'Direct-Assignment';
    public const CASE_DISTRIBUTION_ROUND_ROBIN = 'Round-Robin';
    public const CASE_DISTRIBUTION_LEAST_BUSY = 'Least-Busy';

    public function isAvailableForFetching(): bool
    {
        return $this->isActive() && $this->get('useImap');
    }

    public function isAvailableForSending(): bool
    {
        return $this->isActive() && $this->get('useSmtp');
    }

    public function isActive(): bool
    {
        return $this->get('status') === self::STATUS_ACTIVE;
    }

    public function smtpIsForMassEmail(): bool
    {
        return (bool) $this->get('smtpIsForMassEmail');
    }

    public function storeSentEmails(): bool
    {
        return (bool) $this->get('storeSentEmails');
    }

    public function getReplyToAddress(): ?string
    {
        return $this->get('replyToAddress');
    }

    public function getReplyFromAddress(): ?string
    {
        return $this->get('replyFromAddress');
    }

    public function getReplyFromName(): ?string
    {
        return $this->get('replyFromName');
    }

    public function getName(): ?string
    {
        return $this->get(Field::NAME);
    }

    public function getFromName(): ?string
    {
        return $this->get('fromName');
    }

    public function getEmailAddress(): ?string
    {
        return $this->get('emailAddress');
    }

    public function getAssignToUser(): ?Link
    {
        /** @var ?Link */
        return $this->getValueObject('assignToUser');
    }

    public function getTargetUserPosition(): ?string
    {
        return $this->get('targetUserPosition');
    }

    public function getTeam(): ?Link
    {
        /** @var ?Link */
        return $this->getValueObject('team');
    }

    public function getTeams(): LinkMultiple
    {
        /** @var LinkMultiple */
        return $this->getValueObject('teams');
    }

    public function addAllTeamUsers(): bool
    {
        return (bool) $this->get('addAllTeamUsers');
    }

    public function keepFetchedEmailsUnread(): bool
    {
        return (bool) $this->get('keepFetchedEmailsUnread');
    }

    public function getFetchData(): stdClass
    {
        $data = $this->get('fetchData') ?? (object) [];

        if (!property_exists($data, 'lastUID')) {
            $data->lastUID = (object) [];
        }

        if (!property_exists($data, 'lastDate')) {
            $data->lastDate = (object) [];
        }

        if (!property_exists($data, 'byDate')) {
            $data->byDate = (object) [];
        }

        return $data;
    }

    public function getFetchSince(): ?Date
    {
        /** @var ?Date */
        return $this->getValueObject('fetchSince');
    }

    public function getEmailFolder(): ?Link
    {
        return null;
    }

    /**
     * @return string[]
     */
    public function getMonitoredFolderList(): array
    {
        return $this->get('monitoredFolders') ?? [];
    }

    public function getHost(): ?string
    {
        return $this->get('host');
    }

    public function getPort(): ?int
    {
        return $this->get('port');
    }

    public function getUsername(): ?string
    {
        return $this->get('username');
    }

    public function getPassword(): ?string
    {
        return $this->get('password');
    }

    public function getSecurity(): ?string
    {
        return $this->get('security');
    }

    /**
     * @return ?class-string<object>
     */
    public function getImapHandlerClassName(): ?string
    {
        /** @var ?class-string<object> */
        return $this->get('imapHandler');
    }

    public function createCase(): bool
    {
        return (bool) $this->get('createCase');
    }

    public function autoReply(): bool
    {
        return (bool) $this->get('reply');
    }

    public function getSentFolder(): ?string
    {
        return $this->get('sentFolder');
    }

    public function getGroupEmailFolder(): ?Link
    {
        /** @var ?Link */
        return $this->getValueObject('groupEmailFolder');
    }

    public function getCaseDistribution(): ?string
    {
        return $this->get('caseDistribution');
    }

    public function getSmtpHost(): ?string
    {
        return $this->get('smtpHost');
    }

    public function getSmtpPort(): ?int
    {
        return $this->get('smtpPort');
    }

    public function getSmtpAuth(): bool
    {
        return $this->get('smtpAuth');
    }

    public function getSmtpSecurity(): ?string
    {
        return $this->get('smtpSecurity');
    }

    public function getSmtpUsername(): ?string
    {
        return $this->get('smtpUsername');
    }

    public function getSmtpPassword(): ?string
    {
        return $this->get('smtpPassword');
    }

    public function getSmtpAuthMechanism(): ?string
    {
        return $this->get('smtpAuthMechanism');
    }

    /**
     * @return ?class-string<object>
     */
    public function getSmtpHandlerClassName(): ?string
    {
        /** @var ?class-string<object> */
        return $this->get('smtpHandler');
    }
}
