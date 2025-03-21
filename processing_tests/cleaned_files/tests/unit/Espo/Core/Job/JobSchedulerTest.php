<?php
//FORAI:F2765;DEF[C2361:JobSchedulerTest,F11870:setUp,F11871:testSchedule1,F11872:testSchedule2,F11873:testSchedule3];IMP[F1548:C1277,F1549:C1279,F1559:C1288,F268:C128,F2603:C2212,F2604:C2213];EXP[C2361,F11870,F11871,F11872,F11873];LANG[php]//

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

namespace tests\unit\Espo\Core\Job;

use Espo\Core\Job\JobScheduler;
use Espo\Core\Job\QueueName;
use Espo\Core\Utils\DateTime;
use Espo\Core\Job\Job\Data;

use Espo\ORM\EntityManager;

use Espo\Entities\Job as JobEntity;

use tests\unit\testClasses\Core\Job\TestJob;
use tests\unit\testClasses\Core\Job\TestJobDataLess;

use DateTimeImmutable;
use DateInterval;

class JobSchedulerTest extends \PHPUnit\Framework\TestCase
{
    private $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManager::class);
    }

    public function testSchedule1(): void
    {
        $scheduler = new JobScheduler($this->entityManager);

        $jobEntity = $this->createMock(JobEntity::class);

        $time = new DateTimeImmutable();

        $delay = DateInterval::createFromDateString('1 minute');

        $this->entityManager
            ->expects($this->once())
            ->method('createEntity')
            ->with(
                JobEntity::ENTITY_TYPE,
                [
                    'name' => TestJob::class,
                    'className' => TestJob::class,
                    'queue' => QueueName::Q0,
                    'group' => null,
                    'data' => (object) [
                        'test' => '1',
                    ],
                    'executeTime' => $time->modify('+1 minute')->format(DateTime::SYSTEM_DATE_TIME_FORMAT),
                    'targetId' => null,
                    'targetType' => null,
                ]
            )
            ->willReturn($jobEntity);

        $jobEntityReturned = $scheduler
            ->setClassName(TestJob::class)
            ->setQueue(QueueName::Q0)
            ->setData([
                'test' => '1',
            ])
            ->setTime($time)
            ->setDelay($delay)
            ->schedule();

        $this->assertSame($jobEntityReturned, $jobEntity);
    }

    public function testSchedule2(): void
    {
        $scheduler = new JobScheduler($this->entityManager);

        $jobEntity = $this->createMock(JobEntity::class);

        $time = new DateTimeImmutable();

        $this->entityManager
            ->expects($this->once())
            ->method('createEntity')
            ->with(
                JobEntity::ENTITY_TYPE,
               [
                    'name' => TestJob::class,
                    'className' => TestJob::class,
                    'queue' => null,
                    'group' => 'g-1',
                    'data' => (object) [
                        'test' => '1',
                    ],
                    'executeTime' => $time->format(DateTime::SYSTEM_DATE_TIME_FORMAT),
                    'targetId' => 'test-id',
                    'targetType' => 'TestType',
                ]
            )
            ->willReturn($jobEntity);

        $data = Data
            ::create([
                'test' => '1',
            ])
            ->withTargetId('test-id')
            ->withTargetType('TestType');

        $jobEntityReturned = $scheduler
            ->setClassName(TestJob::class)
            ->setGroup('g-1')
            ->setData($data)
            ->setTime($time)
            ->schedule();

        $this->assertSame($jobEntityReturned, $jobEntity);
    }

    public function testSchedule3(): void
    {
        $scheduler = new JobScheduler($this->entityManager);

        $jobEntity = $this->createMock(JobEntity::class);

        $this->entityManager
            ->expects($this->once())
            ->method('createEntity')
            ->with(
                JobEntity::ENTITY_TYPE,
                $this->callback(
                    function (array $data): bool {
                        return is_string($data['executeTime']);
                    }
                )
            )
            ->willReturn($jobEntity);


        $jobEntityReturned = $scheduler
            ->setClassName(TestJobDataLess::class)
            ->schedule();

        $this->assertSame($jobEntityReturned, $jobEntity);
    }
}
