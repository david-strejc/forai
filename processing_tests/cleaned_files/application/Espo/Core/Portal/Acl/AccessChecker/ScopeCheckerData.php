<?php
//FORAI:F1058;DEF[C822:ScopeCheckerData,F4848:__construct,F4849:isOwn,F4850:inAccount,F4851:inContact,F4852:createBuilder];IMP[];EXP[C822,F4849,F4850,F4851,F4852];LANG[php]//

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

namespace Espo\Core\Portal\Acl\AccessChecker;

use Closure;

/**
 * Scope checker data.
 */
class ScopeCheckerData
{
    public function __construct(
        private Closure $isOwnChecker,
        private Closure $inAccountChecker,
        private Closure $inContactChecker
    ) {}

    public function isOwn(): bool
    {
        return ($this->isOwnChecker)();
    }

    public function inAccount(): bool
    {
        return ($this->inAccountChecker)();
    }

    public function inContact(): bool
    {
        return ($this->inContactChecker)();
    }

    public static function createBuilder(): ScopeCheckerDataBuilder
    {
        return new ScopeCheckerDataBuilder();
    }
}
