<?php
//FORAI:F990;DEF[C758:SmsUserSetup,F4511:__construct,F4512:getData,F4513:verifyData];IMP[F988:C756,F927:C708,F1072:C835];EXP[C758,F4512,F4513];LANG[php]//

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

namespace Espo\Core\Authentication\TwoFactor\Sms;

use Espo\Core\Authentication\TwoFactor\Exceptions\NotConfigured;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Portal\Utils\Config;
use Espo\Entities\User;
use Espo\Core\Authentication\TwoFactor\UserSetup;

use stdClass;

/**
 * @noinspection PhpUnused
 */
class SmsUserSetup implements UserSetup
{
    public function __construct(
        private Util $util,
        private Config $config
    ) {}

    public function getData(User $user): stdClass
    {
        if (!$this->config->get('smsProvider')) {
            throw new NotConfigured("No SMS provider.");
        }

        return (object) [
            'phoneNumberList' => $user->getPhoneNumberGroup()->getNumberList(),
        ];
    }

    public function verifyData(User $user, stdClass $payloadData): bool
    {
        $code = $payloadData->code ?? null;

        if ($code === null) {
            throw new BadRequest("No code.");
        }

        $codeModified = str_replace(' ', '', trim($code));

        if (!$codeModified) {
            return false;
        }

        return $this->util->verifyCode($user, $codeModified);
    }
}
