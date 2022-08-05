<?php

/*
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SARL 202 ecommerce
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL 202 ecommerce is strictly forbidden.
 * In order to obtain a license, please contact us: tech@202-ecommerce.com
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise a une licence commerciale
 * concedee par la societe 202 ecommerce
 * Toute utilisation, reproduction, modification ou distribution du present
 * fichier source sans contrat de licence ecrit de la part de la SARL 202 ecommerce est
 * expressement interdite.
 * Pour obtenir une licence, veuillez contacter 202-ecommerce <tech@202-ecommerce.com>
 * ...........................................................................
 *
 * @author    202-ecommerce <tech@202-ecommerce.com>
 * @copyright Copyright (c) 202-ecommerce
 * @license   Commercial license
 */

namespace BridgeAddon\Entity;

use ObjectModel;

class BridgeTransaction extends ObjectModel
{
    /** @var int */
    public $id_bridge_transaction;

    /** @var string */
    public $id_transaction;

    /** @var int */
    public $id_cart;

    /** @var string */
    public $status;

    /** @var int */
    public $id_order;

    /** @var int */
    public $id_bank;

    /** @var int */
    public $url;

    /** @var string Date */
    public $date_add;

    /** @var string Date */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'bridge_transactions',
        'multilang' => false,
        'primary' => 'id_bridge_transaction',
        'fields' => [
            'id_transaction' => [
                'type' => self::TYPE_STRING,
                'required' => false,
            ],
            'id_cart' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => true,
            ],
            'status' => [
                'type' => self::TYPE_STRING,
                'required' => false,
            ],
            'id_order' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => false,
            ],
            'id_bank' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => true,
            ],
            'url' => [
                'type' => self::TYPE_STRING,
            ],
            'date_add' => [
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'copy_post' => false,
            ],
            'date_upd' => [
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'copy_post' => false,
            ],
        ],
    ];
}
