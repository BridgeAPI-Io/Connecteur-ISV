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

namespace BridgeAddon\API\Object\Response\Impl\CreatePayment;

use BridgeAddon\API\Object\Response\AbstractResponseObject;

class CreatePaymentErrorResponse extends AbstractResponseObject
{
    protected $code;

    protected $message;

    public function fillResponse($response)
    {
        $this->code = !empty($response['errors'][0]['code']) ? $response['errors'][0]['code'] : ''; // TODO
        $this->message = !empty($response['errors'][0]['message']) ? $response['errors'][0]['message'] : ''; // TODO

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }
}
