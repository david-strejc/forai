<?php
//FORAI:F2053;DEF[C1668:Cron,F9122:__construct,F9123:run];IMP[F1546:C1276,F1662:C1385,F1656:C1380];EXP[C1668,F9123];LANG[php]//

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

namespace Espo\Core\ApplicationRunners;

use Espo\Core\Application\Runner;
use Espo\Core\Job\JobManager;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Log;

/**
 * Runs Cron.
 */
class Cron implements Runner
{
    use Cli;
    use SetupSystemUser;

    public function __construct(private JobManager $jobManager, private Config $config, private Log $log)
    {}

    public function run(): void
    {
        if ($this->config->get('cronDisabled')) {
            $this->log->warning("Cron is not run because it's disabled with 'cronDisabled' param.");

            return;
        }

        $this->jobManager->process();
    }
}
