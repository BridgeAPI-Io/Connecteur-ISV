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

namespace BridgeAddon\API\Object\Response\Impl\GetPayment;

use BridgeAddon\API\Object\Response\AbstractResponseObject;

class TransactionResponse extends AbstractResponseObject
{
    protected $id;

    protected $amount;

    protected $status;

    protected $currency;

    protected $label;

    protected $clientReference;

    protected $endToEndId;

    public function fillResponse($response)
    {
        $this->id = !empty($response['id']) ? $response['id'] : null; // TODO
        $this->amount = !empty($response['amount']) ? (float) $response['amount'] : 0;
        $this->status = !empty($response['status']) ? $response['status'] : null; // TODO
        $this->currency = !empty($response['currency']) ? $response['currency'] : null; // TODO
        $this->label = !empty($response['label']) ? $response['label'] : '';
        $this->clientReference = !empty($response['client_reference']) ? $response['client_reference'] : null; // TODO
        $this->endToEndId = !empty($response['end_to_end_id']) ? $response['end_to_end_id'] : null; // TODO

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return mixed
     */
    public function getClientReference()
    {
        return $this->clientReference;
    }

    /**
     * @return mixed
     */
    public function getEndToEndId()
    {
        return $this->endToEndId;
    }
}
