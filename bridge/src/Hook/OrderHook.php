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

namespace BridgeAddon\Hook;

use BridgeAddon\Entity\BridgeTransaction;
use BridgeAddon\Service\PaymentService;
use BridgeAddon\Utils\ServiceContainer;
use BridgeClasslib\Hook\AbstractHook;

class OrderHook extends AbstractHook
{
    const AVAILABLE_HOOKS = [
        'displayOrderConfirmation',
        'actionEmailSendBefore',
    ];

    public $context;

    /**
     * Assign correct template for payment mails
     * If order is paid by bridge (transaction done on this cart)
     */
    public function actionEmailSendBefore($params)
    {
        if ($params['template'] !== 'payment') {
            return $params;
        }

        $idOrder = (int) $params['templateVars']['{id_order}'];

        if ($idOrder <= 0) {
            return $params;
        }

        /** @var \Order $order */
        $order = new \Order($idOrder);

        if (\Validate::isLoadedObject($order) === false) {
            return $params;
        }

        if ($order->module !== $this->module->name) {
            return $params;
        }

        /** @var PaymentService $paymentService */
        $paymentService = ServiceContainer::getInstance()->get(PaymentService::class);

        /** @var BridgeTransaction $bridgeTransaction */
        $bridgeTransaction = $paymentService->getTransaction($order->id_cart);

        if (\Validate::isLoadedObject($bridgeTransaction) === false) {
            return $params;
        }

        $params['template'] = $this->module->name;
        $params['templatePath'] = $this->pathTemplateEmail();

        return $params;
    }

    /**
     * @return string $templatePath : path of template for bridge mail (default module or overrided)
     */
    protected function pathTemplateEmail()
    {
        $this->context = \Context::getContext();

        $templateName = $this->context->language->iso_code . DIRECTORY_SEPARATOR . 'bridge.html';
        $templateNameEn = 'en' . DIRECTORY_SEPARATOR . 'bridge.html';

        $defaultModuleDir = _PS_MODULE_DIR_ . $this->module->name . DIRECTORY_SEPARATOR . 'mails';

        $pathToFindEmail = [
            _PS_THEME_DIR_ . 'mails' . DIRECTORY_SEPARATOR . $templateName => _PS_THEME_DIR_ . 'mails',
            $defaultModuleDir . DIRECTORY_SEPARATOR . $templateName => $defaultModuleDir,
            _PS_THEME_DIR_ . 'mails' . DIRECTORY_SEPARATOR . $templateNameEn => _PS_THEME_DIR_ . 'mails',
        ];

        foreach ($pathToFindEmail as $file => $path) {
            if (\Tools::file_exists_cache($file)) {
                return $path;
            }
        }

        return $defaultModuleDir;
    }
}
