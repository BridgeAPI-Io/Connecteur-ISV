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

namespace BridgeAddon\API\Client;

use BridgeAddon\API\ApiConfig;
use BridgeAddon\API\Exception\ClientException;
use BridgeAddon\API\Exception\RequestException;
use BridgeAddon\API\Factory\RequestValidatorFactory;
use BridgeAddon\API\Factory\ResponseFactory;
use BridgeAddon\API\Logger\ApiLogger;
use BridgeAddon\API\Request\AbstractRequest;
use BridgeClasslib\Extensions\ProcessLogger\ProcessLoggerHandler;
use Exception;

class Client
{
    /**
     * @var CurlClient
     */
    private $client;

    /**
     * @var ApiConfig
     */
    private $apiConfig;

    /**
     * @var ProcessLoggerHandler
     */
    private $logger;

    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * @var RequestValidatorFactory
     */
    private $requestValidatorFactory;

    /**
     * @var ApiLogger
     */
    private $apiLogger;

    /**
     * Client constructor.
     *
     * @param ApiConfig $apiConfig
     * @param ProcessLoggerHandler $logger
     * @param ResponseFactory $responseFactory
     * @param RequestValidatorFactory $requestValidatorFactory
     */
    public function __construct(ApiConfig $apiConfig,
                                ProcessLoggerHandler $logger,
                                ResponseFactory $responseFactory,
                                RequestValidatorFactory $requestValidatorFactory)
    {
        $this->apiConfig = $apiConfig;
        $this->logger = $logger;
        $this->responseFactory = $responseFactory;
        $this->requestValidatorFactory = $requestValidatorFactory;
        $this->apiLogger = ApiLogger::getInstance();
    }

    /**
     * @param AbstractRequest $request
     *
     * @return mixed
     *
     * @throws ClientException
     * @throws RequestException
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function call(AbstractRequest $request, $logResponse = true)
    {
        $this->logger->openLogger();

        try {
            $requestValidator = $this->requestValidatorFactory->getValidatorByRequestObject($request->getRequestObject());
            if (!is_null($requestValidator) && !$requestValidator->validate()) {
                throw new RequestException($requestValidator->getError());
            }

            $this->client = $this->createClient();
            $response = $this->executeRequest($request);

            $responseData = json_decode($response['content'], true);

            $responseObject = $this->responseFactory->getResponseFromRequest(
                $request,
                $response['status'],
                $responseData
            );

            if ($logResponse === true && \Bridge::IS_FILE_LOGGER_ACTIVE === true) {
                $this->apiLogger->logResponse($responseObject, $responseData);
            }

            $responseObject->fillResponse($responseData);

            return $responseObject;
        } catch (Exception $e) {
            $this->logger->logError(
                sprintf($e->getMessage()),
                (new \ReflectionClass($request))->getShortName(),
                null,
                !empty($request->getRequestObject()->getRequestIdentifier())
                    ? $request->getRequestObject()->getRequestIdentifier()
                    : null
            );
            throw $e;
        } finally {
            $this->logger->closeLogger();
        }
    }

    /**
     * @param AbstractRequest $request
     *
     * @throws ClientException
     */
    private function createClient()
    {
        try {
            return new CurlClient();
        } catch (Exception $exception) {
            throw new ClientException($exception->getMessage());
        }
    }

    private function executeRequest(AbstractRequest $request)
    {
        try {
            if (empty($this->client)) {
                throw new ClientException('Curl Client object is empty');
            }

            $keyProdTest = '';
            $isProduction = (bool) \Configuration::get(\Bridge::PRODUCTION_MODE);
            if ($isProduction === true) {
                $keyProdTest .= '_PRODUCTION';
            }

            $options['headers'] = [
                'Client-Id' => $this->apiConfig->getClientId($keyProdTest),
                'Client-Secret' => $this->apiConfig->getClientSecret($keyProdTest),
                'Bridge-Version' => $this->apiConfig->getVersion(),
            ];

            if (!empty($request->getRequestObject()->getQueryParams())) {
                /** @var QueryParam $queryParam */
                foreach ($request->getRequestObject()->getQueryParams() as $queryParam) {
                    $options['query'][$queryParam->getName()] = $queryParam->getValue();
                }
            }

            if ($isProduction === false) {
                $options['query']['sandbox'] = 1;
                $options['headers']['sandbox'] = 1;
            }

            if (!empty($request->getRequestObject()->getParams())) {
                $bodyJSON = json_encode($request->getRequestObject()->getParams());
                $options['body'] = $bodyJSON;
                $options['headers']['Content-type'] = 'application/json';

                if (\Bridge::IS_FILE_LOGGER_ACTIVE === true) {
                    $this->apiLogger->logRequest($request, $bodyJSON);
                }
            }

            $result = $this->client->callCURL(
                $request->getMethod(),
                $this->getApiConfig()->getBaseUrl() . $request->getUrl(),
                $options
            );

            return $result;
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * @return ApiConfig
     */
    public function getApiConfig()
    {
        return $this->apiConfig;
    }
}
