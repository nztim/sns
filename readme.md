# SNS

Package to receive, verify and process SNS webhooks into Events.

### Installation

* `composer require nztim/sns`

### Usage

* Create a route to receive SNS requests, connect it to `NZTim\SNS\WebhookController` (or your own handler).
* Configure your EventServiceProvider to handle SNS events as required: `SubscriptionConfirmationEvent`, `UnsubscribeConfirmationEvent` and `NotificationEvent`.

### Upgrading

* 2.0 - Moved WebhookController, removed example SnsEventServiceProvider and SnsLoggingListener - make sure they are not referenced 
