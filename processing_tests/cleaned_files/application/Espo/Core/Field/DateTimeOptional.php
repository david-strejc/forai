<?php
//FORAI:F1847;DEF[C1549:DateTimeOptional,F8310:__construct,F8311:fromString,F8312:fromDateTimeString,F8313:toString,F8314:toDateTime,F8315:toTimestamp,F8316:getYear,F8317:getMonth,F8318:getDay,F8319:getDayOfWeek,F8320:getHour,F8321:getMinute,F8322:getSecond,F8323:isAllDay,F8324:getTimezone,F8325:getActualValue,F8326:withTimezone,F8327:withTime,F8328:modify,F8329:add,F8330:subtract,F8331:addDays,F8332:addMonths,F8333:addYears,F8334:addHours,F8335:addMinutes,F8336:addSeconds,F8337:diff,F8338:isGreaterThan,F8339:isLessThan,F8340:isEqualTo,F8341:isLessThanOrEqualTo,F8342:isGreaterThanOrEqualTo,F8343:createNow,F8344:createToday,F8345:fromDateString,F8346:fromTimestamp,F8347:fromDateTime,F8348:fromDateTimeAllDay,F8349:isStringDateTime,F8350:getString,F8351:getDateTime,F8352:getTimestamp];IMP[];EXP[C1549,F8311,F8312,F8313,F8314,F8315,F8316,F8317,F8318,F8319,F8320,F8321,F8322,F8323,F8324,F8325,F8326,F8327,F8328,F8329,F8330,F8331,F8332,F8333,F8334,F8335,F8336,F8337,F8338,F8339,F8340,F8341,F8342,F8343,F8344,F8345,F8346,F8347,F8348,F8349,F8350,F8351,F8352];LANG[php]//

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
 * A date-time or date. Immutable.
 *
 * @immutable
 */
class DateTimeOptional implements DateTimeable
{
    private ?DateTime $dateTimeValue = null;
    private ?Date $dateValue = null;

    private const SYSTEM_FORMAT = 'Y-m-d H:i:s';
    private const SYSTEM_FORMAT_DATE = 'Y-m-d';

    public function __construct(string $value)
    {
        if (self::isStringDateTime($value)) {
            $this->dateTimeValue = new DateTime($value);
        } else {
            $this->dateValue = new Date($value);
        }
    }

    /**
     * Create from a string with a date-time in `Y-m-d H:i:s` format or date in `Y-m-d`.
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    /**
     * Create from a string with a date-time in `Y-m-d H:i:s` format.
     * @noinspection PhpUnused
     */
    public static function fromDateTimeString(string $value): self
    {
        if (!self::isStringDateTime($value)) {
            throw new RuntimeException("Bad value.");
        }

        return self::fromString($value);
    }

    /**
     * Get a string value in `Y-m-d H:i:s` format.
     */
    public function toString(): string
    {
        return $this->getActualValue()->toString();
    }

    /**
     * Get DateTimeImmutable.
     */
    public function toDateTime(): DateTimeImmutable
    {
        return $this->getActualValue()->toDateTime();
    }

    /**
     * Get a timestamp.
     */
    public function toTimestamp(): int
    {
        return $this->getActualValue()->toDateTime()->getTimestamp();
    }

    /**
     * Get a year.
     */
    public function getYear(): int
    {
        return $this->getActualValue()->getYear();
    }

    /**
     * Get a month.
     */
    public function getMonth(): int
    {
        return $this->getActualValue()->getMonth();
    }

    /**
     * Get a day (of month).
     */
    public function getDay(): int
    {
        return $this->getActualValue()->getDay();
    }

    /**
     * Get a day of week. 0 (for Sunday) through 6 (for Saturday).
     */
    public function getDayOfWeek(): int
    {
        return $this->getActualValue()->getDayOfWeek();
    }

    /**
     * Get an hour.
     */
    public function getHour(): int
    {
        if ($this->isAllDay()) {
            return 0;
        }

        /** @var DateTime $value */
        $value = $this->getActualValue();

        return $value->getHour();
    }

    /**
     * Get a minute.
     */
    public function getMinute(): int
    {
        if ($this->isAllDay()) {
            return 0;
        }

        /** @var DateTime $value */
        $value = $this->getActualValue();

        return $value->getMinute();
    }

    /**
     * Get a second.
     */
    public function getSecond(): int
    {
        if ($this->isAllDay()) {
            return 0;
        }

        /** @var DateTime $value */
        $value = $this->getActualValue();

        return $value->getSecond();
    }

    /**
     * Whether is all-day (no time part).
     */
    public function isAllDay(): bool
    {
        return $this->dateValue !== null;
    }

    /**
     * Get a timezone.
     */
    public function getTimezone(): DateTimeZone
    {
        return $this->toDateTime()->getTimezone();
    }

    private function getActualValue(): Date|DateTime
    {
        /** @var Date|DateTime */
        return $this->dateValue ?? $this->dateTimeValue;
    }

    /**
     * Clones and apply a timezone. Non-all-day value is created.
     */
    public function withTimezone(DateTimeZone $timezone): self
    {
        if ($this->isAllDay()) {
            $dateTime = $this->getActualValue()->toDateTime()->setTimezone($timezone);

            return self::fromDateTime($dateTime);
        }

        /** @var DateTime $value */
        $value = $this->getActualValue();

        $dateTime = $value->withTimezone($timezone)->toDateTime();

        return self::fromDateTime($dateTime);
    }

    /**
     * Clones and sets time. Null preserves a current value.
     */
    public function withTime(?int $hour, ?int $minute, ?int $second = 0): self
    {
        if ($this->isAllDay()) {
            $dateTime = DateTime::fromDateTime($this->getActualValue()->toDateTime())
                ->withTime($hour, $minute, $second);

            return self::fromDateTime($dateTime->toDateTime());
        }

        /** @var DateTime $value */
        $value = $this->getActualValue();

        $dateTime = $value->withTime($hour, $minute, $second);

        return self::fromDateTime($dateTime->toDateTime());
    }

    /**
     * Clones and modifies.
     */
    public function modify(string $modifier): self
    {
        if ($this->isAllDay()) {
            assert($this->dateValue !== null);

            return self::fromDateTimeAllDay(
                $this->dateValue->modify($modifier)->toDateTime()
            );
        }

        assert($this->dateTimeValue !== null);

        return self::fromDateTime(
            $this->dateTimeValue->modify($modifier)->toDateTime()
        );
    }

    /**
     * Clones and adds an interval.
     */
    public function add(DateInterval $interval): self
    {
        if ($this->isAllDay()) {
            assert($this->dateValue !== null);

            return self::fromDateTimeAllDay(
                $this->dateValue->add($interval)->toDateTime()
            );
        }

        assert($this->dateTimeValue !== null);

        return self::fromDateTime(
            $this->dateTimeValue->add($interval)->toDateTime()
        );
    }

    /**
     * Clones and subtracts an interval.
     */
    public function subtract(DateInterval $interval): self
    {
        if ($this->isAllDay()) {
            assert($this->dateValue !== null);

            return self::fromDateTimeAllDay(
                $this->dateValue->subtract($interval)->toDateTime()
            );
        }

        assert($this->dateTimeValue !== null);

        return self::fromDateTime(
            $this->dateTimeValue->subtract($interval)->toDateTime()
        );
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
     * Add hours.
     */
    public function addHours(int $hours): self
    {
        $modifier = ($hours >= 0 ? '+' : '-') . abs($hours) . ' hours';

        return $this->modify($modifier);
    }

    /**
     * Add minutes.
     */
    public function addMinutes(int $minutes): self
    {
        $modifier = ($minutes >= 0 ? '+' : '-') . abs($minutes) . ' minutes';

        return $this->modify($modifier);
    }

    /**
     * Add seconds.
     */
    public function addSeconds(int $seconds): self
    {
        $modifier = ($seconds >= 0 ? '+' : '-') . abs($seconds) . ' seconds';

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
     * Create a current time.
     */
    public static function createNow(): self
    {
        return self::fromDateTime(new DateTimeImmutable());
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

        return self::fromDateTimeAllDay($now);
    }

    /**
     * Create from a string with a date in `Y-m-d` format.
     * @noinspection PhpUnused
     */
    public static function fromDateString(string $value): self
    {
        if (self::isStringDateTime($value)) {
            throw new RuntimeException("Bad value.");
        }

        return self::fromString($value);
    }

    /**
     * Create from a timestamp.
     */
    public static function fromTimestamp(int $timestamp): self
    {
        $dateTime = (new DateTimeImmutable)->setTimestamp($timestamp);

        return self::fromDateTime($dateTime);
    }

    /**
     * Create from a DateTimeInterface.
     */
    public static function fromDateTime(DateTimeInterface $dateTime): self
    {
        /** @var DateTimeImmutable $value */
        $value = DateTimeImmutable::createFromFormat(
            self::SYSTEM_FORMAT,
            $dateTime->format(self::SYSTEM_FORMAT),
            $dateTime->getTimezone()
        );

        $utcValue = $value
             ->setTimezone(new DateTimeZone('UTC'))
             ->format(self::SYSTEM_FORMAT);

        $obj = self::fromString($utcValue);

        assert($obj->dateTimeValue !== null);

        $obj->dateTimeValue = $obj->dateTimeValue->withTimezone($dateTime->getTimezone());

        return $obj;
    }

    /**
     * Create all-day from a DateTimeInterface.
     */
    public static function fromDateTimeAllDay(DateTimeInterface $dateTime): self
    {
        $value = $dateTime->format(self::SYSTEM_FORMAT_DATE);

        return new self($value);
    }

    private static function isStringDateTime(string $value): bool
    {
        if (strlen($value) > 10) {
            return true;
        }

        return false;
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
