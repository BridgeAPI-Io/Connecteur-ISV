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

class BankResponse extends AbstractResponseObject
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $countryCode;

    /**
     * @var string
     */
    protected $logoUrl;

//    protected $authenticationType;

    /**
     * @var bool
     */
    protected $isHighlighted;

    protected $primaryColor;

    protected $secondaryColor;

    /**
     * @var ?string
     */
    protected $parentName;

    protected $capabilities = [];

    protected $form = [];

    protected $channelType = [];

    protected $displayOrder;

    public function fillResponse($response)
    {
        $this->id = empty($response['id']) ? 0 : (int) $response['id'];
        $this->name = empty($response['name']) ? '' : $response['name'];
        $this->countryCode = empty($response['country_code']) ? '' : $response['country_code'];
        $this->logoUrl = empty($response['logo_url']) ? '' : $response['logo_url'];
        $this->isHighlighted = empty($response['is_highlighted']) ? false : (bool) $response['is_highlighted'];
        $this->primaryColor = empty($response['primary_color']) ? '' : $response['primary_color'];
        $this->secondaryColor = empty($response['secondary_color']) ? '' : $response['secondary_color'];
        $this->displayOrder = empty($response['display_order']) ? 0 : (int) $response['display_order'];
        $this->capabilities = empty($response['capabilities']) ? [] : $response['capabilities'];
        $this->channelType = empty($response['channel_type']) ? [] : $response['channel_type'];
        $this->parentName = empty($response['parent_name']) ? [] : $response['parent_name'];
        $this->form = empty($response['form']) ? [] : $response['form'];

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @return string
     */
    public function getLogoUrl()
    {
        return $this->logoUrl;
    }

    /**
     * @return bool
     */
    public function isHighlighted()
    {
        return $this->isHighlighted;
    }

    /**
     * @return mixed
     */
    public function getPrimaryColor()
    {
        return $this->primaryColor;
    }

    /**
     * @return mixed
     */
    public function getSecondaryColor()
    {
        return $this->secondaryColor;
    }

    /**
     * @return string|null
     */
    public function getParentName()
    {
        return $this->parentName;
    }

    /**
     * @return array
     */
    public function getCapabilities()
    {
        return $this->capabilities;
    }

    /**
     * @return array
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return array
     */
    public function getChannelType()
    {
        return $this->channelType;
    }

    /**
     * @return mixed
     */
    public function getDisplayOrder()
    {
        return $this->displayOrder;
    }
}
