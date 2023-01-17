<?php declare(strict_types=1);

namespace NZTim\SNS;

class SnsMessageReceived
{
    public array $data;

    public static function fromString(string $message): ?SnsMessageReceived
    {
        $decoded = json_decode($message, true);
        if (!is_array($decoded)) {
            return null;
        }
        $command = new SnsMessageReceived();
        $command->data = $decoded;
        return $command;
    }

    private function __construct() {}
}
