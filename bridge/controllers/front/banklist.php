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

use BridgeAddon\Service\BankService;
use BridgeAddon\Utils\ServiceContainer;
use PrestaShop\PrestaShop\Adapter\Entity\Address;

class BridgeBanklistModuleFrontController extends ModuleFrontController
{
    protected function getBanksTree()
    {
        /** @var BankService $banksService */
        $banksService = ServiceContainer::getInstance()->get(BankService::class);

        try {
            $address = new Address(Context::getContext()->cart->id_address_invoice);
            $country = new Country($address->id_country);
            $isoCode = $country->iso_code;
        } catch (Exception $ex) {
            $isoCode = Context::getContext()->country->iso_code;
        }

        return $banksService->getBanksTree($isoCode);
    }

    public function displayAjaxGetBanksList()
    {
        $banksTree = $this->getBanksTree();

        $this->ajaxDie(json_encode($banksTree));
    }
}
