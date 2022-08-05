<?php
/**
 * Copyright Bridge
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to tech@202-ecommerce.com so we can send you a copy immediately.
 *
 * @author    202 ecommerce <tech@202-ecommerce.com>
 * @copyright Bridge
 * @license   https://opensource.org/licenses/AFL-3.0  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'bridge/vendor/autoload.php';

use BridgeAddon\Entity\BridgeTransaction;
use BridgeAddon\Hook\HookDispatcher;
use BridgeAddon\Utils\BridgeInitialiser;
use BridgeAddon\Utils\PaymentModuleTrait;
use BridgeClasslib\Extensions\ProcessLogger\ProcessLoggerExtension;

class Bridge extends PaymentModule
{
    use PaymentModuleTrait {
        PaymentModuleTrait::__construct as private __pmConstruct;
        PaymentModuleTrait::install as private pmInstall;
        PaymentModuleTrait::uninstall as private pmUninstall;
    }

    /** @var string This module requires at least PHP version */
    public $php_version_required = '5.6';

    public $context;

    /**
     * List of ModuleFrontController used in this Module
     * Module::install() register it, after that you can edit it in BO (for rewrite if needed)
     *
     * @var array
     */
    public $controllers = [
    ];

    /**
     * List of objectModel used in this Module
     *
     * @var array
     */
    public $objectModels = [
        BridgeTransaction::class,
    ];

    public $extensions = [
        ProcessLoggerExtension::class,
    ];

    public $moduleAdminControllers = [
        [
            'name' => [
                'en' => 'Bridge',
                'fr' => 'Bridge',
            ],
            'class_name' => 'AdminBridgeParentMain',
            'parent_class_name' => 'CONFIGURE',
            'visible' => false,
        ],
        [
            'name' => [
                'en' => 'Bridge',
                'fr' => 'Bridge',
            ],
            'class_name' => 'AdminBridgeParent',
            'parent_class_name' => 'AdminParentPayment',
            'visible' => false,
        ],
        [
            'name' => [
                'en' => 'Configuration',
                'fr' => 'Configuration',
                'pt' => 'Configuração',
                'it' => 'Configurazione',
                'de' => 'Konfiguration',
            ],
            'class_name' => 'AdminBridgeConfiguration',
            'parent_class_name' => 'AdminBridgeParent',
            'visible' => true,
        ],
        [
            'name' => [
                'en' => 'Logs',
                'fr' => 'Logs',
            ],
            'class_name' => 'AdminBridgeProcessLogger',
            'parent_class_name' => 'AdminBridgeParent',
            'visible' => true,
        ],
        [
            'name' => [
                'en' => 'FAQ',
                'fr' => 'FAQ',
                'es' => 'PREGUNTAS FRECUENTES',
            ],
            'class_name' => 'AdminBridgeHelp',
            'parent_class_name' => 'AdminBridgeParent',
            'visible' => true,
        ],
    ];

    public $statuses = [
        self::ORDER_STATE_WAITING_PAYMENT_BRIDGE => [
            'name' => [
                'en' => 'Waiting for Bridge Transfer',
                'fr' => 'En attente du virement Bridge',
                'de' => 'Warten auf die Bridge-Überweisung',
                'it' => 'In attesa del trasferimento di Brige',
                'pt' => 'A aguardar a transferência da ponte',
                'es' => 'A la espera del traslado del Bridge',
            ],
            'template' => [
                'en' => 'payment',
            ],
            'color' => '#34209E',
            'logo' => _PS_MODULE_DIR_ . 'bridge/views/img/logo-order.gif',
            'logable' => true,
            'unremovable' => true,
            'send_email' => true,
        ],
    ];

    const CLIENT_SECRET = 'BRIDGE_CLIENT_SECRET';

    const CLIENT_ID = 'BRIDGE_CLIENT_ID';

    const CLIENT_SECRET_PRODUCTION = 'BRIDGE_CLIENT_SECRET_PRODUCTION';

    const CLIENT_ID_PRODUCTION = 'BRIDGE_CLIENT_ID_PRODUCTION';

    const PRODUCTION_MODE = 'BRIDGE_PRODUCTION_MODE';

    const STATUS_PENDING_TRANSFERT = 'BRIDGE_STATUT_PENDING_TRANSFERT';

    const STATUS_RECEIVED_TRANSFERT = 'BRIDGE_STATUS_RECEIVED_TRANSFERT';

    const ORDER_STATE_WAITING_PAYMENT_BRIDGE = 'BRIDGE_ORDER_STATE_WAITING_PAYMENT_BRIDGE';

    const WEBHOOK_CONTACTED = 'BRIDGE_WEBHOOK_CONTACTED';

    const IS_FILE_LOGGER_ACTIVE = false;

    const AVAILABLE_CURRENCIES = [
        'EUR',
    ];

    const PREFERRED_ISO_CODE = 'FR';

    public function __construct()
    {
        $this->module_key = '';
        $this->name = 'bridge';
        $this->version = '1.0.0';
        $this->author = '202 ecommerce';
        $this->tab = 'payments_gateways';
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_,
        ];
        $this->need_instance = true;

        $this->__pmConstruct();

        $this->secure_key = Tools::encrypt($this->name);
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
        $this->displayName = $this->l('Bridge');
        $this->description = $this->l('Easily add direct deposit payment for your customers.');
        $this->hookDispatcher = new HookDispatcher($this);
        $this->hooks = array_merge($this->hooks, $this->hookDispatcher->getAvailableHooks());
    }

    public function getContent()
    {
        Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminBridgeConfiguration'));
    }

    public function isUsingNewTranslationSystem()
    {
        return false;
    }

    public function install()
    {
        $result = $this->pmInstall();
        $this->registerOrderStates();

        $bridgePendingOrderState = Configuration::get(self::ORDER_STATE_WAITING_PAYMENT_BRIDGE);
        $paymentReceived = Configuration::get('PS_OS_PAYMENT');

        Configuration::updateGlobalValue(self::STATUS_PENDING_TRANSFERT, $bridgePendingOrderState);
        Configuration::updateGlobalValue(self::STATUS_RECEIVED_TRANSFERT, $paymentReceived);
        Configuration::updateGlobalValue(self::WEBHOOK_CONTACTED, false);

        $bridgeInitialiser = new BridgeInitialiser();
        $bridgeInitialiser->addIndexes();

        return $result;
    }

    public function uninstall()
    {
        return Module::uninstall();
    }

    public function addRadioCurrencyRestrictionsForModule(array $shops = [])
    {
        if (!$shops) {
            $shops = Shop::getShops(true, null, true);
        }

        $query = 'INSERT INTO `' . _DB_PREFIX_ . 'module_currency` (`id_module`, `id_shop`, `id_currency`) VALUES (%d, %d, %d)';

        $currencies = array_map(function ($currencyIso) {
            return (int) Currency::getIdByIsoCode($currencyIso);
        }, self::AVAILABLE_CURRENCIES);

        $currencies = array_filter($currencies, function ($idCurrency) {
            return $idCurrency > 0;
        });

        foreach ($shops as $idShop) {
            if (!Db::getInstance()->execute(sprintf($query, $this->id, $idShop, $currencies))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Handle Hooks loaded on extension
     *
     * @param string $name Hook name
     * @param array $arguments Hook arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if ($result = $this->handleExtensionsHook($name,
            !empty($arguments[0]) ? $arguments[0] : [])
        ) {
            if (!is_null($result)) {
                return $result;
            }
        }
    }
}
