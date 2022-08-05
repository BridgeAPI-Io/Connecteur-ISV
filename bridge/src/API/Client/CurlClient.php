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

namespace BridgeAddon\API\Client;

class CurlClient
{
    protected $curl;

    /**
     * @param string $method - POST / GET / PUT Http method
     * @param string $url - URL for the request
     * @param string[] $options - Options (headers) for the request
     */
    public function callCURL($method, $url, $options)
    {
        $curl = $this->createCurlInstance($this->setQueryParams($url, $options));
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->setHeaders($options['headers']));

        if ($method === 'POST') {
            $this->setPostOptions($curl, $options);
        }

        $response = curl_exec($curl);
        $this->curl = $curl;

        return [
            'content' => $response,
            'status' => curl_getinfo($curl, CURLINFO_HTTP_CODE),
        ];
    }

    private function createCurlInstance($url)
    {
        if (defined('CURL_SSLVERSION_TLSv1_2') == false) {
            define('CURL_SSLVERSION_TLSv1_2', 6);
        }
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);

        return $curl;
    }

    private function setQueryParams($url, $options)
    {
        foreach ($options['query'] as $option => $value) {
            $url .= (int) strpos($url, '?') > 0 ? '&' : '?';
            $url .= $option . '=' . $value;
        }

        return $url;
    }

    private function setHeaders($headersArray)
    {
        $headers = [];
        foreach ($headersArray as $key => $value) {
            $headers[] = $key . ': ' . $value;
        }

        return $headers;
    }

    private function setPostOptions($curl, $options)
    {
        curl_setopt($curl, CURLOPT_POSTFIELDS, $options['body']);
        curl_setopt($curl, CURLOPT_POST, true);
    }
}
