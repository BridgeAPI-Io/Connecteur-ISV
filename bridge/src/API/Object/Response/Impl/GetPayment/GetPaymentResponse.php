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

class GetPaymentResponse extends AbstractResponseObject
{
    protected $id;

    protected $status;

    protected $user;

    protected $transactions = [];

    protected $bankId;

    protected $createdAt;

    protected $updatedAt;

    public function fillResponse($response)
    {
        $this->id = !empty($response['id']) ? $response['id'] : null; // TODO
        $this->status = !empty($response['status']) ? $response['status'] : null; // TODO
        if (!empty($response['user'])) {
            $this->user = (new UserResponse())->fillResponse($response['user']);
        }
        $this->createdAt = !empty($response['created_at']) ? $response['created_at'] : null; //TODO format
        $this->updatedAt = !empty($response['updated_at']) ? $response['updated_at'] : null; //TODO format
        $this->bankId = !empty($response['bank_id']) ? $response['bank_id'] : null; //TODO format

        if (!empty($response['transactions'])) {
            foreach ($response['transactions'] as $transaction) {
                $transactionObj = (new TransactionResponse())->fillResponse($transaction);
                if (empty($transactionObj)) {
                    continue;
                }
                $this->transactions[] = $transactionObj;
            }
        }

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
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return array
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * @return mixed
     */
    public function getBankId()
    {
        return $this->bankId;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
