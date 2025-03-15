<?php
//FORAI:F2381;DEF[C1994:Meeting<Entity>,F9920:setName,F9921:getName,F9922:setDescription,F9923:getDescription,F9924:getStatus,F9925:getDateStart,F9926:setDateStart,F9927:getDateEnd,F9928:setDateEnd,F9929:setAssignedUserId,F9930:getCreatedBy,F9931:getModifiedBy,F9932:getAssignedUser,F9933:getTeams,F9934:getUsers,F9935:getContacts,F9936:getLeads,F9937:setUsers,F9938:setContacts,F9939:setLeads,F9940:setParent,F9941:setAssignedUser,F9942:setTeams,F9943:setAccount,F9944:getAccount,F9945:getParent,F9946:getUid,F9947:setUid,F9948:setJoinUrl,F9949:getJoinUrl];IMP[F1845:C1547,F1849:C1551,F1851:C1553,F1909:C1608];EXP[C1994,F9920,F9921,F9922,F9923,F9924,F9925,F9926,F9927,F9928,F9929,F9930,F9931,F9932,F9933,F9934,F9935,F9936,F9937,F9938,F9939,F9940,F9941,F9942,F9943,F9944,F9945,F9946,F9947,F9948,F9949];LANG[php]//

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

use Espo\Core\Field\DateTimeOptional;
use Espo\Core\Field\Link;
use Espo\Core\Field\LinkMultiple;
use Espo\Core\Field\LinkParent;
use Espo\Core\Name\Field;
use Espo\Core\ORM\Entity;
use Espo\Entities\User;
use Espo\ORM\Entity as OrmEntity;

class Meeting extends Entity
{
    public const ENTITY_TYPE = 'Meeting';

    public const ATTENDEE_STATUS_NONE = 'None';
    public const ATTENDEE_STATUS_ACCEPTED = 'Accepted';
    public const ATTENDEE_STATUS_TENTATIVE = 'Tentative';
    public const ATTENDEE_STATUS_DECLINED = 'Declined';

    public const STATUS_PLANNED = 'Planned';
    public const STATUS_HELD = 'Held';
    public const STATUS_NOT_HELD = 'Not Held';

    public const LINK_USERS = 'users';
    public const LINK_CONTACTS = 'contacts';
    public const LINK_LEADS = 'leads';

    public function setName(?string $name): self
    {
        return $this->set(Field::NAME, $name);
    }

    public function getName(): ?string
    {
        return $this->get(Field::NAME);
    }

    public function setDescription(?string $description): self
    {
        return $this->set('description', $description);
    }

    public function getDescription(): ?string
    {
        return $this->get('description');
    }

    public function getStatus(): ?string
    {
        return $this->get('status');
    }

    public function getDateStart(): ?DateTimeOptional
    {
        /** @var ?DateTimeOptional */
        return $this->getValueObject('dateStart');
    }

    public function setDateStart(?DateTimeOptional $dateStart): self
    {
        $this->setValueObject('dateStart', $dateStart);

        return $this;
    }

    public function getDateEnd(): ?DateTimeOptional
    {
        /** @var ?DateTimeOptional */
        return $this->getValueObject('dateEnd');
    }

    public function setDateEnd(?DateTimeOptional $dateEnd): self
    {
        $this->setValueObject('dateEnd', $dateEnd);

        return $this;
    }

    public function setAssignedUserId(?string $assignedUserId): self
    {
        $this->set('assignedUserId', $assignedUserId);

        return $this;
    }

    public function getCreatedBy(): ?Link
    {
        /** @var ?Link */
        return $this->getValueObject(Field::CREATED_BY);
    }

    public function getModifiedBy(): ?Link
    {
        /** @var ?Link */
        return $this->getValueObject(Field::MODIFIED_BY);
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

    public function getUsers(): LinkMultiple
    {
        /** @var LinkMultiple */
        return $this->getValueObject(self::LINK_USERS);
    }

    public function getContacts(): LinkMultiple
    {
        /** @var LinkMultiple */
        return $this->getValueObject(self::LINK_CONTACTS);
    }

    public function getLeads(): LinkMultiple
    {
        /** @var LinkMultiple */
        return $this->getValueObject(self::LINK_LEADS);
    }

    public function setUsers(LinkMultiple $users): self
    {
        return $this->setValueObject(self::LINK_USERS, $users);
    }

    public function setContacts(LinkMultiple $contacts): self
    {
        return $this->setValueObject(self::LINK_CONTACTS, $contacts);
    }

    public function setLeads(LinkMultiple $leads): self
    {
        return $this->setValueObject(self::LINK_LEADS, $leads);
    }

    public function setParent(Entity|LinkParent|null $parent): self
    {
        if ($parent instanceof LinkParent) {
            $this->setValueObject(Field::PARENT, $parent);

            return $this;
        }

        $this->relations->set(Field::PARENT, $parent);

        return $this;
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

    public function setAccount(Link|Account|null $account): self
    {
        return $this->setRelatedLinkOrEntity('account', $account);
    }

    public function getAccount(): ?Account
    {
        /** @var ?Account */
        return $this->relations->getOne('account');
    }

    public function getParent(): ?OrmEntity
    {
        return $this->relations->getOne(Field::PARENT);
    }

    /**
     * @since 9.0.0
     */
    public function getUid(): ?string
    {
        return $this->get('uid');
    }

    /**
     * @since 9.0.0
     */
    public function setUid(?string $uid): self
    {
        return $this->set('uid', $uid);
    }

    /**
     * @since 9.0.0
     */
    public function setJoinUrl(?string $joinUrl): self
    {
        return $this->set('joinUrl', $joinUrl);
    }

    /**
     * @since 9.0.0
     */
    public function getJoinUrl(): ?string
    {
        return $this->get('joinUrl');
    }
}
