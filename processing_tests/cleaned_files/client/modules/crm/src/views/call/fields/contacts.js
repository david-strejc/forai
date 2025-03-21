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


//FORAI:F2995;DEF[C2570:extends];IMP[];EXP[C2570];LANG[javascript]//

import AttendeesFieldView from 'modules/crm/views/meeting/fields/attendees';

export default class extends AttendeesFieldView {

    getAttributeList() {
        return [
            ...super.getAttributeList(),
            'phoneNumbersMap',
        ]
    }

    getDetailLinkHtml(id, name) {
        const html = super.getDetailLinkHtml(id, name);

        const key = this.foreignScope + '_' + id;
        const phoneNumbersMap = this.model.get('phoneNumbersMap') || {};

        if (!(key in phoneNumbersMap)) {
            return html;
        }

        const number = phoneNumbersMap[key];

        const $item = $(html);

        // @todo Format phone number.

        $item
            .append(
                ' ',
                $('<span>').addClass('text-muted middle-dot'),
                ' ',
                $('<a>')
                    .attr('href', 'tel:' + number)
                    .attr('data-phone-number', number)
                    .attr('data-action', 'dial')
                    .addClass('small')
                    .text(number)
            )

        return $('<div>')
            .append($item)
            .get(0).outerHTML;
    }
}
