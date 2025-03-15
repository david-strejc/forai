<?php
//FORAI:F2755;DEF[C2353:EmailSenderTest<TestCase>,F11847:setUp,F11848:createEmail,F11849:testSend1];IMP[F846:C649,F1632:C1355,F1447:C1194,F1427:C1178,F1422:C1172,F1423:C1174,F1475:C1218,F1413:C1164,F1662:C1385,F1656:C1380];EXP[C2353,F11847,F11848,F11849];LANG[php]//

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

namespace tests\unit\Espo\Core\Mail;

use Laminas\Mail\Transport\Smtp as SmtpTransport;

use Espo\Core\InjectableFactory;
use Espo\Entities\Email;
use Espo\Core\FileStorage\Manager;
use Espo\Core\Mail\Account\Account;
use Espo\Core\Mail\Account\SendingAccountProvider;
use Espo\Core\Mail\ConfigDataProvider;
use Espo\Core\Mail\EmailSender;
use Espo\Core\Mail\Sender;
use Espo\Core\Mail\Smtp\TransportFactory;
use Espo\Core\Mail\SmtpParams;
use Espo\Core\ORM\EntityManager;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Log;

use PHPUnit\Framework\TestCase;

class EmailSenderTest extends TestCase
{
    public function setUp(): void
    {
        $this->config = $this->createMock(Config::class);
        $entityManager = $this->createMock(EntityManager::class);
        $injectableFactory = $this->createMock(InjectableFactory::class);
        $transportFactory = $this->createMock(TransportFactory::class);
        $this->transport = $this->createMock(SmtpTransport::class);

        $accountProvider = $this->createMock(SendingAccountProvider::class);
        $log = $this->createMock(Log::class);

        $emailSender = new EmailSender(
            $this->config,
            $accountProvider,
            $injectableFactory
        );

        $configDataProvider = $this->createMock(ConfigDataProvider::class);

        $sender = new Sender(
            $this->config,
            $entityManager,
            $log,
            $transportFactory,
            $accountProvider,
            $this->createMock(Manager::class),
            $configDataProvider
        );

        $this->emailSender = $emailSender;

        $injectableFactory
            ->expects($this->any())
            ->method('createWithBinding')
            ->willReturn($sender);

        $transportFactory
            ->expects($this->any())
            ->method('create')
            ->willReturn($this->transport);


        $account = $this->createMock(Account::class);

        $account
            ->expects($this->once())
            ->method('getSmtpParams')
            ->willReturn(
                SmtpParams::create('test-server', 85)
            );

        $accountProvider
            ->expects($this->once())
            ->method('getSystem')
            ->willReturn($account);

        $configDataProvider
            ->expects($this->any())
            ->method('getSystemOutboundAddress')
            ->willReturn(null);
    }

    protected function createEmail(array $data) : Email
    {
        $email = $this->getMockBuilder(Email::class)->disableOriginalConstructor()->getMock();

        $email
            ->expects($this->any())
            ->method('get')
            ->will(
                $this->returnCallback(
                    function ($name) use ($data) {
                        return $data[$name] ?? null;
                    }
                )
            );

        $email
            ->expects($this->any())
            ->method('getBodyPlainForSending')
            ->willReturn('test');

        $email
            ->expects($this->any())
            ->method('isNew')
            ->willReturn(true);

        return $email;
    }

    public function testSend1()
    {
        $email = $this->createEmail([
            'name' => 'test',
            'from' => 'test@tester.com',
        ]);

        $this->transport
            ->expects($this->once())
            ->method('send');

        $this->emailSender->send($email);
    }
}
