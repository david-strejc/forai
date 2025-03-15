<?php
//FORAI:F1122;DEF[C881:ArgumentList,F5134:__construct,F5135:getLastValidKey,F5136:rewind,F5137:getArgumentByIndex,F5138:current,F5139:key,F5140:next,F5141:valid,F5142:offsetExists,F5143:offsetGet,F5144:offsetSet,F5145:offsetUnset,F5146:count,F5147:seek];IMP[];EXP[C881,F5135,F5136,F5137,F5138,F5139,F5140,F5141,F5142,F5143,F5144,F5145,F5146,F5147];LANG[php]//

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

namespace Espo\Core\Formula;

use BadMethodCallException;
use OutOfBoundsException;
use Iterator;
use Countable;
use ArrayAccess;
use SeekableIterator;

/**
 * A list of function arguments.
 *
 * @implements ArrayAccess<int, Argument>
 * @implements Iterator<Argument>
 * @implements SeekableIterator<int, Argument>
 */
class ArgumentList implements Evaluatable, Iterator, Countable, ArrayAccess, SeekableIterator
{
    private int $position = 0;

    /**
     * @param array<int, mixed> $dataList
     */
    public function __construct(private array $dataList)
    {}

    private function getLastValidKey(): int
    {
        $keys = array_keys($this->dataList);

        $i = end($keys);

        if ($i === false) {
            return -1;
        }

        while ($i > 0) {
            if (array_key_exists($i, $this->dataList)) {
                break;
            }

            $i--;
        }

        return $i;
    }

    public function rewind(): void
    {
        $this->position = 0;

        while (!$this->valid() && $this->position <= $this->getLastValidKey()) {
            $this->position ++;
        }
    }

    private function getArgumentByIndex(int $index): Argument
    {
        return new Argument($this->dataList[$index]);
    }

    /**
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->getArgumentByIndex($this->position);
    }

    /**
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->position;
    }

    public function next(): void
    {
        do {
            $this->position ++;
            $next = false;

            if (
                !$this->valid() &&
                $this->position <= $this->getLastValidKey()
            ) {
                $next = true;
            }
        } while ($next);
    }

    public function valid(): bool
    {
        return array_key_exists($this->position, $this->dataList);
    }

    /**
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->dataList);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            return null;
        }

        return $this->getArgumentByIndex($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        throw new BadMethodCallException('Setting is not allowed.');
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        throw new BadMethodCallException('Unsetting is not allowed.');
    }

    public function count(): int
    {
        return count($this->dataList);
    }

    /**
     * @param int $offset
     */
    public function seek($offset): void
    {
        $this->position = $offset;

        if (!$this->valid()) {
            throw new OutOfBoundsException("Invalid seek offset ($offset).");
        }
    }
}
