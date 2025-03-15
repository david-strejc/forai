<?php
//FORAI:F665;DEF[C478:Kanban,F3135:__construct,F3136:setEntityType,F3137:setSearchParams,F3138:setCountDisabled,F3139:setOrderDisabled,F3140:setUserId,F3141:setMaxOrderNumber,F3142:getResult,F3143:getStatusField,F3144:getStatusList,F3145:getStatusIgnoreList];IMP[F927:C708,F931:C709,F926:C705,F1089:C848,F1107:C868,F1787:C1500,F1805:C1520,F1794:C1505,F1309:C1067,F1665:C1390];EXP[C478,F3136,F3137,F3138,F3139,F3140,F3141,F3142,F3143,F3144,F3145];LANG[php]//

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

namespace Espo\Tools\Kanban;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\FieldProcessing\ListLoadProcessor;
use Espo\Core\FieldProcessing\Loader\Params as FieldLoaderParams;
use Espo\Core\Record\Collection;
use Espo\Core\Record\Select\ApplierClassNameListProvider;
use Espo\Core\Record\ServiceContainer as RecordServiceContainer;
use Espo\Core\Select\SearchParams;
use Espo\Core\Select\SelectBuilderFactory;
use Espo\Core\Utils\Metadata;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;

class Kanban
{
    private const DEFAULT_MAX_ORDER_NUMBER = 50;
    private const MAX_GROUP_LENGTH = 100;

    private ?string $entityType = null;
    private bool $countDisabled = false;
    private bool $orderDisabled = false;
    private ?SearchParams $searchParams = null;
    private ?string $userId = null;
    private int $maxOrderNumber = self::DEFAULT_MAX_ORDER_NUMBER;

    public function __construct(
        private Metadata $metadata,
        private SelectBuilderFactory $selectBuilderFactory,
        private EntityManager $entityManager,
        private ListLoadProcessor $listLoadProcessor,
        private RecordServiceContainer $recordServiceContainer,
        private ApplierClassNameListProvider $applierClassNameListProvider,
    ) {}

    public function setEntityType(string $entityType): self
    {
        $this->entityType = $entityType;

        return $this;
    }

    public function setSearchParams(SearchParams $searchParams): self
    {
        $this->searchParams = $searchParams;

        return $this;
    }

    public function setCountDisabled(bool $countDisabled): self
    {
        $this->countDisabled = $countDisabled;

        return $this;
    }

    public function setOrderDisabled(bool $orderDisabled): self
    {
        $this->orderDisabled = $orderDisabled;

        return $this;
    }

    public function setUserId(string $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function setMaxOrderNumber(?int $maxOrderNumber): self
    {
        if ($maxOrderNumber === null) {
            $this->maxOrderNumber = self::DEFAULT_MAX_ORDER_NUMBER;

            return $this;
        }

        $this->maxOrderNumber = $maxOrderNumber;

        return $this;
    }

    /**
     * Get kanban record data.
     *
     * @throws Error
     * @throws Forbidden
     * @throws BadRequest
     */
    public function getResult(): Result
    {
        if (!$this->entityType) {
            throw new Error("Entity type is not specified.");
        }

        if (!$this->searchParams) {
            throw new Error("No search params.");
        }

        $searchParams = $this->searchParams;

        $recordService = $this->recordServiceContainer->get($this->entityType);

        $maxSize = $searchParams->getMaxSize();

        if ($this->countDisabled && $maxSize) {
            $searchParams = $searchParams->withMaxSize($maxSize + 1);
        }

        $query = $this->selectBuilderFactory
            ->create()
            ->from($this->entityType)
            ->withStrictAccessControl()
            ->withSearchParams($searchParams)
            ->withAdditionalApplierClassNameList(
                $this->applierClassNameListProvider->get($this->entityType)
            )
            ->build();

        $statusField = $this->getStatusField();
        $statusList = $this->getStatusList();
        $statusIgnoreList = $this->getStatusIgnoreList();

        $groupList = [];

        $repository = $this->entityManager->getRDBRepository($this->entityType);

        $hasMore = false;

        foreach ($statusList as $status) {
            if (in_array($status, $statusIgnoreList)) {
                continue;
            }

            if (!$status) {
                continue;
            }

            $itemSelectBuilder = $this->entityManager
                ->getQueryBuilder()
                ->select()
                ->clone($query);

            $itemSelectBuilder->where([
                $statusField => $status,
            ]);

            $itemQuery = $itemSelectBuilder->build();

            $newOrder = $itemQuery->getOrder();

            array_unshift($newOrder, [
                'COALESCE:(kanbanOrder.order, ' . ($this->maxOrderNumber + 1) . ')',
                'ASC',
            ]);

            if ($this->userId && !$this->orderDisabled) {
                $group = mb_substr($status, 0, self::MAX_GROUP_LENGTH);

                $itemQuery = $this->entityManager
                    ->getQueryBuilder()
                    ->select()
                    ->clone($itemQuery)
                    ->order($newOrder)
                    ->leftJoin(
                        'KanbanOrder',
                        'kanbanOrder',
                        [
                            'kanbanOrder.entityType' => $this->entityType,
                            'kanbanOrder.entityId:' => 'id',
                            'kanbanOrder.group' => $group,
                            'kanbanOrder.userId' => $this->userId,
                        ]
                    )
                    ->build();
            }

            $collectionSub = $repository
                ->clone($itemQuery)
                ->find();

            if (!$this->countDisabled) {
                $totalSub = $repository->clone($itemQuery)->count();
            } else {
                $recordCollection = Collection::createNoCount($collectionSub, $maxSize);

                $collectionSub = $recordCollection->getCollection();
                $totalSub = $recordCollection->getTotal();

                if ($totalSub === Collection::TOTAL_HAS_MORE) {
                    $hasMore = true;
                }
            }

            $loadProcessorParams = FieldLoaderParams
                ::create()
                ->withSelect($searchParams->getSelect());

            foreach ($collectionSub as $e) {
                $this->listLoadProcessor->process($e, $loadProcessorParams);

                $recordService->prepareEntityForOutput($e);
            }

            /** @var Collection<Entity> $itemRecordCollection */
            $itemRecordCollection = new Collection($collectionSub, $totalSub);

            $groupList[] = new GroupItem($status, $itemRecordCollection);
        }

        $total = !$this->countDisabled ?
            $repository->clone($query)->count() :
            ($hasMore ? Collection::TOTAL_HAS_MORE : Collection::TOTAL_HAS_NO_MORE);

        return new Result($groupList, $total);
    }

    /**
     * @throws Error
     */
    private function getStatusField(): string
    {
        assert(is_string($this->entityType));

        $statusField = $this->metadata->get(['scopes', $this->entityType, 'statusField']);

        if (!$statusField) {
            throw new Error("No status field for entity type '$this->entityType'.");
        }

        return $statusField;
    }

    /**
     * @return string[]
     * @throws Error
     */
    private function getStatusList(): array
    {
        assert(is_string($this->entityType));

        $statusField = $this->getStatusField();

        $statusList = $this->metadata->get(['entityDefs', $this->entityType, 'fields', $statusField, 'options']);

        if (empty($statusList)) {
            throw new Error("No options for status field for entity type '$this->entityType'.");
        }

        return $statusList;
    }

    /**
     * @return string[]
     */
    private function getStatusIgnoreList(): array
    {
        assert(is_string($this->entityType));

        return $this->metadata->get(['scopes', $this->entityType, 'kanbanStatusIgnoreList'], []);
    }
}
