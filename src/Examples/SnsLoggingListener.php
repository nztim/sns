<?php declare(strict_types=1);

namespace NZTim\SNS\Examples;

use NZTim\SNS\Events\SnsEventInterface;
use Psr\Log\LoggerInterface;

class SnsLoggingListener
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(SnsEventInterface $event)
    {
        $message = 'SNS ' . $event->type() . ': ' . $event->message();
        $this->logger->info($message);
    }
}
