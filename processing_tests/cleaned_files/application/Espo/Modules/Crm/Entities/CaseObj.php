<?php
//FORAI:F2372;DEF[C1984:CaseObj<Entity>,F9798:setName,F9799:setDescription,F9800:getDescription,F9801:getName,F9802:getStatus,F9803:getInboundEmailId,F9804:getAccount,F9805:getContact,F9806:getContacts,F9807:getLead,F9808:getAssignedUser,F9809:getTeams,F9810:getAttachmentIdList,F9811:getAttachments,F9812:setAssignedUser,F9813:setTeams,F9814:setAccount,F9815:setContact,F9816:isInternal,F9817:setIsInternal];IMP[F1845:C1547,F1849:C1551,F1909:C1608,F312:C174];EXP[C1984,F9798,F9799,F9800,F9801,F9802,F9803,F9804,F9805,F9806,F9807,F9808,F9809,F9810,F9811,F9812,F9813,F9814,F9815,F9816,F9817];LANG[php]//

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

namespace Espo\Modules\Crm\Entities;

use Espo\Core\Field\Link;
use Espo\Core\Field\LinkMultiple;
use Espo\Core\Name\Field;
use Espo\Core\ORM\Entity;
use Espo\Entities\Attachment;
use Espo\Entities\User;
use Espo\ORM\EntityCollection;

class CaseObj extends Entity
{
    public const ENTITY_TYPE = 'Case';

    public const STATUS_NEW = 'New';
    public const STATUS_ASSIGNED = 'Assigned';
    public const STATUS_CLOSED = 'Closed';
    public const STATUS_PENDING = 'Pending';
    public const STATUS_REJECTED = 'Rejected';
    public const STATUS_DUPLICATE = 'Duplicate';

    protected $entityType = 'Case';

    public function setName(?string $name): self
    {
        return $this->set(Field::NAME, $name);
    }

    public function setDescription(?string $description): self
    {
        return $this->set('description', $description);
    }

    public function getDescription(): ?string
    {
        return $this->get('description');
    }

    public function getName(): ?string
    {
        return $this->get(Field::NAME);
    }

    public function getStatus(): ?string
    {
        return $this->get('status');
    }

    public function getInboundEmailId(): ?string
    {
        return $this->get('inboundEmailId');
    }

    public function getAccount(): ?Account
    {
        /** @var ?Account */
        return $this->relations->getOne('account');
    }

    /**
     * A primary contact.
     */
    public function getContact(): ?Contact
    {
        /** @var ?Contact */
        return $this->relations->getOne('contact');
    }

    public function getContacts(): LinkMultiple
    {
        /** @var LinkMultiple */
        return $this->getValueObject('contacts');
    }

    public function getLead(): ?Lead
    {
        /** @var ?Lead */
        return $this->relations->getOne('lead');
    }

    public function getAssignedUser(): ?Link
    {
        /** @var ?Link */
        return $this->getValueObject(Field::ASSIGNED_USER);
    }

    public function getTeams(): LinkMultiple
    {
        /** @var LinkMultiple */
        return $this->getValueObject(Field::TEAMS);
    }

    /**
     * @return string[]
     */
    public function getAttachmentIdList(): array
    {
        /** @var string[] */
        return $this->getLinkMultipleIdList('attachments');
    }

    /**
     * @return EntityCollection<Attachment>
     */
    public function getAttachments(): EntityCollection
    {
        /** @var EntityCollection<Attachment> */
        return $this->relations->getMany('attachments');
    }

    public function setAssignedUser(Link|User|null $assignedUser): self
    {
        return $this->setRelatedLinkOrEntity(Field::ASSIGNED_USER, $assignedUser);
    }

    public function setTeams(LinkMultiple $teams): self
    {
        $this->setValueObject(Field::TEAMS, $teams);

        return $this;
    }

    public function setAccount(Account|Link|null $account): self
    {
        return $this->setRelatedLinkOrEntity('account', $account);
    }

    public function setContact(Contact|Link|null $contact): self
    {
        return $this->setRelatedLinkOrEntity('contact', $contact);
    }

    /**
     * @since 9.0.0
     */
    public function isInternal(): bool
    {
        return (bool) $this->get('isInternal');
    }

    /**
     * @since 9.0.0
     */
    public function setIsInternal(bool $isInternal): self
    {
        return $this->set('isInternal', $isInternal);
    }
}
