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


//FORAI:F3075;DEF[F12911:actionSetHeld,F12912:actionSetNotHeld,F12913:actionSetCompleted];IMP[];EXP[F12911,F12912,F12913];LANG[javascript]//

define('crm:views/record/list-activities-dashlet',
['views/record/list-expanded', 'crm:views/meeting/record/list', 'crm:views/task/record/list'],
function (Dep, MeetingList, TaskList) {

    return Dep.extend({

        actionSetHeld: function (data) {
            MeetingList.prototype.actionSetHeld.call(this, data);
        },

        actionSetNotHeld: function (data) {
            MeetingList.prototype.actionSetNotHeld.call(this, data);
        },

        actionSetCompleted: function (data) {
            TaskList.prototype.actionSetCompleted.call(this, data);
        },
    });
});
