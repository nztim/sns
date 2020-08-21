<?php declare(strict_types=1);

namespace NZTim\SNS\Examples;

use NZTim\Logger\Logger;
use NZTim\SNS\Events\SnsEventInterface;

class SnsLoggingListener
{
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function handle(SnsEventInterface $event)
    {
        $message = 'SNS ' . $event->type() . ': ' . $event->message();
        $this->logger->info('sns', $message);
    }
}
