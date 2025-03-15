<?php
//FORAI:F1015;DEF[C782:Base,F4628:__construct,F4629:getName,F4630:checkAccess,F4631:checkControllerAccess,F4632:getService,F4633:getContainer,F4634:getUser,F4635:getAcl,F4636:getAclManager,F4637:getConfig,F4638:getPreferences,F4639:getMetadata,F4640:getServiceFactory];IMP[F853:C659,F926:C705,F849:C654,F1662:C1385,F1665:C1390,F279:C140];EXP[C782,F4629,F4630,F4631,F4632,F4633,F4634,F4635,F4636,F4637,F4638,F4639,F4640];LANG[php]//

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

namespace Espo\Core\Controllers;

use Espo\Core\Acl;
use Espo\Core\AclManager;
use Espo\Core\Container;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\ServiceFactory;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Metadata;

use Espo\Entities\Preferences;
use Espo\Entities\User;

/**
 * @deprecated Don't extend.
 * @todo Remove in v10.0.
 */
abstract class Base
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    public static $defaultAction = 'index';

    /**
     * @deprecated
     * @var Container
     */
    private $container;

    /**
     * @var User;
     */
    protected $user;

    /**
     * @var Acl;
     */
    protected $acl;

    /**
     * @deprecated
     * @var AclManager
     */
    protected $aclManager;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @deprecated
     * @var Preferences
     */
    protected $preferences;

    /**
     * @deprecated
     * @var Metadata
     */
    protected $metadata;

    /**
     * @deprecated
     * @var ServiceFactory
     */
    protected $serviceFactory;

    /**
     * @throws Forbidden
     * @internal Most dependencies are for backward compatibility.
     */
    public function __construct(
        Container $container,
        User $user,
        Acl $acl,
        AclManager $aclManager,
        Config $config,
        Preferences $preferences,
        Metadata $metadata,
        ServiceFactory $serviceFactory
    ) {
        $this->container = $container;
        $this->user = $user;
        $this->acl = $acl;
        $this->aclManager = $aclManager;
        $this->config = $config;
        $this->preferences = $preferences;
        $this->metadata = $metadata;
        $this->serviceFactory = $serviceFactory;

        if (empty($this->name)) {
            $name = get_class($this);

            $matches = null;

            if (preg_match('@\\\\([\w]+)$@', $name, $matches)) {
                $name = $matches[1];
            }

            $this->name = $name;
        }

        $this->checkControllerAccess();

        if (!$this->checkAccess()) {
            throw new Forbidden("No access to '{$this->name}'.");
        }
    }

    /**
     * @deprecated
     */
    protected function getName(): string
    {
        /** @var string */
        return $this->name;
    }

    /**
     * Check access to controller.
     */
    protected function checkAccess(): bool
    {
        return true;
    }

    /**
     * @return void;
     * @deprecated
     */
    protected function checkControllerAccess()
    {
        return;
    }

    /**
     * @deprecated
     * @param string $name
     */
    protected function getService($name): object
    {
        return $this->serviceFactory->create($name);
    }

    /**
     * @deprecated Use Aware interfaces to inject dependencies.
     *
     * @return Container
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * @deprecated
     *
     * @return User
     */
    protected function getUser()
    {
        /** @var User */
        return $this->container->get('user');
    }

    /**
     * @deprecated
     *
     * @return Acl
     */
    protected function getAcl()
    {
        /** @var Acl */
        return $this->container->get('acl');
    }

    /**
     * @deprecated
     *
     * @return AclManager
     */
    protected function getAclManager()
    {
        /** @var AclManager */
        return $this->container->get('aclManager');
    }

    /**
     * @deprecated
     *
     * @return Config
     */
    protected function getConfig()
    {
        /** @var Config */
        return $this->container->get('config');
    }

    /**
     * @deprecated
     * @return Preferences
     */
    protected function getPreferences()
    {
        /** @var Preferences */
        return $this->container->get('preferences');
    }

    /**
     * @deprecated
     *
     * @return Metadata
     */
    protected function getMetadata()
    {
        /** @var Metadata */
        return $this->container->get('metadata');
    }

    /**
     * @deprecated
     *
     * @return ServiceFactory
     */
    protected function getServiceFactory()
    {
        /** @var ServiceFactory */
        return $this->container->get('serviceFactory');
    }
}
