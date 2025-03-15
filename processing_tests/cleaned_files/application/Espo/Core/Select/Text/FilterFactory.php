<?php
//FORAI:F1351;DEF[C1104:FilterFactory,F5782:__construct,F5783:create];IMP[F1665:C1390,F846:C649,F1989:C1619,F1995:C1625];EXP[C1104,F5783];LANG[php]//

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

namespace Espo\Core\Select\Text;

use Espo\Core\Utils\Metadata;
use Espo\Core\InjectableFactory;
use Espo\Entities\User;
use Espo\Core\Binding\BindingContainerBuilder;
use Espo\Core\Binding\ContextualBinder;

class FilterFactory
{
    public function __construct(
        private Metadata $metadata,
        private InjectableFactory $injectableFactory
    ) {}

    public function create(string $entityType, User $user): Filter
    {
        /** @var class-string<Filter> $className */
        $className = $this->metadata->get(['selectDefs', $entityType, 'textFilterClassName']) ??
            DefaultFilter::class;

        $bindingContainer = BindingContainerBuilder::create()
            ->bindInstance(User::class, $user)
            ->inContext(
                $className,
                function (ContextualBinder $binder) use ($entityType) {
                    $binder->bindValue('$entityType', $entityType);
                }
            )
            ->inContext(
                DefaultFilter::class,
                function (ContextualBinder $binder) use ($entityType) {
                    $binder->bindValue('$entityType', $entityType);
                }
            )
            ->build();

        return $this->injectableFactory->createWithBinding($className, $bindingContainer);
    }
}
