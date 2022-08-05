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

namespace BridgeAddon\API\Request;

use BridgeAddon\API\Object\Request\RequestObjectInterface;

class AbstractRequest
{
    /**
     * @var string
     */
    protected $method;

    /**
     * @var
     */
    protected $url;

    /**
     * @var RequestObjectInterface
     */
    protected $requestObject;

    public function __construct(RequestObjectInterface $requestObject)
    {
        $this->requestObject = $requestObject;
    }

    //region Fields

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return RequestObjectInterface
     */
    public function getRequestObject()
    {
        return $this->requestObject;
    }

    public function getRequestOptions()
    {
        if (!is_null($this->getRequestObject())) {
            return $this->requestObject->getParams();
        }

        return null;
    }

    /**
     * @param RequestObjectInterface $requestObject
     *
     * @return $this
     */
    public function setRequestObject(RequestObjectInterface $requestObject)
    {
        $this->requestObject = $requestObject;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    //endregion
}
