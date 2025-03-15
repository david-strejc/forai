<?php
//FORAI:F1853;DEF[C1555:Address,F8437:__construct,F8438:hasStreet,F8439:hasCity,F8440:hasCountry,F8441:hasState,F8442:hasPostalCode,F8443:getStreet,F8444:getCity,F8445:getCountry,F8446:getState,F8447:getPostalCode,F8448:withStreet,F8449:withCity,F8450:withCountry,F8451:withState,F8452:withPostalCode,F8453:create,F8454:createBuilder];IMP[F1861:C1563];EXP[C1555,F8438,F8439,F8440,F8441,F8442,F8443,F8444,F8445,F8446,F8447,F8448,F8449,F8450,F8451,F8452,F8453,F8454];LANG[php]//

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

use Espo\Core\Field\Address\AddressBuilder;

/**
 * An address value object. Immutable.
 *
 * @immutable
 */
class Address
{
    public function __construct(
        private ?string $country = null,
        private ?string $state = null,
        private ?string $city = null,
        private ?string $street = null,
        private ?string $postalCode = null
    ) {}

    /**
     * Whether has a street.
     */
    public function hasStreet(): bool
    {
        return $this->street !== null;
    }

    /**
     * Whether has a city.
     */
    public function hasCity(): bool
    {
        return $this->city !== null;
    }

    /**
     * Whether has a country.
     */
    public function hasCountry(): bool
    {
        return $this->country !== null;
    }

    /**
     * Whether has a state.
     */
    public function hasState(): bool
    {
        return $this->state !== null;
    }

    /**
     * Whether has a postal code.
     */
    public function hasPostalCode(): bool
    {
        return $this->postalCode !== null;
    }

    /**
     * Get a street.
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * Get a city.
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * Get a country.
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * Get a state.
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * Get a postal code.
     */
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * Clone with a street.
     */
    public function withStreet(?string $street): self
    {
        return self::createBuilder()
            ->clone($this)
            ->setStreet($street)
            ->build();
    }

    /**
     * Clone with a city.
     */
    public function withCity(?string $city): self
    {
        return self::createBuilder()
            ->clone($this)
            ->setCity($city)
            ->build();
    }

    /**
     * Clone with a country.
     */
    public function withCountry(?string $country): self
    {
        return self::createBuilder()
            ->clone($this)
            ->setCountry($country)
            ->build();
    }

    /**
     * Clone with a state.
     */
    public function withState(?string $state): self
    {
        return self::createBuilder()
            ->clone($this)
            ->setState($state)
            ->build();
    }

    /**
     * Clone with a postal code.
     */
    public function withPostalCode(?string $postalCode): self
    {
        return self::createBuilder()
            ->clone($this)
            ->setPostalCode($postalCode)
            ->build();
    }

    /**
     * Create an empty address.
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * Create a builder.
     */
    public static function createBuilder(): AddressBuilder
    {
        return new AddressBuilder();
    }
}
