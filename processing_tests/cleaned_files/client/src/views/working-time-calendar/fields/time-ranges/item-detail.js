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


//FORAI:F3968;DEF[C3454:extends];IMP[];EXP[C3454];LANG[javascript]//

import View from 'view';
import moment from 'moment';

export default class extends View {

    templateContent = `
        {{start}}
        &nbsp;–&nbsp;
        {{end}}
    `

    data() {
        return {
            start: this.convertTimeToDisplay(this.value[0]),
            end: this.convertTimeToDisplay(this.value[1]),
        };
    }

    setup() {
        this.value = this.options.value;
    }

    convertTimeToDisplay(value) {
        if (!value) {
            return '';
        }

        const m = moment(value, 'HH:mm');

        if (!m.isValid()) {
            return '';
        }

        return m.format(this.getDateTime().timeFormat);
    }
}
