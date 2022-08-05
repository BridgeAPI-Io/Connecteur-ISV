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

namespace BridgeAddon\API\Logger;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class ApiLogger
{
    public $logname;

    const LOGGER_NAME = 'bridge';

    const MAX_LOG_FILE_SIZE = 2000000000;

    private static $instance = null;

    private $stream;

    /**
     * @var Logger
     */
    private $logger;

    final protected function __construct()
    {
        $this->logname = 'bridge-' . date('Ymd') . '.log';
        $this->build();
    }

    protected function __clone()
    {
    }

    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize a singleton.');
    }

    protected function build()
    {
        $logFile = _PS_MODULE_DIR_ . 'bridge/logs/' . $this->logname;
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile));
        }
        if (file_exists($logFile)) {
            $fileSize = filesize($logFile);
            if ($fileSize > self::MAX_LOG_FILE_SIZE) {
                unlink($logFile);
            }
        }
        $this->stream = fopen($logFile, 'a+');
        $this->logger = new Logger(self::LOGGER_NAME, [new StreamHandler($this->stream)]);
    }

    public function logRequest($requestObject, $data)
    {
        $this->logger->addInfo((new \ReflectionClass($requestObject))->getShortName() . '; Data: ' . $data);
    }

    public function logResponse($responseObject, $data)
    {
        $this->logger->addInfo((new \ReflectionClass($responseObject))->getShortName() . '; Data: ' . json_encode($data));
    }

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function __destruct()
    {
        fclose($this->stream);
    }
}
