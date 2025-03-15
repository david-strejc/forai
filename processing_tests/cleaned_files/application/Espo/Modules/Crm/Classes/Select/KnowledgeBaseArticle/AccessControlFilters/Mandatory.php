<?php
//FORAI:F2559;DEF[C2168:Mandatory,F10574:__construct,F10575:apply];IMP[F1665:C1390,F275:C136,F2384:C1997,F380:C223,F369:C213];EXP[C2168,F10575];LANG[php]//

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

namespace Espo\Modules\Crm\Classes\Select\KnowledgeBaseArticle\AccessControlFilters;

use Espo\Core\Select\AccessControl\Filter;
use Espo\Core\Utils\Metadata;
use Espo\Entities\Portal;
use Espo\Modules\Crm\Entities\KnowledgeBaseArticle;
use Espo\ORM\Query\Part\Condition;
use Espo\ORM\Query\Part\Expression;
use Espo\ORM\Query\SelectBuilder;
use Espo\Entities\User;

class Mandatory implements Filter
{
    public function __construct(
        private User $user,
        private Metadata $metadata
    ) {}

    public function apply(SelectBuilder $queryBuilder): void
    {
        if (!$this->user->isPortal()) {
            return;
        }

        $statusList = $this->metadata->get("entityDefs.KnowledgeBaseArticle.fields.status.activeOptions") ??
            [KnowledgeBaseArticle::STATUS_PUBLISHED];

        $queryBuilder
            ->where(['status' => $statusList])
            ->where(
                Condition::in(
                    Expression::column('id'),
                    SelectBuilder::create()
                        ->select('knowledgeBaseArticleId')
                        ->from(KnowledgeBaseArticle::ENTITY_TYPE . Portal::ENTITY_TYPE)
                        ->where(['portalId' => $this->user->getPortalId()])
                        ->build()
                )
            );
    }
}
