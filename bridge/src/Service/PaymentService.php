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

namespace BridgeAddon\Service;

use BridgeAddon\API\Client\Client;
use BridgeAddon\API\Factory\RequestFactory;
use BridgeAddon\API\Object\Front\CreatePaymentFrontResponse;
use BridgeAddon\API\Object\Request\Impl\CreatePaymentRequestObject;
use BridgeAddon\API\Object\Request\Impl\GetPaymentRequestObject;
use BridgeAddon\API\Object\Response\Impl\CreatePayment\CreatePaymentErrorResponse;
use BridgeAddon\API\Object\Response\Impl\CreatePayment\CreatePaymentResponse;
use BridgeAddon\API\Object\Response\Impl\GetPayment\GetPaymentResponse;
use BridgeAddon\Entity\BridgeTransaction;
use BridgeAddon\Model\Constant\PaymentStatuses;
use BridgeAddon\Repository\PaymentRepository;
use BridgeClasslib\Extensions\ProcessLogger\ProcessLoggerHandler;
use Cart;
use Configuration;
use PaymentModule;

class PaymentService
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * @var PaymentRepository
     */
    protected $paymentRepository;

    /**
     * @var \PaymentModule
     */
    public $module;

    /**
     * @var ProcessLoggerHandler
     */
    public $logger;

    /**
     * @var string
     */
    protected $idTransaction;

    /**
     * @param Client $client
     * @param RequestFactory $requestFactory
     * @param PaymentRepository $paymentRepository
     */
    public function __construct(
        Client $client,
        RequestFactory $requestFactory,
        PaymentRepository $paymentRepository,
        ProcessLoggerHandler $logger,
        PaymentModule $module
    ) {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
        $this->paymentRepository = $paymentRepository;
        $this->logger = $logger;
        $this->module = $module;
    }

    /**
     * @param int $idBank - id of bank used for payment
     * @param int $idCart - id of cart payed
     *
     * @return string|bool - false if error while getting URL
     */
    public function createPayment($idBank, $idCart)
    {
        $requestObject = new CreatePaymentRequestObject();
        $requestObject->setBankId((int) $idBank);
        $requestObject->setIdCart((int) $idCart);

        $request = $this->requestFactory->createRequestFromObject($requestObject);

        $response = $this->client->call($request, true);

        $frontResponse = new CreatePaymentFrontResponse();
        if ($response instanceof CreatePaymentResponse) {
            $bridgeTransaction = new BridgeTransaction();
            $bridgeTransaction->id_cart = $idCart;
            $bridgeTransaction->id_bank = $idBank;
            $bridgeTransaction->id_transaction = $response->getId();
            $bridgeTransaction->url = $response->getUrl();
            if ($bridgeTransaction->save() === true) {
                return $frontResponse->fillResponse([
                    'success' => true,
                    'url' => $response->getUrl(),
                ]);
            } else {
                $errorMsg = \Db::getInstance()->getMsgError();
                $this->logger->openLogger();
                $this->logger->logError(
                    $errorMsg,
                    (new \ReflectionClass($this))->getShortName(),
                    null,
                    'Payment save transaction error'
                );
                $this->logger->closeLogger();

                return $frontResponse->fillResponse([
                    'success' => false,
                    'url' => '',
                ]);
            }
        }

        if ($response instanceof CreatePaymentErrorResponse) {
            $errorMsg = $response->getMessage();

            $this->logger->openLogger();
            $this->logger->logError(
                $errorMsg,
                (new \ReflectionClass($this))->getShortName(),
                null,
                'Payment creation error'
            );
            $this->logger->closeLogger();
        }

        return $frontResponse->fillResponse([
            'success' => false,
            'url' => '',
        ]);
    }

    /**
     * @return bool Return if transaction is pending for current cart
     *              If transaction's status is in array of PaymentStatuses::SUCCESS_PAYMENTS then transaction is pending
     */
    public function bridgeTransactionPending($idCart, PaymentService $paymentService)
    {
        $bridgeTransaction = $paymentService->getTransaction($idCart);

        $time = $this->getMinutesUntilTransaction($bridgeTransaction->date_upd);

        return $time < 15 && in_array($bridgeTransaction->status, PaymentStatuses::SUCCESS_PAYMENTS);
    }

    /**
     * @return int minutes until last update of transaction
     */
    private function getMinutesUntilTransaction($dateFrom)
    {
        $dateNow = date_create('now');
        $dateFrom = date_create($dateFrom);

        $diff = date_diff($dateFrom, $dateNow);

        return ($diff->days * 24 * 60) + ($diff->h * 60) + ($diff->i);
    }

    /**
     * @param Cart $cart
     */
    public function getPaymentInfo($cart)
    {
        /** @var BridgeTransaction $bridgeTransaction */
        $bridgeTransaction = $this->getTransaction($cart->id);
        $this->idTransaction = $bridgeTransaction->id_transaction;

        $requestObject = new GetPaymentRequestObject();
        $requestObject->setId($this->idTransaction);

        $request = $this->requestFactory->createRequestFromObject($requestObject);

        $response = $this->client->call($request, true);

        if ($response instanceof GetPaymentResponse) {
            $bridgeTransaction->status = $response->getStatus();
            $bridgeTransaction->save();

            return $bridgeTransaction->status;
        }

        return PaymentStatuses::ERROR_NOT_FOUND;
    }

    /**
     * @param Cart $cart
     */
    public function saveTransaction($cart, $idOrder, $status)
    {
        /** @var BridgeTransaction $bridgeTransaction */
        $bridgeTransaction = $this->getTransaction($cart->id);
        $this->idTransaction = $bridgeTransaction->id_transaction;

        $bridgeTransaction->id_order = $idOrder;
        $bridgeTransaction->status = $status;
        $bridgeTransaction->save();
    }

    /**
     * @param Cart $cart
     * @param \Customer $customer
     */
    public function validateOrder($cart, $customer, $status)
    {
        $context = \Context::getContext();
        $currency = $context->currency;
        $total = (float) $cart->getOrderTotal(true, Cart::BOTH);
        $this->setTransactionId($cart);

        $extra_vars = [
            'transaction_id' => $this->idTransaction,
        ];

        $pendingName = null !== _PS_OS_BANKWIRE_ ? _PS_OS_BANKWIRE_ : 'PS_OS_BANKWIRE';
        $defaultPending = Configuration::getGlobalValue($pendingName);

        $idStatePayment = Configuration::get(
            \Bridge::STATUS_PENDING_TRANSFERT,
            null,
            null,
            $context->shop->id,
            $defaultPending
        );

        if (in_array(strtoupper($status), PaymentStatuses::DONE_PAYMENTS) === true) {
            $idStatePayment = $this->getReceivedTransfertStatus();
        }

        $this->module->validateOrder(
            $cart->id,
            $idStatePayment,
            $total,
            $this->module->l('Pay with Bridge', 'PaymentService'),
            null,
            $extra_vars,
            (int) $currency->id,
            false,
            $customer->secure_key
        );
    }

    /**
     * Update Order Payment that as just happen with transaction ID
     *
     * @param Order $order order created
     * @param string $transaction_id payment transaction ID
     */
    public function setTransactionId($cart)
    {
        /** @var BridgeTransaction $bridgeTransaction */
        $bridgeTransaction = $this->getTransaction($cart->id);
        $this->idTransaction = $bridgeTransaction->id_transaction;
    }

    /**
     * Get the entity BridgeTransaction by Id Cart
     *
     * @param int $id Id of order / cart to search by
     *
     * @return BridgeTransaction
     */
    public function getTransaction($idCart, $idTransaction = 0)
    {
        return $this->paymentRepository->getTransaction($idCart, $idTransaction);
    }

    /**
     * Get the order linked to one Cart
     *
     * @param int $idCart id of cart concerned
     *
     * @return \Order
     */
    public function getOrderByIdCart($idCart)
    {
        return new \Order($this->paymentRepository->getOrderIdOfCart($idCart));
    }

    /**
     * @return int Status payment received saved for shop concerned by the current card
     */
    public function getReceivedTransfertStatus()
    {
        $context = \Context::getContext();
        $idShop = $context->cart->id_shop;

        $receivedName = null !== _PS_OS_WS_PAYMENT_ ? _PS_OS_WS_PAYMENT_ : 'PS_OS_WS_PAYMENT';
        $defaultReceived = Configuration::getGlobalValue($receivedName);

        return Configuration::get(
            \Bridge::STATUS_RECEIVED_TRANSFERT,
            null,
            null,
            $idShop,
            $defaultReceived
        );
    }

    /**
     * Set status of current order with value saved for shop concerned by the cart
     */
    public function setPaymentStatus($cart, $statusPayment)
    {
        /** @var \Order $oderCart */
        $orderCart = $this->getOrderByIdCart($cart->id);

        if ((int) $orderCart->id_cart !== $cart->id) {
            return 'Error : cart linked to transaction does not match to order linked to.';
        }

        /** @var BridgeTransaction $bridgeTransaction */
        $bridgeTransaction = $this->getTransaction($cart->id);
        $bridgeTransaction->status = $statusPayment;
        $bridgeTransaction->save();

        $statusReceived = (int) $this->getReceivedTransfertStatus();

        if ($orderCart->getCurrentState() === $statusReceived) {
            return 'Order is already in this state, nothing done.';
        }

        $orderCart->setCurrentState($statusReceived);

        return 'Transaction updated with paid state.';
    }
}
