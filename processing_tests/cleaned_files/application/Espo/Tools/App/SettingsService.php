<?php
//FORAI:F595;DEF[C411:SettingsService,F2772:__construct,F2773:getConfigData,F2774:getMetadataConfigData,F2775:getLoginData,F2776:isPortalWithAuthenticationProvider,F2777:setConfigData,F2778:loadAdditionalParams,F2779:filterDataByAccess,F2780:filterEntityTypeParams,F2781:populateDatabaseWithCurrencyRates,F2782:filterData,F2783:isRestrictedMode,F2784:processValidation,F2785:getPortalRepository];IMP[F1427:C1178,F1644:C1368,F302:C162,F927:C708,F931:C709,F926:C705,F1007:C774,F850:C655,F846:C649,F854:C662,F1681:C1402,F1665:C1390,F1662:C1385,F1716:C1438,F1712:C1433,F275:C136,F810:C617];EXP[C411,F2773,F2774,F2775,F2776,F2777,F2778,F2779,F2780,F2781,F2782,F2783,F2784,F2785];LANG[php]//

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

namespace Espo\Tools\App;

use Espo\Core\Mail\ConfigDataProvider as EmailConfigDataProvider;
use Espo\Core\Utils\ThemeManager;
use Espo\Entities\Settings;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Authentication\Util\MethodProvider as AuthenticationMethodProvider;
use Espo\Core\ApplicationState;
use Espo\Core\Acl;
use Espo\Core\InjectableFactory;
use Espo\Core\DataManager;
use Espo\Core\FieldValidation\FieldValidationManager;
use Espo\Core\Utils\Currency\DatabasePopulator as CurrencyDatabasePopulator;
use Espo\Core\Utils\Metadata;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Config\ConfigWriter;
use Espo\Core\Utils\Config\Access;

use Espo\Entities\Portal;
use Espo\Repositories\Portal as PortalRepository;

use stdClass;

class SettingsService
{
    public function __construct(
        private ApplicationState $applicationState,
        private Config $config,
        private ConfigWriter $configWriter,
        private Metadata $metadata,
        private Acl $acl,
        private EntityManager $entityManager,
        private DataManager $dataManager,
        private FieldValidationManager $fieldValidationManager,
        private InjectableFactory $injectableFactory,
        private Access $access,
        private AuthenticationMethodProvider $authenticationMethodProvider,
        private ThemeManager $themeManager,
        private Config\SystemConfig $systemConfig,
        private EmailConfigDataProvider $emailConfigDataProvider,
    ) {}

    /**
     * Get config data.
     */
    public function getConfigData(): stdClass
    {
        $data = $this->config->getAllNonInternalData();

        $this->filterDataByAccess($data);
        $this->filterData($data);
        $this->loadAdditionalParams($data);

        return $data;
    }

    /**
     * Get metadata to be used in config.
     */
    public function getMetadataConfigData(): stdClass
    {
        $data = (object) [];

        unset($data->loginView);

        $loginView = $this->metadata->get(['clientDefs', 'App', 'loginView']);

        if ($loginView) {
            $data->loginView = $loginView;
        }

        $loginData = $this->getLoginData();

        if ($loginData) {
            $data->loginData = (object) $loginData;
        }

        return $data;
    }

    /**
     * @return ?array{
     *     handler: string,
     *     fallback: bool,
     *     data: stdClass,
     *     method: string,
     * }
     */
    private function getLoginData(): ?array
    {
        $method = $this->authenticationMethodProvider->get();

        /** @var array<string, mixed> $mData */
        $mData = $this->metadata->get(['authenticationMethods', $method, 'login']) ?? [];

        /** @var ?string $handler */
        $handler = $mData['handler'] ?? null;

        if (!$handler) {
            return null;
        }

        $isProvider = $this->isPortalWithAuthenticationProvider();

        if (!$isProvider && $this->applicationState->isPortal()) {
            /** @var ?bool $portal */
            $portal = $mData['portal'] ?? null;

            if ($portal === null) {
                /** @var ?string $portalConfigParam */
                $portalConfigParam = $mData['portalConfigParam'] ?? null;

                $portal = $portalConfigParam && $this->config->get($portalConfigParam);
            }

            if (!$portal) {
                return null;
            }
        }

        /** @var ?bool $fallback */
        $fallback = !$this->applicationState->isPortal() ?
            ($mData['fallback'] ?? null) :
            false;

        if ($fallback === null) {
            /** @var ?string $fallbackConfigParam */
            $fallbackConfigParam = $mData['fallbackConfigParam'] ?? null;

            $fallback = $fallbackConfigParam && $this->config->get($fallbackConfigParam);
        }

        if ($isProvider) {
            $fallback = false;
        }

        /** @var stdClass $data */
        $data = (object) ($mData['data'] ?? []);

        return [
            'handler' => $handler,
            'fallback' => $fallback,
            'method' => $method,
            'data' => $data,
        ];
    }

    private function isPortalWithAuthenticationProvider(): bool
    {
        if (!$this->applicationState->isPortal()) {
            return false;
        }

        $portal = $this->applicationState->getPortal();

        return (bool) $this->authenticationMethodProvider->getForPortal($portal);
    }

    /**
     * Set config data.
     *
     * @throws BadRequest
     * @throws Forbidden
     * @throws Error
     */
    public function setConfigData(stdClass $data): void
    {
        $user = $this->applicationState->getUser();

        if (!$user->isAdmin()) {
            throw new Forbidden();
        }

        $ignoreItemList = array_merge(
            $this->access->getSystemParamList(),
            $this->access->getReadOnlyParamList(),
            $this->isRestrictedMode() && !$user->isSuperAdmin() ?
                $this->access->getSuperAdminParamList() : []
        );

        foreach ($ignoreItemList as $item) {
            unset($data->$item);
        }

        $entity = $this->entityManager->getNewEntity(Settings::ENTITY_TYPE);

        $entity->set($data);
        $entity->setAsNotNew();

        $this->processValidation($entity, $data);

        if (
            isset($data->useCache) &&
            $data->useCache !== $this->systemConfig->useCache()
        ) {
            $this->dataManager->clearCache();
        }

        $this->configWriter->setMultiple(get_object_vars($data));
        $this->configWriter->save();

        if (isset($data->personNameFormat)) {
            $this->dataManager->clearCache();
        }

        if (isset($data->defaultCurrency) || isset($data->baseCurrency) || isset($data->currencyRates)) {
            $this->populateDatabaseWithCurrencyRates();
        }
    }

    private function loadAdditionalParams(stdClass $data): void
    {
        if ($this->applicationState->isPortal()) {
            $portal = $this->applicationState->getPortal();

            $this->getPortalRepository()->loadUrlField($portal);

            $data->siteUrl = $portal->get('url');
        }

        if (
            (
                $this->emailConfigDataProvider->getSystemOutboundAddress() ||
                $this->config->get('internalSmtpServer')
            ) &&
            !$this->config->get('passwordRecoveryDisabled')
        ) {
            $data->passwordRecoveryEnabled = true;
        }

        $data->logoSrc = $this->themeManager->getLogoSrc();
    }

    private function filterDataByAccess(stdClass $data): void
    {
        $user = $this->applicationState->getUser();

        $ignoreItemList = [];

        foreach ($this->access->getSystemParamList() as $item) {
            $ignoreItemList[] = $item;
        }

        foreach ($this->access->getInternalParamList() as $item) {
            $ignoreItemList[] = $item;
        }

        if (!$user->isAdmin() || $user->isSystem()) {
            foreach ($this->access->getAdminParamList() as $item) {
                $ignoreItemList[] = $item;
            }
        }

        /*if ($this->isRestrictedMode() && !$user->isSuperAdmin()) {
            // @todo Maybe add restriction level for non-super admins.
        }*/

        foreach ($ignoreItemList as $item) {
            unset($data->$item);
        }

        if ($user->isSystem()) {
            $globalItemList = $this->access->getGlobalParamList();

            foreach (array_keys(get_object_vars($data)) as $item) {
                if (!in_array($item, $globalItemList)) {
                    unset($data->$item);
                }
            }
        }
    }

    private function filterEntityTypeParams(stdClass $data): void
    {
        $entityTypeListParamList = $this->metadata->get(['app', 'config', 'entityTypeListParamList']) ?? [];

        /** @var string[] $scopeList */
        $scopeList = array_keys($this->metadata->get(['entityDefs'], []));

        foreach ($scopeList as $scope) {
            if (!$this->metadata->get(['scopes', $scope, 'acl'])) {
                continue;
            }

            if ($this->acl->tryCheck($scope)) {
                continue;
            }

            foreach ($entityTypeListParamList as $param) {
                $list = $data->$param ?? [];

                foreach ($list as $i => $item) {
                    if ($item === $scope) {
                        unset($list[$i]);
                    }
                }

                $data->$param = array_values($list);
            }
        }
    }

    private function populateDatabaseWithCurrencyRates(): void
    {
        $this->injectableFactory->create(CurrencyDatabasePopulator::class)->process();
    }

    private function filterData(stdClass $data): void
    {
        $user = $this->applicationState->getUser();

        if (!$user->isAdmin() && !$user->isSystem()) {
            $this->filterEntityTypeParams($data);
        }

        $fieldDefs = $this->metadata->get(['entityDefs', 'Settings', 'fields']);

        foreach ($fieldDefs as $field => $fieldParams) {
            if ($fieldParams['type'] === 'password') {
                unset($data->$field);
            }
        }

        if (empty($data->useWebSocket)) {
            unset($data->webSocketUrl);
        }

        if ($user->isSystem()) {
            return;
        }

        if ($user->isAdmin()) {
            return;
        }

        if (
            !$this->acl->checkScope('Email', 'create') ||
            !$this->config->get('outboundEmailIsShared')
        ) {
            unset($data->outboundEmailFromAddress);
            unset($data->outboundEmailFromName);
            unset($data->outboundEmailBccAddress);
        }
    }

    private function isRestrictedMode(): bool
    {
        return (bool) $this->config->get('restrictedMode');
    }

    /**
     * @throws BadRequest
     */
    private function processValidation(Entity $entity, stdClass $data): void
    {
        $this->fieldValidationManager->process($entity, $data);
    }

    private function getPortalRepository(): PortalRepository
    {
        /** @var PortalRepository */
        return $this->entityManager->getRepository(Portal::ENTITY_TYPE);
    }
}
