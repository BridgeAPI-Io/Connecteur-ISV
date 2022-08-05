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

use BridgeAddon\Service\ConfigService;
use BridgeAddon\Utils\ServiceContainer;

class AdminBridgeConfigurationController extends ModuleAdminController
{
    /** @var \Module Instance of your module automatically set by ModuleAdminController */
    public $module;

    /** @var string Associated object class name */
    public $className = 'Configuration';

    /** @var string Associated table name */
    public $table = 'configuration';

    /** @var string Associated table name */
    public $bootstrap = false;

    /** @var string */
    public $clientID;

    /** @var string */
    public $clientSecret;

    /** @var string */
    public $clientIDProduction;

    /** @var string */
    public $clientSecretProduction;

    /** @var string */
    public $productionMode;

    /**
     * @see AdminController::initPageHeaderToolbar()
     */
    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        // Remove the help icon of the toolbar which no useful for us
        $this->context->smarty->clearAssign('help_link');
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS(_PS_MODULE_DIR_ . 'bridge/views/js/admin.js');
        $this->addCSS(_PS_MODULE_DIR_ . 'bridge/views/css/admin.css');
    }

    public function initContent()
    {
        $langParameter = Tools::getValue('lang', null);
        $prevLanguage = $this->context->language;
        if ($langParameter !== null) {
            try {
                $choosedLanguage = new Language(Language::getIdByIso($langParameter));
                $this->context->language = $choosedLanguage;
            } catch (Exception $ex) {
            }
        }

        $cookieBridgeSave = Context::getContext()->cookie->__get('bridgesave');
        if ($cookieBridgeSave == 'ok') {
            $this->confirmations[] = $this->module->l('Successful update.', 'AdminBridgeConfiguration');
        }
        Context::getContext()->cookie->__unset('bridgesave', '');

        $this->content .= $this->renderConfiguration();
        parent::initContent();
        $this->context->language = $prevLanguage;
    }

    public function initVarContent()
    {
        $idShop = $this->context->shop->id;
        $this->clientID = Configuration::get(
            Bridge::CLIENT_ID,
            null,
            null,
            $idShop,
            Tools::getValue('client_id', '')
        );
        $this->clientSecret = Configuration::get(
            Bridge::CLIENT_SECRET,
            null,
            null,
            $idShop,
            Tools::getValue('client_secret', '')
        );
        $this->clientIDProduction = Configuration::get(
            Bridge::CLIENT_ID_PRODUCTION,
            null,
            null,
            $idShop,
            Tools::getValue('client_id_production', '')
        );
        $this->clientSecretProduction = Configuration::get(
            Bridge::CLIENT_SECRET_PRODUCTION,
            null,
            null,
            $idShop,
            Tools::getValue('client_secret_production', '')
        );
        $this->productionMode = (bool) Configuration::get(
            Bridge::PRODUCTION_MODE,
            null,
            null,
            $idShop,
            Tools::getValue('production_mode', false)
        );
    }

    protected function renderConfiguration()
    {
        $this->initVarContent();
        $tplFile = _PS_MODULE_DIR_ . 'bridge/views/templates/admin/configuration/layout-configuration.tpl';

        if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_ALL) {
            $tplFile = _PS_MODULE_DIR_ . 'bridge/views/templates/admin/configuration/multishop-and-not-selected.tpl';
            $tplVars = [
                'bridge_imgfile' => $this->getImgNotSelected(),
            ];
        } else {
            $urlWebhook = Context::getContext()->link->getModuleLink(
                $this->module->name,
                'webhook',
                [
                    'secure_key' => $this->module->secure_key,
                ]
            );
            $tplVars = [
                'configuration' => $this->getConfigurationVariables(),
                'config_check' => $this->checkSpecifications(),
                'webhook_url_text' => $urlWebhook,
                'webhook_url' => $urlWebhook,
            ];

            Media::addJsDef([
                'bridge' => [
                    'translations' => [
                        'copy_link_webhook' => $this->module->l(
                            'WebHook URL sent to clipboard',
                            'AdminBridgeConfiguration'
                        ),
                    ],
                ],
            ]);

            $alertHere = empty($this->confirmations) && empty($this->errors);

            $tplVars['configuration']['alert'] = $alertHere !== true;
        }

        $tpl = Context::getContext()->smarty->createTemplate($tplFile);
        $tpl->assign($tplVars);

        return $tpl->fetch();
    }

    protected function getImgNotSelected()
    {
        $isoCode = strtolower($this->context->getContext()->language->iso_code);
        $fileImg = '/multishop-not-selected-' . $isoCode . '.jpg';
        if (is_file(_PS_MODULE_DIR_ . $this->module->name . '/' . $fileImg) === false) {
            $isoCode = 'en'; // TODO
        }

        return '/modules/' . $this->module->name . '/views/img/' . $fileImg;
    }

    protected function checkSpecifications()
    {
        /** @var ConfigService $configService */
        $configService = ServiceContainer::getInstance()->get(ConfigService::class);

        $curlInfos = curl_version();
        $versionOpenSSL = null !== OPENSSL_VERSION_NUMBER ? OPENSSL_VERSION_NUMBER : -1;
        $versionSSLCURL = $curlInfos !== false ? $curlInfos['version'] . ' ' . $curlInfos['ssl_version'] : '';

        $sslActivated = $configService->isSslActive();
        $tlsCallCurl = $configService->isTlsActive();
        $infoSSLTLS = $versionOpenSSL !== -1 && $sslActivated === true
        ? $this->module->l('SSL enabled', 'AdminBridgeConfiguration')
        : $this->module->l('SSL not enabled on all the shop', 'AdminBridgeConfiguration');
        $infoSSLTLS .= $tlsCallCurl['error_message'] !== '' ? ' - ' . $tlsCallCurl['error_message'] : '';

        $banksList = $configService->isListingBanks($this->clientID, $this->clientSecret);

        $idShop = $this->context->shop->id;
        $webHookContacted = (bool) Configuration::get(Bridge::WEBHOOK_CONTACTED, null, null, $idShop, false);
        $webHookInfo = $webHookContacted === true ?
            $this->module->l('Ok', 'AdminBridgeConfiguration')
            : $this->module->l('No', 'AdminBridgeConfiguration');

        if ($banksList['success'] !== true) {
            $this->errors[] = $banksList['message'];
        }

        return [
            [
                'name' => 'CURL',
                'info' => $versionSSLCURL !== '' ?
                    'version v.' . $versionSSLCURL
                    : $this->module->l('not installed', 'AdminBridgeConfiguration'),
                'ok' => $curlInfos !== false,
            ],
            [
                'name' => 'SSL & TLS v1.2',
                'info' => $infoSSLTLS,
                'ok' => $versionOpenSSL !== -1 && $sslActivated === true && $tlsCallCurl['status'],
            ],
            [
                'name' => $this->module->l('Connected to Bridge API', 'AdminBridgeConfiguration'),
                'info' => $banksList['message'],
                'ok' => $banksList['success'],
            ],
            [
                'name' => $this->module->l('Webhook triggered', 'AdminBridgeConfiguration'),
                'info' => $webHookInfo,
                'ok' => $webHookContacted,
            ],
            [
                'name' => $this->module->l('Production environment', 'AdminBridgeConfiguration'),
                'info' => '',
                'ok' => (bool) $this->productionMode,
            ],
        ];
    }

    protected function getOrderStates()
    {
        $orderStates = OrderState::getOrderStates($this->context->language->id);

        return array_map(function ($state) {
            return [
                'name' => $state['name'],
                'id' => $state['id_order_state'],
            ];
        }, $orderStates);
    }

    public function postProcess()
    {
        $idShop = $this->context->shop->id;
        $isSubmitted = false;

        if (Tools::isSubmit('account_submit')) {
            $this->postAccountSubmit($idShop);
            $isSubmitted = true;
        } elseif (Tools::isSubmit('states_submit')) {
            $this->postStateSubmit($idShop);
            $isSubmitted = true;
        }

        if ($isSubmitted) {
            if (empty($this->_errors) === true) {
                Context::getContext()->cookie->__set('bridgesave', 'ok');
            } else {
                Context::getContext()->cookie->__set('bridgesave', 'error');
            }
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminBridgeConfiguration'));
        }
    }

    protected function postAccountSubmit($idShop)
    {
        $clientID = Tools::getValue('client_id');
        $clientSecret = Tools::getValue('client_secret');
        $clientIDProd = Tools::getValue('client_id_production');
        $clientSecretProd = Tools::getValue('client_secret_production');
        $isProduction = Tools::getValue('production_mode');
        $isKeyChanged = $this->isKeysChanged(
            $isProduction,
            $idShop,
            $clientIDProd,
            $clientSecretProd,
            $clientID,
            $clientSecret
        );
        if ($isKeyChanged === true) {
            Configuration::updateValue(Bridge::WEBHOOK_CONTACTED, false, false, null, $idShop);
        }
        Configuration::updateValue(Bridge::CLIENT_ID, $clientID, false, null, $idShop);
        Configuration::updateValue(Bridge::CLIENT_SECRET, $clientSecret, false, null, $idShop);
        Configuration::updateValue(Bridge::CLIENT_ID_PRODUCTION, $clientIDProd, false, null, $idShop);
        Configuration::updateValue(Bridge::CLIENT_SECRET_PRODUCTION, $clientSecretProd, false, null, $idShop);
        Configuration::updateValue(Bridge::PRODUCTION_MODE, $isProduction, false, null, $idShop);
    }

    protected function postStateSubmit($idShop)
    {
        $receivedState = Tools::getValue('received_state');
        $pendingState = Tools::getValue('pending_state');
        Configuration::updateValue(Bridge::STATUS_PENDING_TRANSFERT, $pendingState, false, null, $idShop);
        Configuration::updateValue(Bridge::STATUS_RECEIVED_TRANSFERT, $receivedState, false, null, $idShop);
    }

    protected function isKeysChanged($isProduction, $idShop, $clientIDProd, $clientSecretProd, $clientID, $clientSecret)
    {
        $isKeyChanged = false;
        if ((bool) $isProduction === true) {
            $clientIdBefore = Configuration::get(Bridge::CLIENT_ID_PRODUCTION, null, null, $idShop, '');
            $clientSecretBefore = Configuration::get(Bridge::CLIENT_SECRET_PRODUCTION, null, null, $idShop, '');
            $isKeyChanged = ($clientIdBefore !== $clientIDProd) || ($clientSecretBefore !== $clientSecretProd);
        } else {
            $clientIdBefore = Configuration::get(Bridge::CLIENT_ID, null, null, $idShop, '');
            $clientSecretBefore = Configuration::get(Bridge::CLIENT_SECRET, null, null, $idShop, '');
            $isKeyChanged = ($clientIdBefore !== $clientID) || ($clientSecretBefore !== $clientSecret);
        }

        return $isKeyChanged;
    }

    protected function getConfigurationVariables()
    {
        $idShop = $this->context->shop->id;

        $pendingName = null !== _PS_OS_BANKWIRE_ ? _PS_OS_BANKWIRE_ : 'PS_OS_BANKWIRE';
        $defaultPending = Configuration::getGlobalValue($pendingName);

        $receivedName = null !== _PS_OS_WS_PAYMENT_ ? _PS_OS_WS_PAYMENT_ : 'PS_OS_WS_PAYMENT';
        $defaultReceived = Configuration::getGlobalValue($receivedName);

        $pendingTransfer = Configuration::get(
            Bridge::STATUS_PENDING_TRANSFERT,
            null,
            null,
            $idShop,
            $defaultPending
        );

        $receivedTransfer = Configuration::get(
            Bridge::STATUS_RECEIVED_TRANSFERT,
            null,
            null,
            $idShop,
            $defaultReceived
        );

        return [
            'url_form_config' => $this->context->link->getAdminLink('AdminBridgeConfiguration'),
            'production_mode' => $this->productionMode,
            'client_id' => $this->clientID,
            'client_secret' => $this->clientSecret,
            'client_id_production' => $this->clientIDProduction,
            'client_secret_production' => $this->clientSecretProduction,
            'order_states' => $this->getOrderStates(),
            'pending_state' => Tools::getValue('pending_state', $pendingTransfer),
            'received_state' => Tools::getValue('received_state', $receivedTransfer),
        ];
    }
}
