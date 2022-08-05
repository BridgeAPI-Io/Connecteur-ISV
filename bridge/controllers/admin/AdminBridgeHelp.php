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
class AdminBridgeHelpController extends ModuleAdminController
{
    /** @var \Module Instance of your module automatically set by ModuleAdminController */
    public $module;

    /**
     * @see AdminController::initPageHeaderToolbar()
     */
    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        // Remove the help icon of the toolbar which no useful for us
        $this->context->smarty->clearAssign('help_link');
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS(_PS_MODULE_DIR_ . 'bridge/views/js/admin.js');
        $this->addCSS(_PS_MODULE_DIR_ . 'bridge/views/css/admin.css');
    }

    public function initContent()
    {
        $language = $this->context->language->iso_code;
        $locale = strtolower(Tools::getValue('lang', $language));

        $template = 'content-' . $locale . '.tpl';
        $tplFile = _PS_MODULE_DIR_ . 'bridge/views/templates/admin/help/' . $template;

        if (is_file($tplFile) === false) {
            $template = 'content-en.tpl';
        }

        $tplFile = _PS_MODULE_DIR_ . 'bridge/views/templates/admin/help/content.tpl';

        $tpl = $this->context->smarty->createTemplate($tplFile);

        $urlWebhook = Context::getContext()->link->getModuleLink(
            $this->module->name,
            'webhook',
            [
                'secure_key' => $this->module->secure_key,
            ]
        );

        $tpl->assign([
            'template_file' => './' . $template,
            'links' => [
                'contact' => 'https://contact.bridgeapi.io/fr-contactez-nous',
                'signup' => 'https://dashboard.bridgeapi.io/signup?utm_campaign=UTM',
                'testpay' => 'https://bridgeapi.zendesk.com/hc/en-150/articles/4428826451602-Guide-How-to-make-your-first-test-payment',
                'webhook' => $urlWebhook,
            ],
            'infos' => [
                'shop' => Context::getContext()->link->getBaseLink(),
                'php' => phpversion(),
                'ps_version' => (string) _PS_VERSION_,
                'bridge_version' => (string) $this->module->version,
                'multishop' => Shop::isFeatureActive() ? $this->module->l('Yes') : $this->module->l('No'),
            ],
        ]);

        $this->content .= $tpl->fetch();

        parent::initContent();
    }
}
