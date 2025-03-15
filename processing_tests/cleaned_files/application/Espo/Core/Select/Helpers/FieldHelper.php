<?php
//FORAI:F1397;DEF[C1145:FieldHelper,F5921:__construct,F5922:getSeed,F5923:hasAssignedUsersField,F5924:hasCollaboratorsField,F5925:hasAssignedUserField,F5926:hasCreatedByField,F5927:hasTeamsField,F5928:hasContactField,F5929:hasContactsRelation,F5930:hasParentField,F5931:hasAccountField,F5932:hasAccountsRelation,F5933:getRelationDefs,F5934:getRelationParam,F5935:getRelationEntityType];IMP[F1909:C1608,F1665:C1390,F292:C153,F2374:C1986,F2378:C1991,F412:C251,F349:C202];EXP[C1145,F5922,F5923,F5924,F5925,F5926,F5927,F5928,F5929,F5930,F5931,F5932,F5933,F5934,F5935];LANG[php]//

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

namespace Espo\Core\Select\Helpers;

use Espo\Core\Name\Field;
use Espo\Core\Utils\Metadata;
use Espo\Entities\Team;
use Espo\Entities\User;
use Espo\Modules\Crm\Entities\Account;
use Espo\Modules\Crm\Entities\Contact;
use Espo\ORM\Defs\Params\RelationParam;
use Espo\ORM\Defs\RelationDefs;
use Espo\ORM\EntityManager;
use Espo\ORM\Entity;
use Espo\ORM\BaseEntity;
use Espo\ORM\Type\RelationType;

/**
 * @todo Rewrite using EntityDefs class. Then write unit tests.
 */
class FieldHelper
{
    private ?Entity $seed = null;

    private const LINK_CONTACTS = 'contacts';
    private const LINK_CONTACT = 'contact';
    private const LINK_ACCOUNTS = 'accounts';
    private const LINK_ACCOUNT = 'account';
    private const LINK_PARENT = Field::PARENT;
    private const LINK_TEAMS = Field::TEAMS;
    private const LINK_ASSIGNED_USERS = Field::ASSIGNED_USERS;
    private const LINK_ASSIGNED_USER = Field::ASSIGNED_USER;
    private const LINK_CREATED_BY = Field::CREATED_BY;
    private const LINK_COLLABORATORS = Field::COLLABORATORS;

    public function __construct(
        private string $entityType,
        private EntityManager $entityManager,
        private Metadata $metadata,
    ) {}

    private function getSeed(): Entity
    {
        return $this->seed ?? $this->entityManager->getNewEntity($this->entityType);
    }

    public function hasAssignedUsersField(): bool
    {
        if (
            $this->getSeed()->hasRelation(self::LINK_ASSIGNED_USERS) &&
            $this->getSeed()->hasAttribute(self::LINK_ASSIGNED_USERS . 'Ids') &&
            $this->getRelationEntityType(self::LINK_ASSIGNED_USERS) === User::ENTITY_TYPE
        ) {
            return true;
        }

        return false;
    }

    public function hasCollaboratorsField(): bool
    {
        if (
            $this->metadata->get("scopes.$this->entityType.collaborators") &&
            $this->getSeed()->hasRelation(self::LINK_COLLABORATORS) &&
            $this->getSeed()->hasAttribute(self::LINK_COLLABORATORS . 'Ids') &&
            $this->getRelationEntityType(self::LINK_COLLABORATORS) === User::ENTITY_TYPE
        ) {
            return true;
        }

        return false;
    }

    public function hasAssignedUserField(): bool
    {
        if (
            $this->getSeed()->hasAttribute(self::LINK_ASSIGNED_USER . 'Id') &&
            $this->getSeed()->hasRelation(self::LINK_ASSIGNED_USER) &&
            $this->getRelationEntityType(self::LINK_ASSIGNED_USER) === User::ENTITY_TYPE
        ) {
            return true;
        }

        return false;
    }

    public function hasCreatedByField(): bool
    {
        if (
            $this->getSeed()->hasAttribute(self::LINK_CREATED_BY . 'Id') &&
            $this->getSeed()->hasRelation(self::LINK_CREATED_BY) &&
            $this->getRelationEntityType(self::LINK_CREATED_BY) === User::ENTITY_TYPE
        ) {
            return true;
        }

        return false;
    }

    public function hasTeamsField(): bool
    {
        if (
            $this->getSeed()->hasRelation(self::LINK_TEAMS) &&
            $this->getSeed()->hasAttribute(self::LINK_TEAMS . 'Ids') &&
            $this->getRelationEntityType(self::LINK_TEAMS) === Team::ENTITY_TYPE
        ) {
            return true;
        }

        return false;
    }

    public function hasContactField(): bool
    {
        return
            $this->getSeed()->hasAttribute(self::LINK_CONTACT . 'Id') &&
            $this->getRelationEntityType(self::LINK_CONTACT) === Contact::ENTITY_TYPE;
    }

    public function hasContactsRelation(): bool
    {
        return
            $this->getSeed()->hasRelation(self::LINK_CONTACTS) &&
            $this->getRelationEntityType(self::LINK_CONTACTS) === Contact::ENTITY_TYPE;
    }

    public function hasParentField(): bool
    {
        return
            $this->getSeed()->hasAttribute(self::LINK_PARENT . 'Id') &&
            $this->getSeed()->hasRelation(self::LINK_PARENT) &&
            $this->getSeed()->getRelationType(self::LINK_PARENT) === RelationType::BELONGS_TO_PARENT;
    }

    public function hasAccountField(): bool
    {
        return
            $this->getSeed()->hasAttribute(self::LINK_ACCOUNT . 'Id') &&
            $this->getRelationEntityType(self::LINK_ACCOUNT) === Account::ENTITY_TYPE;
    }

    public function hasAccountsRelation(): bool
    {
        return
            $this->getSeed()->hasRelation(self::LINK_ACCOUNTS) &&
            $this->getRelationEntityType(self::LINK_ACCOUNTS) === Account::ENTITY_TYPE;
    }

    public function getRelationDefs(string $name): RelationDefs
    {
        return $this->entityManager
            ->getDefs()
            ->getEntity($this->entityType)
            ->getRelation($name);
    }

    /**
     * @noinspection PhpSameParameterValueInspection
     */
    private function getRelationParam(Entity $entity, string $relation, string $param): mixed
    {
        if ($entity instanceof BaseEntity) {
            return $entity->getRelationParam($relation, $param);
        }

        $entityDefs = $this->entityManager
            ->getDefs()
            ->getEntity($entity->getEntityType());

        if (!$entityDefs->hasRelation($relation)) {
            return null;
        }

        return $entityDefs->getRelation($relation)->getParam($param);
    }

    private function getRelationEntityType(string $relation): ?string
    {
        return $this->getRelationParam($this->getSeed(), $relation, RelationParam::ENTITY);
    }
}
