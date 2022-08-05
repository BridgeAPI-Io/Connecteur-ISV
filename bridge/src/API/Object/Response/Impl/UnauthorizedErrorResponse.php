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

namespace BridgeAddon\API\Object\Response\Impl;

use BridgeAddon\API\Object\Response\AbstractResponseObject;

class UnauthorizedErrorResponse extends AbstractResponseObject
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $documentationUrl;

    public function fillResponse($response)
    {
        return $this->setType(empty($response['type']) === false ? $response['type'] : 'Unknown')
            ->setMessage(empty($response['message']) === false ? $response['message'] : 'no message')
            ->setDocumentationUrl(
                empty($response['documentation_url']) === false
                ? $response['documentation_url']
                : 'no documentation URL'
            );
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getDocumentationUrl()
    {
        return $this->documentationUrl;
    }

    /**
     * @param string $documentationUrl
     */
    public function setDocumentationUrl($documentationUrl)
    {
        $this->documentationUrl = $documentationUrl;

        return $this;
    }
}
