<?php
//FORAI:F389;DEF[C231:Comparison,F1648:__construct,F1649:getRaw,F1650:getRawKey,F1651:getRawValue,F1652:equal,F1653:notEqual,F1654:like,F1655:notLike,F1656:greater,F1657:greaterOrEqual,F1658:less,F1659:lessOrEqual,F1660:in,F1661:notIn,F1662:notEqualAny,F1663:greaterAny,F1664:lessAny,F1665:greaterOrEqualAny,F1666:lessOrEqualAny,F1667:equalAll,F1668:greaterAll,F1669:lessAll,F1670:greaterOrEqualAll,F1671:lessOrEqualAll,F1672:createComparison,F1673:createInOrNotInArray,F1674:createInOrNotInSubQuery];IMP[F377:C220];EXP[C231,F1649,F1650,F1651,F1652,F1653,F1654,F1655,F1656,F1657,F1658,F1659,F1660,F1661,F1662,F1663,F1664,F1665,F1666,F1667,F1668,F1669,F1670,F1671,F1672,F1673,F1674];LANG[php]//

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

namespace Espo\ORM\Query\Part\Where;

use Espo\ORM\Query\Part\Expression;
use Espo\ORM\Query\Part\WhereItem;
use Espo\ORM\Query\Select;

use RuntimeException;

/**
 * Compares an expression to a value or another expression. Immutable.
 *
 * @immutable
 */
class Comparison implements WhereItem
{
    private const OPERATOR_EQUAL = '=';
    private const OPERATOR_NOT_EQUAL = '!=';
    private const OPERATOR_GREATER = '>';
    private const OPERATOR_GREATER_OR_EQUAL = '>=';
    private const OPERATOR_LESS = '<';
    private const OPERATOR_LESS_OR_EQUAL = '<=';
    private const OPERATOR_LIKE = '*';
    private const OPERATOR_NOT_LIKE = '!*';
    private const OPERATOR_IN_SUB_QUERY = '=s';
    private const OPERATOR_NOT_IN_SUB_QUERY = '!=s';
    private const OPERATOR_NOT_EQUAL_ANY = '!=any';
    private const OPERATOR_GREATER_ANY = '>any';
    private const OPERATOR_GREATER_OR_EQUAL_ANY = '>=any';
    private const OPERATOR_LESS_ANY = '<any';
    private const OPERATOR_LESS_OR_EQUAL_ANY = '<=any';
    private const OPERATOR_EQUAL_ALL = '=all';
    private const OPERATOR_GREATER_ALL = '>all';
    private const OPERATOR_GREATER_OR_EQUAL_ALL = '>=all';
    private const OPERATOR_LESS_ALL = '<all';
    private const OPERATOR_LESS_OR_EQUAL_ALL = '<=all';

    private string $rawKey;
    private mixed $rawValue;

    private function __construct(string $rawKey, mixed $rawValue)
    {
        $this->rawKey = $rawKey;
        $this->rawValue = $rawValue;
    }

    public function getRaw(): array
    {
        return [$this->rawKey => $this->rawValue];
    }

    public function getRawKey(): string
    {
        return $this->rawKey;
    }

    public function getRawValue(): mixed
    {
        return $this->rawValue;
    }

    /**
     * Create '=' comparison.
     *
     * @param Expression $argument1 An expression.
     * @param Expression|Select|string|int|float|bool|null $argument2 A scalar, expression or sub-query.
     * @return self
     */
    public static function equal(
        Expression $argument1,
        Expression|Select|string|int|float|bool|null $argument2
    ): self {

        return self::createComparison(self::OPERATOR_EQUAL, $argument1, $argument2);
    }

    /**
     * Create '!=' comparison.
     *
     * @param Expression $argument1 An expression.
     * @param Expression|Select|string|int|float|bool|null $argument2 A scalar, expression or sub-query.
     * @return self
     */
    public static function notEqual(
        Expression $argument1,
        Expression|Select|string|int|float|bool|null $argument2
    ): self {

        return self::createComparison(self::OPERATOR_NOT_EQUAL, $argument1, $argument2);
    }

    /**
     * Create 'LIKE' comparison.
     *
     * @param Expression $subject What to test.
     * @param Expression|string $pattern A pattern.
     * @return self
     */
    public static function like(Expression $subject, Expression|string $pattern): self
    {
        return self::createComparison(self::OPERATOR_LIKE, $subject, $pattern);
    }

    /**
     * Create 'NOT LIKE' comparison.
     *
     * @param Expression $subject What to test.
     * @param Expression|string $pattern A pattern.
     * @return self
     */
    public static function notLike(Expression $subject, Expression|string $pattern): self
    {
        return self::createComparison(self::OPERATOR_NOT_LIKE, $subject, $pattern);
    }

    /**
     * Create '>' comparison.
     *
     * @param Expression $argument1 An expression.
     * @param Expression|Select|string|int|float $argument2 A scalar, expression or sub-query.
     * @return self
     */
    public static function greater(Expression $argument1, Expression|Select|string|int|float $argument2): self
    {
        return self::createComparison(self::OPERATOR_GREATER, $argument1, $argument2);
    }

    /**
     * Create '>=' comparison.
     *
     * @param Expression $argument1 An expression.
     * @param Expression|Select|string|int|float $argument2 A scalar, expression or sub-query.
     * @return self
     */
    public static function greaterOrEqual(Expression $argument1, Expression|Select|string|int|float $argument2): self
    {
        return self::createComparison(self::OPERATOR_GREATER_OR_EQUAL, $argument1, $argument2);
    }

    /**
     * Create '<' comparison.
     *
     * @param Expression $argument1 An expression.
     * @param Expression|Select|string|int|float $argument2 A scalar, expression or sub-query.
     * @return self
     */
    public static function less(Expression $argument1, Expression|Select|string|int|float $argument2): self
    {
        return self::createComparison(self::OPERATOR_LESS, $argument1, $argument2);
    }

    /**
     * Create '<=' comparison.
     *
     * @param Expression $argument1 An expression.
     * @param Expression|Select|string|int|float $argument2 A scalar, expression or sub-query.
     * @return self
     */
    public static function lessOrEqual(Expression $argument1, Expression|Select|string|int|float $argument2): self
    {
        return self::createComparison(self::OPERATOR_LESS_OR_EQUAL, $argument1, $argument2);
    }

    /**
     * Create 'IN' comparison.
     *
     * @param Expression $subject What to test.
     * @param Select|scalar[] $set A set of values. A select query or array of scalars.
     * @return self
     */
    public static function in(Expression $subject, Select|array $set): self
    {
        if ($set instanceof Select) {
            return self::createInOrNotInSubQuery(self::OPERATOR_IN_SUB_QUERY, $subject, $set);
        }

        return self::createInOrNotInArray(self::OPERATOR_EQUAL, $subject, $set);
    }

    /**
     * Create 'NOT IN' comparison.
     *
     * @param Expression $subject What to test.
     * @param Select|scalar[] $set A set of values. A select query or array of scalars.
     * @return self
     */
    public static function notIn(Expression $subject, Select|array $set): self
    {
        if ($set instanceof Select) {
            return self::createInOrNotInSubQuery(self::OPERATOR_NOT_IN_SUB_QUERY, $subject, $set);
        }

        return self::createInOrNotInArray(self::OPERATOR_NOT_EQUAL, $subject, $set);
    }

    /**
     * Create '!= ANY' comparison.
     *
     * @param Expression $argument An expression.
     * @param Select $subQuery A sub-query.
     * @return self
     */
    public static function notEqualAny(Expression $argument, Select $subQuery): self
    {
        return self::createComparison(self::OPERATOR_NOT_EQUAL_ANY, $argument, $subQuery);
    }

    /**
     * Create '> ANY' comparison.
     *
     * @param Expression $argument An expression.
     * @param Select $subQuery A sub-query.
     * @return self
     */
    public static function greaterAny(Expression $argument, Select $subQuery): self
    {
        return self::createComparison(self::OPERATOR_GREATER_ANY, $argument, $subQuery);
    }

    /**
     * Create '< ANY' comparison.
     *
     * @param Expression $argument An expression.
     * @param Select $subQuery A sub-query.
     * @return self
     */
    public static function lessAny(Expression $argument, Select $subQuery): self
    {
        return self::createComparison(self::OPERATOR_LESS_ANY, $argument, $subQuery);
    }

    /**
     * Create '>= ANY' comparison.
     *
     * @param Expression $argument An expression.
     * @param Select $subQuery A sub-query.
     * @return self
     */
    public static function greaterOrEqualAny(Expression $argument, Select $subQuery): self
    {
        return self::createComparison(self::OPERATOR_GREATER_OR_EQUAL_ANY, $argument, $subQuery);
    }

    /**
     * Create '<= ANY' comparison.
     *
     * @param Expression $argument An expression.
     * @param Select $subQuery A sub-query.
     * @return self
     */
    public static function lessOrEqualAny(Expression $argument, Select $subQuery): self
    {
        return self::createComparison(self::OPERATOR_LESS_OR_EQUAL_ANY, $argument, $subQuery);
    }

    /**
     * Create '= ALL' comparison.
     *
     * @param Expression $argument An expression.
     * @param Select $subQuery A sub-query.
     * @return self
     */
    public static function equalAll(Expression $argument, Select $subQuery): self
    {
        return self::createComparison(self::OPERATOR_EQUAL_ALL, $argument, $subQuery);
    }

    /**
     * Create '> ALL' comparison.
     *
     * @param Expression $argument An expression.
     * @param Select $subQuery A sub-query.
     * @return self
     */
    public static function greaterAll(Expression $argument, Select $subQuery): self
    {
        return self::createComparison(self::OPERATOR_GREATER_ALL, $argument, $subQuery);
    }

    /**
     * Create '< ALL' comparison.
     *
     * @param Expression $argument An expression.
     * @param Select $subQuery A sub-query.
     * @return self
     */
    public static function lessAll(Expression $argument, Select $subQuery): self
    {
        return self::createComparison(self::OPERATOR_LESS_ALL, $argument, $subQuery);
    }

    /**
     * Create '>= ALL' comparison.
     *
     * @param Expression $argument An expression.
     * @param Select $subQuery A sub-query.
     * @return self
     */
    public static function greaterOrEqualAll(Expression $argument, Select $subQuery): self
    {
        return self::createComparison(self::OPERATOR_GREATER_OR_EQUAL_ALL, $argument, $subQuery);
    }

    /**
     * Create '<= ALL' comparison.
     *
     * @param Expression $argument An expression.
     * @param Select $subQuery A sub-query.
     * @return self
     */
    public static function lessOrEqualAll(Expression $argument, Select $subQuery): self
    {
        return self::createComparison(self::OPERATOR_LESS_OR_EQUAL_ALL, $argument, $subQuery);
    }

    private static function createComparison(
        string $operator,
        Expression|string $argument1,
        Expression|Select|string|int|float|bool|null $argument2
    ): self {

        if (is_string($argument1)) {
            $key = $argument1;

            if ($key === '') {
                throw new RuntimeException("Expression can't be empty.");
            }
        } else {
            $key = $argument1->getValue();
        }

        if (str_ends_with($key, ':')) {
            throw new RuntimeException("Expression should not end with `:`.");
        }

        $key .= $operator;

        if ($argument2 instanceof Expression) {
            $key .= ':';

            $value = $argument2->getValue();
        } else {
            $value = $argument2;
        }

        return new self($key, $value);
    }

    /**
     * @param scalar[] $valueList
     */
    private static function createInOrNotInArray(
        string $operator,
        Expression|string $argument1,
        array $valueList
    ): self {

        foreach ($valueList as $item) {
            if (!is_scalar($item)) {
                throw new RuntimeException("Array items must be scalar.");
            }
        }

        if (is_string($argument1)) {
            $key = $argument1;

            if ($key === '') {
                throw new RuntimeException("Expression can't be empty.");
            }

            if (str_ends_with($key, ':')) {
                throw new RuntimeException("Expression can't end with `:`.");
            }
        } else {
            $key = $argument1->getValue();
        }

        $key .= $operator;

        return new self($key, $valueList);
    }

    private static function createInOrNotInSubQuery(
        string $operator,
        Expression|string $argument1,
        Select $query
    ): self {

        if (is_string($argument1)) {
            $key = $argument1;

            if ($key === '') {
                throw new RuntimeException("Expression can't be empty.");
            }

            if (str_ends_with($key, ':')) {
                throw new RuntimeException("Expression can't end with `:`.");
            }
        } else {
            $key = $argument1->getValue();
        }

        $key .= $operator;

        return new self($key, $query);
    }
}
