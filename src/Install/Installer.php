<?php

namespace Gcromo\Install;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;
use Module;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tab;

class Installer
{
    private const TAB_PARENT_CLASS_NAME = 'AdminGcromo';
    private const TAB_CHILD_CLASS_NAME = 'AdminGcromoBudget';

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
                `reference` VARCHAR(32) NOT NULL,
                `id_customer` INT UNSIGNED NULL,
                `title` VARCHAR(255) NOT NULL,
                `status` VARCHAR(32) NOT NULL DEFAULT "draft",
                `total_tax_excl` DECIMAL(20,6) NOT NULL DEFAULT 0,
                `total_tax_incl` DECIMAL(20,6) NOT NULL DEFAULT 0,
                `valid_until` DATETIME NULL,
                `date_add` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `date_upd` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY `uniq_gcromo_budget_reference` (`reference`),
                KEY `idx_gcromo_budget_customer` (`id_customer`)
            ) ENGINE=%2$s DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
            $this->dbPrefix,
            \pSQL(_MYSQL_ENGINE_)
        );

        $budgetLineTable = sprintf(
            'CREATE TABLE IF NOT EXISTS `%1$sgcromo_budget_line` (
                `id_gcromo_budget_line` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `id_gcromo_budget` INT UNSIGNED NOT NULL,
                `description` VARCHAR(255) NOT NULL,
                `quantity` DECIMAL(20,6) NOT NULL DEFAULT 1,
                `unit_price_tax_excl` DECIMAL(20,6) NOT NULL DEFAULT 0,
                `unit_price_tax_incl` DECIMAL(20,6) NOT NULL DEFAULT 0,
                `position` INT UNSIGNED NOT NULL DEFAULT 0,
                CONSTRAINT `fk_gcromo_budget_line_budget`
                    FOREIGN KEY (`id_gcromo_budget`) REFERENCES `%1$sgcromo_budget`(`id_gcromo_budget`)
                    ON DELETE CASCADE
            ) ENGINE=%2$s DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
            $this->dbPrefix,
            \pSQL(_MYSQL_ENGINE_)
        );

        try {
            $this->connection->executeStatement($budgetTable);
            $this->connection->executeStatement($budgetLineTable);
        } catch (DBALException $exception) {
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
        return $this->createParentTab() && $this->createChildTab();
    }

    private function uninstallTabs(): bool
    {
        $childId = (int) Tab::getIdFromClassName(self::TAB_CHILD_CLASS_NAME);
        if ($childId) {
            $tab = new Tab($childId);
            $tab->delete();
        }

        $parentId = (int) Tab::getIdFromClassName(self::TAB_PARENT_CLASS_NAME);
        if ($parentId) {
            $tab = new Tab($parentId);
            $tab->delete();
        }

        return true;
    }

    private function createParentTab(): bool
    {
        if ((int) Tab::getIdFromClassName(self::TAB_PARENT_CLASS_NAME)) {
            return true;
        }

        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = self::TAB_PARENT_CLASS_NAME;
        $tab->module = $this->module->name;
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminParentCustomer');
        if (property_exists($tab, 'icon')) {
            $tab->icon = 'summarize';
        }

        foreach ($this->languages as $lang) {
            $tab->name[$lang['id_lang']] = $this->translator->trans('Budgets', [], 'Modules.Gcromo.Admin', $lang['locale']);
        }

        return (bool) $tab->add();
    }

    private function createChildTab(): bool
    {
        if ((int) Tab::getIdFromClassName(self::TAB_CHILD_CLASS_NAME)) {
            return true;
        }

        $parentId = (int) Tab::getIdFromClassName(self::TAB_PARENT_CLASS_NAME);
        if (0 === $parentId) {
            return false;
        }

        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = self::TAB_CHILD_CLASS_NAME;
        $tab->module = $this->module->name;
        $tab->id_parent = $parentId;
        if (property_exists($tab, 'route_name')) {
            $tab->route_name = 'admin_gcromo_budget_index';
        }
        if (property_exists($tab, 'icon')) {
            $tab->icon = 'request_quote';
        }

        foreach ($this->languages as $lang) {
            $tab->name[$lang['id_lang']] = $this->translator->trans('Budget manager', [], 'Modules.Gcromo.Admin', $lang['locale']);
        }

        return (bool) $tab->add();
    }
}
