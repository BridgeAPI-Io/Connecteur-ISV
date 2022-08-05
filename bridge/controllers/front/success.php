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

use BridgeAddon\Model\Constant\PaymentStatuses;
use BridgeAddon\Service\PaymentService;
 use BridgeAddon\Utils\ServiceContainer;

class BridgeSuccessModuleFrontController extends ModuleFrontController
{
    /** @var \PaymentModule */
    public $module;

    /** Prevent init content from Front Controller (case cart created by webhook) */
    public function init()
    {
    }

    public function initContent()
    {
        $orderUrl = Context::getContext()->link->getPageLink(
            'order',
            null,
            null,
            [
                'step' => 1,
            ]
        );
        $idCartBridge = Tools::getValue('id_cart');

        $cart = new Cart($idCartBridge);

        if (
            Validate::isLoadedObject($cart) === false || $this->module->active == 0 || $cart->id_address_delivery == 0
            || $cart->id_address_invoice == 0 || $cart->id_customer == 0
        ) {
            $this->setErrorTemplate($this->module->l('Error with the cart. Please refresh your page.'), $idCartBridge);
            $this->redirectWithNotifications($orderUrl);
        }

        /** @var PaymentService $paymentService */
        $paymentService = ServiceContainer::getInstance()->get(PaymentService::class);
        $this->isTransactionPending($paymentService, (int) $idCartBridge, $orderUrl);

        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            $this->setErrorTemplate(
                $this->module->l('Error with the customer. Please verify your order.'),
                $idCartBridge
            );
            $this->redirectWithNotifications($orderUrl);
        }

        $status = $paymentService->getPaymentInfo($cart);
        $errorPayment = $status === PaymentStatuses::ERROR_NOT_FOUND;

        if ($errorPayment === true || in_array($status, PaymentStatuses::SUCCESS_PAYMENTS) === false) {
            $this->setErrorTemplate(
                $this->module->l('Error with the payment status, please try again.'),
                $idCartBridge
            );
            $this->redirectWithNotifications($orderUrl);
        }

        if ($cart->orderExists() === true) {
            $this->redirectToOrder($cart, $customer);
        }

        parent::init();
        parent::initContent();

        $paymentService->validateOrder($cart, $customer, $status);

        $idOrder = $this->module->currentOrder;

        $paymentService->saveTransaction($cart, $idOrder, $status);

        $this->redirectToOrder($cart, $customer);
    }

    private function isTransactionPending(PaymentService $paymentService, $idCart, $orderUrl)
    {
        $bridgeTransaction = $paymentService->getTransaction($idCart);
        if ($bridgeTransaction->status === PaymentStatuses::PROCESS_IN_PROGRESS) {
            $this->setErrorTemplate($this->module->l('Order validation in progress, please try again in a few moment.'),
                $idCart
            );
            $this->redirectWithNotifications($orderUrl);
        } elseif (Validate::isLoadedObject($bridgeTransaction) && empty($bridgeTransaction->status) === true) {
            $bridgeTransaction->status = PaymentStatuses::PROCESS_IN_PROGRESS;
            $bridgeTransaction->save();
        }
    }

    protected function setErrorTemplate($errorMsg, $idCart)
    {
        $paramsCallBack = [
            'id_cart' => $idCart,
        ];

        $isProduction = (bool) \Configuration::get(\Bridge::PRODUCTION_MODE);
        if ($isProduction === false) {
            $paramsCallBack['sandbox'] = 1;
        }

        $urlRetrySuccess = Context::getContext()->link->getModuleLink(
                'bridge',
                'success',
                $paramsCallBack
        );

        $template = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/front/payment_error.tpl';

        Context::getContext()->smarty->assign([
            'error_with_payment' => $errorMsg,
            'url_retry' => $urlRetrySuccess,
        ]);

        $this->errors[] = Context::getContext()->smarty->fetch($template);
    }

    protected function redirectToOrder($cart, $customer)
    {
        $linkOrder = $this->context->link->getPageLink(
            'order-confirmation',
            null,
            null,
            [
                'id_cart' => $cart->id,
                'id_module' => $this->module->id,
                'id_order' => $this->module->currentOrder,
                'key' => $customer->secure_key,
            ]
        );

        $this->redirectWithNotifications($linkOrder);
    }
}
