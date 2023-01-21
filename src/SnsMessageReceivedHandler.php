<?php declare(strict_types=1);

namespace NZTim\SNS;

use Illuminate\Events\Dispatcher;
use NZTim\Logger\Logger;
use NZTim\SNS\Events\SnsEventInterface;

class SnsMessageReceivedHandler
{
    private SnsMessageValidator $validator;
    private Logger $logger;
    private SnsMessageFactory $factory;
    private Dispatcher $dispatcher;
    private bool $dispatch;

    public function __construct(SnsMessageValidator $validator, Logger $logger, SnsMessageFactory $factory, Dispatcher $dispatcher)
    {
        $this->validator = $validator;
        $this->logger = $logger;
        $this->factory = $factory;
        $this->dispatcher = $dispatcher;
        $this->dispatch = true;
    }

    // Disable dispatch using this method (via Service provider) in order to handle the event manually rather than via dispatcher.
    public function disableDispatch(): SnsMessageReceivedHandler
    {
        $this->dispatch = false;
        return $this;
    }

    public function handle(SnsMessageReceived $messageReceived): ?SnsEventInterface
    {
        $result = $this->validator->validate($messageReceived->data);
        if (!$result->success) {
            $this->logger->warning('sns', 'Validation failure: ' . $result->message, $messageReceived->data);
            return null;
        }
        $event = $this->factory->create($messageReceived->data);
        if ($this->dispatch) {
            $this->dispatcher->dispatch($event);
        }
        return $event;
    }
}
