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

class CommonHook extends AbstractHook
{
    protected $module;

    const AVAILABLE_HOOKS = [
        'actionFrontControllerSetMedia',
        'displayBackOfficeHeader',
    ];

    public function actionFrontControllerSetMedia($params)
    {
        $controller = Context::getContext()->controller;
        if ($controller instanceof \OrderController) {
            Media::addJsDef([
                'bridge' => [
                    'translations' => [
                        'back' => $this->module->l('Back', $this->getClassShortName()),
                        'pay' => $this->module->l('Pay via Bridge', $this->getClassShortName()),
                        'choose_bank' => $this->module->l('Choose my bank', $this->getClassShortName()),
                        'search_bank' => $this->module->l('Search a bank', $this->getClassShortName()),
                        'no_results_found' => $this->module->l('No results to display', $this->getClassShortName()),
                    ],
                    'bank_list_url' => Context::getContext()->link->getModuleLink($this->module->name, 'banklist'),
                ],
            ]);
            $controller->registerJavascript(
                'bridge-vue-main',
                'modules/' . $this->module->name . '/views/js/front/front.js',
                [
                    'priority' => 500,
                ]
            );
            $controller->registerStylesheet(
                'bridge-vue-main-css',
                'modules/' . $this->module->name . '/views/css/front.css',
                [
                    'priority' => 500,
                ]
            );
        }
    }
}
