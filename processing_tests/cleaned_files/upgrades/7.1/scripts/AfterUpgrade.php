<?php
//FORAI:F141;DEF[C22:AfterUpgrade,F61:run,F62:updateConfig];IMP[F853:C659,F1662:C1385,F1716:C1438];EXP[C22,F61,F62];LANG[php]//

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

use Espo\Core\Container;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Config\ConfigWriter;

class AfterUpgrade
{
    public function run(Container $container): void
    {
        try {
            $this->updateConfig(
                $container->get('config'),
                $container->get('injectableFactory')->create(ConfigWriter::class)
            );
        }
        catch (\Throwable $e) {}
    }

    private function updateConfig(Config $config, ConfigWriter $configWriter): void
    {
        $configWriter->set('recordsPerPageSelect', 10);
        $configWriter->save();
    }
}
