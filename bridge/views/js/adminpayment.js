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


document.onreadystatechange = addPaymentBridge;

function addPaymentBridge() {
    var bridgeZones = $('.bridge-payment-zone');
    // console.log(bridgeZones);
    if (bridgeZones && bridgeZones.length) {
        return false;
    }

    // console.log(bridgetplconf);
    // $('.module-item-list').append(bridgetplconf);

    console.log($('#tab_modules_list_installed > table > tbody'));
    $('#tab_modules_list_installed > table > tbody').append('<tr><td rowspan=3>' + bridgetplconf + '</td></tr>');
}