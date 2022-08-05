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

namespace BridgeAddon\API\Object\Response\Impl\Banks;

use BridgeAddon\API\Object\Response\AbstractResponseObject;

class ListBanksResponse extends AbstractResponseObject
{
    protected $banks = [];

    private $after = '';

    public function fillResponse($response)
    {
        if (empty($response['pagination']) === false && empty($response['pagination']['next_uri']) === false) {
            $this->after = $this->getAfterParam($response['pagination']['next_uri']);
        }

        if (!empty($response['resources']) && is_array($response['resources'])) {
            foreach ($response['resources'] as $resource) {
                $bank = (new BankResponse())->fillResponse($resource);
                if (empty($bank)) {
                    continue;
                }
                $this->banks[] = $bank;
            }
        }

        return $this;
    }

    protected function getAfterParam($url)
    {
        $paramsString = explode('?', $url);
        $allParams = explode('&', $paramsString[1]);
        foreach ($allParams as $aParam) {
            if (strpos($aParam, 'after=') !== false) {
                return str_replace('after=', '', $aParam);
            }
        }

        return '';
    }

    /**
     * @return mixed
     */
    public function getBanks()
    {
        return $this->banks;
    }

    /**
     * @return mixed
     */
    public function setBanks($banks)
    {
        $this->banks = $banks;
    }

    /**
     * @return mixed
     */
    public function getAfter()
    {
        return $this->after;
    }
}
