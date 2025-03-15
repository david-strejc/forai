<?php
//FORAI:F1736;DEF[C1454:Column,F7713:__construct,F7714:create,F7715:getName,F7716:getType,F7717:isNotNull,F7718:getLength,F7719:getDefault,F7720:getAutoincrement,F7721:getUnsigned,F7722:getPrecision,F7723:getScale,F7724:getFixed,F7725:getCollation,F7726:getCharset,F7727:withNotNull,F7728:withLength,F7729:withDefault,F7730:withAutoincrement,F7731:withUnsigned,F7732:withPrecision,F7733:withScale,F7734:withFixed,F7735:withCollation,F7736:withCharset];IMP[];EXP[C1454,F7714,F7715,F7716,F7717,F7718,F7719,F7720,F7721,F7722,F7723,F7724,F7725,F7726,F7727,F7728,F7729,F7730,F7731,F7732,F7733,F7734,F7735,F7736];LANG[php]//

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

namespace Espo\Core\Utils\Database\Schema;

/**
 * A DB column parameters.
 */
class Column
{
    private bool $notNull = false;
    private ?int $length = null;
    private mixed $default = null;
    private ?bool $autoincrement = null;
    private ?int $precision = null;
    private ?int $scale = null;
    private ?bool $unsigned = null;
    private ?bool $fixed = null;
    private ?string $collation = null;
    private ?string $charset = null;

    private function __construct(
        private string $name,
        private string $type
    ) {}

    public static function create(string $name, string $type): self
    {
        return new self($name, $type);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isNotNull(): bool
    {
        return $this->notNull;
    }

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function getDefault(): mixed
    {
        return $this->default;
    }

    public function getAutoincrement(): ?bool
    {
        return $this->autoincrement;
    }

    public function getUnsigned(): ?bool
    {
        return $this->unsigned;
    }

    public function getPrecision(): ?int
    {
        return $this->precision;
    }

    public function getScale(): ?int
    {
        return $this->scale;
    }

    public function getFixed(): ?bool
    {
        return $this->fixed;
    }

    public function getCollation(): ?string
    {
        return $this->collation;
    }

    public function getCharset(): ?string
    {
        return $this->charset;
    }

    public function withNotNull(bool $notNull = true): self
    {
        $obj = clone $this;
        $obj->notNull = $notNull;

        return $obj;
    }

    public function withLength(?int $length): self
    {
        $obj = clone $this;
        $obj->length = $length;

        return $obj;
    }

    public function withDefault(mixed $default): self
    {
        $obj = clone $this;
        $obj->default = $default;

        return $obj;
    }

    public function withAutoincrement(?bool $autoincrement = true): self
    {
        $obj = clone $this;
        $obj->autoincrement = $autoincrement;

        return $obj;
    }

    /**
     * Unsigned. Supported only by MySQL.
     */
    public function withUnsigned(?bool $unsigned = true): self
    {
        $obj = clone $this;
        $obj->unsigned = $unsigned;

        return $obj;
    }

    public function withPrecision(?int $precision): self
    {
        $obj = clone $this;
        $obj->precision = $precision;

        return $obj;
    }

    public function withScale(?int $scale): self
    {
        $obj = clone $this;
        $obj->scale = $scale;

        return $obj;
    }

    /**
     * Fixed length. For string and binary types.
     */
    public function withFixed(?bool $fixed = true): self
    {
        $obj = clone $this;
        $obj->fixed = $fixed;

        return $obj;
    }

    public function withCollation(?string $collation): self
    {
        $obj = clone $this;
        $obj->collation = $collation;

        return $obj;
    }

    public function withCharset(?string $charset): self
    {
        $obj = clone $this;
        $obj->charset = $charset;

        return $obj;
    }
}
