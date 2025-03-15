<?php
//FORAI:F1026;DEF[C792:ServiceParams,F4707:__construct,F4708:create,F4709:getParams,F4710:isIdle,F4711:withIsIdle];IMP[];EXP[C792,F4708,F4709,F4710,F4711];LANG[php]//

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

namespace Espo\Core\MassAction;

/**
 * @immutable
 */
class ServiceParams
{
    private bool $isIdle = false;

    private function __construct(private Params $params)
    {}

    public static function create(Params $params): self
    {
        return new self($params);
    }

    public function getParams(): Params
    {
        return $this->params;
    }

    public function isIdle(): bool
    {
        return $this->isIdle;
    }

    public function withIsIdle(bool $isIdle = true): self
    {
        $obj = clone $this;
        $obj->isIdle = $isIdle;

        return $obj;
    }
}
