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


//FORAI:F3769;DEF[C3255:MessageNotificationItemView<BaseNotificationItemView>];IMP[];EXP[C3255];LANG[javascript]//

import BaseNotificationItemView from 'views/notification/items/base';
import DOMPurify from 'dompurify';

class MessageNotificationItemView extends BaseNotificationItemView {

    template = 'notification/items/message'

    data() {
        return {
            ...super.data(),
            style: this.style,
        };
    }

    setup() {
        const data = /** @type {Object.<string, *>} */this.model.get('data') || {};

        const messageRaw = this.model.get('message') || data.message || '';

        const message = this.getHelper().transformMarkdownText(messageRaw);

        this.messageTemplate = DOMPurify.sanitize(message, {}).toString();

        this.userId = data.userId;
        this.style = data.style || 'text-muted';

        this.messageData['entityType'] = this.translateEntityType(data.entityType);

        this.messageData['user'] =
            $('<a>')
                .attr('href', '#User/view/' + data.userId)
                .attr('data-id', data.userId)
                .attr('data-scope', 'User')
                .text(data.userName);

        this.messageData['entity'] =
            $('<a>')
                .attr('href', '#' + data.entityType + '/view/' + data.entityId)
                .attr('data-id', data.entityId)
                .attr('data-scope', data.entityType)
                .text(data.entityName);

        this.createMessage();
    }
}

export default MessageNotificationItemView;
