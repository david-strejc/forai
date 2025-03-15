<?php
//FORAI:F1844;DEF[C1546:Date,F8269:__construct,F8270:toString,F8271:toDateTime,F8272:toTimestamp,F8273:getYear,F8274:getMonth,F8275:getDay,F8276:getDayOfWeek,F8277:modify,F8278:add,F8279:subtract,F8280:addDays,F8281:addMonths,F8282:addYears,F8283:diff,F8284:isGreaterThan,F8285:isLessThan,F8286:isEqualTo,F8287:isLessThanOrEqualTo,F8288:isGreaterThanOrEqualTo,F8289:createToday,F8290:fromString,F8291:fromDateTime,F8292:getString,F8293:getDateTime,F8294:getTimestamp];IMP[];EXP[C1546,F8270,F8271,F8272,F8273,F8274,F8275,F8276,F8277,F8278,F8279,F8280,F8281,F8282,F8283,F8284,F8285,F8286,F8287,F8288,F8289,F8290,F8291,F8292,F8293,F8294];LANG[php]//

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

namespace Espo\Core\Field;

use Espo\Core\Field\DateTime\DateTimeable;

use DateTimeImmutable;
use DateTimeInterface;
use DateInterval;
use DateTimeZone;
use RuntimeException;

/**
 * A date value object. Immutable.
 *
 * @immutable
 */
class Date implements DateTimeable
{
    private string $value;
    private DateTimeImmutable $dateTime;

    private const SYSTEM_FORMAT = 'Y-m-d';

    public function __construct(string $value)
    {
        if (!$value) {
            throw new RuntimeException("Empty value.");
        }

        $this->value = $value;

        $parsedValue = DateTimeImmutable::createFromFormat(
            '!' . self::SYSTEM_FORMAT,
            $value,
            new DateTimeZone('UTC')
        );

        if ($parsedValue === false) {
            throw new RuntimeException("Bad value.");
        }

        $this->dateTime = $parsedValue;

        if ($this->value !== $this->dateTime->format(self::SYSTEM_FORMAT)) {
            throw new RuntimeException("Bad value.");
        }
    }

    /**
     * Get a string value in `Y-m-d` format.
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * Get DateTimeImmutable.
     */
    public function toDateTime(): DateTimeImmutable
    {
        return $this->dateTime;
    }

    /**
     * Get a timestamp.
     */
    public function toTimestamp(): int
    {
        return $this->dateTime->getTimestamp();
    }

    /**
     * Get a year.
     */
    public function getYear(): int
    {
        return (int) $this->dateTime->format('Y');
    }

    /**
     * Get a month.
     */
    public function getMonth(): int
    {
        return (int) $this->dateTime->format('n');
    }

    /**
     * Get a day (of month).
     */
    public function getDay(): int
    {
        return (int) $this->dateTime->format('j');
    }

    /**
     * Get a day of week. 0 (for Sunday) through 6 (for Saturday).
     */
    public function getDayOfWeek(): int
    {
        return (int) $this->dateTime->format('w');
    }

    /**
     * Clones and modifies.
     */
    public function modify(string $modifier): self
    {
        /** @var DateTimeImmutable|false $dateTime */
        $dateTime = $this->dateTime->modify($modifier);

        if (!$dateTime) {
            throw new RuntimeException("Modify failure.");
        }

        return self::fromDateTime($dateTime);
    }

    /**
     * Clones and adds an interval.
     */
    public function add(DateInterval $interval): self
    {
        $dateTime = $this->dateTime->add($interval);

        return self::fromDateTime($dateTime);
    }

    /**
     * Clones and subtracts an interval.
     */
    public function subtract(DateInterval $interval): self
    {
        $dateTime = $this->dateTime->sub($interval);

        return self::fromDateTime($dateTime);
    }

    /**
     * Add days.
     */
    public function addDays(int $days): self
    {
        $modifier = ($days >= 0 ? '+' : '-') . abs($days) . ' days';

        return $this->modify($modifier);
    }

    /**
     * Add months.
     */
    public function addMonths(int $months): self
    {
        $modifier = ($months >= 0 ? '+' : '-') . abs($months) . ' months';

        return $this->modify($modifier);
    }

    /**
     * Add years.
     */
    public function addYears(int $years): self
    {
        $modifier = ($years >= 0 ? '+' : '-') . abs($years) . ' years';

        return $this->modify($modifier);
    }

    /**
     * A difference between another object (date or date-time) and self.
     */
    public function diff(DateTimeable $other): DateInterval
    {
        return $this->toDateTime()->diff($other->toDateTime());
    }

    /**
     * Whether greater than a given value.
     */
    public function isGreaterThan(DateTimeable $other): bool
    {
        return $this->toDateTime() > $other->toDateTime();
    }

    /**
     * Whether less than a given value.
     */
    public function isLessThan(DateTimeable $other): bool
    {
        return $this->toDateTime() < $other->toDateTime();
    }

    /**
     * Whether equals to a given value.
     */
    public function isEqualTo(DateTimeable $other): bool
    {
        return $this->toDateTime() == $other->toDateTime();
    }

    /**
     * Whether less than or equals to a given value.
     * @since 9.0.0
     */
    public function isLessThanOrEqualTo(DateTimeable $other): bool
    {
        return $this->isLessThan($other) || $this->isEqualTo($other);
    }

    /**
     * Whether greater than or equals to a given value.
     * @since 9.0.0
     */
    public function isGreaterThanOrEqualTo(DateTimeable $other): bool
    {
        return $this->isGreaterThan($other) || $this->isEqualTo($other);
    }

    /**
     * Create a today.
     */
    public static function createToday(?DateTimeZone $timezone = null): self
    {
        $now = new DateTimeImmutable();

        if ($timezone) {
            $now = $now->setTimezone($timezone);
        }

        return self::fromDateTime($now);
    }

    /**
     * Create from a string with a date in `Y-m-d` format.
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    /**
     * Create from a DateTimeInterface.
     */
    public static function fromDateTime(DateTimeInterface $dateTime): self
    {
        $value = $dateTime->format(self::SYSTEM_FORMAT);

        return new self($value);
    }

    /**
     * @deprecated As of v8.1. Use `toString` instead.
     * @todo Remove in v10.0.
     */
    public function getString(): string
    {
        return $this->toString();
    }

    /**
     * @deprecated As of v8.1. Use `toDateTime` instead.
     * @todo Remove in v10.0.
     */
    public function getDateTime(): DateTimeImmutable
    {
        return $this->toDateTime();
    }

    /**
     * @deprecated As of v8.1. Use `toTimestamp` instead.
     * @todo Remove in v10.0.
     */
    public function getTimestamp(): int
    {
        return $this->toTimestamp();
    }
}
