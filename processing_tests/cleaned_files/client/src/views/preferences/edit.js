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


//FORAI:F3522;DEF[C3003:extends];IMP[];EXP[C3003];LANG[javascript]//

import EditView from 'views/edit';

export default class extends EditView {

    userName = ''

    setup() {
        super.setup();

        this.userName = this.model.get('name');
    }

    getHeader() {
        return this.buildHeaderHtml([
            $('<span>').text(this.translate('Preferences')),
            $('<span>').text(this.userName),
        ]);
    }

    updatePageTitle() {
        let title = this.getLanguage().translate(this.scope, 'scopeNames');

        if (this.model.id !== this.getUser().id && this.userName) {
            title += ' · ' + this.userName;
        }

        this.setPageTitle(title);
    }
}
