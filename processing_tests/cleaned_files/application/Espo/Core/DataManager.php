<?php
//FORAI:F854;DEF[C662:DataManager,F3999:__construct,F4000:rebuild,F4001:clearCache,F4002:rebuildDatabase,F4003:rebuildMetadata,F4004:updateCacheTimestamp,F4005:updateAppTimestamp,F4006:populateConfigParameters,F4007:setFullTextConfigParameters,F4008:setCryptKeyConfigParameter,F4009:disableHooks,F4010:enableHooks,F4011:checkModules];IMP[F931:C709,F1412:C1162,F1723:C1443,F1747:C1463,F1741:C1457,F1665:C1390,F1662:C1385,F1716:C1438,F1693:C1416,F1656:C1380,F1652:C1377,F1501:C1233,F1403:C1154,F1717:C1439];EXP[C662,F4000,F4001,F4002,F4003,F4004,F4005,F4006,F4007,F4008,F4009,F4010,F4011];LANG[php]//

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

namespace Espo\Core;

use Espo\Core\Exceptions\Error;
use Espo\Core\ORM\EntityManagerProxy;
use Espo\Core\Utils\Database\Helper as DatabaseHelper;
use Espo\Core\Utils\Database\Schema\RebuildMode;
use Espo\Core\Utils\Database\Schema\SchemaManagerProxy;
use Espo\Core\Utils\File\Manager as FileManager;
use Espo\Core\Utils\Metadata;
use Espo\Core\Utils\Util;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Config\ConfigWriter;
use Espo\Core\Utils\Metadata\OrmMetadataData;
use Espo\Core\Utils\Log;
use Espo\Core\Utils\Module;
use Espo\Core\Rebuild\RebuildActionProcessor;
use Espo\Core\ORM\DatabaseParamsFactory;
use Espo\Core\Utils\Config\MissingDefaultParamsSaver as ConfigMissingDefaultParamsSaver;

use Throwable;

/**
 * Clears cache, rebuilds the application.
 */
class DataManager
{
    private string $cachePath = 'data/cache';

    public function __construct(
        private EntityManagerProxy $entityManager,
        private Config $config,
        private ConfigWriter $configWriter,
        private Metadata $metadata,
        private OrmMetadataData $ormMetadataData,
        private HookManager $hookManager,
        private SchemaManagerProxy $schemaManager,
        private Log $log,
        private Module $module,
        private RebuildActionProcessor $rebuildActionProcessor,
        private ConfigMissingDefaultParamsSaver $configMissingDefaultParamsSaver,
        private FileManager $fileManager,
        private DatabaseParamsFactory $databaseParamsFactory,
        private InjectableFactory $injectableFactory
    ) {}

    /**
     * Rebuild the system with metadata, database and cache clearing.
     *
     * @param ?string[] $entityTypeList
     * @throws Error
     */
    public function rebuild(?array $entityTypeList = null): void
    {
        $this->clearCache();
        $this->disableHooks();
        $this->checkModules();
        $this->rebuildMetadata();
        $this->populateConfigParameters();
        $this->rebuildDatabase($entityTypeList);
        $this->rebuildActionProcessor->process();
        $this->configMissingDefaultParamsSaver->process();
        $this->enableHooks();
    }

    /**
     * Clear cache.
     *
     * @throws Error
     */
    public function clearCache(): void
    {
        $this->module->clearCache();

        $result = $this->fileManager->removeInDir($this->cachePath);

        if (!$result) {
            throw new Error("Error while clearing cache");
        }

        $this->updateCacheTimestamp();
    }

    /**
     * Rebuild database.
     *
     * @param ?string[] $entityTypeList
     * @param RebuildMode::* $mode
     * @throws Error
     */
    public function rebuildDatabase(?array $entityTypeList = null, string $mode = RebuildMode::SOFT): void
    {
        if ($entityTypeList && $this->config->get('database.platform') === 'Postgresql') {
            // Prevents sequences from being dropped.
            // @todo Refactor.
            $entityTypeList = null;
        }

        $schemaManager = $this->schemaManager;

        try {
            $result = $schemaManager->rebuild($entityTypeList, $mode);
        } catch (Throwable $e) {
            $result = false;

            $this->log->error(
                "Failed to rebuild database schema. {$e->getMessage()}; {$e->getFile()}:{$e->getLine()}",
                ['exception' => $e]
            );
        }

        if (!$result) {
            throw new Error("Error while rebuilding database. See log file for details.");
        }

        $databaseType = strtolower($schemaManager->getDatabaseHelper()->getType());

        if (
            !$this->config->get('actualDatabaseType') ||
            $this->config->get('actualDatabaseType') !== $databaseType
        ) {
            $this->configWriter->set('actualDatabaseType', $databaseType);
        }

        $databaseVersion = $schemaManager->getDatabaseHelper()->getVersion();

        if (
            !$this->config->get('actualDatabaseVersion') ||
            $this->config->get('actualDatabaseVersion') !== $databaseVersion
        ) {
            $this->configWriter->set('actualDatabaseVersion', $databaseVersion);
        }

        $this->configWriter->updateCacheTimestamp();
        $this->configWriter->save();
    }

    /**
     * Rebuild metadata.
     */
    public function rebuildMetadata(): void
    {
        $this->metadata->init(true);
        $this->ormMetadataData->reload();
        $this->entityManager->getMetadata()->updateData();

        $this->updateCacheTimestamp();
    }

    /**
     * Update cache timestamp.
     */
    public function updateCacheTimestamp(): void
    {
        $this->configWriter->updateCacheTimestamp();
        $this->configWriter->save();
    }

    /**
     * Update app timestamp.
     */
    public function updateAppTimestamp(): void
    {
        $this->configWriter->set('appTimestamp', time());
        $this->configWriter->save();
    }

    private function populateConfigParameters(): void
    {
        $this->setFullTextConfigParameters();
        $this->setCryptKeyConfigParameter();

        if (!$this->config->get('appTimestamp')) {
            $this->updateAppTimestamp();
        }

        $this->configWriter->save();
    }

    private function setFullTextConfigParameters(): void
    {
        $databaseParams = $this->databaseParamsFactory->create();

        if ($databaseParams->getPlatform() !== 'Mysql') {
            return;
        }

        $helper = $this->injectableFactory->create(DatabaseHelper::class);

        $fullTextSearchMinLength = $helper->getParam('ft_min_word_len');

        if ($fullTextSearchMinLength !== null) {
            $fullTextSearchMinLength = (int) $fullTextSearchMinLength;
        }

        $this->configWriter->set('fullTextSearchMinLength', $fullTextSearchMinLength);
    }

    private function setCryptKeyConfigParameter(): void
    {
        if ($this->config->get('cryptKey')) {
            return;
        }

        $cryptKey = Util::generateSecretKey();

        $this->configWriter->set('cryptKey', $cryptKey);
    }

    private function disableHooks(): void
    {
        $this->hookManager->disable();
    }

    private function enableHooks(): void
    {
        $this->hookManager->enable();
    }

    /**
     * @throws Error
     */
    private function checkModules(): void
    {
        $moduleNameList = $this->module->getList();

        if (count(array_unique($moduleNameList)) !== count($moduleNameList)) {
            throw new Error(
                "There is a same module in both `custom` and `internal` directories. " .
                "Should be only in one location."
            );
        }
    }
}
