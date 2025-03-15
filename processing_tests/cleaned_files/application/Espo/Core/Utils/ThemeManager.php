<?php
//FORAI:F1644;DEF[C1368:ThemeManager,F7217:__construct,F7218:getName,F7219:getStylesheet,F7220:getLogoSrc,F7221:isDark];IMP[F1702:C1424];EXP[C1368,F7218,F7219,F7220,F7221];LANG[php]//

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

namespace Espo\Core\Utils;

use Espo\Core\Utils\Theme\MetadataProvider;

class ThemeManager
{
    private string $defaultName = 'Espo';

    private string $defaultLogoSrc = 'client/img/logo.svg';

    public function __construct(
        private Config $config,
        private Metadata $metadata,
        private MetadataProvider $metadataProvider,
    ) {}

    public function getName(): string
    {
        return $this->config->get('theme') ?? $this->defaultName;
    }

    public function getStylesheet(): string
    {
        return $this->metadataProvider->getStylesheet($this->getName());
    }

    public function getLogoSrc(): string
    {
        return $this->metadata->get(['themes', $this->getName(), 'logo']) ?? $this->defaultLogoSrc;
    }

    public function isDark(): bool
    {
        return $this->metadataProvider->isDark($this->getName());
    }
}
