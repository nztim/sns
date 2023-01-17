<?php declare(strict_types=1);

namespace NZTim\SNS;

use NZTim\SNS\Events\NotificationEvent;
use NZTim\SNS\Events\SnsEventInterface;
use NZTim\SNS\Events\SubscriptionConfirmationEvent;
use NZTim\SNS\Events\UnsubscribeConfirmationEvent;
use RuntimeException;

class SnsMessageFactory
{
    public function create(array $data): SnsEventInterface
    {
        $type = $data['Type'] ?? '';
        return match ($type) {
            'SubscriptionConfirmation' => SubscriptionConfirmationEvent::fromArray($data),
            'Notification' => NotificationEvent::fromArray($data),
            'UnsubscribeConfirmation' => UnsubscribeConfirmationEvent::fromArray($data),
            default => throw new RuntimeException('Unknown SNS Message Type: ' . $type),
        };
    }
}

/*
* https://docs.aws.amazon.com/sns/latest/dg/sns-message-and-json-formats.html
*/
