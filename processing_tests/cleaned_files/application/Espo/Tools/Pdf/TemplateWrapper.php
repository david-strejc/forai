<?php
//FORAI:F450;DEF[C275:TemplateWrapper,F2026:__construct,F2027:getFontFace,F2028:getBottomMargin,F2029:getTopMargin,F2030:getLeftMargin,F2031:getRightMargin,F2032:hasFooter,F2033:getFooter,F2034:getFooterPosition,F2035:hasHeader,F2036:getHeader,F2037:getHeaderPosition,F2038:getBody,F2039:getPageOrientation,F2040:getPageFormat,F2041:getPageWidth,F2042:getPageHeight,F2043:hasTitle,F2044:getTitle,F2045:getStyle];IMP[F314:C173];EXP[C275,F2027,F2028,F2029,F2030,F2031,F2032,F2033,F2034,F2035,F2036,F2037,F2038,F2039,F2040,F2041,F2042,F2043,F2044,F2045];LANG[php]//

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

use Espo\Entities\Template as TemplateEntity;

class TemplateWrapper implements Template
{
    protected TemplateEntity $template;

    public function __construct(TemplateEntity $template)
    {
        $this->template = $template;
    }

    public function getFontFace(): ?string
    {
        return $this->template->get('fontFace');
    }

    public function getBottomMargin(): float
    {
        return $this->template->get('bottomMargin') ?? 0.0;
    }

    public function getTopMargin(): float
    {
        return $this->template->get('topMargin') ?? 0.0;
    }

    public function getLeftMargin(): float
    {
        return $this->template->get('leftMargin') ?? 0.0;
    }

    public function getRightMargin(): float
    {
        return $this->template->get('rightMargin') ?? 0.0;
    }

    public function hasFooter(): bool
    {
        return $this->template->get('printFooter') ?? false;
    }

    public function getFooter(): string
    {
        return $this->template->get('footer') ?? '';
    }

    public function getFooterPosition(): float
    {
        return $this->template->get('footerPosition') ?? 0.0;
    }

    public function hasHeader(): bool
    {
        return $this->template->get('printHeader') ?? false;
    }

    public function getHeader(): string
    {
        return $this->template->get('header') ?? '';
    }

    public function getHeaderPosition(): float
    {
        return $this->template->get('headerPosition') ?? 0.0;
    }

    public function getBody(): string
    {
        return $this->template->get('body') ?? '';
    }

    public function getPageOrientation(): string
    {
        return $this->template->get('pageOrientation') ?? 'Portrait';
    }

    public function getPageFormat(): string
    {
        return $this->template->get('pageFormat') ?? 'A4';
    }

    public function getPageWidth(): float
    {
        return $this->template->get('pageWidth') ?? 0.0;
    }

    public function getPageHeight(): float
    {
        return $this->template->get('pageHeight') ?? 0.0;
    }

    public function hasTitle(): bool
    {
        return $this->template->get('title') !== null;
    }

    public function getTitle(): string
    {
        return $this->template->get('title') ?? '';
    }

    public function getStyle(): ?string
    {
        return $this->template->get('style') ?? null;
    }
}
