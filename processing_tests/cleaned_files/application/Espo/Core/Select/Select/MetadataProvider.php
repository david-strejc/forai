<?php
//FORAI:F1313;DEF[C1072:MetadataProvider,F5584:__construct,F5585:getDefaultOrderBy,F5586:getSelectAttributesDependencyMap,F5587:getAclPortalAttributeList,F5588:getAclAttributeList,F5589:hasAttribute,F5590:isAttributeNotStorable,F5591:getAttributeType];IMP[F1665:C1390];EXP[C1072,F5585,F5586,F5587,F5588,F5589,F5590,F5591];LANG[php]//

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

namespace Espo\Core\Select\Select;

use Espo\Core\Utils\Metadata;
use Espo\ORM\EntityManager;

class MetadataProvider
{
    public function __construct(private Metadata $metadata, private EntityManager $entityManager)
    {}

    public function getDefaultOrderBy(string $entityType): ?string
    {
        return $this->metadata->get([
            'entityDefs', $entityType, 'collection', 'orderBy'
        ]) ?? null;
    }

    /**
     * @return ?array<string, string[]>
     */
    public function getSelectAttributesDependencyMap(string $entityType): ?array
    {
        return $this->metadata->get([
            'selectDefs', $entityType, 'selectAttributesDependencyMap'
        ]) ?? null;
    }

    /**
     * @return ?string[]
     */
    public function getAclPortalAttributeList(string $entityType): ?array
    {
        return $this->metadata->get([
            'selectDefs', $entityType, 'aclPortalAttributeList'
        ]) ?? null;
    }

    /**
     * @return ?string[]
     */
    public function getAclAttributeList(string $entityType): ?array
    {
        return $this->metadata->get([
            'selectDefs', $entityType, 'aclAttributeList'
        ]) ?? null;
    }

    public function hasAttribute(string $entityType, string $attribute): bool
    {
        return $this->entityManager
            ->getMetadata()
            ->getDefs()
            ->getEntity($entityType)
            ->hasAttribute($attribute);
    }

    public function isAttributeNotStorable(string $entityType, string $attribute): bool
    {
        return $this->entityManager
            ->getMetadata()
            ->getDefs()
            ->getEntity($entityType)
            ->getAttribute($attribute)
            ->isNotStorable();
    }

    public function getAttributeType(string $entityType, string $attribute): string
    {
        return $this->entityManager
            ->getMetadata()
            ->getDefs()
            ->getEntity($entityType)
            ->getAttribute($attribute)
            ->getType();
    }
}
