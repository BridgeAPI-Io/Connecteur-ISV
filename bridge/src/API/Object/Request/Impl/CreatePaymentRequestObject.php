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

namespace BridgeAddon\API\Object\Request\Impl;

use BridgeAddon\API\Object\Request\RequestObjectInterface;
use Cart;
use Configuration;
use Context;
use Currency;
use Customer;
use Tools;

class CreatePaymentRequestObject implements RequestObjectInterface
{
    /**
     * @var string
     */
    protected $currency;

    /**
     * @var int
     */
    protected $clientId;

    /**
     * @var int
     */
    protected $idCart;

    /**
     * @var
     */
    protected $bankId;

    public function getParams()
    {
        if ($this->getIdCart() === -1) {
            $paramsTest = $this->testSandboxPayment();

            return $paramsTest;
        }

        $cart = new Cart($this->getIdCart());
        $currency = new Currency($cart->id_currency);
        $customer = new Customer($cart->id_customer);

        $isProduction = (bool) \Configuration::get(\Bridge::PRODUCTION_MODE);
        $paramsCallBack = [
            'id_cart' => $cart->id,
        ];
        if ($isProduction === false) {
            $paramsCallBack['sandbox'] = 1;
        }

        return [
            'successful_callback_url' => Context::getContext()->link->getModuleLink(
                'bridge',
                'success',
                $paramsCallBack
            ),
            'unsuccessful_callback_url' => Context::getContext()->link->getModuleLink(
                'bridge',
                'error',
                $paramsCallBack
            ),
            'transactions' => [
                [
                    'currency' => $currency->iso_code,
                    'label' => substr(Configuration::get('PS_SHOP_NAME'), 0, 40),
                    'amount' => $cart->getOrderTotal(),
                    'client_reference' => strval($customer->id),
                    'end_to_end_id' => strval($cart->id),
                ],
            ],
            'user' => [
                'name' => $customer->firstname . ' ' . $customer->lastname,
                'ip_address' => Tools::getRemoteAddr(),
            ],
            'bank_id' => $this->getBankId(),
        ];
    }

    public function testSandboxPayment()
    {
        $context = Context::getContext();

        return [
            'successful_callback_url' => $context->link->getAdminLink('AdminBridgeConfiguration'),
            'unsuccessful_callback_url' => $context->link->getAdminLink('AdminBridgeConfiguration') . '?err',
            'transactions' => [
                [
                    'currency' => 'EUR',
                    'label' => 'Test sandbox' . substr(Configuration::get('PS_SHOP_NAME'), 0, 25),
                    'amount' => 10.00,
                    'client_reference' => 'testsandbox' . strval($this->getBankId()),
                    'end_to_end_id' => 'testsandbox' . strval($this->getBankId()) . date('YmdHis'),
                ],
            ],
            'user' => [
                'name' => 'Test sandbox' . substr(Configuration::get('PS_SHOP_NAME'), 0, 25),
                'ip_address' => Tools::getRemoteAddr(),
            ],
            'bank_id' => $this->getBankId(),
        ];
    }

    public function getQueryParams()
    {
        return null;
    }

    public function getRequestIdentifier()
    {
        return $this->getIdCart();
    }

    /**
     * @return int
     */
    public function getIdCart()
    {
        return $this->idCart;
    }

    /**
     * @param int $idCart
     *
     * @return CreatePaymentRequestObject
     */
    public function setIdCart($idCart)
    {
        $this->idCart = $idCart;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBankId()
    {
        return $this->bankId;
    }

    /**
     * @param mixed $bankId
     *
     * @return CreatePaymentRequestObject
     */
    public function setBankId($bankId)
    {
        $this->bankId = $bankId;

        return $this;
    }
}
