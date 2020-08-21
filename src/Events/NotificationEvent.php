<?php declare(strict_types=1);

namespace NZTim\SNS\Events;

class NotificationEvent implements SnsEventInterface
{
    private array $data;

    private function __construct() {}

    public static function fromArray(array $data): NotificationEvent
    {
        $event = new NotificationEvent();
        $event->data = $data;
        return $event;
    }

    public function arn(): string
    {
        return $this->data['TopicArn'] ?? '';
    }

    public function type(): string
    {
        return 'Notification';
    }

    public function message(): string
    {
        return $this->data['Message'] ?? '';
    }

    public function data(): array
    {
        return $this->data;
    }
}
