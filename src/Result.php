<?php declare(strict_types=1);

namespace NZTim\SNS;

class Result
{
    private bool $success;
    private string $message;

    private function __construct() {}

    public static function createSuccess(string $message = ''): Result
    {
        $result = new Result();
        $result->success = true;
        $result->message = $message;
        return $result;
    }

    public static function createFailed(string $message): Result
    {
        $result = new Result();
        $result->success = false;
        $result->message = $message;
        return $result;
    }

    public function success(): bool
    {
        return $this->success;
    }

    public function failed(): bool
    {
        return !$this->success;
    }

    public function message(): string
    {
        return $this->message;
    }
}
