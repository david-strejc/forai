<?php
//FORAI:F1473;DEF[C1217:Event,F6412:withAttendees,F6413:withOrganizer,F6414:withDateStart,F6415:withDateEnd,F6416:withLocation,F6417:withName,F6418:withDescription,F6419:withTimezone,F6420:withUid,F6421:withIsAllDay,F6422:getUid,F6423:isAllDay,F6424:getName,F6425:getDateStart,F6426:getDateEnd,F6427:getLocation,F6428:getDescription,F6429:create,F6430:convertDate,F6431:getOrganizerEmailAddress,F6432:getAttendeeEmailAddressList,F6433:getEmailAddressFromAttendee];IMP[];EXP[C1217,F6412,F6413,F6414,F6415,F6416,F6417,F6418,F6419,F6420,F6421,F6422,F6423,F6424,F6425,F6426,F6427,F6428,F6429,F6430,F6431,F6432,F6433];LANG[php]//

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

namespace Espo\Core\Mail\Event;

use Espo\Core\Utils\DateTime as DateTimeUtil;

use DateTime;
use DateTimeZone;
use RuntimeException;

class Event
{
    private ?string $attendees = null;
    private ?string $organizer = null;
    private ?string $dateStart = null;
    private ?string $dateEnd = null;
    private ?string $location = null;
    private ?string $name = null;
    private ?string $description = null;
    private ?string $timezone = null;
    private ?string $uid = null;

    private bool $isAllDay = false;

    public function withAttendees(?string $attendees): self
    {
        $obj = clone $this;
        $obj->attendees = $attendees;

        return $obj;
    }

    public function withOrganizer(?string $organizer): self
    {
        $obj = clone $this;
        $obj->organizer = $organizer;

        return $obj;
    }

    public function withDateStart(?string $dateStart): self
    {
        $obj = clone $this;
        $obj->dateStart = $dateStart;

        return $obj;
    }

    public function withDateEnd(?string $dateEnd): self
    {
        $obj = clone $this;
        $obj->dateEnd = $dateEnd;

        return $obj;
    }

    public function withLocation(?string $location): self
    {
        $obj = clone $this;
        $obj->location = $location;

        return $obj;
    }

    public function withName(?string $name): self
    {
        $obj = clone $this;
        $obj->name = $name;

        return $obj;
    }

    public function withDescription(?string $description): self
    {
        $obj = clone $this;
        $obj->description = $description;

        return $obj;
    }

    public function withTimezone(?string $timezone): self
    {
        $obj = clone $this;
        $obj->timezone = $timezone;

        return $obj;
    }

    public function withUid(?string $uid): self
    {
        $obj = clone $this;
        $obj->uid = $uid;

        return $obj;
    }

    public function withIsAllDay(bool $isAllDay): self
    {
        $obj = clone $this;
        $obj->isAllDay = $isAllDay;

        return $obj;
    }

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function isAllDay(): bool
    {
        return $this->isAllDay;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getDateStart(): ?string
    {
        return $this->convertDate($this->dateStart);
    }

    public function getDateEnd(): ?string
    {
        return $this->convertDate($this->dateEnd, true);
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public static function create(): self
    {
        return new self();
    }

    private function convertDate(?string $value, bool $isEnd = false): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($this->isAllDay) {
            $dt = DateTime::createFromFormat('Ymd', $value);

            if ($dt === false) {
                throw new RuntimeException("Could not parse '{$value}'.");
            }

            if ($isEnd) {
                $dt->modify('-1 day');
            }

            return $dt->format(DateTimeUtil::SYSTEM_DATE_FORMAT);
        }

        $timezone = $this->timezone ?? 'UTC';

        $dt = DateTime::createFromFormat('Ymd\THis', $value, new DateTimeZone($timezone));

        if ($dt === false) {
            throw new RuntimeException("Could not parse '{$value}'.");
        }

        $dt->setTimezone(new DateTimeZone('UTC'));

        return $dt->format(DateTimeUtil::SYSTEM_DATE_TIME_FORMAT);
    }

    public function getOrganizerEmailAddress(): ?string
    {
        return $this->getEmailAddressFromAttendee($this->organizer);
    }

    /**
     * @return string[]
     */
    public function getAttendeeEmailAddressList(): array
    {
        if ($this->attendees === null || $this->attendees === '') {
            return [];
        }

        $list = [];

        foreach (explode(',', $this->attendees) as $item) {
            $emailAddress = $this->getEmailAddressFromAttendee($item);

            if ($emailAddress === null) {
                continue;
            }

            $list[] = $emailAddress;
        }

        return $list;
    }

    private function getEmailAddressFromAttendee(?string $item): ?string
    {
        if ($item === null || $item === '') {
            return null;
        }

        if (explode(':', $item)[0] !== 'MAILTO') {
            return null;
        }

        return explode(':', $item)[1] ?? null;
    }
}
