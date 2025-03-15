<?php
//FORAI:F1408;DEF[C1157:AttributeExtractorFactory,F5981:__construct,F5982:create,F5983:getClassName];IMP[F846:C649,F1665:C1390,F328:C185];EXP[C1157,F5982,F5983];LANG[php]//

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

namespace Espo\Core\ORM;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;

use Espo\ORM\Metadata as OrmMetadata;
use Espo\ORM\Value\AttributeExtractor;
use Espo\ORM\Value\AttributeExtractorFactory as AttributeExtractorFactoryInterface;

use RuntimeException;

/**
 * @template T of object
 * @implements AttributeExtractorFactoryInterface<T>
 */
class AttributeExtractorFactory implements AttributeExtractorFactoryInterface
{
    public function __construct(
        private Metadata $metadata,
        private OrmMetadata $ormMetadata,
        private InjectableFactory $injectableFactory
    ) {}

    /**
     * @return AttributeExtractor<T>
     */
    public function create(string $entityType, string $field): AttributeExtractor
    {
        $className = $this->getClassName($entityType, $field);

        if (!$className) {
            throw new RuntimeException("Could not get AttributeExtractor for '{$entityType}.{$field}'.");
        }

        return $this->injectableFactory->createWith($className, ['entityType' => $entityType]);
    }

    /**
     * @return ?class-string<AttributeExtractor<T>>
     */
    private function getClassName(string $entityType, string $field): ?string
    {
        $fieldDefs = $this->ormMetadata
            ->getDefs()
            ->getEntity($entityType)
            ->getField($field);

        $className = $fieldDefs->getParam('attributeExtractorClassName');

        if ($className) {
            /** @var class-string<AttributeExtractor<T>> */
            return $className;
        }

        $type = $fieldDefs->getType();

        /** @var ?class-string<AttributeExtractor<T>> */
        return $this->metadata->get(['fields', $type, 'attributeExtractorClassName']);
    }
}
