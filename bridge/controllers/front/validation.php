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

use BridgeAddon\API\Object\Front\CreatePaymentFrontResponse;
use BridgeAddon\Service\PaymentService;
use BridgeAddon\Utils\ServiceContainer;

class BridgeValidationModuleFrontController extends ModuleFrontController
{
    protected function getPaymentId($idBank, $idCart)
    {
        /** @var PaymentService $paymentService */
        $paymentService = ServiceContainer::getInstance()->get(PaymentService::class);

        if ($paymentService->bridgeTransactionPending($idCart, $paymentService) === true) {
            $this->ajaxDie(json_encode([
                'success' => true,
                'message' => $this->getUrlSuccess($idCart),
            ]));
        }
        /** @var CreatePaymentFrontResponse $response */
        $response = $paymentService->createPayment($idBank, $idCart);

        if ($response->isSuccess() === true) {
            return [
                'success' => true,
                'message' => $response->getUrl(),
            ];
        } else {
            $errorMsg = $this->l('Error while retrieving the payment link. Please try again.');

            $this->ajaxDie(json_encode([
                'success' => false,
                'message' => $errorMsg,
            ]));
        }
    }

    private function getUrlSuccess($idCart)
    {
        $paramsCallBack = [
            'id_cart' => $idCart,
        ];

        $isProduction = (bool) \Configuration::get(\Bridge::PRODUCTION_MODE);
        if ($isProduction === false) {
            $paramsCallBack['sandbox'] = 1;
        }

        return Context::getContext()->link->getModuleLink(
                'bridge',
                'success',
                $paramsCallBack
        );
    }

    public function displayAjaxGetPaymentId()
    {
        $idBank = Tools::getValue('idBank');
        if ($idBank === false || $idBank <= 0) {
            $this->ajaxDie(json_encode([
                'success' => false,
                'message' => $this->l('Error, bank selected is invalid. Please verify the selected bank'),
            ]));
        }

        $idCart = -1;
        try {
            $idCart = Context::getContext()->cart->id;
        } catch (Exception $ex) {
        }
        if ($idBank === false || $idBank <= 0) {
            $this->ajaxDie(json_encode([
                'success' => false,
                'message' => $this->l('Error with the cart. Please refresh your page.'),
            ]));
        }

        try {
            $url = $this->getPaymentId($idBank, $idCart);
        } catch (Exception $ex) {
            $url = [
                'success' => false,
                'message' => $this->l('Error while retrieving the payment link. Please try again.'),
            ];
        }

        $this->ajaxDie(json_encode($url));
    }
}
