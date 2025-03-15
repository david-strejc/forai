<?php
//FORAI:F2291;DEF[C1902:General,F9577:__construct,F9578:build,F9579:applyField,F9580:applyFieldPersonName,F9581:applyFieldEmail,F9582:applyFieldPhone,F9583:applyFieldVarchar,F9584:getEmailAddressList,F9585:getPhoneNumberList];IMP[F1841:C1541,F1842:C1544,F1416:C1169,F1662:C1385,F1665:C1390,F323:C181,F380:C223,F392:C234,F387:C229,F348:C201];EXP[C1902,F9578,F9579,F9580,F9581,F9582,F9583,F9584,F9585];LANG[php]//

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

namespace Espo\Classes\DuplicateWhereBuilders;

use Espo\Core\Duplicate\WhereBuilder;
use Espo\Core\Field\EmailAddressGroup;
use Espo\Core\Field\PhoneNumberGroup;
use Espo\Core\ORM\Entity as CoreEntity;
use Espo\Core\ORM\Type\FieldType;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Metadata;
use Espo\ORM\Defs;
use Espo\ORM\Entity;
use Espo\ORM\Query\Part\Condition as Cond;
use Espo\ORM\Query\Part\Where\OrGroup;
use Espo\ORM\Query\Part\Where\OrGroupBuilder;
use Espo\ORM\Query\Part\WhereItem;
use Espo\ORM\Type\AttributeType;

/**
 * @implements WhereBuilder<CoreEntity>
 */
class General implements WhereBuilder
{
    public function __construct(
        private Metadata $metadata,
        private Defs $ormDefs,
        private Config $config
    ) {}

    /**
     * @param CoreEntity $entity
     */
    public function build(Entity $entity): ?WhereItem
    {
        /** @var string[] $fieldList */
        $fieldList = $this->metadata->get(['scopes', $entity->getEntityType(), 'duplicateCheckFieldList']) ?? [];

        $orBuilder = OrGroup::createBuilder();

        $toCheck = false;

        foreach ($fieldList as $field) {
            $toCheckItem = $this->applyField($field, $entity, $orBuilder);

            if ($toCheckItem) {
                $toCheck = true;
            }
        }

        if (!$toCheck) {
            return null;
        }

        return $orBuilder->build();
    }

    private function applyField(
        string $field,
        CoreEntity $entity,
        OrGroupBuilder $orBuilder
    ): bool {

        $type = $this->ormDefs
            ->getEntity($entity->getEntityType())
            ->tryGetField($field)
            ?->getType();

        if ($type === FieldType::PERSON_NAME) {
            return $this->applyFieldPersonName($field, $entity, $orBuilder);
        }

        if ($type === FieldType::EMAIL) {
            return $this->applyFieldEmail($field, $entity, $orBuilder);
        }

        if ($type === FieldType::PHONE) {
            return $this->applyFieldPhone($field, $entity, $orBuilder);
        }

        if ($entity->getAttributeType($field) === AttributeType::VARCHAR) {
            return $this->applyFieldVarchar($field, $entity, $orBuilder);
        }

        return false;
    }

    private function applyFieldPersonName(
        string $field,
        CoreEntity $entity,
        OrGroupBuilder $orBuilder
    ): bool {

        $first = 'first' . ucfirst($field);
        $last = 'last' . ucfirst($field);

        if (!$entity->get($first) && !$entity->get($last)) {
            return false;
        }

        $orBuilder->add(
            Cond::and(
                Cond::equal(
                    Cond::column($first),
                    $entity->get($first)
                ),
                Cond::equal(
                    Cond::column($last),
                    $entity->get($last)
                )
            )
        );

        return true;
    }

    private function applyFieldEmail(
        string $field,
        CoreEntity $entity,
        OrGroupBuilder $orBuilder
    ): bool {

        $toCheck = false;

        if (
            ($entity->get($field) || $entity->get($field . 'Data')) &&
            (
                $entity->isNew() ||
                $entity->isAttributeChanged($field) ||
                $entity->isAttributeChanged($field . 'Data')
            )
        ) {
            foreach ($this->getEmailAddressList($entity) as $emailAddress) {
                $orBuilder->add(
                    Cond::equal(
                        Cond::column($field),
                        $emailAddress
                    )
                );

                $toCheck = true;
            }
        }

        return $toCheck;
    }

    private function applyFieldPhone(
        string $field,
        CoreEntity $entity,
        OrGroupBuilder $orBuilder
    ): bool {

        $toCheck = false;

        $isNumeric = $this->config->get('phoneNumberNumericSearch');

        $column = $isNumeric ?
            $field . 'Numeric' :
            $field;

        if (
            ($entity->get($field) || $entity->get($field . 'Data')) &&
            (
                $entity->isNew() ||
                $entity->isAttributeChanged($field) ||
                $entity->isAttributeChanged($field . 'Data')
            )
        ) {
            foreach ($this->getPhoneNumberList($entity) as $number) {
                if ($isNumeric) {
                    $number = preg_replace('/[^0-9]/', '', $number);
                }

                $orBuilder->add(
                    Cond::equal(
                        Cond::column($column),
                        $number
                    )
                );

                $toCheck = true;
            }
        }

        return $toCheck;
    }

    private function applyFieldVarchar(
        string $field,
        CoreEntity $entity,
        OrGroupBuilder $orBuilder
    ): bool {

        if (!$entity->get($field)) {
            return false;
        }

        $orBuilder->add(
            Cond::equal(
                Cond::column($field),
                $entity->get($field)
            ),
        );

        return true;
    }

    /**
     * @return string[]
     */
    private function getEmailAddressList(CoreEntity $entity): array
    {
        if ($entity->get('emailAddressData')) {
            /** @var EmailAddressGroup $eaGroup */
            $eaGroup = $entity->getValueObject('emailAddress');

            return $eaGroup->getAddressList();
        }

        if ($entity->get('emailAddress')) {
            return [
                $entity->get('emailAddress')
            ];
        }

        return [];
    }

    /**
     * @return string[]
     */
    private function getPhoneNumberList(CoreEntity $entity): array
    {
        if ($entity->get('phoneNumberData')) {
            /** @var PhoneNumberGroup $eaGroup */
            $eaGroup = $entity->getValueObject('phoneNumber');

            return $eaGroup->getNumberList();
        }

        if ($entity->get('phoneNumber')) {
            return [$entity->get('phoneNumber')];
        }

        return [];
    }
}
