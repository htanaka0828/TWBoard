<?php
namespace TWB\Services;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class LogService
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * LogService constructor.
     * @param Logger|null $logger
     * @param StreamHandler|null $streamHandler
     */
    public function __construct(Logger $logger = null, StreamHandler $streamHandler = null)
    {
        $this->logger = $logger ?: new Logger('app');
        if (!$streamHandler) {
            $streamHandler = $streamHandler ?: new StreamHandler('php://stderr', Logger::DEBUG);
            $dateFormat = 'Y-m-d H:i:s';
            $lineFormat = '[%datetime%] %level_name%: %message%' . PHP_EOL;
            $formatter = new LineFormatter($lineFormat, $dateFormat, true);
            $streamHandler->setFormatter($formatter);
        }
        $this->logger->pushHandler($streamHandler);
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param array $arr
     * @return string
     */
    public function arrayToJson(array $arr)
    {
        return json_encode($arr, JSON_UNESCAPED_UNICODE);
    }
}