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

namespace BridgeAddon\API\Object\Front;

class WebHookResponse
{
    /** @var string */
    protected $bodyContent;

    /** @var string */
    protected $typeWebHook;

    /** @var string */
    protected $transactionId;

    /** @var string */
    protected $statuspayment;

    public function __construct()
    {
        $bodyContent = file_get_contents('php://input');
        $contentWebHook = json_decode($bodyContent, true);

        $this->typeWebHook = empty($contentWebHook['type']) === false ? $contentWebHook['type'] : '';
        if (
            empty($contentWebHook['content']) === true
            || empty($contentWebHook['content']['payment_request_id']) === true
            || empty($contentWebHook['content']['status']) === true
        ) {
            $this->statuspayment = '';
            $this->transactionId = '';

            return $this;
        }

        $this->statuspayment = $contentWebHook['content']['status'];
        $this->transactionId = $contentWebHook['content']['payment_request_id'];

        return $this;
    }

    /**
     * @return string
     */
    public function getBodyContent()
    {
        return $this->bodyContent;
    }

    /**
     * @return string
     */
    public function getTypeWebHook()
    {
        return $this->typeWebHook;
    }

    /**
     * @return string
     */
    public function getStatuspayment()
    {
        return $this->statuspayment;
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }
}
