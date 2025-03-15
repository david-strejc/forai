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


//FORAI:F3970;DEF[C3456:TreeCollection<Collection>];IMP[];EXP[C3456];LANG[javascript]//

/** @module collections/tree */

import Collection from 'collection';

class TreeCollection extends Collection {

    /**
     * @return {TreeCollection}
     */
    createSeed() {
        const seed = new this.constructor();

        seed.url = this.url;
        seed.model = this.model;
        seed.name = this.name;
        seed.entityType = this.entityType;
        seed.defs = this.defs;

        return seed;
    }

    prepareAttributes(response, options) {
        const list = super.prepareAttributes(response, options);

        const seed = this.clone();

        seed.reset();

        this.path = response.path;
        /**
         * @type {{
         *     name: string,
         *     upperId?: string,
         *     upperName?: string,
         * }|null}
         */
        this.categoryData = response.data || null;

        const prepare = (list, depth) => {
            list.forEach(data => {
                data.depth = depth;

                const childCollection = this.createSeed();

                childCollection.parentId = data.id;

                if (data.childList) {
                    if (data.childList.length) {
                        prepare(data.childList, depth + 1);

                        childCollection.set(data.childList);
                        data.childCollection = childCollection;

                        return;
                    }

                    data.childCollection = childCollection;

                    return;
                }

                if (data.childList === null) {
                    data.childCollection = null;

                    return;
                }

                data.childCollection = childCollection;
            });
        };

        prepare(list, 0);

        return list;
    }

    fetch(options) {
        options = options || {};
        options.data = options.data || {};

        if (this.parentId) {
            options.data.parentId = this.parentId;
        }

        return super.fetch(options);
    }
}

export default TreeCollection;
