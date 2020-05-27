<?php declare(strict_types=1);

namespace NZTim\SNS\Events;

interface SnsEventInterface
{
    public static function fromArray(array $data): SnsEventInterface;
    public function type(): string;
    public function message(): string;
    public function data(): array;
}

/*
 * https://docs.aws.amazon.com/sns/latest/dg/sns-message-and-json-formats.html
 */
