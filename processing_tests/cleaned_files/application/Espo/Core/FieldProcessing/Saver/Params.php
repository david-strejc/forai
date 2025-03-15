<?php
//FORAI:F1112;DEF[C873:Params,F5072:__construct,F5073:hasOption,F5074:getOption,F5075:getRawOptions,F5076:withRawOptions,F5077:create];IMP[];EXP[C873,F5073,F5074,F5075,F5076,F5077];LANG[php]//

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

namespace Espo\Core\FieldProcessing\Saver;

/**
 * @immutable
 */
class Params
{
    /**
     * @var array<string, mixed>
     */
    private $options = [];

    public function __construct()
    {
    }

    public function hasOption(string $option): bool
    {
        return array_key_exists($option, $this->options);
    }

    /**
     * @return mixed
     */
    public function getOption(string $option)
    {
        return $this->options[$option] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    public function getRawOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array<string, mixed> $options
      */
    public function withRawOptions(array $options): self
    {
        $obj = clone $this;

        $obj->options = $options;

        return $obj;
    }

    public static function create(): self
    {
        return new self();
    }
}
