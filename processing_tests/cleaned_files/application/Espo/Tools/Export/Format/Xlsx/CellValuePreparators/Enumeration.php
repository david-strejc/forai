<?php
//FORAI:F709;DEF[C519:Enumeration,F3311:__construct,F3312:prepare];IMP[F1648:C1369,F323:C181,F692:C501];EXP[C519,F3312];LANG[php]//

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

namespace Espo\Tools\Export\Format\Xlsx\CellValuePreparators;

use Espo\Core\Utils\Language;
use Espo\ORM\Defs;
use Espo\ORM\Entity;
use Espo\Tools\Export\Format\CellValuePreparator;
use Espo\Tools\Export\Format\Xlsx\FieldHelper;

class Enumeration implements CellValuePreparator
{
    public function __construct(
        private Defs $ormDefs,
        private Language $language,
        private FieldHelper $fieldHelper
    ) {}

    public function prepare(Entity $entity, string $name): ?string
    {
        if (!$entity->has($name)) {
            return null;
        }

        $value = $entity->get($name);

        $fieldData = $this->fieldHelper->getData($entity->getEntityType(), $name);

        if (!$fieldData) {
            return $value;
        }

        $entityType = $fieldData->getEntityType();
        $field = $fieldData->getField();

        $translation = $this->ormDefs
            ->getEntity($entityType)
            ->getField($field)
            ->getParam('translation');

        if (!$translation) {
            return $this->language->translateOption($value, $field, $entityType);
        }

        $map = $this->language->get($translation);

        if (!is_array($map)) {
            return $value;
        }

        return $map[$value] ?? $value;
    }
}
