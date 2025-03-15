<?php
//FORAI:F2877;DEF[C2469:FieldValidationTest<BaseTestCase>,F12501:setFieldsDefs,F12502:testRequiredVarchar1,F12503:testUpdateRequiredVarchar1,F12504:testRequiredVarchar2,F12505:testRequiredVarchar3,F12506:testMaxLength1,F12507:testMaxLength2,F12508:testRequiredLink1,F12509:getAdminUser,F12510:testRequiredLink2,F12511:testRequiredLinkMultiple1,F12512:testRequiredCurrency1,F12513:testRequiredCurrency2,F12514:testRequiredCurrency3,F12515:testRequiredEnum1,F12516:testRequiredEnum2,F12517:testRequiredEnum3,F12518:testSkipRequired,F12519:testSettings,F12520:testCurrencyValid,F12521:testPhoneNumber];IMP[F851:C656,F927:C708,F1802:C1518,F1794:C1505,F1795:C1508,F1716:C1438,F2374:C1986,F595:C411];EXP[C2469,F12501,F12502,F12503,F12504,F12505,F12506,F12507,F12508,F12509,F12510,F12511,F12512,F12513,F12514,F12515,F12516,F12517,F12518,F12519,F12520,F12521];LANG[php]//

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

namespace tests\integration\Espo\Record;

use Espo\Core\Application;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Record\CreateParams;
use Espo\Core\Record\ServiceContainer;
use Espo\Core\Record\UpdateParams;
use Espo\Core\Utils\Config\ConfigWriter;
use Espo\Entities\User;
use Espo\Modules\Crm\Entities\Account;
use Espo\Modules\Crm\Entities\Lead;
use Espo\ORM\EntityManager;
use Espo\Tools\App\SettingsService as SettingsService;
use tests\integration\Core\BaseTestCase;

class FieldValidationTest extends BaseTestCase
{
    private function setFieldsDefs(Application $app, string $entityType, array $data)
    {
        $metadata = $app->getContainer()->get('metadata');

        $metadata->set('entityDefs', $entityType, [
            'fields' => $data,
        ]);

        $metadata->save();
    }

    public function testRequiredVarchar1()
    {
        $app = $this->createApplication();

        $this->setFieldsDefs($app, 'Account', [
            'name' => [
                'required' => true,
            ],
        ]);

        $this->expectException(BadRequest::class);

        $app->getContainer()
            ->getByClass(ServiceContainer::class)
            ->get('Account')
            ->create((object) [
                'name' => null,
            ], CreateParams::create());
    }

    public function testUpdateRequiredVarchar1()
    {
        $app = $this->createApplication();

        $this->setFieldsDefs($app, 'Account', [
            'name' => [
                'required' => true,
            ],
        ]);

        $entity = $app->getContainer()
            ->getByClass(ServiceContainer::class)
            ->get('Account')
            ->create(
                (object) [
                    'name' => 'test'
                ],
                CreateParams::create()
            );

        $this->expectException(BadRequest::class);

        $app->getContainer()
            ->getByClass(ServiceContainer::class)
            ->get('Account')
            ->update(
                $entity->getId(),
                (object) [
                    'name' => '',
                ],
                UpdateParams::create()
            );
    }

    public function testRequiredVarchar2()
    {
        $app = $this->createApplication();

        $this->setFieldsDefs($app, 'Account', [
            'name' => [
                'required' => true,
            ],
        ]);

        $this->expectException(BadRequest::class);

        $app->getContainer()
            ->getByClass(ServiceContainer::class)
            ->get('Account')
            ->create(
                (object) [

                ],
                CreateParams::create()
            );
    }

    public function testRequiredVarchar3()
    {
        $app = $this->createApplication();

        $this->setFieldsDefs($app, 'Account', [
            'name' => [
                'required' => true,
            ],
        ]);

        $app->getContainer()
            ->getByClass(ServiceContainer::class)
            ->get('Account')
            ->create((object) [
                'name' => 'test',
            ], CreateParams::create());

        $this->assertTrue(true);
    }

    public function testMaxLength1()
    {
        $app = $this->createApplication();

        $this->setFieldsDefs($app, 'Account', [
            'name' => [
                'required' => true,
                'maxLength' => 5,
            ],
        ]);

        $this->expectException(BadRequest::class);

        $app->getContainer()
            ->getByClass(ServiceContainer::class)
            ->get('Account')
            ->create((object) [
                'name' => '123456',
            ], CreateParams::create());
    }

    public function testMaxLength2()
    {
        $app = $this->createApplication();

        $this->setFieldsDefs($app, 'Account', [
            'name' => [
                'required' => true,
                'maxLength' => 5,
            ]
        ]);

        $app->getContainer()
            ->getByClass(ServiceContainer::class)
            ->get('Account')
            ->create((object) [
                'name' => '12345',
            ], CreateParams::create());

        $this->assertTrue(true);
    }

    public function testRequiredLink1()
    {
        $app = $this->createApplication();

        $this->setFieldsDefs($app, 'Account', [
            'assignedUser' => [
                'required' => true,
            ]
        ]);

        $this->expectException(BadRequest::class);

        $app->getContainer()
            ->getByClass(ServiceContainer::class)
            ->get('Account')
            ->create((object) [
                'name' => 'test',
                'assignedUserId' => null,
            ], CreateParams::create());
    }

    private function getAdminUser(): User
    {
        $repository = $this->getContainer()
            ->getByClass(EntityManager::class)
            ->getRDBRepositoryByClass(User::class);

        $user = $repository
            ->where(['type' => User::TYPE_ADMIN])
            ->findOne();

        if (!$user) {
            $user = $repository->getNew();
            $user->set('userName', 'test-admin');
            $user->set('type', User::TYPE_ADMIN);

            $repository->save($user);
        }

        return $user;
    }

    public function testRequiredLink2()
    {
        $app = $this->createApplication();

        $this->setFieldsDefs($app, 'Account', [
            'assignedUser' => [
                'required' => true,
            ]
        ]);

        $app->getContainer()
            ->getByClass(ServiceContainer::class)
            ->get('Account')
            ->create((object) [
                'name' => 'test',
                'assignedUserId' => $this->getAdminUser()->getId(),
            ], CreateParams::create());

        $this->assertTrue(true);
    }

    public function testRequiredLinkMultiple1()
    {
        $app = $this->createApplication();

        $this->setFieldsDefs($app, 'Account', [
            'teams' => [
                'required' => true,
            ]
        ]);

        $this->expectException(BadRequest::class);

        $app->getContainer()
            ->getByClass(ServiceContainer::class)
            ->get('Account')
            ->create((object) [
                'name' => 'test',
                'teamsIds' => [],
            ], CreateParams::create());
    }

    public function testRequiredCurrency1()
    {
        $app = $this->createApplication();

        $this->setFieldsDefs($app, 'Lead', [
            'opportunityAmount' => [
                'required' => true,
            ]
        ]);

        $this->expectException(BadRequest::class);

        $app->getContainer()
            ->getByClass(ServiceContainer::class)
            ->get('Lead')
            ->create((object) [
                'lastName' => 'test',
                'opportunityAmount' => null,
            ], CreateParams::create());
    }

    public function testRequiredCurrency2()
    {
        $app = $this->createApplication();

        $this->setFieldsDefs($app, 'Lead', [
            'opportunityAmount' => [
                'required' => true,
            ]
        ]);

        $app->getContainer()
            ->getByClass(ServiceContainer::class)
            ->get('Lead')
            ->create((object) [
                'lastName' => 'test',
                'opportunityAmount' => 100,
                'opportunityAmountCurrency' => null,
            ], CreateParams::create());

        $this->assertTrue(true);
    }

    public function testRequiredCurrency3()
    {
        $app = $this->createApplication();

        $this->setFieldsDefs($app, 'Lead', [
            'opportunityAmount' => [
                'required' => true,
            ]
        ]);

        $app->getContainer()
            ->getByClass(ServiceContainer::class)
            ->get('Lead')
            ->create((object) [
                'lastName' => 'test',
                'opportunityAmount' => 100,
                'opportunityAmountCurrency' => 'USD',
            ], CreateParams::create());

        $this->assertTrue(true);
    }

    public function testRequiredEnum1()
    {
        $app = $this->createApplication();

        $this->setFieldsDefs($app, 'Lead', [
            'status' => [
                'required' => true,
                'default' => null,
            ]
        ]);

        $app = $this->createApplication();

        $this->expectException(BadRequest::class);

        $app->getContainer()
            ->getByClass(ServiceContainer::class)
            ->get('Lead')
            ->create((object) [
                'lastName' => 'test',
            ], CreateParams::create());
    }

    public function testRequiredEnum2()
    {
        $app = $this->createApplication();

        $this->setFieldsDefs($app, 'Lead', [
            'status' => [
                'required' => true,
            ]
        ]);

        $this->expectException(BadRequest::class);

        $app->getContainer()
            ->getByClass(ServiceContainer::class)
            ->get('Lead')
            ->create((object) [
                'lastName' => 'test',
                'status' => null,
            ], CreateParams::create());
    }

    public function testRequiredEnum3()
    {
        $app = $this->createApplication();

        $this->setFieldsDefs($app, 'Lead', [
            'status' => [
                'required' => true,
            ]
        ]);

        $app->getContainer()
            ->getByClass(ServiceContainer::class)
            ->get('Lead')
            ->create((object) [
                'lastName' => 'test',
                'status' => 'New',
            ], CreateParams::create());

        $this->assertTrue(true);
    }

    public function testSkipRequired()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->getContainer()
            ->getByClass(ServiceContainer::class)
            ->get('Meeting')
            ->create((object) [
                'name' => 'test',
                'dateStart' => '2021-01-01 00:00:00',
                'duration' => 1000,
                'assignedUserId' => $this->getAdminUser()->getId(),
            ], CreateParams::create());

        $this->assertTrue(true);
    }

    public function testSettings()
    {
        $service = $this->getContainer()->get('injectableFactory')->create(SettingsService::class);

        $this->expectException(BadRequest::class);

        $service->setConfigData((object) [
            'activitiesEntityList' => 'should-be-array',
        ]);
    }

    public function testCurrencyValid(): void
    {
        $service = $this->getContainer()
            ->getByClass(ServiceContainer::class)
            ->getByClass(Lead::class);

        /** @noinspection PhpUnhandledExceptionInspection */
        $service->create((object) [
            'lastName' => 'Test 1',
            'opportunityAmount' => '100.10',
        ], CreateParams::create());

        /** @noinspection PhpUnhandledExceptionInspection */
        $service->create((object) [
            'lastName' => 'Test 2',
            'opportunityAmount' => '100',
        ], CreateParams::create());

        /** @noinspection PhpUnhandledExceptionInspection */
        $service->create((object) [
            'lastName' => 'Test 3',
            'opportunityAmount' => '',
        ], CreateParams::create());

        /** @noinspection PhpUnhandledExceptionInspection */
        $service->create((object) [
            'lastName' => 'Test 4',
            'opportunityAmount' => null,
        ], CreateParams::create());

        $this->expectException(BadRequest::class);

        /** @noinspection PhpUnhandledExceptionInspection */
        $service->create((object) [
            'lastName' => 'Test Bad 1',
            'opportunityAmount' => 'bad-value',
        ], CreateParams::create());
    }

    public function testPhoneNumber(): void
    {
        $service = $this->getContainer()
            ->getByClass(ServiceContainer::class)
            ->getByClass(Account::class);

        $configWriter = $this->getInjectableFactory()->create(ConfigWriter::class);
        $configWriter->set('phoneNumberExtensions', true);
        $configWriter->save();

        /** @noinspection PhpUnhandledExceptionInspection */
        $service->create((object)[
            'name' => 'Test 1',
            'phoneNumberData' => [
                (object)[
                    'phoneNumber' => '+38 09 044 433 22 ext. 001',
                ],
            ],
        ], CreateParams::create());

        $thrown = false;

        try {
            /** @noinspection PhpUnhandledExceptionInspection */
            $service->create((object)[
                'name' => 'Test 2',
                'phoneNumberData' => [
                    (object)[
                        'phoneNumber' => '+38 09 044 433 22 ext. ABC',
                    ],
                    (object)[
                        'phoneNumber' => '+38 09 044 433 33 ext. 1234567',
                    ],
                ],
            ], CreateParams::create());
        } catch (BadRequest) {
            $thrown = true;
        }

        $this->assertTrue($thrown);
    }
}
