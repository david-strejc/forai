<?php
//FORAI:F1566;DEF[C1295:Params,F6763:withUseProcessPool,F6764:withNoLock,F6765:withQueue,F6766:withGroup,F6767:withLimit,F6768:useProcessPool,F6769:noLock,F6770:getQueue,F6771:getGroup,F6772:getLimit,F6773:create];IMP[];EXP[C1295,F6763,F6764,F6765,F6766,F6767,F6768,F6769,F6770,F6771,F6772,F6773];LANG[php]//

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

namespace Espo\Core\Job\QueueProcessor;

class Params
{
    private bool $useProcessPool = false;
    private bool $noLock = false;
    private ?string $queue = null;
    private ?string $group = null;
    private int $limit = 0;

    public function withUseProcessPool(bool $useProcessPool): self
    {
        $obj = clone $this;
        $obj->useProcessPool = $useProcessPool;

        return $obj;
    }

    public function withNoLock(bool $noLock): self
    {
        $obj = clone $this;
        $obj->noLock = $noLock;

        return $obj;
    }

    public function withQueue(?string $queue): self
    {
        $obj = clone $this;
        $obj->queue = $queue;

        return $obj;
    }

    public function withGroup(?string $group): self
    {
        $obj = clone $this;
        $obj->group = $group;

        return $obj;
    }

    public function withLimit(int $limit): self
    {
        $obj = clone $this;
        $obj->limit = $limit;

        return $obj;
    }

    public function useProcessPool(): bool
    {
        return $this->useProcessPool;
    }

    public function noLock(): bool
    {
        return $this->noLock;
    }

    public function getQueue(): ?string
    {
        return $this->queue;
    }

    public function getGroup(): ?string
    {
        return $this->group;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public static function create(): self
    {
        return new self();
    }
}
