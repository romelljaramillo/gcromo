<?php

namespace Gcromo\Install;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;
use Module;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tab;

class Installer
{
    private const ADMIN_TAB_DEFINITIONS = [
        [
            'class_name' => 'AdminParentGcromo',
            'parent_class_name' => '0',
            'route_name' => null,
            'icon' => 'summarize',
            'visible' => true,
            'wording' => 'GCromo',
            'wording_domain' => 'Modules.Gcromo.Admin',
        ],
        [
            'class_name' => 'AdminGcromo',
            'parent_class_name' => 'AdminParentGcromo',
            'route_name' => null,
            'icon' => 'request_quote',
            'visible' => true,
            'wording' => 'Budgets',
            'wording_domain' => 'Modules.Gcromo.Admin',
        ],
        [
            'class_name' => 'AdminGcromoConfiguration',
            'parent_class_name' => 'AdminParentGcromo',
            'route_name' => 'admin_gcromo_configuration_index',
            'icon' => 'settings',
            'visible' => true,
            'wording' => 'Configuration',
            'wording_domain' => 'Modules.Gcromo.Admin',
        ],
        [
            'class_name' => 'AdminGcromoCustomer',
            'parent_class_name' => 'AdminParentGcromo',
            'route_name' => 'admin_gcromo_customer_index',
            'icon' => 'groups',
            'visible' => true,
            'wording' => 'Customers',
            'wording_domain' => 'Modules.Gcromo.Admin',
        ],
        [
            'class_name' => 'AdminGcromoBudget',
            'parent_class_name' => 'AdminParentGcromo',
            'route_name' => 'admin_gcromo_budget_index',
            'icon' => 'request_quote',
            'visible' => true,
            'wording' => 'Budgets',
            'wording_domain' => 'Modules.Gcromo.Admin',
        ],
    ];

    private Connection $connection;
    private string $dbPrefix;
    private array $languages;
    private Module $module;
    private TranslatorInterface $translator;

    public function __construct(Connection $connection, string $dbPrefix, array $languages, Module $module, TranslatorInterface $translator)
    {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->languages = $languages;
        $this->module = $module;
        $this->translator = $translator;
    }

    public function install(): bool
    {
        return $this->createTables() && $this->installTabs();
    }

    public function uninstall(): bool
    {
        return $this->dropTables() && $this->uninstallTabs();
    }

    private function createTables(): bool
    {
        $budgetTable = sprintf(
            'CREATE TABLE IF NOT EXISTS `%1$sgcromo_budget` (
                `id_gcromo_budget` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `quote_reference` VARCHAR(32) NOT NULL,
                `quote_date` DATE NULL,
                `customer_id` INT UNSIGNED NULL,
                `customer_name` VARCHAR(255) NOT NULL DEFAULT "",
                `product_name` VARCHAR(255) NOT NULL DEFAULT "",
                `product_summary` TEXT NULL,
                `work_scope` VARCHAR(255) NOT NULL DEFAULT "",
                `dimension_height_cm` DECIMAL(10,2) NOT NULL DEFAULT 0,
                `dimension_width_primary_cm` DECIMAL(10,2) NOT NULL DEFAULT 0,
                `dimension_width_secondary_cm` DECIMAL(10,2) NOT NULL DEFAULT 0,
                `sales_rep` VARCHAR(255) NOT NULL DEFAULT "",
                `status` VARCHAR(32) NOT NULL DEFAULT "draft",
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY `uniq_gcromo_budget_reference` (`quote_reference`),
                KEY `idx_gcromo_budget_customer` (`customer_id`),
                KEY `idx_gcromo_budget_status` (`status`)
            ) ENGINE=%2$s DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
            $this->dbPrefix,
            \pSQL(_MYSQL_ENGINE_)
        );

        try {
            $this->connection->executeStatement($budgetTable);
        } catch (DBALException $exception) {
            \PrestaShopLogger::addLog(
                sprintf('[GCromo] Failed to create gcromo_budget table: %s', $exception->getMessage()),
                3,
                null,
                \Gcromo::class,
                (int) $this->module->id
            );

            return false;
        }

        return true;
    }

    private function dropTables(): bool
    {
        $queries = [
            sprintf('DROP TABLE IF EXISTS `%sgcromo_budget_line`', $this->dbPrefix),
            sprintf('DROP TABLE IF EXISTS `%sgcromo_budget`', $this->dbPrefix),
        ];

        try {
            foreach ($queries as $query) {
                $this->connection->executeStatement($query);
            }
        } catch (DBALException $exception) {
            return false;
        }

        return true;
    }

    private function installTabs(): bool
    {
        foreach (self::ADMIN_TAB_DEFINITIONS as $definition) {
            if (false === $this->createTab($definition)) {
                return false;
            }
        }

        return true;
    }

    private function uninstallTabs(): bool
    {
        foreach (array_reverse(self::ADMIN_TAB_DEFINITIONS) as $definition) {
            $tabId = (int) Tab::getIdFromClassName($definition['class_name']);
            if ($tabId) {
                $tab = new Tab($tabId);
                $tab->delete();
            }
        }

        return true;
    }

    private function createTab(array $definition): bool
    {
        if ((int) Tab::getIdFromClassName($definition['class_name'])) {
            return true;
        }

        $parentId = 0;
        $parentClassName = $definition['parent_class_name'] ?? null;

        if (null !== $parentClassName && '' !== $parentClassName) {
            if ('0' === $parentClassName || 0 === $parentClassName) {
                $parentId = 0;
            } else {
                $parentId = (int) Tab::getIdFromClassName($parentClassName);
            }
        }

        if (null !== $parentClassName && '' !== $parentClassName && '0' !== $parentClassName && 0 === $parentId) {
            \PrestaShopLogger::addLog(
                sprintf('[GCromo] Parent tab %s not found, falling back to root menu.', $parentClassName),
                2,
                null,
                \Gcromo::class,
                (int) $this->module->id
            );

            $parentId = 0;
        }

        $tab = new Tab();
        $tab->active = $definition['visible'] ?? true ? 1 : 0;
        $tab->class_name = $definition['class_name'];
        $tab->module = $this->module->name;
        $tab->id_parent = $parentId;

        if (!empty($definition['route_name']) && property_exists($tab, 'route_name')) {
            $tab->route_name = $definition['route_name'];
        }

        if (!empty($definition['icon']) && property_exists($tab, 'icon')) {
            $tab->icon = $definition['icon'];
        }

        foreach ($this->languages as $lang) {
            $tab->name[$lang['id_lang']] = $this->translator->trans(
                $definition['wording'] ?? $definition['class_name'],
                [],
                $definition['wording_domain'] ?? 'Modules.Gcromo.Admin',
                $lang['locale']
            );
        }

        $added = (bool) $tab->add();

        if (!$added) {
            \PrestaShopLogger::addLog(
                sprintf('[GCromo] Unable to create admin tab %s.', $definition['class_name']),
                3,
                null,
                \Gcromo::class,
                (int) $this->module->id
            );
        }

        return $added;
    }
}
