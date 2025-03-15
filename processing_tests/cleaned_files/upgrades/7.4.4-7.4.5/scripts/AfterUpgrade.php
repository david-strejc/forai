<?php
//FORAI:F149;DEF[C31:AfterUpgrade,F86:run,F87:updateMetadata,F88:updateEventMetadata];IMP[F853:C659,F1665:C1390,F2075:C1687];EXP[C31,F86,F87,F88];LANG[php]//

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

use Espo\Core\Container;
use Espo\Core\Utils\Metadata;
use Espo\Core\Templates\Entities\Event;

class AfterUpgrade
{
    public function run(Container $container): void
    {
        $this->updateMetadata($container->get('metadata'));
    }

    private function updateMetadata(Metadata $metadata): void
    {
        $this->updateEventMetadata($metadata);

        $metadata->save();
    }

    private function updateEventMetadata(Metadata $metadata): void
    {
        $defs = $metadata->get(['scopes']);

        foreach ($defs as $entityType => $item) {
            $isCustom = $item['isCustom'] ?? false;
            $type = $item['type'] ?? false;

            if (!$isCustom || $type !== Event::TEMPLATE_TYPE) {
                continue;
            }

            if (!is_string($metadata->get(['entityDefs', $entityType, 'fields', 'duration', 'select']))) {
                continue;
            }

            $metadata->delete('entityDefs', $entityType, 'fields.duration.orderBy');

            $metadata->set('entityDefs', $entityType, [
                'fields' => [
                    'duration' => [
                        'select' => [
                            'select' => "TIMESTAMPDIFF_SECOND:(dateStart, dateEnd)"
                        ],
                        'order' => [
                            'order' => [["TIMESTAMPDIFF_SECOND:(dateStart, dateEnd)", "{direction}"]]
                        ],
                    ]
                ]
            ]);
        }
    }
}
