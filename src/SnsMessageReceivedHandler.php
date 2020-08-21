<?php declare(strict_types=1);

namespace NZTim\SNS;

use Illuminate\Events\Dispatcher;
use NZTim\Logger\Logger;

class SnsMessageReceivedHandler
{
    private SnsMessageValidator $validator;
    private Logger $logger;
    private SnsMessageFactory $factory;
    private Dispatcher $dispatcher;

    public function __construct(SnsMessageValidator $validator, Logger $logger, SnsMessageFactory $factory, Dispatcher $dispatcher)
    {
        $this->validator = $validator;
        $this->logger = $logger;
        $this->factory = $factory;
        $this->dispatcher = $dispatcher;
    }

    public function handle(SnsMessageReceived $snsMessageRaw): void
    {
        $result = $this->validator->validate($snsMessageRaw->data());
        if ($result->failed()) {
            $this->logger->warning('sns', 'Validation failure: ' . $result->message(), $snsMessageRaw->data());
            return;
        }
        $event = $this->factory->create($snsMessageRaw->data());
        $this->dispatcher->dispatch($event);
    }
}
/*
 * handle() method returns void because this is normally handled by the queue so return value isn't used.
 * Similarly the event dispatch is here so that the controller can queue the whole thing and forget about it.
 * Any action to be taken as a result of the message happens via the event listeners (if any).
 */
