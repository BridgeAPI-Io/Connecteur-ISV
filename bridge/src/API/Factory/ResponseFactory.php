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

use BridgeAddon\API\Object\Response\Impl\Banks\BankResponse;
use BridgeAddon\API\Object\Response\Impl\Banks\ListBanksResponse;
use BridgeAddon\API\Object\Response\Impl\CreatePayment\CreatePaymentErrorResponse;
use BridgeAddon\API\Object\Response\Impl\CreatePayment\CreatePaymentResponse;
use BridgeAddon\API\Object\Response\Impl\GetPayment\GetPaymentResponse;
use BridgeAddon\API\Object\Response\Impl\UnauthorizedErrorResponse;
use BridgeAddon\API\Request\AbstractRequest;
use BridgeAddon\API\Request\CreatePaymentRequest;
use BridgeAddon\API\Request\GetBankRequest;
use BridgeAddon\API\Request\GetPaymentRequest;
use BridgeAddon\API\Request\ListBanksRequest;

class ResponseFactory
{
    public function getResponseFromRequest(AbstractRequest $request, $statusCode = 200, $responseData = [])
    {
        switch (true) {
            case $request instanceof ListBanksRequest:
                if ($statusCode != 200) {
                    return new UnauthorizedErrorResponse();
                }

                return new ListBanksResponse();

            case $request instanceof GetPaymentRequest:
                if ($statusCode != 200) {
                    return new UnauthorizedErrorResponse();
                }

                return new GetPaymentResponse();

            case $request instanceof CreatePaymentRequest:
                if ($statusCode !== 200) {
                    if (isset($responseData['errors'])) {
                        return new CreatePaymentErrorResponse();
                    } else {
                        return new UnauthorizedErrorResponse();
                    }
                }

                return new CreatePaymentResponse();

            case $request instanceof GetBankRequest:
                if ($statusCode != 200) {
                    return new UnauthorizedErrorResponse();
                }

                return new BankResponse();

            default:
                return null;
        }
    }
}
