<?php declare(strict_types=1);

namespace NZTim\SNS\Examples;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use NZTim\SNS\Events\NotificationEvent;
use NZTim\SNS\Events\SubscriptionConfirmationEvent;
use NZTim\SNS\Events\UnsubscribeConfirmationEvent;

class SnsEventServiceProvider extends ServiceProvider
{
    protected $listen = [
        SubscriptionConfirmationEvent::class => [
            SnsLoggingListener::class,
        ],
        UnsubscribeConfirmationEvent::class  => [
            SnsLoggingListener::class,
        ],
        NotificationEvent::class             => [
            SnsLoggingListener::class,
        ],
    ];
}
