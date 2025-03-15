<?php
//FORAI:F1717;DEF[C1439:MissingDefaultParamsSaver,F7657:__construct,F7658:process];IMP[F1662:C1385];EXP[C1439,F7658];LANG[php]//

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

namespace Espo\Core\Utils\Config;

use Espo\Core\Utils\Config;
use Espo\Core\Utils\File\Manager as FileManager;

use RuntimeException;

class MissingDefaultParamsSaver
{
    private string $defaultConfigPath = 'application/Espo/Resources/defaults/config.php';

    public function __construct(
        private Config $config,
        private ConfigWriter $configWriter,
        private FileManager $fileManager
    ) {}

    public function process(): void
    {
        $data = $this->fileManager->getPhpSafeContents($this->defaultConfigPath);

        if (!is_array($data)) {
            throw new RuntimeException();
        }

        /** @var array<string, mixed> $data */

        $newData = [];

        foreach ($data as $param => $value) {
            if ($this->config->has($param)) {
                continue;
            }

            $newData[$param] = $value;
        }

        if (!count($newData)) {
            return;
        }

        $this->configWriter->setMultiple($newData);
        $this->configWriter->save();
    }
}
