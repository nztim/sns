<?php declare(strict_types=1);

namespace NZTim\SNS\Events;

class SubscriptionConfirmationEvent implements SnsEventInterface
{
    private array $data;

    private function __construct() {}

    public static function fromArray(array $data): SubscriptionConfirmationEvent
    {
        $event = new SubscriptionConfirmationEvent();
        $event->data = $data;
        return $event;
    }

    public function type(): string
    {
        return 'Subscription Confirmation';
    }

    public function message(): string
    {
        return $this->data['Message'] ?? '';
    }

    public function confirmationUrl(): string
    {
        return $this->data['SubscribeURL'] ?? '';
    }
}
