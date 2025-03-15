<?php
//FORAI:F1310;DEF[C1069:SelectBuilder,C1070:names,F5543:__construct,F5544:from,F5545:clone,F5546:build,F5547:buildQueryBuilder,F5548:forUser,F5549:withSearchParams,F5550:withStrictAccessControl,F5551:withAccessControlFilter,F5552:withDefaultOrder,F5553:withWherePermissionCheck,F5554:withComplexExpressionsForbidden,F5555:withTextFilter,F5556:withPrimaryFilter,F5557:withBoolFilter,F5558:withBoolFilterList,F5559:withWhere,F5560:withAdditionalApplierClassNameList,F5561:applyPrimaryFilter,F5562:applyBoolFilterList,F5563:applyTextFilter,F5564:applyAccessControlFilter,F5565:applyDefaultOrder,F5566:applyWhereItemList,F5567:applyWhereItem,F5568:applyFromSearchParams,F5569:applyAdditional,F5570:createWhereApplier,F5571:createSelectApplier,F5572:createOrderApplier,F5573:createLimitApplier,F5574:createAccessControlFilterApplier,F5575:createTextFilterApplier,F5576:createPrimaryFilterApplier,F5577:createBoolFilterListApplier,F5578:createAdditionalApplier];IMP[F927:C708,F926:C705,F1341:C1097,F1330:C1087,F1322:C1080,F1394:C1144,F1352:C1107,F1321:C1078,F1312:C1071,F1387:C1137,F1360:C1113,F1381:C1132,F1314:C1073,F1347:C1100,F1344:C1099,F1343:C1098,F377:C220,F369:C213];EXP[C1069,C1070,F5544,F5545,F5546,F5547,F5548,F5549,F5550,F5551,F5552,F5553,F5554,F5555,F5556,F5557,F5558,F5559,F5560,F5561,F5562,F5563,F5564,F5565,F5566,F5567,F5568,F5569,F5570,F5571,F5572,F5573,F5574,F5575,F5576,F5577,F5578];LANG[php]//

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

namespace Espo\Core\Select;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Select\Applier\Factory as ApplierFactory;
use Espo\Core\Select\Where\Params as WhereParams;
use Espo\Core\Select\Where\Item as WhereItem;
use Espo\Core\Select\Order\Params as OrderParams;
use Espo\Core\Select\Text\FilterParams as TextFilterParams;
use Espo\Core\Select\Where\Applier as WhereApplier;
use Espo\Core\Select\Select\Applier as SelectApplier;
use Espo\Core\Select\Order\Applier as OrderApplier;
use Espo\Core\Select\AccessControl\Applier as AccessControlFilterApplier;
use Espo\Core\Select\Primary\Applier as PrimaryFilterApplier;
use Espo\Core\Select\Bool\Applier as BoolFilterListApplier;
use Espo\Core\Select\Text\Applier as TextFilterApplier;
use Espo\Core\Select\Applier\Appliers\Limit as LimitApplier;
use Espo\Core\Select\Applier\Appliers\Additional as AdditionalApplier;

use Espo\ORM\Query\Select as Query;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;

use Espo\Entities\User;

use LogicException;

/**
 * Builds select queries for ORM. Applies search parameters(passed from front-end),
 * ACL restrictions, filters, etc.
 */
class SelectBuilder
{
    private ?string $entityType = null;
    private ?OrmSelectBuilder $queryBuilder = null;
    private ?Query $sourceQuery = null;
    private ?SearchParams $searchParams = null;
    private bool $applyAccessControlFilter = false;
    private bool $applyDefaultOrder = false;
    private ?string $textFilter = null;
    private ?string $primaryFilter = null;
    /** @var string[] */
    private array $boolFilterList = [];
    /** @var WhereItem[] */
    private array $whereItemList = [];
    private bool $applyWherePermissionCheck = false;
    private bool $applyComplexExpressionsForbidden = false;
    /** @var class-string<Applier\AdditionalApplier>[]  */
    private array $additionalApplierClassNameList = [];

    public function __construct(
        private User $user,
        private ApplierFactory $applierFactory
    ) {}

    /**
     * Specify an entity type to select from.
     */
    public function from(string $entityType): self
    {
        if ($this->sourceQuery) {
            throw new LogicException("Can't call 'from' after 'clone'.");
        }

        $this->entityType = $entityType;

        return $this;
    }

    /**
     * Start building from an existing select query.
     */
    public function clone(Query $query): self
    {
        if ($this->entityType && $this->entityType !== $query->getFrom()) {
            throw new LogicException("Not matching entity type.");
        }

        $this->entityType = $query->getFrom();
        $this->sourceQuery = $query;

        return $this;
    }

    /**
     * Build a result query.
     *
     * @throws Forbidden
     * @throws BadRequest
     */
    public function build(): Query
    {
        return $this->buildQueryBuilder()->build();
    }

    /**
     * Build an ORM query builder. Used to continue building but by means of ORM.
     *
     * @throws Forbidden
     * @throws BadRequest
     */
    public function buildQueryBuilder(): QueryBuilder
    {
        $this->queryBuilder = new OrmSelectBuilder();

        if (!$this->entityType) {
            throw new LogicException("No entity type.");
        }

        if ($this->sourceQuery) {
            $this->queryBuilder->clone($this->sourceQuery);
        } else {
            $this->queryBuilder->from($this->entityType);
        }

        $this->applyFromSearchParams();

        if (count($this->whereItemList)) {
            $this->applyWhereItemList();
        }

        if ($this->applyDefaultOrder) {
            $this->applyDefaultOrder();
        }

        if ($this->primaryFilter) {
            $this->applyPrimaryFilter();
        }

        if (count($this->boolFilterList)) {
            $this->applyBoolFilterList();
        }

        if ($this->textFilter) {
            $this->applyTextFilter();
        }

        if ($this->applyAccessControlFilter) {
            $this->applyAccessControlFilter();
        }

        $this->applyAdditional();

        /** @var QueryBuilder */
        return $this->queryBuilder;
    }

    /**
     * Switch a user for whom a select query will be built.
     */
    public function forUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Apply search parameters.
     *
     * Note: If there's no order set in the search parameters then a default order will be applied.
     */
    public function withSearchParams(SearchParams $searchParams): self
    {
        $this->searchParams = $searchParams;

        $this->withBoolFilterList(
            $searchParams->getBoolFilterList()
        );

        $primaryFilter = $searchParams->getPrimaryFilter();

        if ($primaryFilter) {
            $this->withPrimaryFilter($primaryFilter);
        }

        $textFilter = $searchParams->getTextFilter();

        if ($textFilter !== null) {
            $this->withTextFilter($textFilter);
        }

        return $this;
    }

    /**
     * Apply maximum restrictions for a user.
     */
    public function withStrictAccessControl(): self
    {
        $this->withAccessControlFilter();
        $this->withWherePermissionCheck();
        $this->withComplexExpressionsForbidden();

        return $this;
    }

    /**
     * Apply an access control filter.
     */
    public function withAccessControlFilter(): self
    {
        $this->applyAccessControlFilter = true;

        return $this;
    }

    /**
     * Apply a default order.
     */
    public function withDefaultOrder(): self
    {
        $this->applyDefaultOrder = true;

        return $this;
    }

    /**
     * Check permissions to where items.
     */
    public function withWherePermissionCheck(): self
    {
        $this->applyWherePermissionCheck = true;

        return $this;
    }

    /**
     * Forbid complex expression usage.
     */
    public function withComplexExpressionsForbidden(): self
    {
        $this->applyComplexExpressionsForbidden = true;

        return $this;
    }

    /**
     * Apply a text filter.
     */
    public function withTextFilter(string $textFilter): self
    {
        $this->textFilter = $textFilter;

        return $this;
    }

    /**
     * Apply a primary filter.
     */
    public function withPrimaryFilter(string $primaryFilter): self
    {
        $this->primaryFilter = $primaryFilter;

        return $this;
    }

    /**
     * Apply a bool filter.
     */
    public function withBoolFilter(string $boolFilter): self
    {
        $this->boolFilterList[] = $boolFilter;

        return $this;
    }

    /**
     * Apply a list of bool filters.
     *
     * @param string[] $boolFilterList
     */
    public function withBoolFilterList(array $boolFilterList): self
    {
        $this->boolFilterList = array_merge($this->boolFilterList, $boolFilterList);

        return $this;
    }

    /**
     * Apply a Where Item.
     */
    public function withWhere(WhereItem $whereItem): self
    {
        $this->whereItemList[] = $whereItem;

        return $this;
    }

    /**
     * Apply a list of additional applier class names.
     *
     * @param class-string<Applier\AdditionalApplier>[] $additionalApplierClassNameList
     */
    public function withAdditionalApplierClassNameList(array $additionalApplierClassNameList): self
    {
        $this->additionalApplierClassNameList = array_merge(
            $this->additionalApplierClassNameList,
            $additionalApplierClassNameList
        );

        return $this;
    }

    /**
     * @throws BadRequest
     */
    private function applyPrimaryFilter(): void
    {
        assert($this->queryBuilder !== null);
        assert($this->primaryFilter !== null);

        $this->createPrimaryFilterApplier()
            ->apply(
                $this->queryBuilder,
                $this->primaryFilter
            );
    }

    /**
     * @throws BadRequest
     */
    private function applyBoolFilterList(): void
    {
        assert($this->queryBuilder !== null);

        $this->createBoolFilterListApplier()
            ->apply(
                $this->queryBuilder,
                $this->boolFilterList
            );
    }

    private function applyTextFilter(): void
    {
        assert($this->queryBuilder !== null);
        assert($this->textFilter !== null);

        $this->createTextFilterApplier()
            ->apply(
                $this->queryBuilder,
                $this->textFilter,
                TextFilterParams::create()
            );
    }

    private function applyAccessControlFilter(): void
    {
        assert($this->queryBuilder !== null);

        $this->createAccessControlFilterApplier()
            ->apply(
                $this->queryBuilder
            );
    }

    /**
     * @throws Forbidden
     * @throws BadRequest
     */
    private function applyDefaultOrder(): void
    {
        assert($this->queryBuilder !== null);

        $order = $this->searchParams?->getOrder();

        $params = OrderParams::fromAssoc([
            'forceDefault' => true,
            'order' => $order,
        ]);

        $this->createOrderApplier()
            ->apply(
                $this->queryBuilder,
                $params
            );
    }

    /**
     * @throws BadRequest
     * @throws Forbidden
     */
    private function applyWhereItemList(): void
    {
        foreach ($this->whereItemList as $whereItem) {
            $this->applyWhereItem($whereItem);
        }
    }

    /**
     * @throws BadRequest
     * @throws Forbidden
     */
    private function applyWhereItem(WhereItem $whereItem): void
    {
        assert($this->queryBuilder !== null);

        $params = WhereParams::fromAssoc([
            'applyPermissionCheck' => $this->applyWherePermissionCheck,
            'forbidComplexExpressions' => $this->applyComplexExpressionsForbidden,
        ]);

        $this->createWhereApplier()
            ->apply(
                $this->queryBuilder,
                $whereItem,
                $params
            );
    }

    /**
     * @throws Forbidden
     * @throws BadRequest
     */
    private function applyFromSearchParams(): void
    {
        if (!$this->searchParams) {
            return;
        }

        assert($this->queryBuilder !== null);

        if (
            !$this->applyDefaultOrder &&
            ($this->searchParams->getOrderBy() || $this->searchParams->getOrder())
        ) {
            $params = OrderParams::fromAssoc([
                //'forbidComplexExpressions' => $this->applyComplexExpressionsForbidden,
                'orderBy' => $this->searchParams->getOrderBy(),
                'order' => $this->searchParams->getOrder(),
            ]);

            $this->createOrderApplier()
                ->apply(
                    $this->queryBuilder,
                    $params
                );
        }

        if (!$this->searchParams->getOrderBy() && !$this->searchParams->getOrder()) {
            $this->withDefaultOrder();
        }

        if ($this->searchParams->getMaxSize() !== null || $this->searchParams->getOffset() !== null) {
            $this->createLimitApplier()
                ->apply(
                    $this->queryBuilder,
                    $this->searchParams->getOffset(),
                    $this->searchParams->getMaxSize()
                );
        }

        if ($this->searchParams->getSelect()) {
            $this->createSelectApplier()
                ->apply(
                    $this->queryBuilder,
                    $this->searchParams
                );
        }

        if ($this->searchParams->getWhere()) {
            $this->whereItemList[] = $this->searchParams->getWhere();
        }
    }

    private function applyAdditional(): void
    {
        assert($this->queryBuilder !== null);

        if (count($this->additionalApplierClassNameList) === 0) {
            return;
        }

        $searchParams = SearchParams::fromRaw([
            'boolFilterList' => $this->boolFilterList,
            'primaryFilter' => $this->primaryFilter,
            'textFilter' => $this->textFilter,
        ]);

        if ($this->searchParams) {
            $searchParams = SearchParams::merge($searchParams, $this->searchParams);
        }

        $this->createAdditionalApplier()->apply(
            $this->additionalApplierClassNameList,
            $this->queryBuilder,
            $searchParams
        );
    }

    private function createWhereApplier(): WhereApplier
    {
        assert($this->entityType !== null);

        return $this->applierFactory->createWhere($this->entityType, $this->user);
    }

    private function createSelectApplier(): SelectApplier
    {
        assert($this->entityType !== null);

        return $this->applierFactory->createSelect($this->entityType, $this->user);
    }

    private function createOrderApplier(): OrderApplier
    {
        assert($this->entityType !== null);

        return $this->applierFactory->createOrder($this->entityType, $this->user);
    }

    private function createLimitApplier(): LimitApplier
    {
        assert($this->entityType !== null);

        return $this->applierFactory->createLimit($this->entityType, $this->user);
    }

    private function createAccessControlFilterApplier(): AccessControlFilterApplier
    {
        assert($this->entityType !== null);

        return $this->applierFactory->createAccessControlFilter($this->entityType, $this->user);
    }

    private function createTextFilterApplier(): TextFilterApplier
    {
        assert($this->entityType !== null);

        return $this->applierFactory->createTextFilter($this->entityType, $this->user);
    }

    private function createPrimaryFilterApplier(): PrimaryFilterApplier
    {
        assert($this->entityType !== null);

        return $this->applierFactory->createPrimaryFilter($this->entityType, $this->user);
    }

    private function createBoolFilterListApplier(): BoolFilterListApplier
    {
        assert($this->entityType !== null);

        return $this->applierFactory->createBoolFilterList($this->entityType, $this->user);
    }

    private function createAdditionalApplier(): AdditionalApplier
    {
        assert($this->entityType !== null);

        return $this->applierFactory->createAdditional($this->entityType, $this->user);
    }
}
