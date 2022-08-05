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

namespace BridgeAddon\Hook;

use BridgeClasslib\Hook\AbstractHook;
use Context;
use Media;
use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

class PaymentHook extends AbstractHook
{
    const AVAILABLE_HOOKS = [
        'paymentOptions',
        'paymentReturn',
        'displayPaymentReturn',
    ];

    public function paymentOptions($params)
    {
        /** @var \Currency $currency */
        $currency = new \Currency(Context::getContext()->cart->id_currency);
        if (array_search($currency->iso_code, \Bridge::AVAILABLE_CURRENCIES) === false) {
            return;
        }

        if (!$this->module->active) {
            return;
        }

        return [
            $this->getBridgePaymentOption(),
        ];
    }

    protected function getBridgePaymentOption()
    {
        $logoPayment = Media::getMediaPath(_PS_MODULE_DIR_ . $this->module->name . '/views/img/logo-payment.png');
        $bridgePaymentOption = new PaymentOption();
        $bridgePaymentOption->setCallToActionText($this->l('Instant bank payment '))
            ->setModuleName($this->module->name)
            ->setForm($this->generatePaymentForm())
            ->setAdditionalInformation(Context::getContext()->smarty->fetch('module:bridge/views/templates/front/payment_infos.tpl'))
            ->setLogo($logoPayment)
            ->setBinary(true);

        return $bridgePaymentOption;
    }

    protected function generatePaymentForm()
    {
        Context::getContext()->smarty->assign([
            'action' => Context::getContext()->link->getModuleLink($this->module->name, 'validation', [], true),
        ]);

        return Context::getContext()->smarty->fetch('module:bridge/views/templates/front/payment_form.tpl');
    }

    protected function paymentReturnTemplate()
    {
        return Context::getContext()->smarty->fetch('module:bridge/views/templates/front/payment_return.tpl');
    }

    public function displayPaymentReturn($params)
    {
        return $this->paymentReturnTemplate();
    }

    public function paymentReturn($params)
    {
        return $this->paymentReturnTemplate();
    }
}
