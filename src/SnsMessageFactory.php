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
        switch ($type) {
            case 'SubscriptionConfirmation':
                return SubscriptionConfirmationEvent::fromArray($data);
            case 'Notification':
                return NotificationEvent::fromArray($data);
            case 'UnsubscribeConfirmation':
                return UnsubscribeConfirmationEvent::fromArray($data);
            default:
                throw new RuntimeException('Unknown SNS Message Type: ' . $type);
        }
    }
}

/*
* https://docs.aws.amazon.com/sns/latest/dg/sns-message-and-json-formats.html
*/
