<?php
//FORAI:F1086;DEF[C847:SaveProcessor,F4954:__construct,F4955:process,F4956:getSaverList,F4957:getSaverClassNameList,F4958:createSaver];IMP[F1112:C873,F846:C649,F1665:C1390];EXP[C847,F4955,F4956,F4957,F4958];LANG[php]//

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

namespace Espo\Core\FieldProcessing;

use Espo\Core\ORM\Entity;
use Espo\Core\FieldProcessing\Saver\Params;
use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;

/**
 * Processes saving special fields.
 */
class SaveProcessor
{
    /** @var array<string, Saver<Entity>[]> */
    private $saverListMapCache = [];

    public function __construct(
        private InjectableFactory $injectableFactory,
        private Metadata $metadata
    ) {}

    /**
     * @param array<string, mixed> $options
     */
    public function process(Entity $entity, array $options): void
    {
        $params = Params::create()->withRawOptions($options);

        foreach ($this->getSaverList($entity->getEntityType()) as $processor) {
            $processor->process($entity, $params);
        }
    }

    /**
     * @return Saver<Entity>[]
     */
    private function getSaverList(string $entityType): array
    {
        if (array_key_exists($entityType, $this->saverListMapCache)) {
            return $this->saverListMapCache[$entityType];
        }

        $list = [];

        foreach ($this->getSaverClassNameList($entityType) as $className) {
            $list[] = $this->createSaver($className);
        }

        $this->saverListMapCache[$entityType] = $list;

        return $list;
    }

    /**
     * @return class-string<Saver<Entity>>[]
     */
    private function getSaverClassNameList(string $entityType): array
    {
        $list = $this->metadata
            ->get(['app', 'fieldProcessing', 'saverClassNameList']) ?? [];

        $additionalList = $this->metadata
            ->get(['recordDefs', $entityType, 'saverClassNameList']) ?? [];

        /** @var class-string<Saver<Entity>>[] */
        return array_merge($list, $additionalList);
    }

    /**
     * @param class-string<Saver<Entity>> $className
     * @return Saver<Entity>
     */
    private function createSaver(string $className): Saver
    {
        return $this->injectableFactory->create($className);
    }
}
