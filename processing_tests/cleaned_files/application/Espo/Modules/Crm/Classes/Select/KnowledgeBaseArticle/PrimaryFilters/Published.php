<?php
//FORAI:F2558;DEF[C2166:Published,F10571:__construct,F10572:apply];IMP[F1665:C1390,F2384:C1997,F369:C213];EXP[C2166,F10572];LANG[php]//

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

namespace Espo\Modules\Crm\Classes\Select\KnowledgeBaseArticle\PrimaryFilters;

use Espo\Core\Select\Primary\Filter;
use Espo\Core\Utils\Metadata;
use Espo\Modules\Crm\Entities\KnowledgeBaseArticle;
use Espo\ORM\Query\SelectBuilder;

class Published implements Filter
{
    public function __construct(
        private Metadata $metadata
    ) {}

    public function apply(SelectBuilder $queryBuilder): void
    {
        $statusList = $this->metadata->get("entityDefs.KnowledgeBaseArticle.fields.status.activeOptions") ??
            [KnowledgeBaseArticle::STATUS_PUBLISHED];

        $queryBuilder->where(['status' => $statusList]);
    }
}
