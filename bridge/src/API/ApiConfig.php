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

namespace BridgeAddon\API;

class ApiConfig
{
    const VERSION = '2021-06-01';

    const BASE_URL = 'https://api.bridgeapi.io/v2/';

    public function getClientId($keyProdTest)
    {
        return \Configuration::get(\Bridge::CLIENT_ID . $keyProdTest);
    }

    public function getClientSecret($keyProdTest)
    {
        return \Configuration::get(\Bridge::CLIENT_SECRET . $keyProdTest);
    }

    public function getVersion()
    {
        return self::VERSION;
    }

    public function getBaseUrl()
    {
        return self::BASE_URL;
    }
}
