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


//FORAI:F3245;DEF[C2728:EmailFilterHandler<DynamicHandler>];IMP[];EXP[C2728];LANG[javascript]//

import DynamicHandler from 'dynamic-handler';

class EmailFilterHandler extends DynamicHandler {

    init() {
        if (this.model.isNew()) {
            if (!this.recordView.getUser().isAdmin()) {
                this.recordView.hideField('isGlobal');
            }
        }

        if (
            !this.model.isNew() &&
            !this.recordView.getUser().isAdmin() &&
            !this.model.get('isGlobal')
        ) {
            this.recordView.hideField('isGlobal');
        }

        if (this.model.isNew() && !this.model.get('parentId')) {
            this.model.set('parentType', 'User');
            this.model.set('parentId', this.recordView.getUser().id);
            this.model.set('parentName', this.recordView.getUser().get('name'));

            if (!this.recordView.getUser().isAdmin()) {
                this.recordView.setFieldReadOnly('parent');
            }
        }
        else if (
            this.model.get('parentType') &&
            !this.recordView.options.duplicateSourceId
        ) {
            this.recordView.setFieldReadOnly('parent');
            this.recordView.setFieldReadOnly('isGlobal');
        }

        this.recordView.listenTo(this.model, 'change:isGlobal', (model, value, o) => {
            if (!o.ui) {
                return;
            }

            if (value) {
                this.model.set({
                    action: 'Skip',
                    parentName: null,
                    parentType: null,
                    parentId: null,
                    emailFolderId: null,
                    groupEmailFolderId: null,
                    markAsRead: false,
                });
            }
        });

        this.recordView.listenTo(this.model, 'change:parentType', (model, value, o) => {
            if (!o.ui) {
                return;
            }

            // Avoiding side effects.
            setTimeout(() => {
                if (value !== 'User') {
                    this.model.set('markAsRead', false);
                }

                if (value === 'EmailAccount') {
                    this.model.set('action', 'Skip');
                    this.model.set('emailFolderId', null);
                    this.model.set('groupEmailFolderId', null);
                    this.model.set('markAsRead', false);

                    return;
                }

                if (value !== 'InboundEmail') {
                    if (this.model.get('action') === 'Move to Group Folder') {
                        this.model.set('action', 'Skip');
                    }

                    this.model.set('groupEmailFolderId', null);

                    return;
                }

                if (value !== 'User') {
                    if (this.model.get('action') === 'Move to Folder') {
                        this.model.set('action', 'Skip');
                    }

                    this.model.set('groupFolderId', null);
                }
            }, 40);
        });
    }
}

export default EmailFilterHandler;

