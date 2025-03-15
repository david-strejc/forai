<?php
//FORAI:F1772;DEF[C1485:AttributeDefs,F7859:__construct,F7860:create,F7861:getName,F7862:getType,F7863:withType,F7864:withDbType,F7865:withNotStorable,F7866:withLength,F7867:withDefault,F7868:hasParam,F7869:getParam,F7870:withParam,F7871:withoutParam,F7872:withParamsMerged,F7873:toAssoc];IMP[F413:C250,F348:C201];EXP[C1485,F7860,F7861,F7862,F7863,F7864,F7865,F7866,F7867,F7868,F7869,F7870,F7871,F7872,F7873];LANG[php]//

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

namespace Espo\Core\Utils\Database\Orm\Defs;

use Espo\Core\Utils\Util;
use Espo\ORM\Defs\Params\AttributeParam;
use Espo\ORM\Type\AttributeType;

/**
 * @immutable
 */
class AttributeDefs
{
    /** @var array<string, mixed> */
    private array $params = [];

    private function __construct(private string $name) {}

    public static function create(string $name): self
    {
        return new self($name);
    }

    /**
     * Get an attribute name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get a type.
     *
     * @return AttributeType::*
     */
    public function getType(): ?string
    {
        /** @var ?AttributeType::* $value */
        $value = $this->getParam(AttributeParam::TYPE);

        return $value;
    }

    /**
     * Clone with a type.
     *
     * @param AttributeType::* $type
     */
    public function withType(string $type): self
    {
        return $this->withParam(AttributeParam::TYPE, $type);
    }

    /**
     * Clone with a DB type.
     */
    public function withDbType(string $dbType): self
    {
        return $this->withParam(AttributeParam::DB_TYPE, $dbType);
    }

    /**
     * Clone with not-storable.
     */
    public function withNotStorable(bool $value = true): self
    {
        return $this->withParam(AttributeParam::NOT_STORABLE, $value);
    }

    /**
     * Clone with a length.
     */
    public function withLength(int $length): self
    {
        return $this->withParam(AttributeParam::LEN, $length);
    }

    /**
     * Clone with a default value.
     */
    public function withDefault(mixed $value): self
    {
        return $this->withParam(AttributeParam::DEFAULT, $value);
    }

    /**
     * Whether a parameter is set.
     */
    public function hasParam(string $name): bool
    {
        return array_key_exists($name, $this->params);
    }

    /**
     * Get a parameter value.
     */
    public function getParam(string $name): mixed
    {
        return $this->params[$name] ?? null;
    }

    /**
     * Clone with a parameter.
     */
    public function withParam(string $name, mixed $value): self
    {
        $obj = clone $this;
        $obj->params[$name] = $value;

        return $obj;
    }

    /**
     * Clone without a parameter.
     */
    public function withoutParam(string $name): self
    {
        $obj = clone $this;
        unset($obj->params[$name]);

        return $obj;
    }

    /**
     * Clone with parameters merged.
     *
     * @param array<string, mixed> $params
     */
    public function withParamsMerged(array $params): self
    {
        $obj = clone $this;

        /** @var array<string, mixed> $params */
        $params = Util::merge($this->params, $params);

        $obj->params = $params;

        return $obj;
    }

    /**
     * To an associative array.
     *
     * @return array<string, mixed>
     */
    public function toAssoc(): array
    {
        return $this->params;
    }
}
