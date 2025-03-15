<?php
//FORAI:F2378;DEF[C1991:Contact<Person>,F9872:getAssignedUser,F9873:getAccount,F9874:getAccounts,F9875:setAccount,F9876:getTeams,F9877:getTitle,F9878:setAssignedUser,F9879:setTeams,F9880:setDescription,F9881:getDescription];IMP[F1020:C786,F1845:C1547,F1849:C1551,F1909:C1608];EXP[C1991,F9872,F9873,F9874,F9875,F9876,F9877,F9878,F9879,F9880,F9881];LANG[php]//

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

use Espo\Core\Entities\Person;
use Espo\Core\Field\Link;
use Espo\Core\Field\LinkMultiple;
use Espo\Core\Name\Field;
use Espo\Entities\User;
use Espo\ORM\EntityCollection;

class Contact extends Person
{
    public const ENTITY_TYPE = 'Contact';

    /**
     * An assigned user.
     */
    public function getAssignedUser(): ?Link
    {
        /** @var ?Link */
        return $this->getValueObject(Field::ASSIGNED_USER);
    }

    /**
     * A primary account.
     */
    public function getAccount(): ?Account
    {
        /** @var ?Account */
        return $this->relations->getOne('account');
    }

    /**
     * Get accounts.
     *
     * @return EntityCollection<Account>
     */
    public function getAccounts(): EntityCollection
    {
        /** @var EntityCollection<Account> */
        return $this->relations->getMany('accounts');
    }

    /**
     * Set a primary account.
     */
    public function setAccount(Account|Link|null $account): self
    {
        return $this->setRelatedLinkOrEntity('account', $account);
    }

    /**
     * Teams.
     */
    public function getTeams(): LinkMultiple
    {
        /** @var LinkMultiple */
        return $this->getValueObject(Field::TEAMS);
    }

    /**
     * A title (for a primary account).
     */
    public function getTitle(): ?string
    {
        return $this->get('title');
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

    public function setDescription(?string $description): self
    {
        return $this->set('description', $description);
    }

    public function getDescription(): ?string
    {
        return $this->get('description');
    }
}
