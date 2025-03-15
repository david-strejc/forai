<?php
//FORAI:F295;DEF[C155:LeadCapture<Entity>,F769:isToSubscribeContactIfExists,F770:isActive,F771:hasFormCaptcha,F772:getFormFrameAncestors,F773:getFormText,F774:getFormSuccessText,F775:getFormLanguage,F776:getFormSuccessRedirectUrl,F777:getFieldList,F778:isFieldRequired,F779:getOptInConfirmationSuccessMessage,F780:duplicateCheck,F781:skipOptInConfirmationIfSubscribed,F782:createLeadBeforeOptInConfirmation,F783:optInConfirmation,F784:getOptInConfirmationLifetime,F785:subscribeToTargetList,F786:subscribeContactToTargetList,F787:getFormId,F788:setFormId,F789:getApiKey,F790:setApiKey,F791:getName,F792:getTargetTeamId,F793:getTargetListId,F794:getCampaignId,F795:getInboundEmailId,F796:getLeadSource,F797:getOptInConfirmationEmailTemplateId,F798:getPhoneNumberCountry,F799:hasFormEnabled,F800:getFormTitle,F801:getFormTheme];IMP[F1909:C1608];EXP[C155,F769,F770,F771,F772,F773,F774,F775,F776,F777,F778,F779,F780,F781,F782,F783,F784,F785,F786,F787,F788,F789,F790,F791,F792,F793,F794,F795,F796,F797,F798,F799,F800,F801];LANG[php]//

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
use stdClass;

class LeadCapture extends Entity
{
    public const ENTITY_TYPE = 'LeadCapture';

    /**
     * @deprecated As of v7.2.
     */
    public function isToSubscribeContactIfExists(): bool
    {
        return $this->get('subscribeToTargetList') && $this->get('subscribeContactToTargetList');
    }

    public function isActive(): bool
    {
        return (bool) $this->get('isActive');
    }

    public function hasFormCaptcha(): bool
    {
        return (bool) $this->get('formCaptcha');
    }

    /**
     * @return string[]
     */
    public function getFormFrameAncestors(): array
    {
        return $this->get('formFrameAncestors') ?? [];
    }

    public function getFormText(): ?string
    {
        return $this->get('formText');
    }

    public function getFormSuccessText(): ?string
    {
        return $this->get('formSuccessText');
    }

    public function getFormLanguage(): ?string
    {
        return $this->get('formLanguage');
    }

    public function getFormSuccessRedirectUrl(): ?string
    {
        $url = $this->get('formSuccessRedirectUrl');

        if (!$url) {
            return null;
        }

        if (!str_contains($url, '://')) {
            $url = 'https://' . $url;
        }

        return $url;
    }

    /**
     * @return string[]
     */
    public function getFieldList(): array
    {
        return $this->get('fieldList') ?? [];
    }

    public function isFieldRequired(string $field): bool
    {
        /** @var stdClass $fieldParams */
        $fieldParams = $this->get('fieldParams') ?? (object) [];
        /** @var stdClass $itParams */
        $itParams = $fieldParams->$field ?? (object) [];

        return (bool) ($itParams->required ?? false);
    }

    public function getOptInConfirmationSuccessMessage(): ?string
    {
        return $this->get('optInConfirmationSuccessMessage');
    }

    public function duplicateCheck(): bool
    {
        return (bool) $this->get('duplicateCheck');
    }

    public function skipOptInConfirmationIfSubscribed(): bool
    {
        return (bool) $this->get('skipOptInConfirmationIfSubscribed');
    }

    public function createLeadBeforeOptInConfirmation(): bool
    {
        return (bool) $this->get('createLeadBeforeOptInConfirmation');
    }

    public function optInConfirmation(): bool
    {
        return (bool) $this->get('optInConfirmation');
    }

    public function getOptInConfirmationLifetime(): ?int
    {
        return $this->get('optInConfirmationLifetime');
    }

    public function subscribeToTargetList(): bool
    {
        return (bool) $this->get('subscribeToTargetList');
    }

    public function subscribeContactToTargetList(): bool
    {
        return (bool) $this->get('subscribeContactToTargetList');
    }

    public function getFormId(): ?string
    {
        return $this->get('formId');
    }

    public function setFormId(string $apiKey): self
    {
        return $this->set('formId', $apiKey);
    }

    public function getApiKey(): ?string
    {
        return $this->get('apiKey');
    }

    public function setApiKey(string $apiKey): self
    {
        $this->set('apiKey', $apiKey);

        return $this;
    }

    public function getName(): ?string
    {
        return $this->get(Field::NAME);
    }

    public function getTargetTeamId(): ?string
    {
        return $this->get('targetTeamId');
    }

    public function getTargetListId(): ?string
    {
        return $this->get('targetListId');
    }

    public function getCampaignId(): ?string
    {
        return $this->get('campaignId');
    }

    public function getInboundEmailId(): ?string
    {
        return $this->get('inboundEmailId');
    }

    public function getLeadSource(): ?string
    {
        return $this->get('leadSource');
    }

    public function getOptInConfirmationEmailTemplateId(): ?string
    {
        return $this->get('optInConfirmationEmailTemplateId');
    }

    /**
     * @since 8.1.0
     */
    public function getPhoneNumberCountry(): ?string
    {
        return $this->get('phoneNumberCountry');
    }

    public function hasFormEnabled(): bool
    {
        return (bool) $this->get('formEnabled');
    }

    /**
     * @since 9.1.0
     */
    public function getFormTitle(): ?string
    {
        return $this->get('formTitle');
    }

    /**
     * @since 9.1.0
     */
    public function getFormTheme(): ?string
    {
        return $this->get('formTheme');
    }
}
