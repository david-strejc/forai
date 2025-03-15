<?php
//FORAI:F2425;DEF[C2034:SenderFactory,F10231:__construct,F10232:createForUser];IMP[F1989:C1619,F846:C649];EXP[C2034,F10232];LANG[php]//

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

namespace Espo\Modules\Crm\Tools\Meeting\Invitation;

use Espo\Core\Binding\BindingContainerBuilder;
use Espo\Core\InjectableFactory;
use Espo\Entities\User;

/**
 * @since 9.0.0
 */
class SenderFactory
{
    public function __construct(
        private InjectableFactory $injectableFactory,
    ) {}

    /**
     * Create a sender for a user. The SMTP of the user will be used (if not disabled in the config).
     */
    public function createForUser(User $user): Sender
    {
        return $this->injectableFactory->createWithBinding(
            Sender::class,
            BindingContainerBuilder::create()
                ->bindInstance(User::class, $user)
                ->build()
        );
    }
}
