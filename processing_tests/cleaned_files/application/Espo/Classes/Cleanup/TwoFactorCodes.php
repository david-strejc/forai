<?php
//FORAI:F2312;DEF[C1924:TwoFactorCodes,F9654:__construct,F9655:process];IMP[F1662:C1385,F288:C149];EXP[C1924,F9655];LANG[php]//

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

namespace Espo\Classes\Cleanup;

use Espo\Core\Cleanup\Cleanup;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\DateTime as DateTimeUtil;

use Espo\ORM\EntityManager;

use Espo\Entities\TwoFactorCode;

use DateTime;

class TwoFactorCodes implements Cleanup
{
    private const PERIOD = '5 days';

    private $config;

    private $entityManager;

    public function __construct(Config $config, EntityManager $entityManager)
    {
        $this->config = $config;
        $this->entityManager = $entityManager;
    }

    public function process(): void
    {
        $period = '-' . $this->config->get('cleanupTwoFactorCodesPeriod', self::PERIOD);

        $from = (new DateTime())
            ->modify($period)
            ->format(DateTimeUtil::SYSTEM_DATE_TIME_FORMAT);

        $query = $this->entityManager
            ->getQueryBuilder()
            ->delete()
            ->from(TwoFactorCode::ENTITY_TYPE)
            ->where([
                'createdAt<' => $from,
            ])
            ->build();

        $this->entityManager
            ->getQueryExecutor()
            ->execute($query);
    }
}
