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


//FORAI:F3536;DEF[C3020:PreferencesCalendarScrollHourView<EnumIntFieldView>];IMP[];EXP[C3020];LANG[javascript]//

import EnumIntFieldView from 'views/fields/enum-int';
import moment from 'moment';

class PreferencesCalendarScrollHourView extends EnumIntFieldView {

    setupOptions() {
        super.setupOptions();

        this.translatedOptions = {};
        this.translatedOptions[''] = this.translate('Default');

        const timeFormat = this.getDateTime().getTimeFormat();
        const today = this.getDateTime().getToday();

        this.params.options.forEach(item => {
            if (item === '') {
                return;
            }

            const itemString = today + ' ' + item.toString().padStart(2, '0') + ':00';

            this.translatedOptions[item] = moment.utc(itemString).format(timeFormat);
        });
    }
}

// noinspection JSUnusedGlobalSymbols
export default PreferencesCalendarScrollHourView;
