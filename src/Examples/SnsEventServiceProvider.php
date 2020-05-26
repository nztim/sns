<?php declare(strict_types=1);

namespace NZTim\SNS\Examples;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use NZTim\SNS\Events\SubscriptionConfirmationEvent;

class SnsEventServiceProvider extends ServiceProvider
{
    protected $listen = [
        SubscriptionConfirmationEvent::class => [SnsLoggingListener::class],
    ];
}
