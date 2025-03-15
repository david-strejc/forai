<?php
//FORAI:F456;DEF[F2061:getFontFace,F2062:getBottomMargin,F2063:getTopMargin,F2064:getLeftMargin,F2065:getRightMargin,F2066:hasFooter,F2067:getFooter,F2068:getFooterPosition,F2069:hasHeader,F2070:getHeader,F2071:getHeaderPosition,F2072:getBody,F2073:getPageOrientation,F2074:getPageFormat,F2075:getPageWidth,F2076:getPageHeight,F2077:hasTitle,F2078:getTitle,F2079:getStyle];IMP[];EXP[F2061,F2062,F2063,F2064,F2065,F2066,F2067,F2068,F2069,F2070,F2071,F2072,F2073,F2074,F2075,F2076,F2077,F2078,F2079];LANG[php]//

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

namespace Espo\Tools\Pdf;

interface Template
{
    public const PAGE_FORMAT_CUSTOM = 'Custom';

    public const PAGE_ORIENTATION_PORTRAIT = 'Portrait';
    public const PAGE_ORIENTATION_LANDSCAPE = 'Landscape';

    public function getFontFace(): ?string;

    public function getBottomMargin(): float;

    public function getTopMargin(): float;

    public function getLeftMargin(): float;

    public function getRightMargin(): float;

    public function hasFooter(): bool;

    public function getFooter(): string;

    public function getFooterPosition(): float;

    public function hasHeader(): bool;

    public function getHeader(): string;

    public function getHeaderPosition(): float;

    public function getBody(): string;

    public function getPageOrientation(): string;

    public function getPageFormat(): string;

    public function getPageWidth(): float;

    public function getPageHeight(): float;

    public function hasTitle(): bool;

    public function getTitle(): string;

    public function getStyle(): ?string;
}
