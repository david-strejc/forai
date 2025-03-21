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


//FORAI:F3376;DEF[C2859:extends];IMP[];EXP[C2859];LANG[javascript]//

import ModalView from 'views/modal';

export default class extends ModalView {

    template = 'admin/extensions/done'
    cssName = 'done-modal'
    createButton = true

    data() {
        return {
            version: this.options.version,
            name: this.options.name,
            text: this.translate('extensionInstalled', 'messages', 'Admin')
                .replace('{version}', this.options.version)
                .replace('{name}', this.options.name)
        };
    }

    setup() {
        this.on('remove', () => {
            window.location.reload();
        });

        this.buttonList = [
            {
                name: 'close',
                label: 'Close',
            }
        ];

        this.headerText = this.getLanguage().translate('Installed successfully', 'labels', 'Admin');
    }
}
