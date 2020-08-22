<?php declare(strict_types=1);

namespace NZTim\SNS;

class SnsMessageReceived
{
    private string $data;

    public static function fromArray(array $data): SnsMessageReceived
    {
        $command = new SnsMessageReceived();
        $command->data = json_encode($data);
        return $command;
    }

    private function __construct() {}

    public function data(): array
    {
        return json_decode($this->data, true);
    }
}
