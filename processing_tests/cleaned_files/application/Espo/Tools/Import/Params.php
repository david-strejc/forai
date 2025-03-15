<?php
//FORAI:F514;DEF[C336:Params,F2346:__construct,F2347:getAction,F2348:getDelimiter,F2349:getTextQualifier,F2350:getPersonNameFormat,F2351:getPhoneNumberCountry,F2352:isIdleMode,F2353:isManualMode,F2354:isSilentMode,F2355:headerRow,F2356:skipDuplicateChecking,F2357:startFromLastIndex,F2358:getUpdateBy,F2359:getDefaultValues,F2360:getDateFormat,F2361:getTimeFormat,F2362:getCurrency,F2363:getTimezone,F2364:getDecimalMark,F2365:create,F2366:withAction,F2367:withDelimiter,F2368:withTextQualifier,F2369:withPersonNameFormat,F2370:withPhoneNumberCountry,F2371:withIdleMode,F2372:withManualMode,F2373:withSilentMode,F2374:withHeaderRow,F2375:withSkipDuplicateChecking,F2376:withStartFromLastIndex,F2377:withUpdateBy,F2378:withDefaultValues,F2379:withDateFormat,F2380:withTimeFormat,F2381:withCurrency,F2382:withTimezone,F2383:withDecimalMark,F2384:fromRaw,F2385:getRaw];IMP[F1664:C1386];EXP[C336,F2347,F2348,F2349,F2350,F2351,F2352,F2353,F2354,F2355,F2356,F2357,F2358,F2359,F2360,F2361,F2362,F2363,F2364,F2365,F2366,F2367,F2368,F2369,F2370,F2371,F2372,F2373,F2374,F2375,F2376,F2377,F2378,F2379,F2380,F2381,F2382,F2383,F2384,F2385];LANG[php]//

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

namespace Espo\Tools\Import;

use Espo\Core\Utils\ObjectUtil;
use stdClass;
use TypeError;

/**
 * @immutable
 */
class Params
{
    public const ACTION_CREATE = 'create';
    public const ACTION_CREATE_AND_UPDATE = 'createAndUpdate';
    public const ACTION_UPDATE = 'update';

    private ?string $action = null;
    private ?string $delimiter = null;
    private ?string $textQualifier = null;
    private ?string $personNameFormat = null;
    private bool $idleMode = false;
    private bool $manualMode = false;
    private bool $silentMode = false;
    private bool $headerRow = false;
    private bool $skipDuplicateChecking = false;
    private bool $startFromLastIndex = false;
    /**
     * @var int[]
     */
    private $updateBy = [];
    private stdClass $defaultValues;
    private ?string $dateFormat = null;
    private ?string $timeFormat = null;
    private ?string $currency = null;
    private ?string $timezone = null;
    private ?string $decimalMark = null;
    private ?string $phoneNumberCountry = null;

    private function __construct()
    {
        $this->defaultValues = (object) [];
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function getDelimiter(): ?string
    {
        return $this->delimiter;
    }

    public function getTextQualifier(): ?string
    {
        return $this->textQualifier;
    }

    public function getPersonNameFormat(): ?string
    {
        return $this->personNameFormat;
    }

    public function getPhoneNumberCountry(): ?string
    {
        return $this->phoneNumberCountry;
    }

    public function isIdleMode(): bool
    {
        return $this->idleMode;
    }

    public function isManualMode(): bool
    {
        return $this->manualMode;
    }

    public function isSilentMode(): bool
    {
        return $this->silentMode;
    }

    public function headerRow(): bool
    {
        return $this->headerRow;
    }

    public function skipDuplicateChecking(): bool
    {
        return $this->skipDuplicateChecking;
    }

    public function startFromLastIndex(): bool
    {
        return $this->startFromLastIndex;
    }

    /**
     * @return int[]
     */
    public function getUpdateBy(): array
    {
        return $this->updateBy;
    }

    public function getDefaultValues(): stdClass
    {
        return ObjectUtil::clone($this->defaultValues);
    }

    public function getDateFormat(): ?string
    {
        return $this->dateFormat;
    }

    public function getTimeFormat(): ?string
    {
        return $this->timeFormat;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function getDecimalMark(): ?string
    {
        return $this->decimalMark;
    }

    public static function create(): self
    {
        return new self();
    }

    public function withAction(?string $action): self
    {
        $obj = clone $this;
        $obj->action = $action;

        return $obj;
    }

    public function withDelimiter(?string $delimiter): self
    {
        $obj = clone $this;
        $obj->delimiter = $delimiter;

        return $obj;
    }

    public function withTextQualifier(?string $textQualifier): self
    {
        $obj = clone $this;
        $obj->textQualifier = $textQualifier;

        return $obj;
    }

    public function withPersonNameFormat(?string $personNameFormat): self
    {
        $obj = clone $this;
        $obj->personNameFormat = $personNameFormat;

        return $obj;
    }

    public function withPhoneNumberCountry(?string $phoneNumberCountry): self
    {
        $obj = clone $this;
        $obj->phoneNumberCountry = $phoneNumberCountry;

        return $obj;
    }

    public function withIdleMode(bool $idleMode = true): self
    {
        $obj = clone $this;
        $obj->idleMode = $idleMode;

        return $obj;
    }

    public function withManualMode(bool $manualMode = true): self
    {
        $obj = clone $this;
        $obj->manualMode = $manualMode;

        return $obj;
    }

    public function withSilentMode(bool $silentMode = true): self
    {
        $obj = clone $this;
        $obj->silentMode = $silentMode;

        return $obj;
    }

    public function withHeaderRow(bool $headerRow = true): self
    {
        $obj = clone $this;
        $obj->headerRow = $headerRow;

        return $obj;
    }

    public function withSkipDuplicateChecking(bool $skipDuplicateChecking = true): self
    {
        $obj = clone $this;
        $obj->skipDuplicateChecking = $skipDuplicateChecking;

        return $obj;
    }

    public function withStartFromLastIndex(bool $startFromLastIndex = true): self
    {
        $obj = clone $this;
        $obj->startFromLastIndex = $startFromLastIndex;

        return $obj;
    }

    /**
     * @param int[] $updateBy
     */
    public function withUpdateBy(array $updateBy): self
    {
        $obj = clone $this;
        $obj->updateBy = $updateBy;

        return $obj;
    }

    /**
     * @param stdClass|array<string, mixed>|null $defaultValues
     */
    public function withDefaultValues($defaultValues): self
    {
        if (is_array($defaultValues)) {
            $defaultValues = (object) $defaultValues;
        }

        if (is_null($defaultValues)) {
            $defaultValues = (object) [];
        }

        /** @var stdClass|scalar $defaultValues */

        if (!is_object($defaultValues)) {
            throw new TypeError();
        }

        $obj = clone $this;
        $obj->defaultValues = $defaultValues;

        return $obj;
    }

    public function withDateFormat(?string $dateFormat): self
    {
        $obj = clone $this;
        $obj->dateFormat = $dateFormat;

        return $obj;
    }

    public function withTimeFormat(?string $timeFormat): self
    {
        $obj = clone $this;
        $obj->timeFormat = $timeFormat;

        return $obj;
    }

    public function withCurrency(?string $currency): self
    {
        $obj = clone $this;
        $obj->currency = $currency;

        return $obj;
    }

    public function withTimezone(?string $timezone): self
    {
        $obj = clone $this;
        $obj->timezone = $timezone;

        return $obj;
    }

    public function withDecimalMark(?string $decimalMark): self
    {
        $obj = clone $this;
        $obj->decimalMark = $decimalMark;

        return $obj;
    }

    /**
     * @param stdClass|array<string, mixed>|null $params
     */
    public static function fromRaw($params): self
    {
        if ($params === null) {
            $params = (object) [];
        }

        $raw = (object) $params;

        return self::create()
            ->withAction($raw->action ?? null)
            ->withCurrency($raw->currency ?? null)
            ->withDateFormat($raw->dateFormat ?? null)
            ->withDecimalMark($raw->decimalMark ?? null)
            ->withDefaultValues($raw->defaultValues ?? null)
            ->withDelimiter($raw->delimiter ?? null)
            ->withHeaderRow($raw->headerRow ?? false)
            ->withIdleMode($raw->idleMode ?? false)
            ->withManualMode($raw->manualMode ?? false)
            ->withPersonNameFormat($raw->personNameFormat ?? null)
            ->withPhoneNumberCountry($raw->phoneNumberCountry ?? null)
            ->withSilentMode($raw->silentMode ?? false)
            ->withSkipDuplicateChecking($raw->skipDuplicateChecking ?? false)
            ->withStartFromLastIndex($raw->startFromLastIndex ?? false)
            ->withTextQualifier($raw->textQualifier ?? null)
            ->withTimeFormat($raw->timeFormat ?? false)
            ->withTimezone($raw->timezone ?? null)
            ->withUpdateBy($raw->updateBy ?? []);
    }

    /**
     * @return array<string, mixed>
     */
    public function getRaw(): array
    {
        return [
            'action' => $this->action,
            'currency' => $this->currency,
            'dateFormat' => $this->dateFormat,
            'decimalMark' => $this->decimalMark,
            'defaultValues' => $this->defaultValues,
            'delimiter' => $this->delimiter,
            'headerRow' => $this->headerRow,
            'idleMode' => $this->idleMode,
            'manualMode' => $this->manualMode,
            'personNameFormat' => $this->personNameFormat,
            'silentMode' => $this->silentMode,
            'skipDuplicateChecking' => $this->skipDuplicateChecking,
            'startFromLastIndex' => $this->startFromLastIndex,
            'textQualifier' => $this->textQualifier,
            'phoneNumberCountry' => $this->phoneNumberCountry,
            'timeFormat' => $this->timeFormat,
            'timezone' => $this->timezone,
            'updateBy' => $this->updateBy,
        ];
    }
}
