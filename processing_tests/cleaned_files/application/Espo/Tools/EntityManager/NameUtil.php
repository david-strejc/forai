<?php
//FORAI:F552;DEF[C373:NameUtil,F2556:__construct,F2557:nameIsBad,F2558:nameIsTooShort,F2559:nameIsTooLong,F2560:nameIsNotAllowed,F2561:nameIsUsed,F2562:routeExists,F2563:controllerExists,F2564:relationshipExists,F2565:addCustomPrefix,F2566:fieldExists,F2567:linkExists];IMP[F1909:C1608,F1662:C1385,F1665:C1390,F1661:C1387,F849:C654,F415:C253];EXP[C373,F2557,F2558,F2559,F2560,F2561,F2562,F2563,F2564,F2565,F2566,F2567];LANG[php]//

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

namespace Espo\Tools\EntityManager;

use Espo\Core\Name\Field;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Metadata;
use Espo\Core\Utils\Route;
use Espo\Core\Utils\Util;
use Espo\Core\ServiceFactory;
use Espo\ORM\Defs\Params\EntityParam;
use Espo\ORM\EntityManager;
use Espo\ORM\Entity;
use Espo\ORM\Name\Attribute;

class NameUtil
{
    public const MAX_ENTITY_NAME_LENGTH = 64;
    public const MIN_ENTITY_NAME_LENGTH = 3;

    /**
     * @var string[]
     */
    public const RESERVED_WORLD_LIST = [
        '__halt_compiler',
        'abstract',
        'and',
        'array',
        'as',
        'break',
        'callable',
        'case',
        'catch',
        'class',
        'clone',
        'const',
        'continue',
        'declare',
        'default',
        'die',
        'do',
        'echo',
        'else',
        'elseif',
        'empty',
        'enddeclare',
        'endfor',
        'endforeach',
        'endif',
        'endswitch',
        'endwhile',
        'eval',
        'exit',
        'extends',
        'final',
        'for',
        'foreach',
        'function',
        'global',
        'goto',
        'if',
        'implements',
        'include',
        'include_once',
        'instanceof',
        'insteadof',
        'interface',
        'isset',
        'list',
        'namespace',
        'new',
        'or',
        'print',
        'private',
        'protected',
        'public',
        'require',
        'require_once',
        'return',
        'static',
        'switch',
        'throw',
        'trait',
        'try',
        'unset',
        'use',
        'var',
        'while',
        'xor',
        'common',
        'fn',
        'parent',
        'int',
        'float',
        'bool',
        'string',
        'true',
        'false',
        'null',
        'void',
        'iterable',
        'object',
        'mixed',
        'never',
    ];

    /**
     * @var string[]
     */
    public const FIELD_FORBIDDEN_NAME_LIST = [
        Attribute::ID,
        Attribute::DELETED,
        'deleteId',
        'skipDuplicateCheck',
        'null',
        'false',
        'true',
        Field::VERSION_NUMBER,
        Field::IS_STARRED,
        Field::IS_FOLLOWED,
        Field::FOLLOWERS,
        Field::TEAMS,
        Field::ASSIGNED_USER,
        Field::ASSIGNED_USERS,
        Field::COLLABORATORS,
        Field::STREAM_UPDATED_AT,
        Field::CREATED_BY,
        Field::CREATED_AT,
        Field::MODIFIED_BY,
        Field::MODIFIED_AT,
    ];

    /**
     * @var string[]
     */
    public const LINK_FORBIDDEN_NAME_LIST = [
        'posts',
        'stream',
        'subscription',
        'starSubscription',
        'action',
        'null',
        'false',
        'true',
        'layout',
        'system',
        Field::FOLLOWERS,
        Field::TEAMS,
        Field::ASSIGNED_USER,
        Field::ASSIGNED_USERS,
        Field::COLLABORATORS,
        Field::CREATED_BY,
        Field::MODIFIED_BY,
    ];

    /**
     * @var string[]
     */
    public const ENTITY_TYPE_FORBIDDEN_NAME_LIST = [
        'Common',
        'PortalUser',
        'ApiUser',
        'Timeline',
        'About',
        'Admin',
        'Null',
        'False',
        'True',
        'Base',
        'Layout',
        'Home',
    ];

    public function __construct(
        private Metadata $metadata,
        private ServiceFactory $serviceFactory,
        private EntityManager $entityManager,
        private Route $routeUtil,
        private Config $config
    ) {}

    public function nameIsBad(string $name): bool
    {
        if (!$name) {
            return true;
        }

        if (preg_match('/[^a-zA-Z\d]/', $name)) {
            return true;
        }

        if (preg_match('/[^A-Z]/', $name[0])) {
            return true;
        }

        return false;
    }

    public function nameIsTooShort(string $name): bool
    {
        return strlen($name) < NameUtil::MIN_ENTITY_NAME_LENGTH;
    }

    public function nameIsTooLong(string $name): bool
    {
        return strlen(Util::camelCaseToUnderscore($name)) > NameUtil::MAX_ENTITY_NAME_LENGTH;
    }

    public function nameIsNotAllowed(string $name): bool
    {
        if (in_array($name, self::ENTITY_TYPE_FORBIDDEN_NAME_LIST)) {
            return true;
        }

        if (in_array(strtolower($name), NameUtil::RESERVED_WORLD_LIST)) {
            return true;
        }

        if ($name !== Util::normalizeScopeName($name)) {
            return true;
        }

        return false;
    }

    public function nameIsUsed(string $name): bool
    {
        if ($this->metadata->get(['scopes', $name])) {
            return true;
        }

        if ($this->metadata->get(['entityDefs', $name])) {
            return true;
        }

        if ($this->metadata->get(['clientDefs', $name])) {
            return true;
        }

        if ($this->relationshipExists($name)) {
            return true;
        }

        if ($this->controllerExists($name)) {
            return true;
        }

        if ($this->serviceFactory->checkExists($name)) {
            return true;
        }

        if ($this->routeExists($name)) {
            return true;
        }

        return false;
    }

    private function routeExists(string $name): bool
    {
        foreach ($this->routeUtil->getFullList() as $route) {
            if (
                $route->getRoute() === '/' . $name ||
                str_starts_with($route->getRoute(), '/' . $name . '/')
            ) {
                return true;
            }
        }

        return false;
    }

    private function controllerExists(string $name): bool
    {
        $controllerClassName = 'Espo\\Custom\\Controllers\\' . Util::normalizeClassName($name);

        if (class_exists($controllerClassName)) {
            return true;
        }

        foreach ($this->metadata->getModuleList() as $moduleName) {
            $controllerClassName =
                'Espo\\Modules\\' . $moduleName . '\\Controllers\\' . Util::normalizeClassName($name);

            if (class_exists($controllerClassName)) {
                return true;
            }
        }

        $controllerClassName = 'Espo\\Controllers\\' . Util::normalizeClassName($name);

        if (class_exists($controllerClassName)) {
            return true;
        }

        return false;
    }

    public function relationshipExists(string $name): bool
    {
        /** @var string[] $scopeList */
        $scopeList = array_keys($this->metadata->get(['scopes'], []));

        foreach ($scopeList as $entityType) {
            $relationsDefs = $this->entityManager
                ->getMetadata()
                ->get($entityType, EntityParam::RELATIONS);

            if (empty($relationsDefs)) {
                continue;
            }

            foreach ($relationsDefs as $item) {
                if (empty($item['type']) || empty($item['relationName'])) {
                    continue;
                }

                if (
                    $item['type'] === Entity::MANY_MANY &&
                    ucfirst($item['relationName']) === ucfirst($name)
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    public function addCustomPrefix(string $name, bool $ucFirst = false): string
    {
        if ($this->config->get('customPrefixDisabled')) {
            return $name;
        }

        $prefix = $ucFirst ? 'C' : 'c';

        return $prefix . ucfirst($name);
    }

    public function fieldExists(string $entityType, string $name): bool
    {
        return (bool) $this->metadata->get("entityDefs.$entityType.fields.$name");
    }

    public function linkExists(string $entityType, string $name): bool
    {
        return (bool) $this->metadata->get("entityDefs.$entityType.links.$name");
    }
}
