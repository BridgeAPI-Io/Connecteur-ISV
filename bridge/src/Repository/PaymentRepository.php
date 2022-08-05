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

namespace BridgeAddon\Repository;

use BridgeAddon\Entity\BridgeTransaction;
use Db;

class PaymentRepository
{
    /**
     * Get the entity BridgeTransaction by Id Cart
     *
     * @param int $id Id of transaction / cart to search by
     *
     * @return BridgeTransaction
     */
    public function getTransaction($idCart, $idTransaction = 0)
    {
        $query = new \DbQuery();
        $query->select('id_bridge_transaction');
        if ($idTransaction === 0) {
            $query->where('id_cart = ' . pSQL($idCart));
        } else {
            $query->where('id_transaction = "' . pSQL($idTransaction) . '"');
        }
        $query->from(BridgeTransaction::$definition['table']);
        $query->orderBy(BridgeTransaction::$definition['primary'] . ' DESC');

        $result = Db::getInstance()->getRow($query);
        if (empty($result['id_bridge_transaction']) === true) {
            $bridgeTransaction = new BridgeTransaction();
            $bridgeTransaction->id_cart = $idCart;
        } else {
            $bridgeTransaction = new BridgeTransaction($result['id_bridge_transaction']);
        }

        return $bridgeTransaction;
    }

    /**
     * Get the order linked to one Cart
     *
     * @param int $idCart id of cart concerned
     *
     * @return int $idOrder (0 if no order found)
     */
    public function getOrderIdOfCart($idCart)
    {
        return (int) Db::getInstance()->getValue(
            'SELECT `id_order` FROM `' . _DB_PREFIX_ . 'orders` WHERE `id_cart` = ' . (int) pSQL($idCart),
            false
        );
    }
}
