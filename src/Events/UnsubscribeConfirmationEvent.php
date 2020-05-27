<?php declare(strict_types=1);

namespace NZTim\SNS\Events;

class UnsubscribeConfirmationEvent implements SnsEventInterface
{
    private array $data;

    private function __construct() {}

    public static function fromArray(array $data): UnsubscribeConfirmationEvent
    {
        $event = new UnsubscribeConfirmationEvent();
        $event->data = $data;
        return $event;
    }

    public function type(): string
    {
        return 'Unsubscribe Confirmation';
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
