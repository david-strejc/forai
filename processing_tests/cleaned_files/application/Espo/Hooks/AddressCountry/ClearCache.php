<?php
//FORAI:F774;DEF[C580:ClearCache,F3615:__construct,F3616:afterSave,F3617:afterRemove];IMP[F1641:C1363,F264:C124,F341:C196,F340:C195];EXP[C580,F3616,F3617];LANG[php]//

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

namespace Espo\Hooks\AddressCountry;

use Espo\Core\Hook\Hook\AfterRemove;
use Espo\Core\Hook\Hook\AfterSave;
use Espo\Core\Utils\DataCache;
use Espo\Entities\AddressCountry;
use Espo\ORM\Entity;
use Espo\ORM\Repository\Option\RemoveOptions;
use Espo\ORM\Repository\Option\SaveOptions;

/**
 * @implements AfterRemove<AddressCountry>
 * @implements AfterSave<AddressCountry>
 */
class ClearCache implements AfterRemove, AfterSave
{
    private const CACHE_KEY = 'addressCountryData';

    public function __construct(
        private DataCache $dataCache,
    ) {}

    public function afterSave(Entity $entity, SaveOptions $options): void
    {
        $this->dataCache->clear(self::CACHE_KEY);
    }

    public function afterRemove(Entity $entity, RemoveOptions $options): void
    {
        $this->dataCache->clear(self::CACHE_KEY);
    }
}
