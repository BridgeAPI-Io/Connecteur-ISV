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
use BridgeAddon\API\Logger\ApiLogger;
use BridgeAddon\API\Object\Response\Impl\UnauthorizedErrorResponse;
use BridgeAddon\Utils\ServiceContainer;
use BridgeClasslib\Extensions\ProcessLogger\ProcessLoggerHandler;
use Configuration;
use Exception;

class ConfigService
{
    public $module;

    private $curl;

    private $context;

    /** @var ProcessLoggerHandler */
    protected $logger;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    public function __construct(
        Client $client,
        RequestFactory $requestFactory,
        ProcessLoggerHandler $logger,
        \PaymentModule $module
    ) {
        $this->module = $module;
        $this->logger = $logger;
        $this->context = \Context::getContext();
        $this->client = $client;
        $this->requestFactory = $requestFactory;
    }

    /**
     * Check TLS version 1.2 compability : CURL request to server
     */
    public function isTlsActive()
    {
        $return = [
            'status' => false,
            'error_message' => '',
        ];

        $tls_server = $this->context->link->getModuleLink($this->module->name, 'testcurltls');
        $response = $this->callCURL($tls_server);
        $curl = $this->curl;

        if (trim($response) != 'ok') {
            $return['status'] = false;
            $curl_info = curl_getinfo($curl);
            if ($curl_info['http_code'] == 401) {
                $return['error_message'] = '401 Unauthorised - check htaccess and rights in server.';
            } else {
                $return['error_message'] = curl_error($curl);
            }

            $this->logger->openLogger();
            $this->logger->logError(
                $return['error_message'],
                (new \ReflectionClass($this))->getShortName(),
                null,
                'configuration - TLS test'
            );
            $this->logger->closeLogger();

            $return['error_message'] = $this->module->l('TLS call failed', 'ConfigService');
        } else {
            $return['status'] = true;
        }

        return $return;
    }

    public function callCURL($url)
    {
        if (defined('CURL_SSLVERSION_TLSv1_2') == false) {
            define('CURL_SSLVERSION_TLSv1_2', 6);
        }

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);

        $response = curl_exec($curl);
        $this->curl = $curl;

        $apiLogger = ApiLogger::getInstance();
        if (\Bridge::IS_FILE_LOGGER_ACTIVE === true) {
            $apiLogger->logResponse($this, $response);
        }

        return $response;
    }

    public function isSslActive()
    {
        return Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE');
    }

    public function isListingBanks($clientID, $clientSecret)
    {
        if ($clientSecret === '' || $clientID === '') {
            return [
                'success' => false,
                'message' => $this->module->l('No configuration saved', 'ConfigService'),
            ];
        }

        /** @var BankService $banksService */
        $banksService = ServiceContainer::getInstance()->get(BankService::class);

        $objectBanks = $banksService->getBanks();

        if ($objectBanks instanceof UnauthorizedErrorResponse) {
            try {
                $errorMsg = 'Error : ' . $objectBanks->getMessage();
            } catch (Exception $ex) {
                $errorMsg = sprintf($ex->getMessage());
            }
            $this->logger->openLogger();
            $this->logger->logError(
                $errorMsg,
                (new \ReflectionClass($objectBanks))->getShortName(),
                null,
                'listing bank configuration'
            );
            $this->logger->closeLogger();

            $message = $this->module->l(
                'Error while calling the Bridge API please consult logs for more informations.',
                'ConfigService'
            );

            return [
                'success' => false,
                'message' => $message,
            ];
        }

        return [
            'success' => true,
            'message' => count($objectBanks->getBanks()) . ' banks',
        ];
    }
}
