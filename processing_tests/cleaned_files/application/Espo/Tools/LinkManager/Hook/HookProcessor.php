<?php
//FORAI:F579;DEF[C395:HookProcessor,F2687:__construct,F2688:processCreate,F2689:processDelete,F2690:getCreateHookList,F2691:getDeleteHookList];IMP[F1665:C1390,F846:C649,F576:C393];EXP[C395,F2688,F2689,F2690,F2691];LANG[php]//

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

namespace Espo\Tools\LinkManager\Hook;

use Espo\Core\Utils\Metadata;
use Espo\Core\InjectableFactory;
use Espo\Tools\LinkManager\Params;

class HookProcessor
{
    public function __construct(
        private Metadata $metadata,
        private InjectableFactory $injectableFactory
    ) {}

    public function processCreate(Params $params): void
    {
        foreach ($this->getCreateHookList() as $hook) {
            $hook->process($params);
        }
    }

    public function processDelete(Params $params): void
    {
        foreach ($this->getDeleteHookList() as $hook) {
            $hook->process($params);
        }
    }

    /**
     * @return CreateHook[]
     */
    private function getCreateHookList(): array
    {
        /** @var class-string<CreateHook>[] $classNameList */
        $classNameList = $this->metadata->get(['app', 'linkManager', 'createHookClassNameList']) ?? [];

        $list = [];

        foreach ($classNameList as $className) {
            $list[] = $this->injectableFactory->create($className);
        }

        return $list;
    }

    /**
     * @return DeleteHook[]
     */
    private function getDeleteHookList(): array
    {
        /** @var class-string<DeleteHook>[] $classNameList */
        $classNameList = $this->metadata->get(['app', 'linkManager', 'deleteHookClassNameList']) ?? [];

        $list = [];

        foreach ($classNameList as $className) {
            $list[] = $this->injectableFactory->create($className);
        }

        return $list;
    }
}
