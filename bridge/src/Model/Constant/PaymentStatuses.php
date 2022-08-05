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

namespace BridgeAddon\Model\Constant;

class PaymentStatuses
{
    /** @var array status created : CREA, ACTC, ACCP */
    const CREATED_PAYMENTS = [
        'CREA',
        'ACTC',
        'ACCP',
    ];

    /** @var array status success : PDNG, ACSP, PART or ACSC */
    const SUCCESS_PAYMENTS = [
        'PDNG',
        'ACSP',
        'PART',
        'ACSC',
    ];

    /** @var array status done : PART or ACSC */
    const DONE_PAYMENTS = [
        'PART',
        'ACSC',
    ];

    /** @var array status rejected : CANC, RJCT */
    const REJECTED_PAYMENTS = [
        'CANC',
        'RJCT',
    ];

    /** @var string status not found (error response) */
    const ERROR_NOT_FOUND = 'errorstatus';
    const PROCESS_IN_PROGRESS = 'inprogress';
}
