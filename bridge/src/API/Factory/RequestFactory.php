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

namespace BridgeAddon\API\Factory;

use BridgeAddon\API\Object\Request\Impl\CreatePaymentRequestObject;
use BridgeAddon\API\Object\Request\Impl\GetBankRequestObject;
use BridgeAddon\API\Object\Request\Impl\GetPaymentRequestObject;
use BridgeAddon\API\Object\Request\Impl\ListBanksRequestObject;
use BridgeAddon\API\Object\Request\RequestObjectInterface;
use BridgeAddon\API\Request\CreatePaymentRequest;
use BridgeAddon\API\Request\GetBankRequest;
use BridgeAddon\API\Request\GetPaymentRequest;
use BridgeAddon\API\Request\ListBanksRequest;
use PrestaShopException;

class RequestFactory
{
    public function createRequestFromObject(RequestObjectInterface $requestObject)
    {
        switch (true) {
            case $requestObject instanceof ListBanksRequestObject:
                return new ListBanksRequest($requestObject);

            case $requestObject instanceof GetBankRequestObject:
                return new GetBankRequest($requestObject);

            case $requestObject instanceof CreatePaymentRequestObject:
                return new CreatePaymentRequest($requestObject);

            case $requestObject instanceof GetPaymentRequestObject:
                return new GetPaymentRequest($requestObject);

            default:
                throw new PrestaShopException('Could not find request by object');
        }
    }
}
