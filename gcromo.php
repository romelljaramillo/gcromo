<?php
/**
 * Copyright
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

use Doctrine\DBAL\Connection;
use Gcromo\Install\Installer;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Symfony\Contracts\Translation\TranslatorInterface;

class Gcromo extends Module
{
    private const MODULE_VERSION = '1.0.0';

    /** @var Installer|null */
    private $installer;

    public function __construct()
    {
        $this->name = 'gcromo';
        $this->author = 'Roanja';
        $this->version = self::MODULE_VERSION;
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->tab = 'administration';

        parent::__construct();

        $this->displayName = $this->trans('GCromo Budgets', [], 'Modules.Gcromo.Admin');
        $this->description = $this->trans('Create and manage custom budgets directly from your back office.', [], 'Modules.Gcromo.Admin');
        $this->confirmUninstall = $this->trans('This will remove all GCromo budget data. Continue?', [], 'Modules.Gcromo.Admin');

        $this->ps_versions_compliancy = ['min' => '8.2.3', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return parent::install()
            && $this->registerHook('displayBackOfficeHeader')
            && $this->installConfiguration()
            && $this->getInstaller()->install();
    }

    public function uninstall()
    {
        return $this->uninstallConfiguration()
            && $this->getInstaller()->uninstall()
            && parent::uninstall();
    }

    public function getContent()
    {
        $container = SymfonyContainer::getInstance();
        if (null === $container) {
            return '';
        }

        $router = $container->get('router');
        if ($router) {
            \Tools::redirectAdmin($router->generate('admin_gcromo_configuration_index'));
        }

        return '';
    }

    private function installConfiguration(): bool
    {
        \Configuration::updateValue('GCROMO_REFERENCE_PREFIX', 'GC');
        \Configuration::updateValue('GCROMO_DEFAULT_STATUS', 'draft');
        \Configuration::updateValue('GCROMO_DEFAULT_SALES_REP', '');

        return true;
    }

    private function uninstallConfiguration(): bool
    {
        \Configuration::deleteByName('GCROMO_REFERENCE_PREFIX');
        \Configuration::deleteByName('GCROMO_DEFAULT_STATUS');
        \Configuration::deleteByName('GCROMO_DEFAULT_SALES_REP');

        return true;
    }

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
        $this->context->controller->addJS($this->_path . 'views/js/admin.js');
    }

    private function getInstaller(): Installer
    {
        if (null !== $this->installer) {
            return $this->installer;
        }

        $container = SymfonyContainer::getInstance();
        if (null === $container) {
            \PrestaShopLogger::addLog(
                '[GCromo] Symfony container not available while attempting to build installer.',
                3,
                null,
                static::class,
                (int) $this->id
            );

            throw new \RuntimeException('Symfony container not available.');
        }

        /** @var Connection $connection */
        $connection = $container->get('doctrine.dbal.default_connection');

        /** @var TranslatorInterface $translator */
        $translator = $container->get('translator');

        $this->installer = new Installer(
            $connection,
            _DB_PREFIX_,
            Language::getLanguages(true),
            $this,
            $translator
        );

        return $this->installer;
    }
}
