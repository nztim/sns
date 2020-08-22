<?php declare(strict_types=1);

namespace NZTim\SNS;

use Illuminate\Http\Request;

class SnsMessageReceived
{
    private string $data;

    public static function fromArray(array $data): SnsMessageReceived
    {
        $command = new SnsMessageReceived();
        $command->data = json_encode($data);
        return $command;
    }

    public static function fromLaravelRequest(Request $request): SnsMessageReceived
    {
        $command = new SnsMessageReceived();
        $command->data = strval($request->getContent());
        return $command;
    }

    private function __construct() {}

    public function data(): array
    {
        return json_decode($this->data, true);
    }
}
