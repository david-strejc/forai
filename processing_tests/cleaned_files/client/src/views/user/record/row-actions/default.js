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


//FORAI:F3310;DEF[C2794:UserDefaultRowActionsView<DefaultRowActionsView>];IMP[];EXP[C2794];LANG[javascript]//

import DefaultRowActionsView from 'views/record/row-actions/default';

export default class UserDefaultRowActionsView extends DefaultRowActionsView {


    getActionList() {
        let scope = 'User';

        const model = /** @type {import('models/user').default} */this.model;

        if (model.isPortal()) {
            scope = 'PortalUser';
        } else if (model.isApi()) {
            scope = 'ApiUser';
        }

        const list = [{
            action: 'quickView',
            label: 'View',
            data: {
                id: this.model.id,
                scope: scope,
            },
            link: `#${scope}/view/${this.model.id}`,
        }];

        if (this.options.acl.edit) {
            list.push({
                action: 'quickEdit',
                label: 'Edit',
                data: {
                    id: this.model.id,
                    scope: scope,
                },
                link: `#${scope}/edit/${this.model.id}`,
            });
        }

        this.getAdditionalActionList().forEach(item => list.push(item));

        return list;
    }
}
