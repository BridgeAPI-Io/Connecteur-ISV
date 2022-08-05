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
use BridgeAddon\API\Object\Request\Impl\GetBankRequestObject;
use BridgeAddon\API\Object\Request\Impl\ListBanksRequestObject;
use BridgeAddon\API\Object\Response\Impl\Banks\ListBanksResponse;
use BridgeAddon\API\Object\Response\Impl\UnauthorizedErrorResponse;
use BridgeAddon\Model\Bank\TreeBuilder;

class BankService
{
    /** @var ListBanksResponse */
    protected static $banks = [];

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * @param Client $client
     * @param RequestFactory $requestFactory
     */
    public function __construct(Client $client, RequestFactory $requestFactory)
    {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
    }

    /**
     * @return ListBanksResponse
     */
    public function getBanks()
    {
        if (!empty(self::$banks)) {
            return self::$banks;
        }

        $responseBanks = $this->callBanks();

        self::$banks = $responseBanks;

        $errorResponse = $responseBanks instanceof UnauthorizedErrorResponse;

        if ($errorResponse === true) {
            return self::$banks;
        }

        while ($errorResponse === false && $responseBanks->getAfter() !== '') {
            self::$banks->setBanks(
                array_merge(
                    self::$banks->getBanks(),
                    $responseBanks->getBanks()
                )
            );
            $errorResponse = $responseBanks instanceof UnauthorizedErrorResponse;
            if ($errorResponse === true) {
                break;
            }
            $responseBanks = $this->callBanks($responseBanks->getAfter());
        }

        return self::$banks;
    }

    /**
     * @param string $after
     *
     * @return ListBanksResponse $responseBanks
     */
    protected function callBanks($after = '')
    {
        $requestObject = new ListBanksRequestObject();
        $requestObject->setAfter($after);

        $request = $this->requestFactory->createRequestFromObject($requestObject);

        /** @var ListBanksResponse $responseBanks */
        $responseBanks = $this->client->call($request, false);

        return $responseBanks;
    }

    public function getBanksTree($isoCodeFilter = null)
    {
        $treeBuilder = new TreeBuilder();
        if (!empty($isoCodeFilter)) {
            $treeBuilder->setPreferredIsoCode($isoCodeFilter);
        }

        $banks = $this->getBanks();

        if (($banks instanceof ListBanksResponse) === false) {
            return [];
        }

        return $treeBuilder->build($banks);
    }

    public function getBankById($idBank)
    {
        $requestObject = new GetBankRequestObject();
        $requestObject->setId($idBank);

        $request = $this->requestFactory->createRequestFromObject($requestObject);

        return $this->client->call($request, true);
    }
}
