<?php
//FORAI:F1585;DEF[C1314:AfterUpgrade,F6860:__construct,F6861:run];IMP[F853:C659,F846:C649,F1662:C1385];EXP[C1314,F6861];LANG[php]//

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

namespace Espo\Core\Upgrades\Migrations\V8_1;

use Espo\Core\Container;
use Espo\Core\Upgrades\Migration\Script;
use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Config;

class AfterUpgrade implements Script
{
    public function __construct(
        private Container $container
    ) {}

    public function run(): void
    {
        $config = $this->container->getByClass(Config::class);

        $configWriter = $this->container->getByClass(InjectableFactory::class)
            ->create(Config\ConfigWriter::class);

        $configWriter->setMultiple([
            'phoneNumberNumericSearch' => false,
            'phoneNumberInternational' => false,
        ]);

        if ($config->get('pdfEngine') === 'Tcpdf') {
            $configWriter->set('pdfEngine', 'Dompdf');
        }

        $configWriter->save();
    }
}
